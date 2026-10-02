#!/usr/bin/env python3
"""Header language/country switcher + scope 0524314370 to leak/insulation only."""

from __future__ import annotations

from apply_english_feedback import (
    POSTS,
    leak_insulation_ids,
    restore_header,
    set_call_whatsapp_metas,
    sql,
)
from english_translated_articles import (
    CALL,
    about_html,
    contact_html,
    waterproofing_html,
)
from rukn_rewrite_pipeline import api_get, api_patch, cli

NON_CALL_REPLACEMENTS = [
    (
        f'Call <a href="tel:{CALL}">0524314370</a> or WhatsApp <a href="https://wa.me/971586634710">0586634710</a>',
        'WhatsApp <a href="https://wa.me/971586634710">0586634710</a>',
    ),
    (
        f'<a href="tel:{CALL}">Call 0524314370</a> | <a href="https://wa.me/971586634710">WhatsApp 0586634710</a>',
        '<a href="https://wa.me/971586634710">WhatsApp 0586634710</a>',
    ),
    (
        f'<strong>Call:</strong> <a href="tel:{CALL}">0524314370</a></p>\n<p><strong>WhatsApp:</strong> <a href="https://wa.me/971586634710">0586634710</a>',
        '<strong>WhatsApp:</strong> <a href="https://wa.me/971586634710">0586634710</a>',
    ),
    (
        f'<a href="tel:{CALL}">0524314370</a>',
        '<a href="https://wa.me/971586634710">0586634710</a>',
    ),
]


def main() -> None:
    print("== header: switcher instead of WhatsApp ==")
    restore_header()

    leak_ids = leak_insulation_ids()
    all_ids = sorted({int(v) for v in POSTS.values()})
    print("== Call 0524314370 on leak + insulation only ==")
    for pid in all_ids:
        set_call_whatsapp_metas(pid)
        kind = "CALL" if pid in leak_ids else "WA-only"
        print(" ", kind, pid)

    print("== strip Call number from non leak/insulation EN content ==")
    api_patch(f"wp/v2/posts/{POSTS['about-us']}", {"content": about_html()})
    api_patch(f"wp/v2/posts/{POSTS['contact']}", {"content": contact_html()})
    api_patch(f"wp/v2/posts/{POSTS['waterproofing-company-dubai']}", {"content": waterproofing_html()})
    print(" rewrote about/contact/waterproofing")

    for pid in all_ids:
        if pid in leak_ids:
            continue
        p = api_get(f"wp/v2/posts/{pid}?context=edit")
        raw = p["content"]["raw"]
        new = raw
        for old, nxt in NON_CALL_REPLACEMENTS:
            new = new.replace(old, nxt)
        if new != raw:
            api_patch(f"wp/v2/posts/{pid}", {"content": new})
            print(" content", pid)

    cli("wp litespeed-purge all", write=True)
    cli("wp cache flush", write=True)
    print("leak dubai phone", cli("wp post meta get 12185 phone_number").get("stdout", "").strip())
    print("about phone", cli("wp post meta get 11298 phone_number").get("stdout", "").strip())
    print("contact phone", cli("wp post meta get 12182 phone_number").get("stdout", "").strip())
    print("done")


if __name__ == "__main__":
    main()
