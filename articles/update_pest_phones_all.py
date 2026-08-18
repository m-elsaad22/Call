#!/usr/bin/env python3
"""Set call/WhatsApp numbers on ALL pest-control articles to +971506603374.

Covers insects, birds, reptiles, rodents and related pest services across all cities.
Updates post metas used by [post_call] shortcodes and replaces hardcoded phones in content.
"""

from __future__ import annotations

import json
import os
import re
import sys
import time
import urllib.request
from copy import deepcopy
from pathlib import Path

sys.path.insert(0, str(Path(__file__).resolve().parent))
from rukn_rewrite_pipeline import (  # noqa: E402
    WP,
    api_get,
    auth_header,
    cli,
    php_serialize,
    sql_set_meta,
)

NEW_PHONE = "+971506603374"
NEW_LOCAL = "0506603374"
NEW_WA = "971506603374"  # digits for wa.me links

# Title/slug signals for pest services (insects / birds / reptiles / rodents)
PEST_TITLE_KEYS = [
    "مكافحة",
    "وزغ",
    "برص",
    "فئران",
    "قوارض",
    "طيور",
    "أفاعي",
    "ثعابين",
    "عقارب",
    "صراصير",
    "بق الفراش",
    "بقّ الفراش",
    "رمة",
    "النمل",
    "نمل أبيض",
    "نمل أسود",
    "براغيث",
    "بعوض",
    "ذباب",
    "حشرات",
    "زواحف",
]
PEST_SLUG_KEYS = [
    "pest",
    "insect",
    "termite",
    "rodent",
    "snake",
    "scorpion",
    "cockroach",
    "bed-bug",
    "lizard",
    "flea",
    "ant-control",
    "bird",
    "mosquito",
    "flying-pest",
    "crawling-pest",
    "white-ant",
]
EXCLUDE_TITLE = [
    "مكافحة الحريق",
    "أنظمة السلامة",
    "عزل حمامات",
    "تنظيف حمامات",
    "ترميم حمامات",
]

# Common formats seen on site / previous updates
OLD_PHONE_PATTERNS = [
    r"\+971[\s\-]?5\d{8}",
    r"\+971[\s\-]?05\d{8}",
    r"(?<!\d)05\d{8}(?!\d)",
    r"(?<!\d)5\d{8}(?!\d)",  # local without leading 0 in some metas
    r"9715\d{8}",
]


def is_pest_post(title: str, slug: str) -> bool:
    if any(x in title for x in EXCLUDE_TITLE):
        return False
    if any(k in title for k in PEST_TITLE_KEYS):
        return True
    slug_l = (slug or "").lower()
    return any(k in slug_l for k in PEST_SLUG_KEYS)


def fetch_all_posts() -> list[dict]:
    posts = []
    page = 1
    while True:
        batch = api_get(
            f"wp/v2/posts?per_page=100&page={page}&status=publish&_fields=id,title,link,slug,content"
        )
        if not batch:
            break
        posts.extend(batch)
        if len(batch) < 100:
            break
        page += 1
    return posts


def get_meta(post_id: int, key: str):
    """Read post meta via WP-CLI (arrays come back as JSON objects)."""
    r = cli(f"post meta get {post_id} {key}")
    out = (r.get("stdout") or "").strip()
    if not out or out.lower() in ("null", "false", ""):
        return None
    # Serialized arrays/objects are pretty-printed as JSON by wp-cli
    if out.startswith("{") or out.startswith("["):
        try:
            return json.loads(out)
        except json.JSONDecodeError:
            return out
    # Plain scalar — strip surrounding quotes if present
    if (out.startswith('"') and out.endswith('"')) or (
        out.startswith("'") and out.endswith("'")
    ):
        return out[1:-1]
    return out


def replace_phones_in_text(text: str) -> tuple[str, int]:
    """Replace UAE mobile numbers with the new pest number; keep formatting style roughly."""
    if not text:
        return text, 0
    count = 0

    def repl_plus(m: re.Match) -> str:
        nonlocal count
        raw = re.sub(r"\D", "", m.group(0))
        # Skip if already the new number
        if raw.endswith("506603374") or raw == "971506603374":
            return m.group(0)
        # Only replace UAE mobiles (05x)
        if not re.search(r"9715\d{8}$", raw) and not re.search(r"^05\d{8}$", raw):
            return m.group(0)
        count += 1
        # Preserve spacing style if present
        if " " in m.group(0) or "-" in m.group(0):
            return "+971 50 660 3374"
        return NEW_PHONE

    def repl_local(m: re.Match) -> str:
        nonlocal count
        if m.group(0) == NEW_LOCAL:
            return m.group(0)
        count += 1
        return NEW_LOCAL

    def repl_wa(m: re.Match) -> str:
        nonlocal count
        if m.group(0) == NEW_WA:
            return m.group(0)
        count += 1
        return NEW_WA

    # Order matters: longer/international first
    text2 = re.sub(r"\+971[\s\-]?0?5\d{8}", repl_plus, text)
    text2 = re.sub(r"(?<!\d)9715\d{8}(?!\d)", repl_wa, text2)
    text2 = re.sub(r"(?<!\d)05\d{8}(?!\d)", repl_local, text2)
    return text2, count


def update_call_section(post_id: int) -> dict:
    data = get_meta(post_id, "post__call_section__data")
    if not isinstance(data, dict):
        data = {
            "call_section_title": "تواصل معنا الآن",
            "call_section_content": "تواصل معنا لطلب الخدمة.",
            "call_section_phone": NEW_PHONE,
            "call_section_whatsapp": NEW_PHONE,
        }
    else:
        data = deepcopy(data)
        data["call_section_phone"] = NEW_PHONE
        data["call_section_whatsapp"] = NEW_PHONE
    sql_set_meta(post_id, "post__call_section__data", data)
    return data


def update_service_schema(post_id: int) -> bool:
    data = get_meta(post_id, "YourColor_Service")
    if not isinstance(data, dict):
        return False
    data = deepcopy(data)
    data["telephone"] = NEW_LOCAL
    sql_set_meta(post_id, "YourColor_Service", data)
    return True


def update_simple_phone_metas(post_id: int) -> None:
    for key in (
        "memo-meta-phone",
        "phone",
        "phone_number",
        "whatsapp",
        "whatsapp_number",
    ):
        # Prefer international for memo/call display; local also accepted by theme
        sql_set_meta(post_id, key, NEW_PHONE)


def update_content(post_id: int, content_html: str) -> tuple[str, int]:
    new_html, n = replace_phones_in_text(content_html)
    if n == 0 and NEW_PHONE not in new_html and NEW_LOCAL not in new_html:
        # still push if identical? skip API write
        return content_html, 0
    if new_html == content_html:
        return content_html, 0
    payload = json.dumps({"content": new_html}).encode("utf-8")
    req = urllib.request.Request(
        f"{WP}/wp-json/wp/v2/posts/{post_id}",
        data=payload,
        method="POST",
        headers=auth_header(),
    )
    with urllib.request.urlopen(req, timeout=180) as r:
        json.load(r)
    return new_html, n


def main() -> None:
    print("Fetching posts…", flush=True)
    posts = fetch_all_posts()
    targets = []
    for p in posts:
        title = p["title"]["rendered"] if isinstance(p["title"], dict) else p["title"]
        title = re.sub(r"<[^>]+>", "", title)
        slug = p.get("slug") or ""
        if is_pest_post(title, slug):
            content = p.get("content", {})
            html = content.get("raw") if isinstance(content, dict) else ""
            if not html and isinstance(content, dict):
                html = content.get("rendered") or ""
            targets.append(
                {
                    "id": int(p["id"]),
                    "title": title,
                    "link": p.get("link"),
                    "slug": slug,
                    "content": html or "",
                }
            )

    print(f"Pest posts matched: {len(targets)}", flush=True)
    results = []
    for i, item in enumerate(targets, 1):
        pid = item["id"]
        print(f"\n=== [{i}/{len(targets)}] {item['title']} ({pid}) ===", flush=True)
        try:
            update_call_section(pid)
            update_simple_phone_metas(pid)
            schema_ok = update_service_schema(pid)
            # Prefer raw content from API if available
            raw = ""
            try:
                full = api_get(f"wp/v2/posts/{pid}?context=edit&_fields=content")
                raw = (full.get("content") or {}).get("raw") or ""
            except Exception as e:
                print("  raw content warn:", e, flush=True)
                raw = item["content"]
            _, replaced = update_content(pid, raw) if raw else ("", 0)
            # Verify
            call = get_meta(pid, "post__call_section__data") or {}
            memo = get_meta(pid, "memo-meta-phone")
            wa = get_meta(pid, "whatsapp_number")
            ok = (
                isinstance(call, dict)
                and call.get("call_section_phone") == NEW_PHONE
                and call.get("call_section_whatsapp") == NEW_PHONE
            )
            row = {
                "id": pid,
                "title": item["title"],
                "link": item["link"],
                "ok": ok,
                "call_phone": call.get("call_section_phone") if isinstance(call, dict) else None,
                "call_whatsapp": call.get("call_section_whatsapp") if isinstance(call, dict) else None,
                "memo": memo,
                "whatsapp_number": wa,
                "schema_updated": schema_ok,
                "content_replacements": replaced,
            }
            results.append(row)
            print(
                "OK" if ok else "WARN",
                {
                    "call": row["call_phone"],
                    "wa": row["call_whatsapp"],
                    "memo": memo,
                    "content_replacements": replaced,
                },
                flush=True,
            )
        except Exception as e:
            print("FAIL", pid, e, flush=True)
            results.append({"id": pid, "title": item["title"], "error": str(e)})
        time.sleep(0.15)

    out = Path("/workspace/articles/phone-update-pest-all.json")
    out.write_text(json.dumps(results, ensure_ascii=False, indent=2), encoding="utf-8")
    ok_n = sum(1 for r in results if r.get("ok"))
    fail_n = sum(1 for r in results if r.get("error"))
    print(f"\nWrote {out}")
    print(f"ok={ok_n} fail={fail_n} total={len(results)}")


if __name__ == "__main__":
    main()
