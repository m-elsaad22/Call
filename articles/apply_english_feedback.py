#!/usr/bin/env python3
"""Apply user feedback: remove AR/EN pills, English home = Arabic overlay, translate articles."""

from __future__ import annotations

import binascii
import json
import re
import time
from pathlib import Path
from urllib.error import HTTPError

from rukn_rewrite_pipeline import api_get, api_patch, cli, cli_approved
from english_translated_articles import (
    CITIES,
    CALL as CALL_TEL,
    PHONE_LOCAL,
    PHONE,
    WA,
    about_html,
    contact_html,
    insulation_html,
    insulation_hub_html,
    leak_html,
    leak_hub_html,
    waterproofing_html,
)

ROOT = Path(__file__).resolve().parent
HEADER_CALL = ROOT / "header_page_call_buttons.html"
HOME_JS = ROOT / "header_en_homepage.html"

EN_IDS = json.loads((ROOT / "english-site-build.json").read_text())
POSTS = EN_IDS["posts"]

EXTRAS_TO_DRAFT = [
    "english-home",
    "roof-leakage-repair-uae",
    "pest-control-company-sharjah",
    "pest-control-company-dubai",
    "pest-control-company-abu-dhabi",
    "pest-control-company-ajman",
    "pest-control-company-ras-al-khaimah",
    "pest-control-company-umm-al-quwain",
    "home-cleaning-services-sharjah",
    "cleaning-company-dubai-en",
]


def sql(cmd_sql: str) -> dict:
    cmd = "wp db query " + json.dumps(cmd_sql)
    try:
        return cli(cmd, write=True)
    except HTTPError as e:
        body = e.read().decode() if hasattr(e, "read") else str(e)
        if "approval_required" not in body and "409" not in body:
            raise
        return cli_approved(cmd)


def first_img(post_id: int) -> str:
    try:
        p = api_get(f"wp/v2/posts/{post_id}?context=edit")
        raw = p["content"]["raw"]
        m = re.search(r"<img[^>]+>", raw)
        return m.group(0) if m else ""
    except Exception:
        return ""


def set_seo(pid: int, title: str, excerpt: str, keyword: str) -> None:
    seo_title = f"%title% | {PHONE_LOCAL}"
    for k, v in {
        "rank_math_title": seo_title,
        "rank_math_description": excerpt[:320],
        "rank_math_focus_keyword": keyword,
        "rank_math_robots": "index,follow",
    }.items():
        cli(f"wp post meta update {pid} {k} {json.dumps(v)} --force", write=True)


def set_call_whatsapp_metas(pid: int) -> None:
    """Voice Call = 0524314370. WhatsApp = 0586634710 (never on tel:/Call)."""
    for k, v in {
        "phone_number": CALL_TEL,
        "contact_number": CALL_TEL,
        "phone": CALL_TEL,
        "memo-meta-phone": CALL_TEL,
        "whatsapp": PHONE,
        "whatsapp_number": PHONE,
    }.items():
        cli(f"wp post meta update {pid} {k} {json.dumps(v)} --force", write=True)
    call_json = json.dumps(
        {
            "call_section_phone": CALL_TEL,
            "call_section_whatsapp": WA,
            "call_section_title": "Need this service in the UAE?",
            "call_section_subtitle": "Licensed teams across Dubai, Abu Dhabi, Sharjah, Ajman, Ras Al Khaimah, Fujairah, Umm Al Quwain and Al Ain.",
        }
    )
    cli(f"wp post meta update {pid} post__call_section__data {json.dumps(call_json)} --force", write=True)


def update_post(pid: int, title: str, html: str, excerpt: str, keyword: str) -> None:
    api_patch(f"wp/v2/posts/{pid}", {"title": title, "content": html, "excerpt": excerpt, "status": "publish"})
    cli(f"wp post term set {pid} language en", write=True)
    set_seo(pid, title, excerpt, keyword)
    set_call_whatsapp_metas(pid)
    print(" updated", pid, title[:70])


def restore_header() -> None:
    merged = HEADER_CALL.read_text(encoding="utf-8").strip() + "\n" + HOME_JS.read_text(encoding="utf-8").strip() + "\n"
    hx = binascii.hexlify(merged.encode()).decode()
    r = sql(f"UPDATE wp3mdn_options SET option_value=UNHEX('{hx}') WHERE option_name='ihaf_insert_header'")
    print("header", r.get("stdout") or r)


def drop_en_home_redirect() -> None:
    r = sql("UPDATE wp3mdn_rank_math_redirections SET status='trashed' WHERE id=224")
    print("redirect", r.get("stdout") or r)


def draft_extras() -> None:
    for slug in EXTRAS_TO_DRAFT:
        pid = POSTS.get(slug)
        if not pid:
            continue
        cli(f"wp post update {pid} --post_status=draft", write=True)
        print(" draft", pid, slug)


def main() -> None:
    print("== header without AR/EN pills ==")
    restore_header()
    print("== remove /en/ to english-home redirect ==")
    drop_en_home_redirect()
    print("== draft wrong homepage + extra non-translation pages ==")
    draft_extras()

    print("== leak + insulation translations ==")
    leak_hub_id = POSTS["water-leak-detection-company-uae"]
    ins_hub_id = POSTS["roof-insulation-company-uae"]
    update_post(
        leak_hub_id,
        "Water Leak Detection in the UAE",
        leak_hub_html(),
        "Non-destructive water leak detection across the UAE from Rukn Eltatawer.",
        "water leak detection UAE",
    )
    update_post(
        ins_hub_id,
        "Roof Insulation in the UAE",
        insulation_hub_html(),
        "Roof insulation and waterproofing across the UAE from Rukn Eltatawer.",
        "roof insulation UAE",
    )

    for city in CITIES.values():
        img_l = first_img(city["leak_ar"])
        img_i = first_img(city["insul_ar"])
        leak_id = POSTS[city["leak_slug"]]
        ins_id = POSTS[city["insul_slug"]]
        n = city["name"]
        update_post(
            leak_id,
            f"Water Leak Detection Company in {n}",
            leak_html(city, img_l),
            f"Water leak detection company in {n} without breaking tiles. Thermal cameras, report and written warranty — {PHONE_LOCAL}.",
            f"water leak detection {n}",
        )
        update_post(
            ins_id,
            f"Roof Insulation Company in {n}",
            insulation_html(city, img_i),
            f"Roof insulation company in {n}: thermal and waterproof systems with a written warranty — {PHONE_LOCAL}.",
            f"roof insulation {n}",
        )
        time.sleep(0.1)

    update_post(
        POSTS["waterproofing-company-dubai"],
        "Waterproofing Company in Dubai",
        waterproofing_html(),
        "Waterproofing company in Dubai for roofs, bathrooms and tanks. Written warranty from Rukn Eltatawer.",
        "waterproofing company Dubai",
    )
    update_post(
        POSTS["about-us"],
        "About Us",
        about_html(),
        "About Rukn Eltatawer — integrated home services in the UAE.",
        "Rukn Eltatawer",
    )
    update_post(
        POSTS["contact"],
        "Contact Us",
        contact_html(),
        "Contact Rukn Eltatawer for home services in the UAE.",
        "contact Rukn Eltatawer",
    )

    cli("wp litespeed-purge all", write=True)
    cli("wp cache flush", write=True)
    print("done")


if __name__ == "__main__":
    main()
