#!/usr/bin/env python3
"""Find in-content internal links that 404, bounce home, or are unpublished slugs."""

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
TITLE_RE = re.compile(r"<title[^>]*>(.*?)</title>", re.I | re.S)
CANON_RE = re.compile(
    r"""<link[^>]+rel=['"]canonical['"][^>]+href=['"]([^'"]+)['"]""",
    re.I,
)
CANON_RE2 = re.compile(
    r"""<link[^>]+href=['"]([^'"]+)['"][^>]+rel=['"]canonical['"]""",
    re.I,
)
OGURL_RE = re.compile(
    r"""<meta[^>]+property=['"]og:url['"][^>]+content=['"]([^'"]+)['"]""",
    re.I,
)
BODY_RE = re.compile(r"<body\b([^>]*)>", re.I)
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
HOME_PATHS = {"", "/", "/en", "/en/english-home"}
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
SUGGEST_STOP = {
    "in", "the", "and", "of", "a", "an", "to", "for", "fy", "en",
    "company", "shrkh", "uae",
}


import html as html_module


class HrefParser(HTMLParser):
    def __init__(self) -> None:
        super().__init__()
        self.hrefs: list[str] = []

    def handle_starttag(self, tag: str, attrs: list[tuple[str, str | None]]) -> None:
        if tag != "a":
            return
        for k, v in attrs:
            if k == "href" and v:
                self.hrefs.append(html_module.unescape(v.strip()))


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
                "_fields": "id,slug,link,title,content",
            },
        )
        if not data:
            break
        items.extend(data)
        total_pages = int(headers.get("X-WP-TotalPages") or headers.get("x-wp-totalpages") or 1)
        print(f"  {kind} page {page}/{total_pages} (+{len(data)})", flush=True)
        if page >= total_pages:
            break
        page += 1
        time.sleep(0.04)
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


def extract_hrefs(raw_html: str) -> list[str]:
    try:
        p = HrefParser()
        p.feed(raw_html or "")
        if p.hrefs:
            return p.hrefs
    except Exception:
        pass
    return [html_module.unescape(h) for h in HREF_RE.findall(raw_html or "")]


def norm_path(url: str) -> str:
    parts = urllib.parse.urlsplit(url)
    path = urllib.parse.unquote(parts.path or "/")
    if path != "/" and path.endswith("/"):
        path = path[:-1]
    return path.lower()


def is_home_path(path: str) -> bool:
    p = path.lower()
    if p.endswith("/"):
        p = p[:-1] or "/"
    return p in HOME_PATHS or p == ""


def classify_href(raw: str) -> str | None:
    if not raw or raw.startswith(SKIP_PREFIX):
        return None
    raw = raw.replace("\\/", "/")
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
    return not any(low.startswith(p) for p in TAXONOMY_PREFIX)


def encode_iri(url: str) -> str:
    parts = urllib.parse.urlsplit(url)
    path = urllib.parse.quote(urllib.parse.unquote(parts.path), safe="/-%._~")
    return urllib.parse.urlunsplit((parts.scheme, parts.netloc, path, parts.query, parts.fragment))


def path_slug(url: str) -> str:
    path = urllib.parse.unquote(urllib.parse.urlsplit(url).path).strip("/")
    if not path:
        return ""
    parts = [p for p in path.split("/") if p and p != "en"]
    return parts[-1] if parts else ""


def extract_meta(body: str) -> dict:
    title = ""
    m = TITLE_RE.search(body or "")
    if m:
        title = html_module.unescape(re.sub(r"\s+", " ", m.group(1))).strip()
    canon = ""
    cm = CANON_RE.search(body or "") or CANON_RE2.search(body or "")
    if cm:
        canon = cm.group(1).strip()
    og = ""
    om = OGURL_RE.search(body or "")
    if om:
        og = om.group(1).strip()
    body_cls = ""
    bm = BODY_RE.search(body or "")
    if bm:
        am = re.search(r"""class=['"]([^'"]+)['"]""", bm.group(1), re.I)
        if am:
            body_cls = am.group(1)
    return {"title": title, "canonical": canon, "og_url": og, "body_class": body_cls}


class CaptureRedirect(urllib.request.HTTPRedirectHandler):
    def __init__(self) -> None:
        self.hops: list[tuple[int, str]] = []

    def redirect_request(self, req, fp, code, msg, headers, newurl):
        self.hops.append((code, newurl))
        return super().redirect_request(req, fp, code, msg, headers, newurl)


def http_get(url: str, follow: bool) -> dict:
    url = encode_iri(url)
    if follow:
        cap = CaptureRedirect()
        opener = urllib.request.build_opener(cap)
    else:
        class NoRedirect(urllib.request.HTTPRedirectHandler):
            def redirect_request(self, req, fp, code, msg, headers, newurl):
                return None

        opener = urllib.request.build_opener(NoRedirect())
        cap = None
    req = urllib.request.Request(
        url,
        headers={"User-Agent": UA, "Accept": "text/html"},
        method="GET",
    )
    try:
        with opener.open(req, timeout=30) as r:
            raw = r.read(12000)
            try:
                text = raw.decode(r.headers.get_content_charset() or "utf-8", errors="replace")
            except Exception:
                text = raw.decode("utf-8", errors="replace")
            return {
                "status": r.status,
                "final": r.geturl(),
                "hops": list(cap.hops) if cap else [],
                "html": text,
            }
    except urllib.error.HTTPError as e:
        loc = e.headers.get("Location") or ""
        if loc.startswith("/"):
            loc = urllib.parse.urljoin(BASE, loc)
        snippet = ""
        try:
            snippet = e.read(4000).decode("utf-8", errors="replace")
        except Exception:
            pass
        hops = list(cap.hops) if cap else []
        if loc:
            hops.append((e.code, loc))
        return {
            "status": e.code,
            "final": loc or url,
            "hops": hops,
            "html": snippet,
        }
    except Exception as e:
        return {"status": 0, "final": url, "hops": [], "html": "", "error": str(e)[:180]}


HOME_FP: dict[str, dict] = {}


def load_home_fingerprints() -> None:
    for path in ("/", "/en/"):
        res = http_get(BASE + path, follow=True)
        meta = extract_meta(res.get("html") or "")
        HOME_FP[path.rstrip("/") or "/"] = {
            "title": meta["title"],
            "canonical": norm_path(meta["canonical"] or path),
            "final": norm_path(res.get("final") or path),
        }
        print("home fp", path, HOME_FP[path.rstrip("/") or "/"], flush=True)


def looks_like_homepage(final_url: str, html: str) -> bool:
    if is_home_path(urllib.parse.urlsplit(final_url).path):
        return True
    meta = extract_meta(html or "")
    if "home" in (meta["body_class"] or "").split() and "single" not in (meta["body_class"] or "").split():
        if is_home_path(urllib.parse.urlsplit(meta["canonical"] or "").path):
            return True
    for fp in HOME_FP.values():
        if meta["title"] and fp["title"] and meta["title"] == fp["title"]:
            if is_home_path(urllib.parse.urlsplit(meta["canonical"] or "").path) or not meta["canonical"]:
                return True
    if meta["canonical"] and is_home_path(urllib.parse.urlsplit(meta["canonical"]).path):
        # A real article should not canonicalize to the homepage.
        if "single" not in (meta["body_class"] or ""):
            return True
    return False


def probe(url: str) -> dict:
    followed = http_get(url, follow=True)
    final = followed.get("final") or url
    html = followed.get("html") or ""
    meta = extract_meta(html)
    hops = followed.get("hops") or []
    first_loc = hops[0][1] if hops else ""
    first_code = hops[0][0] if hops else (followed.get("status") or 0)
    home = looks_like_homepage(final, html)
    return {
        "status": first_code or followed.get("status") or 0,
        "final_status": followed.get("status") or 0,
        "location": first_loc,
        "final": final,
        "hops": [{"code": c, "url": u} for c, u in hops],
        "home": home,
        "title": (meta["title"] or "")[:160],
        "canonical": meta["canonical"],
        "error": followed.get("error") or "",
    }


def suggest(slug: str, exist_by_slug: dict[str, str], exist_paths: set[str]) -> str:
    raw = urllib.parse.unquote(slug or "").strip().lower()
    if not raw:
        return ""
    if raw in exist_by_slug:
        return exist_by_slug[raw]
    variants = {
        raw.replace("abudhabi", "abu-dhabi"),
        raw.replace("alain", "al-ain"),
        raw.replace("-rak", "-ras-al-khaimah"),
        raw.replace("rak-", "ras-al-khaimah-"),
        raw.replace("sewage-cleaning", "sewerage-plumbing"),
        raw.replace("sewer-cleaning", "sewerage-plumbing"),
        raw.replace("plumbing-services", "plumbing-maintenance"),
    }
    for v in variants:
        if v in exist_by_slug:
            return exist_by_slug[v]
        for prefix in ("", "en/"):
            p = f"/{prefix}{v}".rstrip("/")
            if p in exist_paths:
                return BASE + p + "/"
    tokens = [t for t in re.split(r"[-_]+", raw) if len(t) > 2 and t not in SUGGEST_STOP]
    if len(tokens) < 2:
        return ""
    scored: list[tuple[int, str]] = []
    for s, link in exist_by_slug.items():
        score = sum(1 for t in tokens if t in s)
        if score >= max(2, len(tokens) - 1):
            scored.append((score, link))
    scored.sort(reverse=True)
    return scored[0][1] if scored else ""


def main() -> None:
    print("== homepage fingerprints ==", flush=True)
    load_home_fingerprints()
    print("== fetch published posts/pages ==", flush=True)
    posts = fetch_collection("posts")
    pages = fetch_collection("pages")
    published = posts + pages
    exist_paths = {norm_path(item["link"]) for item in published}
    exist_by_slug: dict[str, str] = {}
    titles: dict[str, str] = {}
    for item in published:
        exist_by_slug[item["slug"]] = item["link"]
        titles[item["slug"]] = html_module.unescape((item.get("title") or {}).get("rendered") or "")

    sources: dict[str, list[dict]] = defaultdict(list)
    for item in published:
        raw_html = (item.get("content") or {}).get("rendered") or ""
        for raw in extract_hrefs(raw_html):
            absu = classify_href(raw)
            if not absu or not is_article_like(absu):
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
    print("unique article-like internal targets", len(targets), flush=True)

    results: dict[str, dict] = {}
    with ThreadPoolExecutor(max_workers=10) as pool:
        futs = {pool.submit(probe, u): u for u in targets}
        done = 0
        for fut in as_completed(futs):
            u = futs[fut]
            try:
                results[u] = fut.result()
            except Exception as e:
                results[u] = {"status": 0, "final": u, "home": False, "error": str(e)[:160]}
            done += 1
            if done % 40 == 0:
                print(f"  probed {done}/{len(targets)}", flush=True)

    to_home: list[dict] = []
    missing: list[dict] = []
    legacy_ok: list[dict] = []
    for url, probe_r in results.items():
        path = norm_path(url)
        slug = path_slug(url)
        exists = path in exist_paths or slug in exist_by_slug
        status = probe_r.get("status") or 0
        final = probe_r.get("final") or ""
        loc = probe_r.get("location") or ""
        bounced = bool(probe_r.get("home")) or is_home_path(urllib.parse.urlsplit(final).path)
        dest_slug = path_slug(final if not bounced else loc)
        dest_published = dest_slug in exist_by_slug or norm_path(final) in exist_paths
        suggested = suggest(slug, exist_by_slug, exist_paths)
        row = {
            "url": url,
            "status": status,
            "final_status": probe_r.get("final_status") or 0,
            "redirects_to": loc or final,
            "final": final,
            "published": exists,
            "suggested": suggested,
            "suggested_title": titles.get(path_slug(suggested), "") if suggested else "",
            "used_on": unique_used(sources[url]),
            "title": probe_r.get("title") or "",
            "error": probe_r.get("error") or "",
        }
        if bounced and not exists:
            if suggested:
                to_home.append(row)
            else:
                missing.append(row)
        elif bounced and exists:
            to_home.append(row)
        elif dest_published and status in (301, 302, 303, 307, 308) and not bounced:
            row["canonical"] = exist_by_slug.get(dest_slug, final)
            legacy_ok.append(row)
        elif status == 404 or (not exists and not dest_published and status not in (200, 301, 302)):
            missing.append(row)
        elif not exists and not dest_published and not bounced and status == 200:
            # 200 that is not a published post — treat as missing/unpublished.
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
            if row.get("suggested"):
                lines.append(f"- المقال الصحيح/الأقرب: [{row.get('suggested_title') or row['suggested']}]({row['suggested']})")
            if row.get("canonical"):
                lines.append(f"- المقال النهائي: `{row['canonical']}`")
            lines.append(f"- منشور في الموقع: {'نعم' if row['published'] else 'لا'}")
            lines.append("- يظهر داخل:")
            for src in row["used_on"][:12]:
                lines.append(
                    f"  - [{src['slug']}]({src['link']}) — الرابط المكتوب: `{src['href_raw']}`"
                )
            if len(row["used_on"]) > 12:
                lines.append(f"  - … و{len(row['used_on']) - 12} مقالات أخرى")
            lines.append("")
        return "\n".join(lines)

    md = "\n".join(
        [
            "# تدقيق الروابط الداخلية في محتوى المقالات",
            "",
            f"- مقالات منشورة: {len(posts)}",
            f"- صفحات منشورة: {len(pages)}",
            f"- روابط داخلية شبيهة بمقالات (فريدة): {len(targets)}",
            f"- تحوّل للرئيسية (slug خاطئ ومقال بديل موجود): {len(to_home)}",
            f"- مقالات غير موجودة (لا يوجد مقال بهذا الاسم): {len(missing)}",
            f"- روابط عربية قديمة تعمل عبر 301: {len(legacy_ok)}",
            "",
            md_section("روابط مكتوبة خطأ وتصل للصفحة الرئيسية", to_home),
            md_section("روابط تشير إلى مقالات غير موجودة", missing),
            md_section("روابط عربية قديمة تعمل عبر تحويل 301 (ليست معطلة)", legacy_ok),
        ]
    )
    MD.write_text(md, encoding="utf-8")
    print("wrote", OUT, flush=True)
    print("wrote", MD, flush=True)
    print("homepage_bounces", len(to_home), "missing", len(missing), "legacy_ok", len(legacy_ok), flush=True)


if __name__ == "__main__":
    main()
