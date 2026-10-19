#!/usr/bin/env python3
"""Second-pass: similarity clusters, shortcodes, links, refined flags."""
import base64
import csv
import json
import re
import urllib.parse
import urllib.request
from collections import Counter, defaultdict
from html import unescape
from pathlib import Path

import os

AUTH = os.environ.get("WP_APP_AUTH", "")
BASE = "https://www.rukn-eltatawer.com/wp-json"
OUT = Path("/workspace/audit/output")
STRIP = re.compile(r"<[^>]+>")


def api_posts_page(page):
    params = urllib.parse.urlencode(
        {
            "per_page": 100,
            "page": page,
            "status": "publish",
            "_fields": "id,link,slug,title,content",
        }
    )
    req = urllib.request.Request(BASE + "/wp/v2/posts?" + params)
    req.add_header("Authorization", "Basic " + base64.b64encode(AUTH.encode()).decode())
    with urllib.request.urlopen(req, timeout=120) as r:
        return json.loads(r.read()), int(r.headers.get("X-WP-TotalPages", 1))


def strip(html):
    return re.sub(r"\s+", " ", unescape(STRIP.sub(" ", html or ""))).strip()


def skeleton(text):
    """Remove city names and digits for template similarity."""
    t = text.lower()
    for city in [
        "dubai",
        "sharjah",
        "ajman",
        "fujairah",
        "abu dhabi",
        "abudhabi",
        "ras al khaimah",
        "umm al quwain",
        "al ain",
        "دبي",
        "الشارقة",
        "عجمان",
        "الفجيرة",
        "أبوظبي",
        "رأس الخيمة",
        "أم القيوين",
        "العين",
        "2024",
        "2025",
        "2026",
    ]:
        t = t.replace(city, "{city}")
    t = re.sub(r"\d{3,}", "{num}", t)
    return t[:4000]


def main():
    posts = []
    _, tp = api_posts_page(1)
    for p in range(1, tp + 1):
        batch, _ = api_posts_page(p)
        posts.extend(batch)

    shortcodes = Counter()
    internal_links = Counter()
    external_links = Counter()
    link_pattern = re.compile(r'href=["\']([^"\']+)["\']', re.I)
    sc_pattern = re.compile(r"\[([a-zA-Z0-9_-]+)")

    clusters = defaultdict(list)
    for p in posts:
        html = (p.get("content") or {}).get("rendered") or ""
        for m in sc_pattern.findall(html):
            shortcodes[m] += 1
        for href in link_pattern.findall(html):
            if "rukn-eltatawer.com" in href or href.startswith("/"):
                internal_links[href.split("?")[0]] += 1
            elif href.startswith("http"):
                external_links[href.split("?")[0]] += 1
        plain = strip(html)
        sk = skeleton(plain)
        key = sk[:800]
        clusters[key].append(
            {
                "id": p["id"],
                "url": p["link"],
                "slug": p["slug"],
                "title": strip((p.get("title") or {}).get("rendered")),
            }
        )

    template_clusters = [
        {
            "cluster_id": f"TPL-{i}",
            "count": len(v),
            "sample_urls": [x["url"] for x in v[:5]],
            "sample_ids": [x["id"] for x in v[:10]],
        }
        for i, (k, v) in enumerate(
            sorted(clusters.items(), key=lambda x: -len(x[1]))[:40], 1
        )
        if len(v) >= 4
    ]

    # Refine off-topic list manually with rules
    refined_off = []
    for p in posts:
        slug = p.get("slug") or ""
        title = strip((p.get("title") or {}).get("rendered") or "")
        plain = strip((p.get("content") or {}).get("rendered") or "")
        reason = None
        if "makkah" in slug or "مكة" in title:
            reason = "استهداف مكة خارج نطاق الإمارات"
        elif slug.startswith("en/") or "/en/" in p.get("link", ""):
            reason = "محتوى إنجليزي/مسار en — مراجعة تعدد اللغات و canonical"
        elif re.search(r"shrkh-fayr|fire-alarm|فاير", slug + title, re.I):
            reason = "محتوى أنظمة إنذار/فاير — تحقق من offering"
        elif re.search(r"mobile-car-wash", slug):
            reason = "غسيل سيارات متنقل — خارج قائمة الخدمات الأساسية"
        elif re.search(r"lorem|test post|xxx", plain, re.I):
            reason = "نص تجريبي"
        if reason:
            refined_off.append(
                {"id": p["id"], "url": p["link"], "title": title, "reason": reason}
            )

    report = {
        "posts_analyzed": len(posts),
        "top_shortcodes": shortcodes.most_common(30),
        "top_internal_links": internal_links.most_common(40),
        "top_external_domains": Counter(
            urllib.parse.urlparse(u).netloc for u, _ in external_links.most_common(100)
        ).most_common(20),
        "template_similarity_clusters_4plus": template_clusters,
        "refined_off_topic_or_review": refined_off,
        "refined_off_topic_count": len(refined_off),
    }
    (OUT / "deep_analysis.json").write_text(
        json.dumps(report, ensure_ascii=False, indent=2), encoding="utf-8"
    )

    # CSV duplicate overlap clusters
    with (OUT / "duplicate_template_clusters.csv").open("w", newline="", encoding="utf-8-sig") as f:
        w = csv.writer(f)
        w.writerow(["cluster_id", "count", "post_ids", "sample_urls"])
        for c in template_clusters:
            w.writerow(
                [
                    c["cluster_id"],
                    c["count"],
                    "|".join(map(str, c["sample_ids"])),
                    "|".join(c["sample_urls"]),
                ]
            )

    with (OUT / "off_topic_refined.csv").open("w", newline="", encoding="utf-8-sig") as f:
        w = csv.DictWriter(f, fieldnames=["id", "url", "title", "reason"])
        w.writeheader()
        w.writerows(refined_off)

    print(json.dumps({k: report[k] for k in report if k not in ("top_internal_links",)}, ensure_ascii=False, indent=2)[:3000])


if __name__ == "__main__":
    main()
