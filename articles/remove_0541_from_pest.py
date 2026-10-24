#!/usr/bin/env python3
"""Remove +971541673020 from pest / birds / pigeon posts. Keep it on cleaning.

Replacement on those pest posts: +971522901095 / 0522901095 (Call + WhatsApp),
same number already used on Al Ain pest/birds. Do not put 0586634710 on tel:.
Do not touch bathroom-cleaning-* or other cleaning posts.
"""

from __future__ import annotations

import json
import os
import re
import sys
import urllib.request

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))

from wp_client import (
    WP,
    api_get,
    api_post,
    cli,
    cli_approved,
    has_auth,
    public_get,
    purge_caches,
    sql_set_meta,
)

OLD_E164 = "+971541673020"
OLD_LOCAL = "0541673020"
OLD_DIGITS = "971541673020"
NEW_E164 = "+971522901095"
NEW_LOCAL = "0522901095"
NEW_DIGITS = "971522901095"

# Confirmed live content hits (public REST scan).
KNOWN_PEST_IDS = [
    10474,  # pest-control-khor-fakkan
    6581,  # lizard-control-dubai
    6693,  # snake-control-ajman
    6731,  # crawling-pest-control-ras-al-khaimah
    6749,  # snake-control-ras-al-khaimah
    3274,  # termite-control-dubai
    3276,  # termite-control-sharjah
    267,  # pest-control-company-in-ajman
    263,  # insect-control-in-sharjah
    170,  # insect-control-in-dubai
]

PEST_SLUG = re.compile(
    r"pest|termite|lizard|snake|pigeon|bird|insect|cockroach|crawling-pest|mosquito|rodent",
    re.I,
)
PEST_TITLE = re.compile(
    r"حشر|مكافح|رمة|وزغ|برص|ثعبان|حمام|طيور|صراصير|نمل|فأر|قوارض|بق\s|طارد",
    re.I,
)
CLEAN_KEEP = re.compile(
    r"bathroom-cleaning|تنظيف حمامات|hourly|خادمة|تنظيف منازل|تنظيف فلل|تنظيف كنبا",
    re.I,
)

# Number variants → new equivalents. Longer forms first.
REPLACEMENTS = [
    (OLD_E164, NEW_E164),
    ("+971 54 167 3020", NEW_E164),
    ("+971-54-167-3020", NEW_E164),
    (OLD_LOCAL, NEW_LOCAL),
    ("0541 673 020", NEW_LOCAL),
    ("0541-673-020", NEW_LOCAL),
    (OLD_DIGITS, NEW_DIGITS),
]


def is_pest_not_cleaning(slug: str, title: str) -> bool:
    blob = f"{slug} {title}"
    if CLEAN_KEEP.search(blob):
        return False
    return bool(PEST_SLUG.search(slug) or PEST_TITLE.search(title))


def replace_numbers(text: str) -> tuple[str, int]:
    n = 0
    for old, new in REPLACEMENTS:
        c = text.count(old)
        if c:
            text = text.replace(old, new)
            n += c
    return text, n


def leftover_old(text: str) -> int:
    return len(re.findall(r"541673020|0541673020", text))


def public_posts_by_id(pid: int) -> dict:
    req = urllib.request.Request(
        f"{WP}/wp-json/wp/v2/posts/{pid}?_fields=id,slug,title,content,link",
        headers={"User-Agent": "CursorAgent/1.0"},
    )
    with urllib.request.urlopen(req, timeout=45) as r:
        return json.load(r)


def discover_extra_targets() -> list[int]:
    """Re-scan pest-ish public posts for any extra 0541 leftovers."""
    extra = []
    seen = set(KNOWN_PEST_IDS)
    queries = [
        "pest-control",
        "termite-control",
        "pigeon",
        "bird-control",
        "insect-control",
        "مكافحة حشرات",
        "طارد الحمام",
        "مكافحة الطيور",
    ]
    for q in queries:
        try:
            posts = public_get(
                "wp/v2/posts",
                {"search": q, "per_page": 100, "_fields": "id,slug,title"},
            )
        except Exception:
            continue
        for p in posts:
            pid = int(p["id"])
            if pid in seen:
                continue
            title = (p.get("title") or {}).get("rendered") or ""
            if not is_pest_not_cleaning(p.get("slug") or "", title):
                continue
            try:
                full = public_posts_by_id(pid)
            except Exception:
                continue
            html = (full.get("content") or {}).get("rendered") or ""
            if leftover_old(html):
                extra.append(pid)
                seen.add(pid)
    return extra


def update_phone_metas(post_id: int) -> None:
    for key in ("memo-meta-phone", "phone", "phone_number", "whatsapp", "whatsapp_number"):
        try:
            sql_set_meta(post_id, key, NEW_LOCAL)
        except Exception as e:
            print("meta warn", post_id, key, e)
    try:
        data = api_get(
            f"wp/v2/posts/{post_id}",
            {"context": "edit", "_fields": "id,title"},
        )
        title = (data.get("title") or {}).get("raw") or ""
        sql_set_meta(
            post_id,
            "post__call_section__data",
            {
                "call_section_title": "تواصل معنا الآن",
                "call_section_content": title,
                "call_section_phone": NEW_E164,
                "call_section_whatsapp": NEW_E164,
            },
        )
    except Exception as e:
        print("call section warn", post_id, e)


def scrub_rank_math_via_cli(post_id: int) -> None:
    try:
        raw = cli(f"post meta get {post_id} rank_math_title")
        title = (raw.get("stdout") or raw.get("output") or "") if isinstance(raw, dict) else str(raw)
        if leftover_old(title):
            new, _ = replace_numbers(title.strip())
            # Keep it simple; Arabic via SQL UNHEX
            sql_set_meta(post_id, "rank_math_title", new)
            print("rank_math_title scrubbed", post_id)
    except Exception as e:
        print("rankmath title skip", post_id, e)


def scrub_schema_metas(post_ids: list[int]) -> None:
    """Replace 0541 inside serialized schema metas. Same digit length keeps PHP serialize valid."""
    ids = ",".join(str(i) for i in post_ids)
    q = (
        "UPDATE wp3mdn_postmeta SET meta_value="
        "REPLACE(REPLACE(REPLACE(meta_value,'+971541673020','+971522901095'),"
        "'0541673020','0522901095'),'971541673020','971522901095') "
        f"WHERE post_id IN ({ids}) AND meta_value LIKE '%541673020%'"
    )
    cmd = f'db query "{q}"'
    try:
        cli(cmd, write=True)
    except Exception:
        pass
    r = cli_approved(cmd)
    print("schema metas", r.get("stdout") or r)


def apply_one(post_id: int) -> dict:
    post = api_get(
        f"wp/v2/posts/{post_id}",
        {"context": "edit", "_fields": "id,slug,title,content,link"},
    )
    slug = post.get("slug") or ""
    title = (post.get("title") or {}).get("raw") or (post.get("title") or {}).get("rendered") or ""
    if not is_pest_not_cleaning(slug, title):
        return {"id": post_id, "slug": slug, "skipped": "not-pest-or-is-cleaning"}
    raw = (post.get("content") or {}).get("raw") or ""
    new, n = replace_numbers(raw)
    left = leftover_old(new)
    if n:
        api_post(f"wp/v2/posts/{post_id}", {"content": new, "id": post_id})
    update_phone_metas(post_id)
    scrub_rank_math_via_cli(post_id)
    return {
        "id": post_id,
        "slug": slug,
        "replacements": n,
        "leftover": left,
        "link": post.get("link"),
    }


def main() -> int:
    extras = []
    try:
        extras = discover_extra_targets()
    except Exception as e:
        print("discover warn", e)
    targets = list(dict.fromkeys(KNOWN_PEST_IDS + extras))
    print("targets", targets)
    if not has_auth():
        print("WP_USER / WP_APP_PASS missing — would replace 0541673020 → 0522901095 on pest posts only")
        for pid in targets:
            try:
                p = public_posts_by_id(pid)
                html = (p.get("content") or {}).get("rendered") or ""
                print(f"  {pid} {p.get('slug')} leftover={leftover_old(html)}")
            except Exception as e:
                print("  ", pid, e)
        return 2
    results = [apply_one(pid) for pid in targets]
    try:
        scrub_schema_metas(targets)
    except Exception as e:
        print("schema meta warn", e)
    purge_caches()
    print(json.dumps(results, ensure_ascii=False, indent=2))
    bad = [r for r in results if r.get("leftover")]
    return 1 if bad else 0


if __name__ == "__main__":
    raise SystemExit(main())
