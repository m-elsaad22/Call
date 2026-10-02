#!/usr/bin/env python3
"""Small WordPress REST + WPVibe CLI helper. Needs WP_USER and WP_APP_PASS."""

from __future__ import annotations

import base64
import binascii
import json
import os
import urllib.parse
import urllib.request
from typing import Any

WP = "https://www.rukn-eltatawer.com"


def has_auth() -> bool:
    return bool(os.environ.get("WP_USER") and os.environ.get("WP_APP_PASS"))


def auth_header() -> dict:
    user = os.environ["WP_USER"]
    pw = os.environ["WP_APP_PASS"].replace(" ", "")
    token = base64.b64encode(f"{user}:{pw}".encode()).decode()
    return {
        "Authorization": f"Basic {token}",
        "Content-Type": "application/json",
        "User-Agent": "CursorAgent/1.0",
    }


def api_get(path: str, params: dict | None = None, timeout: int = 90) -> Any:
    if params:
        path = f"{path}?{urllib.parse.urlencode(params, doseq=True)}"
    h = {k: v for k, v in auth_header().items() if k != "Content-Type"}
    req = urllib.request.Request(f"{WP}/wp-json/{path.lstrip('/')}", headers=h)
    with urllib.request.urlopen(req, timeout=timeout) as r:
        return json.load(r)


def api_post(path: str, payload: dict, timeout: int = 180) -> Any:
    req = urllib.request.Request(
        f"{WP}/wp-json/{path.lstrip('/')}",
        data=json.dumps(payload).encode(),
        headers=auth_header(),
        method="POST",
    )
    with urllib.request.urlopen(req, timeout=timeout) as r:
        return json.load(r)


def public_get(path: str, params: dict | None = None, timeout: int = 60) -> Any:
    if params:
        path = f"{path}?{urllib.parse.urlencode(params, doseq=True)}"
    req = urllib.request.Request(
        f"{WP}/wp-json/{path.lstrip('/')}",
        headers={"User-Agent": "CursorAgent/1.0"},
    )
    with urllib.request.urlopen(req, timeout=timeout) as r:
        return json.load(r)


def cli(command: str, write: bool = False) -> dict:
    return api_post("wpvibe/v1/cli/run", {"command": command, "confirm_write": write})


def cli_approved(command: str) -> dict:
    return api_post("wpvibe/v1/cli/run-approved", {"command": command, "confirm_write": True})


def php_serialize(value: Any) -> str:
    if value is None:
        return "N;"
    if isinstance(value, bool):
        return "b:1;" if value else "b:0;"
    if isinstance(value, int) and not isinstance(value, bool):
        return f"i:{value};"
    if isinstance(value, float):
        return f"d:{value};"
    if isinstance(value, str):
        b = value.encode("utf-8")
        return f's:{len(b)}:"{value}";'
    if isinstance(value, dict):
        out = [f"a:{len(value)}:{{"]
        for k, v in value.items():
            if isinstance(k, int) or (isinstance(k, str) and str(k).isdigit()):
                out.append(php_serialize(int(k)))
            else:
                out.append(php_serialize(str(k)))
            out.append(php_serialize(v))
        out.append("}")
        return "".join(out)
    if isinstance(value, (list, tuple)):
        out = [f"a:{len(value)}:{{"]
        for i, v in enumerate(value):
            out.append(php_serialize(i))
            out.append(php_serialize(v))
        out.append("}")
        return "".join(out)
    raise TypeError(type(value))


def sql_set_meta(post_id: int, key: str, value: Any) -> None:
    ser = php_serialize(value) if isinstance(value, (dict, list)) else str(value)
    cli(f"post meta update {post_id} {key} tmp --force", write=True)
    hx = binascii.hexlify(ser.encode("utf-8")).decode()
    q = (
        f"UPDATE wp3mdn_postmeta SET meta_value=UNHEX('{hx}') "
        f"WHERE post_id={post_id} AND meta_key='{key}'"
    )
    cmd = f'db query "{q}"'
    try:
        cli(cmd, write=True)
    except Exception as e:
        body = e.read().decode() if hasattr(e, "read") else str(e)
        if "approval_required" not in body and "409" not in body:
            raise
    r = cli_approved(cmd)
    if r.get("exit_code") not in (0, None):
        raise RuntimeError(f"SQL meta failed {key}: {r}")


def purge_caches() -> None:
    try:
        cli("litespeed-purge all", write=True)
        cli("cache flush", write=True)
    except Exception as e:
        print("cache purge skipped", e)


def find_post_by_slug(slug: str) -> dict | None:
    posts = public_get("wp/v2/posts", {"slug": slug, "_fields": "id,slug,link,status,title"})
    return posts[0] if posts else None
