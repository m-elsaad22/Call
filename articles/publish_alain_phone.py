#!/usr/bin/env python3
"""Set floating Call/WhatsApp + Rank Math title phone on Al Ain cleaning/pest/pigeon/marble posts."""
from __future__ import annotations

import base64
import json
import os
import urllib.request

SITE = "https://www.rukn-eltatawer.com"
USER = os.environ.get("WP_USER", "cursor")
APP_PASS = os.environ.get("WP_APP_PASS", "QQEG jJcC hwu4 SvYY YofJ cOJT").replace(" ", "")
TOKEN = base64.b64encode(f"{USER}:{APP_PASS}".encode()).decode()
HEADERS = {
    "Authorization": f"Basic {TOKEN}",
    "User-Agent": "Mozilla/5.0 CursorAgent",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

NEW_E164 = "+971565619644"
NEW_WA = "971565619644"
NEW_LOCAL = "0565619644"
SEO_TITLE = f"%title% {NEW_LOCAL}"

# Unique live posts covering the 55 Al Ain titles (combined pages counted once).
POST_IDS = [
    6036, 968, 962, 6037, 6038, 801, 6053, 6042, 6043, 6044, 6045, 1017, 6046, 986,
    6047, 6048, 6050, 6051, 6039, 6040, 6041, 6052, 6049, 753, 6055, 6054, 265,
    6896, 6881, 6884, 6890, 6893, 6925, 6899, 6901, 6907, 6911, 6914, 6916, 6918,
    745, 6927, 6929, 6932, 6935, 6941, 6943,
]

PHONE_KEYS = (
    "phone_number",
    "whatsapp_number",
    "phone",
    "contact_number",
    "whatsapp",
)


def request(url: str, data=None, timeout: int = 180):
    body = None if data is None else json.dumps(data, ensure_ascii=False).encode("utf-8")
    req = urllib.request.Request(url, data=body, headers=HEADERS, method="POST" if data else "GET")
    with urllib.request.urlopen(req, timeout=timeout) as r:
        raw = r.read().decode("utf-8")
        return json.loads(raw) if raw else {}


def cli(cmd: str, approved: bool = False, confirm_write: bool = False):
    endpoint = "/wp-json/wpvibe/v1/cli/run-approved" if approved else "/wp-json/wpvibe/v1/cli/run"
    payload = {"command": cmd}
    if approved:
        payload["confirm_write"] = confirm_write
    return request(SITE + endpoint, payload)


def db_query(sql: str):
    print("SQL", sql[:180].replace("\n", " "), flush=True)
    out = cli(f'db query "{sql}"', approved=True, confirm_write=True)
    print(json.dumps(out, ensure_ascii=False)[:500], flush=True)
    return out


def id_list() -> str:
    return ",".join(str(i) for i in POST_IDS)


def ensure_phone_keys():
    ids = id_list()
    unions = " UNION ALL ".join(
        f"SELECT '{k}' AS meta_key, '{NEW_E164}' AS meta_value" for k in PHONE_KEYS
    )
    sql = (
        "INSERT INTO wp3mdn_postmeta (post_id, meta_key, meta_value) "
        f"SELECT p.ID, k.meta_key, k.meta_value FROM wp3mdn_posts p "
        f"CROSS JOIN ({unions}) k "
        f"WHERE p.ID IN ({ids}) AND NOT EXISTS ("
        "SELECT 1 FROM wp3mdn_postmeta m WHERE m.post_id = p.ID AND m.meta_key = k.meta_key"
        ")"
    )
    db_query(sql)
    keys = ",".join("'" + k + "'" for k in PHONE_KEYS)
    db_query(
        f"UPDATE wp3mdn_postmeta SET meta_value = '{NEW_E164}' "
        f"WHERE post_id IN ({ids}) AND meta_key IN ({keys})"
    )


def ensure_seo_title():
    ids = id_list()
    b64 = base64.b64encode(SEO_TITLE.encode("utf-8")).decode("ascii")
    db_query(
        "INSERT INTO wp3mdn_postmeta (post_id, meta_key, meta_value) "
        f"SELECT p.ID, 'rank_math_title', FROM_BASE64('{b64}') FROM wp3mdn_posts p "
        f"WHERE p.ID IN ({ids}) AND NOT EXISTS ("
        "SELECT 1 FROM wp3mdn_postmeta m WHERE m.post_id = p.ID AND m.meta_key = 'rank_math_title'"
        ")"
    )
    db_query(
        f"UPDATE wp3mdn_postmeta SET meta_value = FROM_BASE64('{b64}') "
        f"WHERE post_id IN ({ids}) AND meta_key = 'rank_math_title'"
    )


def phone_replace_expr(column: str) -> str:
    # Same-length replacements are safe in HTML and PHP-serialized meta.
    replacements = [
        ("+971541673020", NEW_E164),
        ("+971586634710", NEW_E164),
        ("971541673020", NEW_WA),
        ("971586634710", NEW_WA),
        ("0541673020", NEW_LOCAL),
        ("0586634710", NEW_LOCAL),
    ]
    expr = column
    for old, new in replacements:
        expr = f"REPLACE({expr}, '{old}', '{new}')"
    return expr


def rewrite_descriptions_and_call_blocks():
    ids = id_list()
    expr = phone_replace_expr("meta_value")
    db_query(
        f"UPDATE wp3mdn_postmeta SET meta_value = {expr} "
        f"WHERE post_id IN ({ids}) AND ("
        "meta_value LIKE '%541673020%' OR meta_value LIKE '%586634710%' OR "
        "meta_value LIKE '%0541673020%' OR meta_value LIKE '%0586634710%')"
    )
    db_query(
        f"UPDATE wp3mdn_postmeta SET meta_value = '' "
        f"WHERE post_id IN ({ids}) AND meta_key = 'hide__floating__call'"
    )


def rewrite_post_content():
    ids = id_list()
    content_expr = phone_replace_expr("post_content")
    excerpt_expr = phone_replace_expr("post_excerpt")
    db_query(
        f"UPDATE wp3mdn_posts SET post_content = {content_expr}, "
        f"post_excerpt = {excerpt_expr} WHERE ID IN ({ids})"
    )


def flush_cache():
    for cmd in ["cache flush", "litespeed-purge all"]:
        try:
            print(cmd, cli(cmd, approved=True, confirm_write=True), flush=True)
        except Exception as e:
            print("cache failed", cmd, e, flush=True)


if __name__ == "__main__":
    print("posts", len(POST_IDS), flush=True)
    ensure_phone_keys()
    ensure_seo_title()
    rewrite_descriptions_and_call_blocks()
    rewrite_post_content()
    flush_cache()
    print("DONE", flush=True)
