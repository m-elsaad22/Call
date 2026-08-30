#!/usr/bin/env python3
"""Rewrite leftover pool articles + publish hub/specialty + English twins."""

from __future__ import annotations

import hashlib
import json
import time
from pathlib import Path
from urllib.error import HTTPError

from pool_visual_html import EN_PATH, render, TEL, TEL_LOCAL
from rukn_rewrite_pipeline import api_get, api_patch, api_post, cli, cli_approved, php_serialize

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

LEFTOVER_CLEAN = [
    {"id": 795, "kind": "clean", "city": "أبوظبي", "thumb": 3266},
    {"id": 1046, "kind": "clean", "city": "الشارقة", "thumb": 3266},
    {"id": 1218, "kind": "clean", "city": "دبي", "thumb": 3260},
    {"id": 2370, "kind": "clean", "city": "الفجيرة", "thumb": 2738},
    {"id": 2373, "kind": "clean", "city": "أم القيوين", "thumb": 2901},
    {"id": 2425, "kind": "clean", "city": "العين", "thumb": 3260},
]

AR_EXTRA = [
    {
        "kind": "hub",
        "slug": "swimming-pool-company-uae",
        "title": "شركة مسابح في الإمارات",
        "thumb": 1527,
    },
    {
        "kind": "leak",
        "slug": "swimming-pool-leak-repair-uae",
        "title": "إصلاح تسريب المسبح في الإمارات",
        "thumb": 3255,
    },
    {
        "kind": "waterproof",
        "slug": "swimming-pool-waterproofing-uae",
        "title": "عزل أحواض السباحة في الإمارات",
        "thumb": 1527,
    },
    {
        "kind": "chlorine",
        "slug": "swimming-pool-chlorine-salt-uae",
        "title": "كلور وملح المسابح في الإمارات",
        "thumb": 3263,
    },
    {
        "kind": "heat",
        "slug": "swimming-pool-heating-uae",
        "title": "تدفئة المسابح في الإمارات",
        "thumb": 3261,
    },
    {
        "kind": "jacuzzi",
        "slug": "jacuzzi-service-uae",
        "title": "جاكوزي وتركيب الأحواض الساخنة في الإمارات",
        "thumb": 1527,
    },
]

EN_CITIES = [
    {
        "kind": "build",
        "city": "أبوظبي",
        "ar_id": 38,
        "slug": "swimming-pool-construction-abu-dhabi",
        "title": "Swimming Pool Construction Company in Abu Dhabi",
        "thumb": 1527,
    },
    {
        "kind": "build",
        "city": "دبي",
        "ar_id": 97,
        "slug": "swimming-pool-construction-dubai",
        "title": "Swimming Pool Construction Company in Dubai",
        "thumb": 1527,
    },
    {
        "kind": "build",
        "city": "العين",
        "ar_id": 133,
        "slug": "swimming-pool-construction-al-ain",
        "title": "Swimming Pool Construction Company in Al Ain",
        "thumb": 1527,
    },
    {
        "kind": "build",
        "city": "الشارقة",
        "ar_id": 164,
        "slug": "swimming-pool-construction-sharjah",
        "title": "Swimming Pool Construction Company in Sharjah",
        "thumb": 1527,
    },
    {
        "kind": "build",
        "city": "عجمان",
        "ar_id": 12371,
        "slug": "swimming-pool-construction-ajman",
        "title": "Swimming Pool Construction Company in Ajman",
        "thumb": 1527,
    },
    {
        "kind": "build",
        "city": "رأس الخيمة",
        "ar_id": 12372,
        "slug": "swimming-pool-construction-ras-al-khaimah",
        "title": "Swimming Pool Construction Company in Ras Al Khaimah",
        "thumb": 1527,
    },
    {
        "kind": "build",
        "city": "الفجيرة",
        "ar_id": 12373,
        "slug": "swimming-pool-construction-fujairah",
        "title": "Swimming Pool Construction Company in Fujairah",
        "thumb": 1527,
    },
    {
        "kind": "build",
        "city": "أم القيوين",
        "ar_id": 12374,
        "slug": "swimming-pool-construction-umm-al-quwain",
        "title": "Swimming Pool Construction Company in Umm Al Quwain",
        "thumb": 1527,
    },
    {
        "kind": "clean",
        "city": "أبوظبي",
        "ar_id": 795,
        "slug": "swimming-pool-cleaning-abu-dhabi",
        "title": "Swimming Pool Cleaning Company in Abu Dhabi",
        "thumb": 3266,
    },
    {
        "kind": "clean",
        "city": "دبي",
        "ar_id": 1218,
        "slug": "swimming-pool-cleaning-dubai",
        "title": "Swimming Pool Cleaning Company in Dubai",
        "thumb": 3260,
    },
    {
        "kind": "clean",
        "city": "الشارقة",
        "ar_id": 1046,
        "slug": "swimming-pool-cleaning-sharjah",
        "title": "Swimming Pool Cleaning Company in Sharjah",
        "thumb": 3266,
    },
    {
        "kind": "clean",
        "city": "عجمان",
        "ar_id": 5960,
        "slug": "swimming-pool-cleaning-ajman",
        "title": "Swimming Pool Cleaning Company in Ajman",
        "thumb": 3266,
    },
    {
        "kind": "clean",
        "city": "رأس الخيمة",
        "ar_id": 5981,
        "slug": "swimming-pool-cleaning-ras-al-khaimah",
        "title": "Swimming Pool Cleaning Company in Ras Al Khaimah",
        "thumb": 3260,
    },
    {
        "kind": "clean",
        "city": "الفجيرة",
        "ar_id": 2370,
        "slug": "swimming-pool-cleaning-fujairah",
        "title": "Swimming Pool Cleaning Company in Fujairah",
        "thumb": 2738,
    },
    {
        "kind": "clean",
        "city": "أم القيوين",
        "ar_id": 2373,
        "slug": "swimming-pool-cleaning-umm-al-quwain",
        "title": "Swimming Pool Cleaning Company in Umm Al Quwain",
        "thumb": 2901,
    },
    {
        "kind": "clean",
        "city": "العين",
        "ar_id": 2425,
        "slug": "swimming-pool-cleaning-al-ain",
        "title": "Swimming Pool Cleaning Company in Al Ain",
        "thumb": 3260,
    },
    {
        "kind": "maint",
        "city": "أبوظبي",
        "ar_id": 6384,
        "slug": "swimming-pool-maintenance-abu-dhabi",
        "title": "Swimming Pool Maintenance Company in Abu Dhabi",
        "thumb": 3255,
    },
    {
        "kind": "maint",
        "city": "دبي",
        "ar_id": 6469,
        "slug": "swimming-pool-maintenance-dubai",
        "title": "Swimming Pool Maintenance Company in Dubai",
        "thumb": 3255,
    },
    {
        "kind": "maint",
        "city": "الشارقة",
        "ar_id": 6580,
        "slug": "swimming-pool-maintenance-sharjah",
        "title": "Swimming Pool Maintenance Company in Sharjah",
        "thumb": 3261,
    },
    {
        "kind": "maint",
        "city": "عجمان",
        "ar_id": 6663,
        "slug": "swimming-pool-maintenance-ajman",
        "title": "Swimming Pool Maintenance Company in Ajman",
        "thumb": 3255,
    },
    {
        "kind": "maint",
        "city": "رأس الخيمة",
        "ar_id": 6746,
        "slug": "swimming-pool-maintenance-ras-al-khaimah",
        "title": "Swimming Pool Maintenance Company in Ras Al Khaimah",
        "thumb": 3261,
    },
    {
        "kind": "maint",
        "city": "الفجيرة",
        "ar_id": 6824,
        "slug": "swimming-pool-maintenance-fujairah",
        "title": "Swimming Pool Maintenance Company in Fujairah",
        "thumb": 3261,
    },
    {
        "kind": "maint",
        "city": "أم القيوين",
        "ar_id": 6886,
        "slug": "swimming-pool-maintenance-umm-al-quwain",
        "title": "Swimming Pool Maintenance Company in Umm Al Quwain",
        "thumb": 3255,
    },
    {
        "kind": "maint",
        "city": "العين",
        "ar_id": 6949,
        "slug": "swimming-pool-maintenance-al-ain",
        "title": "Swimming Pool Maintenance Company in Al Ain",
        "thumb": 3263,
    },
]

EN_EXTRA = [
    {
        "kind": "hub",
        "slug": "pool-company-uae",
        "title": "Swimming Pool Company in the UAE",
        "thumb": 1527,
    },
    {
        "kind": "leak",
        "slug": "pool-leak-repair-uae",
        "title": "Swimming Pool Leak Repair in the UAE",
        "thumb": 3255,
    },
    {
        "kind": "waterproof",
        "slug": "pool-waterproofing-uae",
        "title": "Swimming Pool Waterproofing in the UAE",
        "thumb": 1527,
    },
    {
        "kind": "chlorine",
        "slug": "pool-chlorine-salt-uae",
        "title": "Pool Chlorine and Salt Systems in the UAE",
        "thumb": 3263,
    },
    {
        "kind": "heat",
        "slug": "pool-heating-uae",
        "title": "Swimming Pool Heating in the UAE",
        "thumb": 3261,
    },
    {
        "kind": "jacuzzi",
        "slug": "spa-jacuzzi-service-uae",
        "title": "Jacuzzi Installation and Service in the UAE",
        "thumb": 1527,
    },
]

AR_CATS = [2364, 2661]
EN_CATS = [2948]
PHONE_KEYS = (
    "phone_number",
    "contact_number",
    "phone",
    "memo-meta-phone",
    "whatsapp",
    "whatsapp_number",
)
MAP_PATH = Path(__file__).resolve().parent / "pool-en-map.json"


def sql(cmd_sql: str) -> dict:
    cmd = "wp db query " + json.dumps(cmd_sql)
    try:
        return cli(cmd, write=True)
    except HTTPError as e:
        body = e.read().decode() if hasattr(e, "read") else str(e)
        if "approval_required" not in body and "409" not in body:
            raise
        return cli_approved(cmd)


def sh(cmd: str) -> dict:
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


def set_language(pid: int, lang: str) -> None:
    sh(f"wp post term set {int(pid)} language {lang}")


def existing_pll_group(ar_id: int) -> tuple[str, str] | None:
    data = q(
        "SELECT t.slug, tt.description FROM wp3mdn_term_relationships tr "
        "JOIN wp3mdn_term_taxonomy tt ON tr.term_taxonomy_id=tt.term_taxonomy_id "
        "JOIN wp3mdn_terms t ON t.term_id=tt.term_id "
        f"WHERE tr.object_id={int(ar_id)} AND tt.taxonomy='post_translations' LIMIT 1"
    )
    rows = data.get("results") or []
    if rows:
        return str(rows[0]["slug"]), str(rows[0].get("description") or "")
    return None


def link_translations(ar_id: int, en_id: int) -> None:
    desc = php_serialize({"ar": int(ar_id), "en": int(en_id)})
    found = existing_pll_group(int(ar_id))
    if found:
        slug, _old = found
        sh(
            f"wp term update post_translations {slug} --by=slug --description={json.dumps(desc)}"
        )
    else:
        slug = "pll_" + hashlib.md5(f"ar{ar_id}en{en_id}".encode()).hexdigest()[:13]
        sh(
            f"wp term create post_translations {slug} --description={json.dumps(desc)} --porcelain"
        )
    sh(f"wp post term add {int(ar_id)} post_translations {slug}")
    sh(f"wp post term add {int(en_id)} post_translations {slug}")


def find_post(slug: str) -> int | None:
    rows = (
        q(
            "SELECT ID FROM wp3mdn_posts WHERE post_name="
            f"'{slug}' AND post_type='post' AND post_status IN ('publish','draft')"
        ).get("results")
        or []
    )
    if rows:
        return int(rows[0]["ID"])
    try:
        items = api_get("wp/v2/posts", {"slug": slug, "per_page": 5, "status": "any"})
        if isinstance(items, list) and items:
            return int(items[0]["id"])
    except Exception:
        pass
    return None


def upsert_post(title: str, slug: str, html: str, categories: list[int]) -> int:
    pid = find_post(slug)
    body = {
        "title": title,
        "slug": slug,
        "status": "publish",
        "content": html,
        "categories": categories,
    }
    if pid:
        api_patch(f"wp/v2/posts/{pid}", body)
        print("updated", pid, slug, "chars", len(html))
        return pid
    post = api_post("wp/v2/posts", body)
    pid = int(post["id"])
    print("created", pid, slug, "chars", len(html))
    return pid


def publish_item(item: dict, lang: str = "ar") -> None:
    html = render(item["kind"], item.get("city") or "", lang)
    api_patch(f"wp/v2/posts/{item['id']}", {"content": html})
    set_thumb(int(item["id"]), int(item["thumb"]))
    set_phones(int(item["id"]))
    print("updated", item["id"], item["kind"], item.get("city"), lang, "chars", len(html))


def create_build_pages() -> list[int]:
    created = []
    for item in NEW_BUILD:
        title = f"شركة إنشاء وصيانة مسابح في {item['city']}"
        html = render("build", item["city"], "ar")
        pid = upsert_post(title, item["slug"], html, AR_CATS)
        set_thumb(pid, int(item["thumb"]))
        set_phones(pid)
        set_language(pid, "ar")
        created.append(pid)
    return created


def step_thin() -> None:
    print("== step thin ==")
    for item in THIN:
        publish_item(item)


def step_medium() -> None:
    print("== step medium ==")
    for item in MEDIUM:
        publish_item(item)


def step_new() -> None:
    print("== step new construction ==")
    create_build_pages()


def step_leftover() -> None:
    print("== leftover city cleaning ==")
    for item in LEFTOVER_CLEAN:
        publish_item(item)
        time.sleep(0.1)


def step_ar_extra() -> dict[str, int]:
    print("== Arabic hub + specialty ==")
    ids: dict[str, int] = {}
    for item in AR_EXTRA:
        html = render(item["kind"], "", "ar")
        pid = upsert_post(item["title"], item["slug"], html, AR_CATS)
        set_thumb(pid, int(item["thumb"]))
        set_phones(pid)
        set_language(pid, "ar")
        ids[item["kind"]] = pid
        time.sleep(0.1)
    return ids


def step_en(ar_extra: dict[str, int] | None = None) -> dict[str, int]:
    print("== English pool pages ==")
    mapping: dict[str, int] = {}
    for item in EN_CITIES:
        html = render(item["kind"], item["city"], "en")
        expected = EN_PATH[(item["kind"], item["city"])].rstrip("/").rsplit("/", 1)[-1]
        if expected != item["slug"]:
            print("slug mismatch", item["slug"], expected)
        pid = upsert_post(item["title"], item["slug"], html, EN_CATS)
        set_thumb(pid, int(item["thumb"]))
        set_phones(pid)
        set_language(pid, "en")
        try:
            link_translations(int(item["ar_id"]), pid)
        except Exception as e:
            print("pll warn", item["slug"], e)
        mapping[item["slug"]] = pid
        time.sleep(0.12)
    extra_ids = ar_extra or {}
    if not extra_ids:
        for item in AR_EXTRA:
            found = find_post(item["slug"])
            if found:
                extra_ids[item["kind"]] = found
    for item in EN_EXTRA:
        html = render(item["kind"], "", "en")
        pid = upsert_post(item["title"], item["slug"], html, EN_CATS)
        set_thumb(pid, int(item["thumb"]))
        set_phones(pid)
        set_language(pid, "en")
        ar_id = extra_ids.get(item["kind"])
        if ar_id and ar_id != pid:
            try:
                link_translations(int(ar_id), pid)
            except Exception as e:
                print("pll spec warn", item["slug"], e)
        mapping[item["slug"]] = pid
        time.sleep(0.12)
    MAP_PATH.write_text(json.dumps(mapping, indent=2, ensure_ascii=False), encoding="utf-8")
    print("wrote", MAP_PATH)
    return mapping


def purge() -> None:
    cli("wp litespeed-purge all", write=True)
    cli("wp cache flush", write=True)
    try:
        cli("wp rewrite flush", write=True)
    except Exception as e:
        print("rewrite flush warn", e)


def main(which: str = "leftover") -> None:
    ar_extra: dict[str, int] | None = None
    if which in ("thin", "legacy"):
        step_thin()
    if which in ("medium", "legacy"):
        step_medium()
    if which in ("new", "legacy"):
        step_new()
    if which in ("leftover", "ar", "all"):
        step_leftover()
    if which in ("extra", "ar", "all"):
        ar_extra = step_ar_extra()
    if which in ("en", "all"):
        step_en(ar_extra)
    purge()
    print("done", which)


if __name__ == "__main__":
    import sys

    main(sys.argv[1] if len(sys.argv) > 1 else "leftover")
