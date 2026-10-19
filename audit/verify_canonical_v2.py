#!/usr/bin/env python3
"""Canonical verification v2: probe /{slug}/ and /en/{slug}/ for every post."""
from __future__ import annotations

import base64
import csv
import json
import re
import urllib.parse
import urllib.request
from concurrent.futures import ThreadPoolExecutor, as_completed
from html import unescape
from pathlib import Path

SITE = "https://www.rukn-eltatawer.com"
OUT = Path("/workspace/audit/output")
AUTH = open("/cursor/stores/self/rukn-app-password.txt").read()
d = dict(x.split(": ", 1) for x in AUTH.splitlines() if ": " in x)
BASIC = base64.b64encode(f"{d['username']}:{d['app_password']}".encode()).decode()

CANON = re.compile(
    r'<link[^>]+rel=["\']canonical["\'][^>]+href=["\']([^"\']+)["\']|'
    r'<link[^>]+href=["\']([^"\']+)["\'][^>]+rel=["\']canonical["\']',
    re.I,
)
H1 = re.compile(r"<h1[^>]*>(.*?)</h1>", re.I | re.S)
STRIP = re.compile(r"<[^>]+>")


def api_posts():
    out = []
    page = 1
    while True:
        q = urllib.parse.urlencode(
            {"per_page": 100, "page": page, "status": "publish", "_fields": "id,slug,link,lang"}
        )
        req = urllib.request.Request(SITE + "/wp-json/wp/v2/posts?" + q)
        req.add_header("Authorization", "Basic " + BASIC)
        with urllib.request.urlopen(req, timeout=120) as r:
            data = json.loads(r.read())
            tp = int(r.headers.get("X-WP-TotalPages", 1))
        out.extend(data)
        if page >= tp:
            break
        page += 1
    return out


def fetch(url):
    req = urllib.request.Request(url, headers={"User-Agent": "RuknVerify/1.0"})
    with urllib.request.urlopen(req, timeout=45) as r:
        return r.status, r.read(400000).decode("utf-8", errors="ignore")


def head(url):
    req = urllib.request.Request(url, method="HEAD", headers={"User-Agent": "RuknVerify/1.0"})
    try:
        with urllib.request.urlopen(req, timeout=30) as r:
            return r.status
    except Exception as e:
        return getattr(e, "code", "err")


def analyze(html):
    m = CANON.search(html)
    canon = (m.group(1) or m.group(2)) if m else ""
    h1m = H1.search(html)
    h1 = unescape(STRIP.sub("", h1m.group(1))).strip()[:140] if h1m else ""
    ar = len(re.findall(r"[\u0600-\u06FF]", h1))
    en = len(re.findall(r"[A-Za-z]", h1))
    if ar > 10 and ar >= en:
        lang = "ar"
    elif en > 10:
        lang = "en"
    else:
        lang = "mixed"
    robots = re.search(r'<meta[^>]+name=["\']robots["\'][^>]+content=["\']([^"\']*)', html, re.I)
    hreflang = len(re.findall(r"hreflang=", html, re.I))
    return canon, h1, lang, robots.group(1) if robots else "", hreflang


def work(post):
    slug = post["slug"]
    ar_url = f"{SITE}/{slug}/"
    en_url = f"{SITE}/en/{slug}/"
    rest = post["link"]
    row = {
        "post_id": post["id"],
        "slug": slug,
        "rest_permalink": rest,
        "ar_url_probed": ar_url,
        "en_url_probed": en_url,
    }
    try:
        st_ar, html_ar = fetch(ar_url)
        row["ar_http"] = st_ar
        if st_ar == 200:
            c, h1, lang, robots, hl = analyze(html_ar)
            row.update(
                {
                    "ar_canonical": c,
                    "ar_h1": h1,
                    "ar_h1_lang": lang,
                    "ar_robots": robots,
                    "ar_hreflang_count": hl,
                }
            )
        else:
            row["ar_canonical"] = ""
    except Exception as e:
        row["ar_http"] = f"err:{e}"

    en_st = head(en_url)
    row["en_http_head"] = en_st
    if en_st == 200:
        try:
            st, html = fetch(en_url)
            c, h1, lang, robots, hl = analyze(html)
            row.update(
                {
                    "en_canonical": c,
                    "en_h1": h1,
                    "en_h1_lang": lang,
                    "en_robots": robots,
                }
            )
        except Exception:
            pass

    # Issue detection on AR probe
    issue = []
    if row.get("ar_http") == 200:
        ac = row.get("ar_canonical", "")
        if ac and "/en/" in ac and ac.rstrip("/") != ar_url.rstrip("/"):
            issue.append("ar_url_canonical_points_en")
        if row.get("ar_h1_lang") == "en":
            issue.append("ar_url_serves_en_h1")
        if rest.find("/en/") >= 0 and row.get("ar_http") == 200:
            issue.append("dual_url_rest_is_en_but_ar_url_live")
    row["issue_codes"] = "|".join(issue)
    row["expected_canonical_ar"] = ar_url if row.get("ar_h1_lang") == "ar" else en_url
    row["expected_reason"] = (
        "Arabic H1 → self canonical on AR URL"
        if row.get("ar_h1_lang") == "ar"
        else "English H1 on AR URL → canonical should be EN URL or content moved to AR"
    )
    row["polylang_rest_lang"] = post.get("lang") or ""
    row["translation_pair_evidence"] = (
        "REST permalink EN + AR URL 200" if "/en/" in rest and row.get("ar_http") == 200 else "single primary link"
    )
    row["canonical_source"] = "KAYAN SEO kayan_seo_canonical_url (helpers.php) + Rank Math filter; i18n path detection (kayan_i18n_detect_lang_from_path)"
    row["confidence"] = "عالية" if issue else "—"
    row["recommendation"] = "fix in approval phase" if issue else "ok"
    return row


def main():
    posts = api_posts()
    rows = []
    with ThreadPoolExecutor(max_workers=10) as ex:
        futs = [ex.submit(work, p) for p in posts]
        for i, fut in enumerate(as_completed(futs), 1):
            rows.append(fut.result())
            if i % 100 == 0:
                print(i, len(posts))
    rows.sort(key=lambda r: r["post_id"])
    fields = list(rows[0].keys())
    with (OUT / "canonical_issues_verified.csv").open("w", newline="", encoding="utf-8-sig") as f:
        w = csv.DictWriter(f, fieldnames=fields)
        w.writeheader()
        w.writerows(rows)
    summary = {
        "posts": len(rows),
        "ar_url_canonical_points_en": sum(1 for r in rows if "ar_url_canonical_points_en" in r.get("issue_codes", "")),
        "ar_url_serves_en_h1": sum(1 for r in rows if "ar_url_serves_en_h1" in r.get("issue_codes", "")),
        "dual_url_rest_en_ar_live": sum(1 for r in rows if "dual_url" in r.get("issue_codes", "")),
        "both_issues": sum(
            1
            for r in rows
            if "ar_url_canonical_points_en" in r.get("issue_codes", "")
            and "ar_url_serves_en_h1" in r.get("issue_codes", "")
        ),
    }
    (OUT / "canonical_verification_summary.json").write_text(
        json.dumps(summary, ensure_ascii=False, indent=2), encoding="utf-8"
    )
    print(json.dumps(summary, ensure_ascii=False, indent=2))


if __name__ == "__main__":
    main()
