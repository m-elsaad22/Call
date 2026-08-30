#!/usr/bin/env python3
"""Rewrite thin/medium pool articles + create missing construction pages."""

from __future__ import annotations

import json
from urllib.error import HTTPError

from pool_visual_html import render, TEL, TEL_LOCAL, WA
from rukn_rewrite_pipeline import api_get, api_patch, api_post, cli, cli_approved

THIN = [
    {"id": 5960, "kind": "clean", "city": "عجمان", "thumb": 3266},
    {"id": 5981, "kind": "clean", "city": "رأس الخيمة", "thumb": 3260},
    {"id": 6663, "kind": "maint", "city": "عجمان", "thumb": 3255},
    {"id": 6824, "kind": "maint", "city": "الفجيرة", "thumb": 3261},
    {"id": 6886, "kind": "maint", "city": "أم القيوين", "thumb": 3255},
    {"id": 6949, "kind": "maint", "city": "العين", "thumb": 3263},
]

MEDIUM = [
    {"id": 97, "kind": "build", "city": "دبي", "thumb": 1527},
    {"id": 133, "kind": "build", "city": "العين", "thumb": 1527},
    {"id": 164, "kind": "build", "city": "الشارقة", "thumb": 1527},
    {"id": 6384, "kind": "maint", "city": "أبوظبي", "thumb": 3255},
    {"id": 6469, "kind": "maint", "city": "دبي", "thumb": 3255},
    {"id": 6580, "kind": "maint", "city": "الشارقة", "thumb": 3261},
    {"id": 6746, "kind": "maint", "city": "رأس الخيمة", "thumb": 3261},
]

NEW_BUILD = [
    {"slug": "swimming-pool-company-in-ajman", "city": "عجمان", "thumb": 1527},
    {"slug": "swimming-pool-company-in-ras-al-khaimah", "city": "رأس الخيمة", "thumb": 1527},
    {"slug": "swimming-pool-company-in-fujairah", "city": "الفجيرة", "thumb": 1527},
    {"slug": "swimming-pool-company-in-umm-al-quwain", "city": "أم القيوين", "thumb": 1527},
]

PHONE_KEYS = (
    "phone_number",
    "contact_number",
    "phone",
    "memo-meta-phone",
    "whatsapp",
    "whatsapp_number",
)


def sql(cmd_sql: str) -> dict:
    cmd = "wp db query " + json.dumps(cmd_sql)
    try:
        return cli(cmd, write=True)
    except HTTPError as e:
        body = e.read().decode() if hasattr(e, "read") else str(e)
        if "approval_required" not in body and "409" not in body:
            raise
        return cli_approved(cmd)


def q(cmd_sql: str) -> dict:
    r = cli("wp db query " + json.dumps(cmd_sql))
    raw = r.get("stdout") or ""
    try:
        return json.loads(raw)
    except json.JSONDecodeError:
        return r


def set_thumb(pid: int, mid: int) -> None:
    sql(f"DELETE FROM wp3mdn_postmeta WHERE post_id={pid} AND meta_key='_thumbnail_id'")
    sql(
        f"INSERT INTO wp3mdn_postmeta (post_id, meta_key, meta_value) VALUES ({pid},'_thumbnail_id','{mid}')"
    )


def set_phones(pid: int) -> None:
    keys = ",".join("'" + k + "'" for k in PHONE_KEYS)
    sql(f"DELETE FROM wp3mdn_postmeta WHERE post_id={pid} AND meta_key IN ({keys})")
    rows = ",".join(f"({pid},'{k}','{TEL}')" for k in PHONE_KEYS)
    sql(f"INSERT INTO wp3mdn_postmeta (post_id, meta_key, meta_value) VALUES {rows}")
    sql(f"DELETE FROM wp3mdn_postmeta WHERE post_id={pid} AND meta_key='rank_math_title'")
    sql(
        f"INSERT INTO wp3mdn_postmeta (post_id, meta_key, meta_value) "
        f"VALUES ({pid},'rank_math_title','%title% | {TEL_LOCAL}')"
    )


def publish_item(item: dict) -> None:
    html = render(item["kind"], item["city"])
    api_patch(f"wp/v2/posts/{item['id']}", {"content": html})
    set_thumb(int(item["id"]), int(item["thumb"]))
    set_phones(int(item["id"]))
    print("updated", item["id"], item["kind"], item["city"], "chars", len(html))


def create_build_pages() -> list[int]:
    created = []
    for item in NEW_BUILD:
        exists = q(
            "SELECT ID FROM wp3mdn_posts WHERE post_name="
            f"'{item['slug']}' AND post_type='post' AND post_status='publish'"
        ).get("results") or []
        title = f"شركة إنشاء وصيانة مسابح في {item['city']}"
        html = render("build", item["city"])
        if exists:
            pid = int(exists[0]["ID"])
            api_patch(f"wp/v2/posts/{pid}", {"title": title, "content": html})
            print("updated existing", pid, item["slug"])
        else:
            post = api_post(
                "wp/v2/posts",
                {
                    "title": title,
                    "slug": item["slug"],
                    "status": "publish",
                    "content": html,
                    "categories": [2364, 2661],
                },
            )
            pid = int(post["id"])
            print("created", pid, item["slug"])
        set_thumb(pid, int(item["thumb"]))
        set_phones(pid)
        created.append(pid)
    return created


def step_thin() -> None:
    print("== step 1 thin ==")
    for item in THIN:
        publish_item(item)


def step_medium() -> None:
    print("== step 2 medium ==")
    for item in MEDIUM:
        publish_item(item)


def step_new() -> None:
    print("== step 3 new construction ==")
    create_build_pages()


def purge() -> None:
    cli("wp litespeed-purge all", write=True)
    cli("wp cache flush", write=True)


def main(which: str = "thin") -> None:
    if which in ("thin", "all"):
        step_thin()
    if which in ("medium", "all"):
        step_medium()
    if which in ("new", "all"):
        step_new()
    purge()
    print("done", which)


if __name__ == "__main__":
    import sys

    main(sys.argv[1] if len(sys.argv) > 1 else "thin")
