#!/usr/bin/env python3
"""Fix broken related-article links in the Al Ain water-leak post (ID 405).

Existing posts get href-only updates. Missing titles are created by
publish_missing_related_articles.py then linked here.
"""

from __future__ import annotations

import base64
import json
import os
import urllib.parse
import urllib.request

WP = "https://www.rukn-eltatawer.com"


def _auth() -> dict:
    user = os.environ["WP_USER"]
    pw = os.environ["WP_APP_PASS"].replace(" ", "")
    token = base64.b64encode(f"{user}:{pw}".encode()).decode()
    return {
        "Authorization": f"Basic {token}",
        "Content-Type": "application/json",
        "User-Agent": "CursorAgent/1.0",
    }


def api_get(path: str, params: dict | None = None) -> dict:
    if params:
        path = f"{path}?{urllib.parse.urlencode(params, doseq=True)}"
    h = {k: v for k, v in _auth().items() if k != "Content-Type"}
    req = urllib.request.Request(f"{WP}/wp-json/{path.lstrip('/')}", headers=h)
    with urllib.request.urlopen(req, timeout=90) as r:
        return json.load(r)


def api_post(path: str, payload: dict) -> dict:
    req = urllib.request.Request(
        f"{WP}/wp-json/{path.lstrip('/')}",
        data=json.dumps(payload).encode(),
        headers=_auth(),
        method="POST",
    )
    with urllib.request.urlopen(req, timeout=180) as r:
        return json.load(r)


def cli(command: str, write: bool = False) -> dict:
    return api_post(
        "wpvibe/v1/cli/run",
        {"command": command, "confirm_write": write},
    )

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
        "water-pipe-types-uae/",
        "دليل أنواع أنابيب المياه في الإمارات وعمرها الافتراضي",
    ),
    (
        "water-leak-detection-uae/",
        "الفرق بين كشف التسربات بالأشعة تحت الحمراء والكشف الجيوفوني 2026",
        "infrared-vs-geophone-leak-detection/",
        "الفرق بين كشف التسربات بالأشعة تحت الحمراء والكشف الجيوفوني",
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
