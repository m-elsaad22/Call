#!/usr/bin/env python3
"""Read-only technical SEO sampling and sitemap analysis."""
import json
import re
import urllib.request
import xml.etree.ElementTree as ET
from collections import Counter
from pathlib import Path

OUT = Path("/workspace/audit/output")
OUT.mkdir(parents=True, exist_ok=True)
SITE = "https://www.rukn-eltatawer.com"

NS = {"sm": "http://www.sitemaps.org/schemas/sitemap/0.9"}


def fetch(url, timeout=60):
    req = urllib.request.Request(url, headers={"User-Agent": "RuknAudit/1.0"})
    with urllib.request.urlopen(req, timeout=timeout) as r:
        return r.read(), r.status, dict(r.headers)


def parse_sitemap_index():
    urls = []
    data, _, _ = fetch(f"{SITE}/sitemap_index.xml")
    root = ET.fromstring(data)
    for sm in root.findall("sm:sitemap", NS):
        loc = sm.find("sm:loc", NS)
        if loc is not None and loc.text:
            urls.append(loc.text.strip())
    return urls


def parse_urlset(sitemap_url):
    data, _, _ = fetch(sitemap_url)
    root = ET.fromstring(data)
    locs = []
    for u in root.findall("sm:url", NS):
        loc = u.find("sm:loc", NS)
        if loc is not None and loc.text:
            locs.append(loc.text.strip())
    return locs


def check_url(url):
    req = urllib.request.Request(url, method="HEAD", headers={"User-Agent": "RuknAudit/1.0"})
    try:
        with urllib.request.urlopen(req, timeout=30) as r:
            return r.status, dict(r.headers)
    except Exception as e:
        return getattr(e, "code", None) or "ERR", {}


def sample_page_seo(url):
    from html import unescape

    html, status, _ = fetch(url)
    text = html.decode("utf-8", errors="ignore")
    def meta(name=None, prop=None):
        if name:
            m = re.search(rf'<meta[^>]+name=["\']{re.escape(name)}["\'][^>]+content=["\']([^"\']*)', text, re.I)
            if not m:
                m = re.search(rf'<meta[^>]+content=["\']([^"\']*)["\'][^>]+name=["\']{re.escape(name)}["\']', text, re.I)
            return m.group(1) if m else ""
        if prop:
            m = re.search(rf'<meta[^>]+property=["\']{re.escape(prop)}["\'][^>]+content=["\']([^"\']*)', text, re.I)
            return m.group(1) if m else ""
        return ""
    title = re.search(r"<title[^>]*>([^<]+)</title>", text, re.I)
    h1s = re.findall(r"<h1[^>]*>(.*?)</h1>", text, re.I | re.S)
    h1_clean = [re.sub(r"<[^>]+>", "", unescape(h)).strip() for h in h1s[:5]]
    canonical = re.search(r'<link[^>]+rel=["\']canonical["\'][^>]+href=["\']([^"\']*)', text, re.I)
    robots = meta(name="robots")
    return {
        "url": url,
        "http_status": status,
        "title": unescape(title.group(1).strip()) if title else "",
        "meta_description": meta(name="description"),
        "robots_meta": robots,
        "canonical": canonical.group(1) if canonical else "",
        "h1_count": len(h1s),
        "h1_samples": h1_clean[:3],
        "has_schema": "application/ld+json" in text,
        "has_og": bool(meta(prop="og:title")),
    }


def main():
    from html import unescape  # noqa

    sitemaps = parse_sitemap_index()
    all_urls = []
    for sm in sitemaps:
        try:
            all_urls.extend(parse_urlset(sm))
        except Exception as e:
            print("sitemap fail", sm, e)

    url_counts = Counter(all_urls)
    dup_sitemap = [u for u, c in url_counts.items() if c > 1]

    # Sample key templates + GSC problem URLs from Problem file
    samples = [
        f"{SITE}/",
        f"{SITE}/services/",
        f"{SITE}/pricing/",
        f"{SITE}/blog/",
        f"{SITE}/water-leak-detection-dubai/",
        f"{SITE}/flea-control-dubai/",
        f"{SITE}/water-leak-detection-makkah-center/",
        f"{SITE}/category/villa-cleaning/",
        f"{SITE}/sa/",
        f"{SITE}/eg/",
    ]
    seo_rows = []
    for u in samples:
        try:
            seo_rows.append(sample_page_seo(u))
        except Exception as e:
            seo_rows.append({"url": u, "error": str(e)})

    status_checks = []
    for u in [
        f"{SITE}/pricing/",
        f"{SITE}/services/",
        f"{SITE}/sharjah-landscaping-company/",
        f"{SITE}/furniture-assembly-sharjah/",
    ]:
        st, hdrs = check_url(u)
        status_checks.append({"url": u, "status": st, "x_robots": hdrs.get("X-Robots-Tag", "")})

    report = {
        "sitemap_index_count": len(sitemaps),
        "urls_in_sitemaps": len(all_urls),
        "unique_urls_in_sitemaps": len(url_counts),
        "duplicate_urls_in_sitemap": dup_sitemap[:20],
        "duplicate_sitemap_count": len(dup_sitemap),
        "sample_seo": seo_rows,
        "status_checks": status_checks,
    }
    (OUT / "technical_seo.json").write_text(json.dumps(report, ensure_ascii=False, indent=2), encoding="utf-8")
    print(json.dumps({k: report[k] for k in report if k != "sample_seo"}, ensure_ascii=False, indent=2))


if __name__ == "__main__":
    main()
