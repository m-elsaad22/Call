#!/usr/bin/env python3
"""Publish the four missing Al Ain-related articles, then retarget post 405 links.

Existing articles keep their published slugs; only hrefs are rewritten.
New slugs: pool-leak-detection-al-ain, water-pipe-types-uae,
infrared-vs-geophone-leak-detection, water-leak-detection-cost-factors-uae.
"""

from __future__ import annotations

import os
import sys
import time

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))

from related_article_bodies import ARTICLES
from wp_client import api_get, api_post, find_post_by_slug, has_auth, purge_caches, sql_set_meta

ALAIN_POST_ID = 405

IQRA = [
    (
        "شركة كشف تسربات المياه في أبوظبي 2026 – الدليل الشامل",
        "a-water-leak-detection-company-in-abu-dhabi/",
        "شركة كشف تسربات المياه في أبوظبي 2026 – الدليل الشامل",
    ),
    (
        "أسباب ارتفاع فاتورة المياه في الإمارات – دليل التشخيص الذاتي",
        "water-bill-problem-solving-company-in-abu-dhabi/",
        "أسباب ارتفاع فاتورة المياه في الإمارات – دليل التشخيص الذاتي",
    ),
    (
        "دليل أنواع أنابيب المياه في الإمارات وعمرها الافتراضي 2026",
        "water-pipe-types-uae/",
        "دليل أنواع أنابيب المياه في الإمارات وعمرها الافتراضي",
    ),
    (
        "الفرق بين كشف التسربات بالأشعة تحت الحمراء والكشف الجيوفوني 2026",
        "infrared-vs-geophone-leak-detection/",
        "الفرق بين كشف التسربات بالأشعة تحت الحمراء والكشف الجيوفوني",
    ),
    (
        "كيف تختار أفضل شركة كشف تسربات موثوقة في الإمارات – 7 معايير ذهبية",
        "information-about-leak-detection-companies/",
        "كيف تختار أفضل شركة كشف تسربات موثوقة في الإمارات – 7 معايير ذهبية",
    ),
    (
        "أسعار كشف تسربات المياه في الإمارات 2026 – مقارنة شاملة",
        "water-leak-detection-cost-factors-uae/",
        "أسعار كشف تسربات المياه في الإمارات – العوامل التي تغيّر التكلفة",
    ),
]

CLUSTER_OLD = """<ol>
<li><strong><a href="https://www.rukn-eltatawer.com/water-leak-detection-uae/">شركة كشف تسربات المياه في أبوظبي 2026</a></strong> — نفس الخدمة في العاصمة بنفس مستوى الجودة</li>
<li><strong>شركة عزل أسطح في العين 2026</strong> — الخدمة التكميلية لحماية المبنى من أعلى وأسفل</li>
<li><strong>شركة كشف تسربات المسابح في العين 2026</strong> — متخصص في كشف تسربات المسابح بدون تفريغ</li>
<li><strong>شركة عزل خزانات المياه في العين 2026</strong> — حماية خزانك من التسربات والتلوث</li>
<li><strong>أسعار كشف تسربات المياه في الإمارات 2026 – مقارنة شاملة</strong> — لمن يريد مقارنة الأسعار قبل التعاقد</li>
</ol>"""

CLUSTER_NEW = """<ol>
<li><strong><a href="https://www.rukn-eltatawer.com/a-water-leak-detection-company-in-abu-dhabi/">شركة كشف تسربات المياه في أبوظبي</a></strong> — نفس الخدمة في العاصمة</li>
<li><strong><a href="https://www.rukn-eltatawer.com/roof-insulation-company-al-ain/">شركة عزل أسطح العين</a></strong> — الخدمة التكميلية لحماية المبنى من أعلى وأسفل</li>
<li><strong><a href="https://www.rukn-eltatawer.com/pool-leak-detection-al-ain/">شركة كشف تسربات المسابح في العين</a></strong> — متخصص في كشف تسربات المسابح بدون تفريغ</li>
<li><strong><a href="https://www.rukn-eltatawer.com/tank-insulation-al-ain/">شركة عزل خزانات في العين</a></strong> — حماية خزانك من التسربات والتلوث</li>
<li><strong><a href="https://www.rukn-eltatawer.com/water-leak-detection-cost-factors-uae/">أسعار كشف تسربات المياه في الإمارات – العوامل التي تغيّر التكلفة</a></strong> — ما الذي يغيّر تكلفة الكشف قبل التعاقد</li>
</ol>"""

END_ABU_OLD = '<a href="https://www.rukn-eltatawer.com/water-leak-detection-uae/">شركة كشف تسربات المياه في أبوظبي 2026</a>'
END_ABU_NEW = '<a href="https://www.rukn-eltatawer.com/a-water-leak-detection-company-in-abu-dhabi/">شركة كشف تسربات المياه في أبوظبي</a>'


CLUSTER_ITEMS = [
    (
        "شركة عزل أسطح في العين 2026",
        "https://www.rukn-eltatawer.com/roof-insulation-company-al-ain/",
        "شركة عزل أسطح العين",
    ),
    (
        "شركة كشف تسربات المسابح في العين 2026",
        "https://www.rukn-eltatawer.com/pool-leak-detection-al-ain/",
        "شركة كشف تسربات المسابح في العين",
    ),
    (
        "شركة عزل خزانات المياه في العين 2026",
        "https://www.rukn-eltatawer.com/tank-insulation-al-ain/",
        "شركة عزل خزانات في العين",
    ),
    (
        "أسعار كشف تسربات المياه في الإمارات 2026 – مقارنة شاملة",
        "https://www.rukn-eltatawer.com/water-leak-detection-cost-factors-uae/",
        "أسعار كشف تسربات المياه في الإمارات – العوامل التي تغيّر التكلفة",
    ),
]


def rewrite_alain(html: str) -> tuple[str, int]:
    n = 0
    for old_text, new_path, new_text in IQRA:
        old = f'<a href="https://www.rukn-eltatawer.com/water-leak-detection-uae/">{old_text}</a>'
        new = f'<a href="https://www.rukn-eltatawer.com/{new_path}">{new_text}</a>'
        c = html.count(old)
        if c:
            html = html.replace(old, new)
            n += c
    if CLUSTER_OLD in html:
        html = html.replace(CLUSTER_OLD, CLUSTER_NEW)
        n += 1
    for old_text, href, new_text in CLUSTER_ITEMS:
        bare = f"<strong>{old_text}</strong>"
        if bare in html:
            html = html.replace(bare, f'<strong><a href="{href}">{new_text}</a></strong>')
            n += 1
    if END_ABU_OLD in html:
        html = html.replace(END_ABU_OLD, END_ABU_NEW)
        n += 1
    return html, n


def set_phone_metas(post_id: int, spec: dict) -> None:
    local = spec["phone_local"]
    e164 = spec["phone_e164"]
    sql_set_meta(
        post_id,
        "post__call_section__data",
        {
            "call_section_title": "تواصل معنا الآن",
            "call_section_content": spec["excerpt"],
            "call_section_phone": e164,
            "call_section_whatsapp": e164,
        },
    )
    for key, val in {
        "memo-meta-phone": local,
        "phone": local,
        "phone_number": local,
        "whatsapp": local,
        "whatsapp_number": local,
    }.items():
        sql_set_meta(post_id, key, val)


def set_rank_math(post_id: int, spec: dict) -> None:
    api_post(
        "rankmath/v1/updateMeta",
        {
            "objectType": "post",
            "objectID": post_id,
            "meta": {
                "rank_math_title": spec["seo_title"],
                "rank_math_description": spec["excerpt"],
                "rank_math_focus_keyword": spec["focus"],
            },
        },
    )


def publish_one(spec: dict) -> dict:
    existing = find_post_by_slug(spec["slug"])
    body = spec["body"]()
    payload = {
        "title": spec["title"],
        "slug": spec["slug"],
        "status": "publish",
        "content": body,
        "excerpt": spec["excerpt"],
        "featured_media": spec["featured_media"],
    }
    if existing:
        print("update existing", spec["slug"], existing["id"])
        out = api_post(f"wp/v2/posts/{existing['id']}", payload)
    else:
        print("create", spec["slug"])
        out = api_post("wp/v2/posts", payload)
    pid = int(out["id"])
    try:
        set_phone_metas(pid, spec)
    except Exception as e:
        print("phone meta warn", spec["slug"], e)
    try:
        set_rank_math(pid, spec)
    except Exception as e:
        print("rankmath warn", spec["slug"], e)
    print("published", pid, out.get("link"))
    return out


def update_alain_post() -> int:
    post = api_get(f"wp/v2/posts/{ALAIN_POST_ID}", {"context": "edit", "_fields": "id,content"})
    raw = (post.get("content") or {}).get("raw") or (post.get("content") or {}).get("rendered") or ""
    new, n = rewrite_alain(raw)
    leftover = new.count("water-leak-detection-uae")
    print("alain replacements", n, "leftover uae slug", leftover)
    if n == 0:
        print("nothing to replace in Al Ain post raw content")
        return 1
    api_post(f"wp/v2/posts/{ALAIN_POST_ID}", {"content": new, "id": ALAIN_POST_ID})
    print("updated post", ALAIN_POST_ID)
    return 0 if leftover == 0 else 2


def main() -> int:
    if not has_auth():
        print("WP_USER / WP_APP_PASS missing — planned publishes:")
        for spec in ARTICLES:
            print(f"  {spec['slug']}  {spec['title']}  {spec['phone_e164']}")
        print("then rewrite post 405 related links to those slugs + existing AD leak / Al Ain roof")
        return 2
    created = []
    for spec in ARTICLES:
        created.append(publish_one(spec))
        time.sleep(0.4)
    code = update_alain_post()
    purge_caches()
    print("done", [c.get("link") for c in created])
    return code


if __name__ == "__main__":
    raise SystemExit(main())
