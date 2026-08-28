#!/usr/bin/env python3
"""0586634710 / +971586634710 is WhatsApp only — never on Call / tel: buttons."""

from __future__ import annotations

import json
from pathlib import Path

from apply_english_feedback import restore_header, set_call_whatsapp_metas, sql
from english_translated_articles import CALL, CALL_LOCAL, PHONE, PHONE_LOCAL, WA
from rukn_rewrite_pipeline import api_get, api_patch, cli

ROOT = Path(__file__).resolve().parent
EN_IDS = json.loads((ROOT / "english-site-build.json").read_text())["posts"]
EN_POST_IDS = sorted({int(v) for v in EN_IDS.values()})

CALL_KEYS = ("phone_number", "contact_number", "phone", "memo-meta-phone")
WA_NEEDLES = (
    "tel:+971586634710",
    "tel:971586634710",
    "tel:0586634710",
    "tel:+971-58-663-4710",
)

CTA_REPLACEMENTS = [
    (
        'Call or WhatsApp <a href="tel:+971586634710">0586634710</a> / <a href="https://wa.me/971586634710">+971586634710</a>',
        f'Call <a href="tel:{CALL}">{CALL_LOCAL}</a> or WhatsApp <a href="https://wa.me/{WA}">{PHONE_LOCAL}</a>',
    ),
    (
        '<a href="tel:+971586634710">Call 0586634710</a> | <a href="https://wa.me/971586634710">WhatsApp +971586634710</a>',
        f'<a href="tel:{CALL}">Call {CALL_LOCAL}</a> | <a href="https://wa.me/{WA}">WhatsApp {PHONE_LOCAL}</a>',
    ),
    (
        "<strong>Phone:</strong> 0586634710",
        f'<strong>Call:</strong> <a href="tel:{CALL}">{CALL_LOCAL}</a></p>\n<p><strong>WhatsApp:</strong> <a href="https://wa.me/{WA}">{PHONE_LOCAL}</a>',
    ),
    (
        '<a href="tel:+971586634710">0586634710</a>',
        f'<a href="tel:{CALL}">{CALL_LOCAL}</a>',
    ),
]


def in_list(ids: list[int]) -> str:
    return ",".join(str(i) for i in ids)


def replace_content(html: str) -> str:
    out = html
    for old, new in CTA_REPLACEMENTS:
        out = out.replace(old, new)
    out = out.replace("tel:+971586634710", f"tel:{CALL}")
    out = out.replace("tel:971586634710", f"tel:{CALL}")
    out = out.replace("tel:0586634710", f"tel:{CALL}")
    return out


def patch_en_posts() -> None:
    print("== English post Call/WhatsApp metas ==")
    for pid in EN_POST_IDS:
        set_call_whatsapp_metas(pid)
        print(" metas", pid)

    print("== English post tel: content ==")
    for pid in EN_POST_IDS:
        p = api_get(f"wp/v2/posts/{pid}?context=edit")
        raw = p["content"]["raw"]
        new = replace_content(raw)
        if new != raw:
            api_patch(f"wp/v2/posts/{pid}", {"content": new})
            print(" content", pid)
        else:
            print(" content skip", pid)


def sitewide_clear_wa_from_call_metas() -> None:
    skip = in_list(EN_POST_IDS)
    keys = ",".join("'" + k + "'" for k in CALL_KEYS)
    q = (
        "UPDATE wp3mdn_postmeta SET meta_value='' "
        f"WHERE meta_key IN ({keys}) "
        "AND meta_value LIKE '%586634710%' "
        f"AND post_id NOT IN ({skip})"
    )
    r = sql(q)
    print("cleared WA-only Call metas", r.get("stdout") or r)


def sitewide_tel_to_whatsapp() -> None:
    """Any leftover tel: to the WhatsApp-only number becomes a WhatsApp link."""
    q = (
        "UPDATE wp3mdn_posts SET post_content = "
        "REPLACE(REPLACE(REPLACE(REPLACE(post_content,"
        " 'href=\"tel:+971586634710\"', 'href=\"https://wa.me/971586634710\"'),"
        " 'href=\"tel:971586634710\"', 'href=\"https://wa.me/971586634710\"'),"
        " 'href=\"tel:0586634710\"', 'href=\"https://wa.me/971586634710\"'),"
        " 'href=\"tel:+971-58-663-4710\"', 'href=\"https://wa.me/971586634710\"') "
        "WHERE post_content LIKE '%tel:+971586634710%' "
        "OR post_content LIKE '%tel:0586634710%' "
        "OR post_content LIKE '%tel:971586634710%'"
    )
    r = sql(q)
    print("sitewide tel->wa", r.get("stdout") or r)


def verify_sample() -> None:
    r = cli("wp post meta get 12185 phone_number")
    print("EN dubai leak phone_number", (r.get("stdout") or "").strip())
    r = cli("wp post meta get 12185 whatsapp")
    print("EN dubai leak whatsapp", (r.get("stdout") or "").strip())
    p = api_get("wp/v2/posts/12185?context=edit")
    raw = p["content"]["raw"]
    print("EN dubai leak still has tel WA?", "tel:+971586634710" in raw, "tel CALL?", f"tel:{CALL}" in raw)


def main() -> None:
    print("== header: never Call 586634710 ==")
    restore_header()
    patch_en_posts()
    print("== sitewide: empty Call metas that stored the WhatsApp-only number ==")
    sitewide_clear_wa_from_call_metas()
    print("== sitewide: leftover tel:586634710 -> WhatsApp ==")
    sitewide_tel_to_whatsapp()
    cli("wp litespeed-purge all", write=True)
    cli("wp cache flush", write=True)
    verify_sample()
    print("done")


if __name__ == "__main__":
    main()
