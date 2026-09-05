#!/usr/bin/env python3
"""Find in-content internal links that 404 or bounce to the homepage."""

from __future__ import annotations

import json
import re
import time
import urllib.error
import urllib.parse
import urllib.request
from collections import defaultdict
from concurrent.futures import ThreadPoolExecutor, as_completed
from html.parser import HTMLParser
from pathlib import Path

BASE = "https://www.rukn-eltatawer.com"
UA = "Mozilla/5.0 CursorAgentLinkAudit"
OUT = Path(__file__).resolve().parent / "internal-link-audit.json"
MD = Path(__file__).resolve().parent / "internal-link-audit.md"

HREF_RE = re.compile(r"""href\s*=\s*['"]([^'"]+)['"]""", re.I)
SKIP_PREFIX = (
    "tel:",
    "mailto:",
    "javascript:",
    "sms:",
    "whatsapp:",
    "#",
)
SKIP_HOST = (
    "wa.me",
    "api.whatsapp.com",
    "web.whatsapp.com",
    "facebook.com",
    "instagram.com",
    "twitter.com",
    "x.com",
    "youtube.com",
    "youtu.be",
    "t.me",
    "google.com",
    "maps.google",
)
HOME_PATHS = {"", "/", "/en", "/en/", "/en/english-home", "/en/english-home/"}
TAXONOMY_PREFIX = (
    "/category/",
    "/tag/",
    "/author/",
    "/wp-content/",
    "/wp-admin/",
    "/wp-json/",
    "/feed/",
    "/comments/",
)


class HrefParser(HTMLParser):
    def __init__(self) -> None:
        super().__init__()
        self.hrefs: list[str] = []

    def handle_starttag(self, tag: str, attrs: list[tuple[str, str | None]]) -> None:
        if tag != "a":
            return
        for k, v in attrs:
            if k == "href" and v:
                self.hrefs.append(v.strip())


def api(path: str, params: dict) -> tuple[dict, list]:
    qs = urllib.parse.urlencode(params, doseq=True)
    url = f"{BASE}/wp-json/{path}?{qs}"
    req = urllib.request.Request(url, headers={"User-Agent": UA})
    with urllib.request.urlopen(req, timeout=90) as r:
        headers = dict(r.headers.items())
        return headers, json.load(r)


def fetch_collection(kind: str) -> list[dict]:
    items: list[dict] = []
    page = 1
    while True:
        headers, data = api(
            f"wp/v2/{kind}",
            {
                "per_page": 100,
                "page": page,
                "status": "publish",
                "_fields": "id,slug,link,content",
            },
        )
        if not data:
            break
        items.extend(data)
        total_pages = int(headers.get("X-WP-TotalPages") or headers.get("x-wp-totalpages") or 1)
        print(f"  {kind} page {page}/{total_pages} (+{len(data)})")
        if page >= total_pages:
            break
        page += 1
        time.sleep(0.05)
    return items


def unique_used(rows: list[dict]) -> list[dict]:
    seen: set[tuple] = set()
    out: list[dict] = []
    for src in rows:
        key = (src["id"], src.get("href_raw", ""))
        if key in seen:
            continue
        seen.add(key)
        out.append(src)
    return out


def extract_hrefs(html: str) -> list[str]:
    try:
        p = HrefParser()
        p.feed(html or "")
        if p.hrefs:
            return p.hrefs
    except Exception:
        pass
    return HREF_RE.findall(html or "")


def norm_path(url: str) -> str:
    parts = urllib.parse.urlsplit(url)
    path = parts.path or "/"
    if path != "/" and path.endswith("/"):
        path = path[:-1]
    return path.lower()


def is_home_path(path: str) -> bool:
    p = path.lower()
    if p.endswith("/"):
        p = p[:-1] or "/"
    return p in {"", "/", "/en", "/en/english-home"} or p in HOME_PATHS


def classify_href(raw: str) -> str | None:
    if not raw or raw.startswith(SKIP_PREFIX):
        return None
    if raw.startswith("//"):
        raw = "https:" + raw
    parsed = urllib.parse.urlsplit(raw)
    host = (parsed.hostname or "").lower()
    if any(h in host for h in SKIP_HOST):
        return None
    if host and "rukn-eltatawer.com" not in host:
        return None
    if not host:
        if parsed.scheme and parsed.scheme not in ("http", "https"):
            return None
        path = parsed.path or raw
        if path.startswith("#"):
            return None
        if not path.startswith("/"):
            path = "/" + path
        return urllib.parse.urljoin(BASE + "/", path.lstrip("/"))
    path = parsed.path or "/"
    return urllib.parse.urlunsplit(("https", "www.rukn-eltatawer.com", path, "", ""))


def is_article_like(url: str) -> bool:
    path = urllib.parse.urlsplit(url).path or "/"
    if is_home_path(path):
        return False
    low = path.lower()
    return not any(low.startswith(p) or f"{p.rstrip('/')}" == low for p in TAXONOMY_PREFIX)


class NoRedirect(urllib.request.HTTPRedirectHandler):
    def redirect_request(self, req, fp, code, msg, headers, newurl):
        return None


OPENER = urllib.request.build_opener(NoRedirect)


def encode_iri(url: str) -> str:
    parts = urllib.parse.urlsplit(url)
    path = urllib.parse.quote(urllib.parse.unquote(parts.path), safe="/-%._~")
    return urllib.parse.urlunsplit((parts.scheme, parts.netloc, path, parts.query, parts.fragment))


def path_slug(url: str) -> str:
    path = urllib.parse.urlsplit(url).path.strip("/")
    return path.split("/")[-1] if path else ""


def probe(url: str) -> dict:
    url = encode_iri(url)
    req = urllib.request.Request(
        url,
        headers={"User-Agent": UA, "Accept": "text/html"},
        method="GET",
    )
    try:
        with OPENER.open(req, timeout=25) as r:
            final = r.geturl()
            return {
                "status": r.status,
                "location": "",
                "final": final,
                "home": is_home_path(urllib.parse.urlsplit(final).path),
            }
    except urllib.error.HTTPError as e:
        loc = e.headers.get("Location") or ""
        if loc.startswith("/"):
            loc = urllib.parse.urljoin(BASE, loc)
        home = False
        if loc:
            home = is_home_path(urllib.parse.urlsplit(loc).path)
        elif e.code == 200:
            home = is_home_path(urllib.parse.urlsplit(url).path)
        return {
            "status": e.code,
            "location": loc,
            "final": loc or url,
            "home": home,
        }
    except Exception as e:
        return {"status": 0, "location": "", "final": url, "home": False, "error": str(e)[:160]}


def main() -> None:
    print("== fetch published posts/pages ==")
    posts = fetch_collection("posts")
    pages = fetch_collection("pages")
    published = posts + pages
    exist_paths = {norm_path(item["link"]) for item in published}
    exist_paths.update({"/", "/en", "/en/english-home"})
    exist_by_slug = {}
    for item in published:
        exist_by_slug[item["slug"]] = item["link"]

    sources: dict[str, list[dict]] = defaultdict(list)
    for item in published:
        html = (item.get("content") or {}).get("rendered") or ""
        for raw in extract_hrefs(html):
            absu = classify_href(raw)
            if not absu:
                continue
            if not is_article_like(absu):
                continue
            sources[absu].append(
                {
                    "id": item["id"],
                    "slug": item["slug"],
                    "link": item["link"],
                    "href_raw": raw,
                }
            )

    targets = sorted(sources)
    print("unique article-like internal targets", len(targets))

    results = {}
    with ThreadPoolExecutor(max_workers=12) as pool:
        futs = {pool.submit(probe, u): u for u in targets}
        done = 0
        for fut in as_completed(futs):
            u = futs[fut]
            results[u] = fut.result()
            done += 1
            if done % 50 == 0:
                print(f"  probed {done}/{len(targets)}")

    to_home = []
    missing = []
    legacy_ok = []
    for url, probe_r in results.items():
        path = norm_path(url)
        exists = path in exist_paths
        slug = path_slug(url)
        slug_exists = urllib.parse.unquote(slug) in exist_by_slug or slug in exist_by_slug
        status = probe_r.get("status") or 0
        loc = probe_r.get("location") or ""
        loc_path = norm_path(loc) if loc else ""
        bounced = bool(probe_r.get("home")) or loc_path in {"/", "/en"}
        dest_slug = path_slug(loc) if loc else ""
        dest_published = bool(loc) and (
            loc_path in exist_paths or dest_slug in exist_by_slug
        )

        row = {
            "url": url,
            "status": status,
            "redirects_to": loc,
            "published": exists or slug_exists,
            "used_on": unique_used(sources[url]),
        }
        if bounced:
            to_home.append(row)
        elif dest_published and status in (301, 302, 303, 307, 308):
            row["canonical"] = exist_by_slug.get(dest_slug, loc)
            legacy_ok.append(row)
        elif status == 404 or (not exists and not slug_exists and not dest_published):
            missing.append(row)

    to_home.sort(key=lambda r: r["url"])
    missing.sort(key=lambda r: r["url"])
    legacy_ok.sort(key=lambda r: r["url"])

    report = {
        "published_posts": len(posts),
        "published_pages": len(pages),
        "unique_internal_article_links": len(targets),
        "homepage_bounces": to_home,
        "missing_articles": missing,
        "legacy_arabic_slugs_ok": legacy_ok,
    }
    OUT.write_text(json.dumps(report, ensure_ascii=False, indent=2), encoding="utf-8")

    def md_section(title: str, rows: list[dict]) -> str:
        lines = [f"## {title} ({len(rows)})", ""]
        if not rows:
            lines.append("لا يوجد.")
            lines.append("")
            return "\n".join(lines)
        for i, row in enumerate(rows, 1):
            lines.append(f"### {i}. `{row['url']}`")
            lines.append(f"- الحالة: `{row['status']}`")
            if row.get("redirects_to"):
                lines.append(f"- يحوّل إلى: `{row['redirects_to']}`")
            if row.get("canonical"):
                lines.append(f"- المقال النهائي: `{row['canonical']}`")
            lines.append(f"- منشور في الموقع: {'نعم' if row['published'] else 'لا'}")
            lines.append("- يظهر داخل:")
            seen = set()
            for src in row["used_on"]:
                key = (src["id"], src["href_raw"])
                if key in seen:
                    continue
                seen.add(key)
                lines.append(
                    f"  - [{src['slug']}]({src['link']}) — الرابط المكتوب: `{src['href_raw']}`"
                )
            lines.append("")
        return "\n".join(lines)

    md = "\n".join(
        [
            "# تدقيق الروابط الداخلية في محتوى المقالات",
            "",
            f"- مقالات منشورة: {len(posts)}",
            f"- صفحات منشورة: {len(pages)}",
            f"- روابط داخلية شبيهة بمقالات (فريدة): {len(targets)}",
            f"- تحوّل للرئيسية: {len(to_home)}",
            f"- مقالات غير موجودة: {len(missing)}",
            f"- روابط عربية قديمة تحول لمقال منشور: {len(legacy_ok)}",
            "",
            md_section("روابط مكتوبة خطأ وتصل للصفحة الرئيسية", to_home),
            md_section("روابط تشير إلى مقالات غير موجودة", missing),
            md_section("روابط عربية قديمة تعمل عبر تحويل 301 (ليست معطلة)", legacy_ok),
        ]
    )
    MD.write_text(md, encoding="utf-8")
    print("wrote", OUT)
    print("wrote", MD)
    print("homepage_bounces", len(to_home), "missing", len(missing), "legacy_ok", len(legacy_ok))


if __name__ == "__main__":
    main()
