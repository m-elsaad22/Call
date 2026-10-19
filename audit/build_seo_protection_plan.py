#!/usr/bin/env python3
"""Enrich 61 canonical URLs + risk metadata (read-only)."""
from __future__ import annotations

import base64
import csv
import json
import re
import urllib.parse
import urllib.request
import xml.etree.ElementTree as ET
from collections import Counter, defaultdict
from html import unescape
from pathlib import Path

SITE = "https://www.rukn-eltatawer.com"
OUT = Path("/workspace/audit/output")
AUTH = open("/cursor/stores/self/rukn-app-password.txt").read()
d = dict(x.split(": ", 1) for x in AUTH.splitlines() if ": " in x)
BASIC = base64.b64encode(f"{d['username']}:{d['app_password']}".encode()).decode()
NS = {"sm": "http://www.sitemaps.org/schemas/sitemap/0.9"}


def load_sitemap_urls():
    idx = urllib.request.urlopen(SITE + "/sitemap_index.xml", timeout=60).read()
    root = ET.fromstring(idx)
    locs = set()
    for sm in root.findall("sm:sitemap", NS):
        loc = sm.find("sm:loc", NS)
        if loc is None or not loc.text:
            continue
        if "post-sitemap" not in loc.text:
            continue
        data = urllib.request.urlopen(loc.text.strip(), timeout=60).read()
        r2 = ET.fromstring(data)
        for u in r2.findall("sm:url", NS):
            l = u.find("sm:loc", NS)
            if l is not None and l.text:
                locs.add(l.text.rstrip("/"))
    return locs


def fetch_all_post_bodies():
    items = []
    page = 1
    while True:
        q = urllib.parse.urlencode(
            {"per_page": 100, "page": page, "status": "publish", "_fields": "id,link,content"}
        )
        req = urllib.request.Request(SITE + "/wp-json/wp/v2/posts?" + q)
        req.add_header("Authorization", "Basic " + BASIC)
        with urllib.request.urlopen(req, timeout=120) as r:
            data = json.loads(r.read())
            tp = int(r.headers.get("X-WP-TotalPages", 1))
        items.extend(data)
        if page >= tp:
            break
        page += 1
    return items


def count_inbound_links(posts, targets):
    """Count internal hrefs pointing to any of target url variants."""
    norm_targets = set()
    for t in targets:
        norm_targets.add(t.rstrip("/"))
        norm_targets.add(t.rstrip("/") + "/")
        slug = t.rstrip("/").split("/")[-1]
        norm_targets.add("/" + slug)
        norm_targets.add("/en/" + slug)
    counts = defaultdict(int)
    pat = re.compile(r'href=["\']([^"\']+)["\']', re.I)
    for p in posts:
        html = (p.get("content") or {}).get("rendered") or ""
        for href in pat.findall(html):
            h = href.split("?")[0]
            for tid, urls in targets.items():
                ar = urls["ar"].rstrip("/")
                en = urls["en"].rstrip("/")
                if h == ar or h == ar + "/" or h.endswith("/" + ar.split("/")[-1]):
                    counts[tid] += 1
                elif h == en or h == en + "/" or "/en/" + ar.split("/")[-1] in h:
                    counts[tid] += 1
    return counts


def main():
    canon_rows = [
        r
        for r in csv.DictReader(
            (OUT / "canonical_issues_verified.csv").open(encoding="utf-8-sig")
        )
        if r.get("issue_codes")
    ]
    print("61 cluster", len(canon_rows))
    sitemap = load_sitemap_urls()
    print("sitemap post urls", len(sitemap))

    targets = {}
    for r in canon_rows:
        pid = r["post_id"]
        targets[pid] = {"ar": r["ar_url_probed"], "en": r["en_url_probed"]}

    posts = fetch_all_post_bodies()
    inbound = count_inbound_links(posts, targets)

    enriched = []
    for r in canon_rows:
        pid = r["post_id"]
        ar = r["ar_url_probed"].rstrip("/")
        en = r["en_url_probed"].rstrip("/")
        in_smap_ar = ar in sitemap
        in_smap_en = en in sitemap
        enriched.append(
            {
                **r,
                "in_sitemap_ar_url": "yes" if in_smap_ar else "no",
                "in_sitemap_en_url": "yes" if in_smap_en else "no",
                "inbound_internal_links_approx": inbound.get(pid, 0),
                "gsc_clicks_28d": "N/A — no GSC export connected",
                "gsc_avg_position": "N/A",
                "gsc_top_queries": "N/A",
                "ranking_data_available": "no",
                "proposed_decision": "defer_until_gsc — classify URL pair (AR live vs EN canonical) before any change",
                "change_risk_if_canonical_fixed_blind": "high — may shift equity between /slug/ and /en/slug/; EN permalink is REST primary",
                "proposed_decision_rationale": (
                    "Dual 200 URLs; canonical already declares EN as preferred. "
                    "Changing without knowing which URL ranks is unsafe."
                ),
            }
        )

    fields = list(enriched[0].keys())
    with (OUT / "canonical_61_detailed_analysis.csv").open("w", newline="", encoding="utf-8-sig") as f:
        w = csv.DictWriter(f, fieldnames=fields)
        w.writeheader()
        w.writerows(enriched)

    protect_ids = {r["post_id"] for r in canon_rows}
    all_ids = {str(p["id"]) for p in posts}
    protect = {
        "canonical_61_post_ids": sorted(protect_ids, key=int),
        "all_published_post_count": len(all_ids),
        "protected_from_blind_canonical_bulk_change": "All 1364 posts until GSC URL-level data",
        "explicit_high_caution": "61 dual-locale posts + any URL with inbound_internal_links > 0",
    }
    (OUT / "protected_pages_manifest.json").write_text(
        json.dumps(protect, ensure_ascii=False, indent=2), encoding="utf-8"
    )
    print("inbound sample", Counter(inbound.values()).most_common(5))


if __name__ == "__main__":
    main()
