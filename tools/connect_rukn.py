#!/usr/bin/env python3
"""Connect to the live Rukn Eltatawer WordPress site and snapshot public APIs.

Usage:
  python3 tools/connect_rukn.py
  python3 tools/connect_rukn.py --out live/connection-snapshot.json

Optional authenticated WP Vibe read (Application Password):
  RUKN_WP_USER=admin RUKN_WP_APP_PASSWORD='xxxx xxxx ...' python3 tools/connect_rukn.py
"""

from __future__ import annotations

import argparse
import json
import os
import re
import ssl
import sys
import urllib.error
import urllib.parse
import urllib.request
import xml.etree.ElementTree as ET
from datetime import datetime, timezone
from typing import Any

BASE = "https://www.rukn-eltatawer.com"
ORIGIN = "https://rukn-eltatawer.com"
UA = "RuknConnect/1.0 (+https://github.com/m-elsaad22/Call)"
CTX = ssl.create_default_context()
SM_NS = {"sm": "http://www.sitemaps.org/schemas/sitemap/0.9"}


def _headers(extra: dict[str, str] | None = None, user: str | None = None, password: str | None = None) -> dict[str, str]:
    h = {
        "User-Agent": UA,
        "Accept": "application/json, text/xml, text/html;q=0.8",
    }
    if extra:
        h.update(extra)
    if user and password:
        import base64

        token = base64.b64encode(f"{user}:{password}".encode("utf-8")).decode("ascii")
        h["Authorization"] = f"Basic {token}"
    return h


def fetch(
    path: str,
    *,
    user: str | None = None,
    password: str | None = None,
    timeout: int = 45,
) -> dict[str, Any]:
    url = path if path.startswith("http") else BASE + path
    req = urllib.request.Request(url, headers=_headers(user=user, password=password))
    try:
        with urllib.request.urlopen(req, context=CTX, timeout=timeout) as resp:
            body = resp.read()
            headers = {k.lower(): v for k, v in resp.headers.items()}
            return {
                "ok": True,
                "status": resp.status,
                "url": resp.geturl(),
                "content_type": headers.get("content-type", ""),
                "wp_total": headers.get("x-wp-total"),
                "body": body,
                "error": None,
            }
    except urllib.error.HTTPError as exc:
        body = exc.read()
        headers = {k.lower(): v for k, v in exc.headers.items()} if exc.headers else {}
        return {
            "ok": False,
            "status": exc.code,
            "url": url,
            "content_type": headers.get("content-type", ""),
            "wp_total": headers.get("x-wp-total"),
            "body": body,
            "error": f"HTTP {exc.code}",
        }
    except Exception as exc:  # noqa: BLE001 — connection snapshot should never crash
        return {
            "ok": False,
            "status": None,
            "url": url,
            "content_type": "",
            "wp_total": None,
            "body": b"",
            "error": str(exc),
        }


def decode_json(result: dict[str, Any]) -> Any:
    if not result.get("body"):
        return None
    try:
        return json.loads(result["body"].decode("utf-8"))
    except Exception:
        return None


def decode_text(result: dict[str, Any]) -> str:
    body = result.get("body") or b""
    return body.decode("utf-8", "replace") if isinstance(body, (bytes, bytearray)) else str(body)


def json_endpoint(path: str, **kwargs: Any) -> dict[str, Any]:
    result = fetch(path, **kwargs)
    payload = decode_json(result)
    return {
        "ok": result["ok"],
        "status": result["status"],
        "url": result["url"],
        "error": result["error"],
        "wp_total": int(result["wp_total"]) if result.get("wp_total") else None,
        "data": payload,
    }


PHONE_RE = re.compile(r"(?:\+?971|0)5[\d\s\-]{7,}")
WA_RE = re.compile(r"wa\.me/([^\"'\s<>]+)")
TEL_RE = re.compile(r"tel:([^\"'\s<>]+)")
MAIL_RE = re.compile(r"mailto:([^\"'\s<>]+)")


def extract_contact_channels(html: str) -> dict[str, list[str]]:
    phones = sorted({re.sub(r"[\s\-]", "", m.group(0)) for m in PHONE_RE.finditer(html)})
    wa = sorted(set(WA_RE.findall(html)))
    tel = sorted(set(TEL_RE.findall(html)))
    mail = sorted(set(MAIL_RE.findall(html)))
    return {"phones": phones, "whatsapp": wa, "tel": tel, "mailto": mail}


def sitemap_counts() -> dict[str, Any]:
    result = fetch("/sitemap_index.xml")
    if not result["ok"]:
        return {"ok": False, "status": result["status"], "error": result["error"], "sitemaps": []}
    root = ET.fromstring(result["body"])
    locs = [el.text for el in root.findall("sm:sitemap/sm:loc", SM_NS) if el.text]
    sitemaps = []
    total_urls = 0
    for loc in locs:
        sm = fetch(loc)
        count = 0
        if sm["ok"]:
            try:
                sm_root = ET.fromstring(sm["body"])
                count = len(sm_root.findall("sm:url", SM_NS))
            except ET.ParseError:
                count = 0
        total_urls += count
        sitemaps.append({"loc": loc, "ok": sm["ok"], "status": sm["status"], "url_count": count})
    return {
        "ok": True,
        "status": result["status"],
        "index": result["url"],
        "sitemap_count": len(sitemaps),
        "url_count": total_urls,
        "sitemaps": sitemaps,
    }


def list_items(path: str, fields: str, limit: int = 100) -> dict[str, Any]:
    sep = "&" if "?" in path else "?"
    endpoint = f"{path}{sep}per_page={min(limit, 100)}&_fields={fields}"
    result = json_endpoint(endpoint)
    items = result.get("data") if isinstance(result.get("data"), list) else []
    simplified = []
    for item in items:
        title = item.get("title")
        if isinstance(title, dict):
            title = title.get("rendered")
        simplified.append(
            {
                "id": item.get("id"),
                "slug": item.get("slug"),
                "title": title or item.get("name"),
                "link": item.get("link"),
                "modified": item.get("modified"),
                "count": item.get("count"),
            }
        )
    result["items"] = simplified
    result.pop("data", None)
    return result


def connect(user: str | None = None, password: str | None = None) -> dict[str, Any]:
    now = datetime.now(timezone.utc).strftime("%Y-%m-%dT%H:%M:%SZ")
    origin = fetch(ORIGIN + "/")
    home = fetch("/")
    contact = fetch("/contact-us/")
    dni = json_endpoint("/wp-json/kayan/v1/dni")
    vibe_health = json_endpoint("/wp-json/wpvibe/v1/health")
    vibe_ping = json_endpoint("/wp-json/wpvibe/v1/ping")
    rest_index = json_endpoint("/wp-json/")
    rest_data = rest_index.get("data") if isinstance(rest_index.get("data"), dict) else {}

    schema_phone = None
    home_html = decode_text(home)
    match = re.search(r'"telephone"\s*:\s*"([^"]+)"', home_html)
    if match:
        schema_phone = match.group(1)

    snapshot: dict[str, Any] = {
        "connected_at": now,
        "site": {
            "requested": ORIGIN,
            "canonical": BASE,
            "origin_status": origin["status"],
            "origin_final_url": origin["url"],
            "homepage_status": home["status"],
            "homepage_ok": home["ok"],
            "contact_status": contact["status"],
            "name": rest_data.get("name"),
            "description": rest_data.get("description"),
            "gmt_offset": rest_data.get("gmt_offset"),
            "timezone_string": rest_data.get("timezone_string"),
            "namespaces": rest_data.get("namespaces"),
        },
        "wordpress": {
            "rest_ok": rest_index["ok"],
            "rest_status": rest_index["status"],
            "wpvibe": vibe_ping.get("data") or vibe_health.get("data"),
            "wpvibe_health_ok": vibe_health["ok"],
            "application_passwords_hint": "Set RUKN_WP_USER and RUKN_WP_APP_PASSWORD for WP Vibe site-info.",
        },
        "call": {
            "dni": dni.get("data"),
            "dni_ok": dni["ok"],
            "schema_telephone": schema_phone,
            "homepage": extract_contact_channels(home_html),
            "contact_page": extract_contact_channels(decode_text(contact)),
            "public_contact": {
                "whatsapp": "+971586634710",
                "phone": "+971586634710",
                "email": "m@rukn-eltatawer.com",
                "address": "A 306, Mazid, MBZ, UAE",
                "hours": "24/7",
                "contact_url": f"{BASE}/contact-us/",
            },
        },
        "catalog": {
            "posts": list_items("/wp-json/wp/v2/posts", "id,slug,link,title,date", limit=5),
            "pages": list_items("/wp-json/wp/v2/pages", "id,slug,link,title,modified", limit=100),
            "services": list_items("/wp-json/wp/v2/services", "id,slug,link,title,modified", limit=50),
            "cities": list_items("/wp-json/wp/v2/cities", "id,slug,link,name,count", limit=50),
            "sitemaps": sitemap_counts(),
        },
        "auth": {
            "wpvibe_authenticated": False,
            "wpvibe_site_info": None,
        },
    }

    if user and password:
        info = json_endpoint("/wp-json/wpvibe/v1/site-info", user=user, password=password)
        snapshot["auth"]["wpvibe_authenticated"] = bool(info["ok"])
        snapshot["auth"]["wpvibe_site_info"] = {
            "ok": info["ok"],
            "status": info["status"],
            "error": info["error"],
            "data": info.get("data") if info["ok"] else _safe_error(info.get("data")),
        }

    return snapshot


def _safe_error(data: Any) -> Any:
    if isinstance(data, dict):
        return {
            "code": data.get("code"),
            "message": data.get("message"),
            "status": (data.get("data") or {}).get("status") if isinstance(data.get("data"), dict) else None,
        }
    return data


def summarize(snapshot: dict[str, Any]) -> str:
    site = snapshot["site"]
    call = snapshot["call"]
    cat = snapshot["catalog"]
    vibe = snapshot["wordpress"].get("wpvibe") or {}
    dni = call.get("dni") or {}
    lines = [
        f"Connected to {site.get('name') or 'ركن التطور'}",
        f"  Canonical : {site['canonical']}",
        f"  Homepage  : HTTP {site['homepage_status']}",
        f"  Contact   : HTTP {site['contact_status']}  {call['public_contact']['contact_url']}",
        f"  WordPress : {vibe.get('wp_version')}   WP Vibe {vibe.get('plugin_version')}",
        f"  REST      : {', '.join(site.get('namespaces') or [])}",
        "",
        "Call / WhatsApp",
        f"  DNI phone    : {dni.get('phone')}",
        f"  DNI WhatsApp : {dni.get('wa_number')}",
        f"  Schema tel   : {call.get('schema_telephone')}",
        f"  Public tel   : {call['public_contact']['phone']}",
        f"  Public WA    : {call['public_contact']['whatsapp']}",
        f"  Email        : {call['public_contact']['email']}",
        "",
        "Catalog",
        f"  Pages    : {cat['pages'].get('wp_total')}",
        f"  Posts    : {cat['posts'].get('wp_total')}",
        f"  Services : {cat['services'].get('wp_total')}",
        f"  Cities   : {cat['cities'].get('wp_total')}",
        f"  Sitemap  : {cat['sitemaps'].get('url_count')} URLs in {cat['sitemaps'].get('sitemap_count')} files",
    ]
    if snapshot["auth"]["wpvibe_authenticated"]:
        lines.append("  WP Vibe  : authenticated site-info OK")
    else:
        lines.append("  WP Vibe  : public ping only (no application password)")
    return "\n".join(lines)


def main() -> int:
    parser = argparse.ArgumentParser(description="Connect to rukn-eltatawer.com and snapshot public APIs.")
    parser.add_argument("--out", default="live/connection-snapshot.json", help="Snapshot JSON path")
    args = parser.parse_args()

    user = os.environ.get("RUKN_WP_USER") or None
    password = os.environ.get("RUKN_WP_APP_PASSWORD") or None
    snapshot = connect(user=user, password=password)

    out_path = args.out
    os.makedirs(os.path.dirname(out_path) or ".", exist_ok=True)
    with open(out_path, "w", encoding="utf-8") as fh:
        json.dump(snapshot, fh, ensure_ascii=False, indent=2)
        fh.write("\n")

    print(summarize(snapshot))
    print(f"\nSnapshot written to {out_path}")
    if not snapshot["site"]["homepage_ok"]:
        print("Homepage was not reachable.", file=sys.stderr)
        return 1
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
