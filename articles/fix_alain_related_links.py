#!/usr/bin/env python3
"""Fix broken related-article links in the Al Ain water-leak post (ID 405).

Six «اقرأ أيضاً» lines and the end cluster all pointed at
/water-leak-detection-uae/ (302 → homepage). Those six titles were never
published. This script rewrites each one to a real published article.
"""

from __future__ import annotations

import os
import sys

# Local import of WP helpers copied onto this branch when present.
sys.path.insert(0, os.path.dirname(__file__))
try:
    from rukn_rewrite_pipeline import api_get, api_post, cli
except Exception:
    api_get = api_post = cli = None  # type: ignore

POST_ID = 405

# Unique (old_href_fragment, old_anchor) → (new_path, new_anchor)
IQRA = [
    (
        "water-leak-detection-uae/",
        "شركة كشف تسربات المياه في أبوظبي 2026 – الدليل الشامل",
        "a-water-leak-detection-company-in-abu-dhabi/",
        "شركة كشف تسربات المياه في أبوظبي",
    ),
    (
        "water-leak-detection-uae/",
        "أسباب ارتفاع فاتورة المياه في الإمارات – دليل التشخيص الذاتي",
        "water-bill-problem-solving-company-in-abu-dhabi/",
        "شركة حل مشكلة إرتفاع فاتورة المياه في أبوظبي",
    ),
    (
        "water-leak-detection-uae/",
        "دليل أنواع أنابيب المياه في الإمارات وعمرها الافتراضي 2026",
        "5-signs-of-hidden-water-leaks/",
        "5 علامات لتسربات المياه الخفية وكيفية علاجها بدون تكسير بالإمارات",
    ),
    (
        "water-leak-detection-uae/",
        "الفرق بين كشف التسربات بالأشعة تحت الحمراء والكشف الجيوفوني 2026",
        "detecting-underground-water-leaks/",
        "كشف تسربات المياه تحت الأرض دون حفر",
    ),
    (
        "water-leak-detection-uae/",
        "كيف تختار أفضل شركة كشف تسربات موثوقة في الإمارات – 7 معايير ذهبية",
        "information-about-leak-detection-companies/",
        "كل ما تحتاج لمعرفته عن شركات كشف تسربات المياه في الإمارات",
    ),
    (
        "water-leak-detection-uae/",
        "أسعار كشف تسربات المياه في الإمارات 2026 – مقارنة شاملة",
        "obtain-a-water-leak-detection-service/",
        "كيفية الحصول على خدمة كشف تسربات المياه بجودة عالية في الإمارات",
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
<li><strong><a href="https://www.rukn-eltatawer.com/swimming-pools-company-in-al-ain/">شركة إنشاء وصيانة مسابح العين</a></strong> — إنشاء وصيانة المسابح في العين</li>
<li><strong><a href="https://www.rukn-eltatawer.com/tank-insulation-al-ain/">شركة عزل خزانات في العين</a></strong> — حماية خزانك من التسربات والتلوث</li>
<li><strong><a href="https://www.rukn-eltatawer.com/obtain-a-water-leak-detection-service/">كيفية الحصول على خدمة كشف تسربات المياه بجودة عالية في الإمارات</a></strong> — خطوات اختيار خدمة كشف موثوقة</li>
</ol>"""

END_ABU_OLD = '<a href="https://www.rukn-eltatawer.com/water-leak-detection-uae/">شركة كشف تسربات المياه في أبوظبي 2026</a>'
END_ABU_NEW = '<a href="https://www.rukn-eltatawer.com/a-water-leak-detection-company-in-abu-dhabi/">شركة كشف تسربات المياه في أبوظبي</a>'


def rewrite(html: str) -> tuple[str, int, int]:
    n = 0
    for old_frag, old_text, new_path, new_text in IQRA:
        old = f'<a href="https://www.rukn-eltatawer.com/{old_frag}">{old_text}</a>'
        new = f'<a href="https://www.rukn-eltatawer.com/{new_path}">{new_text}</a>'
        c = html.count(old)
        if c:
            html = html.replace(old, new)
            n += c
    if CLUSTER_OLD in html:
        html = html.replace(CLUSTER_OLD, CLUSTER_NEW)
        n += 1
    if END_ABU_OLD in html:
        html = html.replace(END_ABU_OLD, END_ABU_NEW)
        n += 1
    leftover = html.count("water-leak-detection-uae")
    return html, n, leftover


def main() -> int:
    if not os.environ.get("WP_USER") or not os.environ.get("WP_APP_PASS"):
        print("WP_USER / WP_APP_PASS missing — printing planned replacements only.")
        for _, old_t, path, new_t in IQRA:
            print(f"  «{old_t}»")
            print(f"    → https://www.rukn-eltatawer.com/{path}  ({new_t})")
        print("cluster + end أبوظبي link also rewritten")
        return 2
    if api_get is None:
        print("rukn_rewrite_pipeline.py not importable")
        return 1
    post = api_get(f"wp/v2/posts/{POST_ID}", {"context": "edit", "_fields": "id,content"})
    raw = (post.get("content") or {}).get("raw") or (post.get("content") or {}).get("rendered") or ""
    new, n, leftover = rewrite(raw)
    print("replacements", n, "leftover uae slug", leftover)
    if n == 0:
        print("nothing to replace in raw content")
        return 1
    api_post(f"wp/v2/posts/{POST_ID}", {"content": new, "id": POST_ID})
    try:
        cli("litespeed-purge all", write=True)
        cli("cache flush", write=True)
    except Exception as e:
        print("cache purge skipped", e)
    print("updated post", POST_ID)
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
