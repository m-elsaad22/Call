#!/usr/bin/env python3
"""Publish bilingual English UAE content on rukn-eltatawer.com."""

from __future__ import annotations

import json
import hashlib
import time
from pathlib import Path

from rukn_rewrite_pipeline import (
    api_get,
    api_post,
    api_patch,
    cli,
    cli_approved,
    php_serialize,
)
from english_site_content import (
    PHONE_LOCAL,
    PHONE_TEL,
    CITIES,
    all_content,
)

REPORT = Path(__file__).resolve().parent / "english-site-build.json"
HEADER_SNIPPET = Path(__file__).resolve().parent / "header_english_bilingual.html"
PHONE_METAS = {
    "phone_number": PHONE_TEL,
    "contact_number": PHONE_TEL,
    "whatsapp": PHONE_TEL,
    "phone": PHONE_TEL,
    "whatsapp_number": PHONE_TEL,
    "memo-meta-phone": PHONE_TEL,
}


def sh(cmd: str, write: bool = False) -> dict:
    if not cmd.startswith("wp "):
        cmd = "wp " + cmd
    r = cli(cmd, write=write)
    code = r.get("exit_code")
    if code not in (0, None) and r.get("stderr"):
        print("CLI warn:", cmd[:90], str(r.get("stderr"))[:240])
    return r


def out(cmd: str, write: bool = False) -> str:
    return str(sh(cmd, write=write).get("stdout") or "")


def find_item(slug: str, ptype: str) -> dict | None:
    endpoint = "wp/v2/pages" if ptype == "page" else "wp/v2/posts"
    try:
        items = api_get(endpoint, {"slug": slug, "per_page": 5, "status": "any"})
        if isinstance(items, list) and items:
            return items[0]
    except Exception as e:
        print("find fail", slug, e)
    return None


def upsert(item: dict) -> int:
    ptype = item.get("type") or "post"
    slug = item["slug"]
    endpoint = "wp/v2/pages" if ptype == "page" else "wp/v2/posts"
    existing = None
    if item.get("existing_id"):
        try:
            existing = api_get(f"{endpoint}/{int(item['existing_id'])}")
        except Exception:
            existing = None
    if existing is None:
        existing = find_item(slug, ptype)
    body = {
        "title": item["title"],
        "slug": slug,
        "content": item["html"],
        "status": "publish",
        "excerpt": item.get("excerpt") or "",
    }
    if ptype == "post" and item.get("categories"):
        body["categories"] = item["categories"]
    if existing:
        pid = int(existing["id"])
        api_patch(f"{endpoint}/{pid}", body)
        print(f"  updated {ptype} {pid} {slug}")
        return pid
    created = api_post(endpoint, body)
    pid = int(created["id"])
    print(f"  created {ptype} {pid} {slug}")
    return pid


def set_language(post_id: int, lang: str = "en") -> None:
    sh(f"wp post term set {post_id} language {lang}", write=True)


def set_seo(post_id: int, excerpt: str, keyword: str) -> None:
    seo_title = f"%title% {PHONE_LOCAL} Your Right Choice"
    for k, v in {
        "rank_math_title": seo_title,
        "rank_math_description": (excerpt or "")[:320],
        "rank_math_focus_keyword": keyword,
        "rank_math_robots": "index,follow",
        "rank_math_canonical_url": "",
    }.items():
        sh(f"wp post meta update {post_id} {k} {json.dumps(v)}", write=True)
    for k, v in PHONE_METAS.items():
        sh(f"wp post meta update {post_id} {k} {json.dumps(v)}", write=True)
    call_json = json.dumps(
        {
            "call_section_phone": PHONE_TEL,
            "call_section_whatsapp": PHONE_TEL.lstrip("+"),
            "call_section_title": "Need this service in the UAE?",
            "call_section_subtitle": "Licensed teams across Dubai, Abu Dhabi, Sharjah, Ajman, Ras Al Khaimah, Fujairah, Umm Al Quwain and Al Ain.",
        }
    )
    sh(f"wp post meta update {post_id} post__call_section__data {json.dumps(call_json)}", write=True)


def existing_pll_group(ar_id: int) -> tuple[str, str] | None:
    q = (
        "SELECT t.slug, tt.description FROM wp3mdn_term_relationships tr "
        "JOIN wp3mdn_term_taxonomy tt ON tr.term_taxonomy_id=tt.term_taxonomy_id "
        "JOIN wp3mdn_terms t ON t.term_id=tt.term_id "
        f"WHERE tr.object_id={int(ar_id)} AND tt.taxonomy='post_translations' LIMIT 1"
    )
    raw = out(f'wp db query "{q}"')
    try:
        data = json.loads(raw)
        rows = data.get("results") or []
        if rows:
            return str(rows[0]["slug"]), str(rows[0].get("description") or "")
    except Exception:
        pass
    return None


def link_translations(ar_id: int, en_id: int) -> None:
    if not ar_id or not en_id:
        return
    desc = php_serialize({"ar": int(ar_id), "en": int(en_id)})
    found = existing_pll_group(int(ar_id))
    if found:
        slug, _old = found
        sh(
            f"wp term update post_translations {slug} --by=slug --description={json.dumps(desc)}",
            write=True,
        )
    else:
        slug = "pll_" + hashlib.md5(f"ar{ar_id}en{en_id}".encode()).hexdigest()[:13]
        sh(
            f"wp term create post_translations {slug} --description={json.dumps(desc)} --porcelain",
            write=True,
        )
    sh(f"wp post term add {int(ar_id)} post_translations {slug}", write=True)
    sh(f"wp post term add {int(en_id)} post_translations {slug}", write=True)


def append_header_i18n() -> None:
    snippet = HEADER_SNIPPET.read_text(encoding="utf-8").strip()
    current = out("wp option get ihaf_insert_header")
    if "RUKN-EN-I18N-START" in current:
        start = current.find("<!-- RUKN-EN-I18N-START -->")
        end = current.find("<!-- RUKN-EN-I18N-END -->")
        if start != -1 and end != -1:
            merged = current[:start] + snippet + current[end + len("<!-- RUKN-EN-I18N-END -->") :]
        else:
            merged = current + "\n" + snippet
    else:
        merged = current.rstrip() + "\n" + snippet + "\n"
    sh(f"wp option update ihaf_insert_header {json.dumps(merged)}", write=True)
    print("  header i18n snippet saved")


def disable_browser_redirect() -> None:
    sh("wp option patch update polylang browser 0 --format=json", write=True)
    print("  polylang.browser patched")


def add_en_home_redirect() -> None:
    import binascii

    sources = php_serialize(
        [
            {"ignore": "", "pattern": "en/", "comparison": "exact"},
            {"ignore": "", "pattern": "en", "comparison": "exact"},
        ]
    )
    check = out(
        "wp db query \"SELECT id,url_to,status FROM wp3mdn_rank_math_redirections WHERE url_to LIKE '%english-home%' LIMIT 5\""
    )
    if "english-home" in check:
        print("  rank math redirect already present")
        return
    url_to = "https://www.rukn-eltatawer.com/en/english-home/"
    hx = binascii.hexlify(sources.encode("utf-8")).decode()
    sql = (
        "INSERT INTO wp3mdn_rank_math_redirections "
        "(sources,url_to,header_code,hits,status,created,updated,last_accessed) VALUES ("
        f"UNHEX('{hx}'),'{url_to}',301,0,'active',NOW(),NOW(),'0000-00-00 00:00:00')"
    )
    cmd = "wp db query " + json.dumps(sql)
    r = cli(cmd, write=True)
    blob = json.dumps(r).lower()
    if "approval" in blob or r.get("exit_code") not in (0, None):
        r2 = cli_approved(cmd)
        print("  redirect approved", str(r2.get("stdout") or r2)[:240])
    else:
        print("  redirect inserted", str(r.get("stdout") or "")[:240])


def create_english_menu(ids: dict) -> None:
    menus = out("wp menu list --format=json")
    if "English Menu" in menus:
        print("  English Menu already exists")
        return
    created = out("wp menu create 'English Menu' --porcelain", write=True)
    mid = "".join(ch for ch in created if ch.isdigit())
    if not mid:
        print("  menu create failed", created[:200])
        return
    links = [
        ("Home", "/en/english-home/"),
        ("Leak detection", "/en/water-leak-detection-company-uae/"),
        ("Roof insulation", "/en/roof-insulation-company-uae/"),
        ("About", "/en/about-us/"),
        ("Contact", "/en/contact/"),
    ]
    for title, url in links:
        sh(
            f"wp menu item add-custom {mid} {json.dumps(title)} {json.dumps('https://www.rukn-eltatawer.com' + url)}",
            write=True,
        )
    ids["english_menu"] = int(mid)
    print("  English Menu", mid)


def main() -> None:
    created: dict = {"pages": {}, "posts": {}, "cities": [c["slug"] for c in CITIES]}
    print("== Publish English pages and posts ==")
    for item in all_content():
        try:
            pid = upsert(item)
        except Exception as e:
            print("FAIL upsert", item.get("slug"), e)
            if hasattr(e, "read"):
                print(e.read()[:400])
            continue
        set_language(pid, "en")
        set_seo(pid, item.get("excerpt") or "", item.get("keyword") or item["title"])
        if item.get("ar_id"):
            try:
                link_translations(int(item["ar_id"]), pid)
            except Exception as e:
                print("  translation link warn", item["slug"], e)
        bucket = "pages" if item.get("type") == "page" else "posts"
        created[bucket][item["slug"]] = pid
        time.sleep(0.15)

    print("== Polylang + header + redirect ==")
    disable_browser_redirect()
    append_header_i18n()
    try:
        add_en_home_redirect()
    except Exception as e:
        print("redirect warn", e)
        if hasattr(e, "read"):
            print(e.read()[:400])
    try:
        create_english_menu(created)
    except Exception as e:
        print("menu warn", e)

    print("== Flush caches ==")
    sh("wp rewrite flush", write=True)
    sh("wp litespeed-purge all", write=True)
    sh("wp cache flush", write=True)

    REPORT.write_text(json.dumps(created, indent=2), encoding="utf-8")
    print("Wrote", REPORT)
    print(json.dumps(created, indent=2))


if __name__ == "__main__":
    main()
