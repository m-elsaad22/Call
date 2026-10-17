#!/usr/bin/env python3
import base64
import csv
import json
import re
import urllib.parse
import urllib.request
from html import unescape
from pathlib import Path

SITE = "https://www.rukn-eltatawer.com"
OUT = Path("/workspace/audit/output")
AUTH = open("/cursor/stores/self/rukn-app-password.txt").read()
d = dict(x.split(": ", 1) for x in AUTH.splitlines() if ": " in x)
BASIC = base64.b64encode(f"{d['username']}:{d['app_password']}".encode()).decode()
STRIP = re.compile(r"<[^>]+>")


def get_post(pid):
    req = urllib.request.Request(f"{SITE}/wp-json/wp/v2/posts/{pid}?_fields=id,link,slug,title,content")
    req.add_header("Authorization", "Basic " + BASIC)
    with urllib.request.urlopen(req, timeout=60) as r:
        return json.loads(r.read())


def classify(pid, title, plain, url, orig_reason):
    t = title + " " + plain[:800]
    if pid == 9287 or re.search(r"مكة|makkah", t, re.I):
        return "غير مرتبط بالنشاط فعلًا", "استهداف جغرافي (مكة) خارج نطاق خدمات الإمارات المعتمدة", "عالية"
    if "mobile-car-wash" in url or "غسيل سيارات" in title:
        return "يحتاج إلى مراجعة بشرية", "خدمة غسيل سيارات — قريبة من التنظيف لكن غير مدرجة في CPT الخدمات الـ12", "متوسطة"
    if re.search(r"fire|fayr|alarm|فاير|إنذار|حريق", url + title, re.I):
        return "مرتبط بالنشاط ويحتاج تحسينًا", "موضوع أنظمة إنذار/سلامة — تحقق من offering الفعلي؛ slug transliterated", "متوسطة"
    if "/en/" in url:
        return "مرتبط بالنشاط ويجب الاحتفاظ به", "صفحة إنجليزية ضمن استراتيجية /en — ليست off-topic؛ تحتاج سياسة hreflang/canonical", "عالية"
    if re.search(r"السعودية|مكة|الرياض|جدة", plain) and not re.search(r"مكة", title, re.I):
        return "مرتبط بالنشاط ويجب الاحتفاظ به", "ذكر دول/مدن في boilerplate فقط — false positive سابق", "عالية"
    if len(plain.split()) < 250:
        return "مرتبط بالنشاط ويحتاج تحسينًا", "محتوى رقيق", "متوسطة"
    return "مرتبط بالنشاط ويجب الاحتفاظ به", orig_reason or "خدمة منزلية/صيانة في الإمارات", "عالية"


def main():
    off = list(csv.DictReader((OUT / "off_topic_refined.csv").open(encoding="utf-8-sig")))
    false_ids = []
    for r in csv.DictReader((OUT / "article_audit_refined.csv").open(encoding="utf-8-sig")):
        if r.get("confidence") == "منخفض":
            false_ids.append(int(r["post_id"]))
    ids = []
    seen = set()
    for row in off:
        i = int(row["id"])
        if i not in seen:
            ids.append((i, row.get("reason", "")))
            seen.add(i)
    for i in false_ids:
        if i not in seen:
            ids.append((i, "auto false positive Saudi/boilerplate"))
            seen.add(i)

    rows = []
    for pid, orig in ids:
        p = get_post(pid)
        title = unescape(STRIP.sub("", (p.get("title") or {}).get("rendered", "")))
        plain = unescape(STRIP.sub("", (p.get("content") or {}).get("rendered", "")))
        cat, reason, conf = classify(pid, title, plain, p["link"], orig)
        rows.append(
            {
                "post_id": pid,
                "title": title,
                "url": p["link"],
                "original_audit_flag": orig,
                "verified_category": cat,
                "specific_reason": reason,
                "word_count_approx": len(plain.split()),
                "evidence": "Full post content via REST",
                "confidence": conf,
                "recommendation": "no delete/noindex in verification phase",
            }
        )
    with (OUT / "off_topic_verified.csv").open("w", newline="", encoding="utf-8-sig") as f:
        w = csv.DictWriter(f, fieldnames=list(rows[0].keys()))
        w.writeheader()
        w.writerows(rows)
    from collections import Counter

    print(Counter(r["verified_category"] for r in rows))


if __name__ == "__main__":
    main()
