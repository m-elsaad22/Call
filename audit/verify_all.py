#!/usr/bin/env python3
"""Evidence-based verification outputs (read-only)."""
from __future__ import annotations

import base64
import csv
import json
import os
import re
import urllib.error
import urllib.parse
import urllib.request
from concurrent.futures import ThreadPoolExecutor, as_completed
from difflib import SequenceMatcher
from html import unescape
from pathlib import Path

SITE = "https://www.rukn-eltatawer.com"
BASE = SITE + "/wp-json"
OUT = Path("/workspace/audit/output")
AUTH_FILE = Path("/cursor/stores/self/rukn-app-password.txt")

CANON1 = re.compile(r'<link[^>]+rel=["\']canonical["\'][^>]+href=["\']([^"\']+)["\']', re.I)
CANON2 = re.compile(r'<link[^>]+href=["\']([^"\']+)["\'][^>]+rel=["\']canonical["\']', re.I)
ROBOTS = re.compile(r'<meta[^>]+name=["\']robots["\'][^>]+content=["\']([^"\']*)["\']', re.I)
STRIP = re.compile(r"<[^>]+>")


def load_auth():
    t = AUTH_FILE.read_text()
    d = dict(x.split(": ", 1) for x in t.splitlines() if ": " in x)
    return f"{d['username']}:{d['app_password']}"


AUTH = os.environ.get("WP_APP_AUTH") or load_auth()


def api_get(path, params=None):
    url = BASE + path + ("?" + urllib.parse.urlencode(params) if params else "")
    req = urllib.request.Request(url)
    req.add_header("Authorization", "Basic " + base64.b64encode(AUTH.encode()).decode())
    with urllib.request.urlopen(req, timeout=120) as r:
        return json.loads(r.read()), r.headers


def fetch_html(url, max_bytes=250000):
    req = urllib.request.Request(url, headers={"User-Agent": "RuknVerify/1.0"})
    with urllib.request.urlopen(req, timeout=45) as r:
        return r.status, r.read(max_bytes).decode("utf-8", errors="ignore")


def head_status(url):
    req = urllib.request.Request(url, method="HEAD", headers={"User-Agent": "RuknVerify/1.0"})
    try:
        with urllib.request.urlopen(req, timeout=30) as r:
            return r.status
    except urllib.error.HTTPError as e:
        return e.code


def parse_seo(html):
    canon = CANON1.search(html) or CANON2.search(html)
    robots = ROBOTS.search(html)
    h1m = re.findall(r"<h1[^>]*>(.*?)</h1>", html, re.I | re.S)
    h1 = unescape(STRIP.sub("", h1m[0])).strip()[:120] if h1m else ""
    hreflang = re.findall(
        r'<link[^>]+hreflang=["\']([^"\']+)["\'][^>]+href=["\']([^"\']+)["\']',
        html,
        re.I,
    )
    sample = html[:25000]
    ar = len(re.findall(r"[\u0600-\u06FF]", sample))
    en = len(re.findall(r"[A-Za-z]", sample))
    lang = "ar" if ar > en * 0.05 and ar > 80 else ("en" if en > 200 else "mixed/unclear")
    return {
        "canonical": canon.group(1) if canon else "",
        "robots_meta": robots.group(1) if robots else "",
        "h1": h1,
        "content_lang_heuristic": lang,
        "hreflang_count": len(hreflang),
    }


def get_all_posts():
    items = []
    page = 1
    tp = 1
    while page <= tp:
        data, hdrs = api_get(
            "/wp/v2/posts",
            {
                "per_page": 100,
                "page": page,
                "status": "publish",
                "_fields": "id,link,slug,title,content,lang",
            },
        )
        tp = int(hdrs.get("X-WP-TotalPages", 1))
        items.extend(data)
        page += 1
    return items


def verify_canonical_posts(posts):
    rows = []
    ar_posts = [p for p in posts if "/en/" not in p.get("link", "")]

    def work(p):
        ar_url = p["link"]
        st, html = fetch_html(ar_url)
        seo = parse_seo(html)
        canon = seo["canonical"]
        en_url = SITE + "/en/" + p.get("slug", "") + "/"
        issue = ""
        expected = ar_url
        expected_why = "Self-referencing canonical for Arabic URL serving Arabic content"
        if seo["content_lang_heuristic"] == "en":
            expected = en_url
            expected_why = "URL without /en/ serves English body — either redirect to /en/ or restore Arabic content; canonical should match the language actually served"
        if canon.rstrip("/") == ar_url.rstrip("/"):
            canon_type = "self"
        elif "/en/" in canon and canon.rstrip("/") != ar_url.rstrip("/"):
            canon_type = "points_to_en"
            issue = "canonical_en_mismatch"
        elif not canon:
            canon_type = "missing"
            issue = "missing_canonical"
        else:
            canon_type = "other"
            issue = "other_canonical"
        if seo["content_lang_heuristic"] == "en" and canon_type == "points_to_en":
            issue = "confirmed_en_served_on_ar_url_and_canonical_en"
        elif seo["content_lang_heuristic"] == "ar" and canon_type == "points_to_en":
            issue = "ar_content_but_canonical_en"
        en_http = head_status(canon if "/en/" in canon else en_url) if canon or en_url else ""
        pll = "Polylang lang field: " + str(p.get("lang", ""))
        return {
            "post_id": p["id"],
            "ar_url": ar_url,
            "canonical_current": canon,
            "en_counterpart_url": en_url,
            "ar_http_status": st,
            "en_http_status": en_http,
            "content_language_observed": seo["content_lang_heuristic"],
            "h1_observed": seo["h1"],
            "robots_meta": seo["robots_meta"],
            "indexing_signal": "noindex" if "noindex" in seo["robots_meta"].lower() else "indexable",
            "hreflang_tags_count": seo["hreflang_count"],
            "polylang_translation_evidence": pll,
            "canonical_source_code": "kayan_seo_canonical_url()+print_canonical; filters rank_math/frontend/canonical; kayan_i18n path/lang",
            "issue_code": issue,
            "expected_canonical": expected,
            "expected_reason": expected_why,
            "confidence": "عالية" if issue else "—",
            "recommendation": "verify_before_fix" if issue else "ok",
        }

    with ThreadPoolExecutor(max_workers=8) as ex:
        futs = {ex.submit(work, p): p for p in ar_posts}
        done = 0
        for fut in as_completed(futs):
            rows.append(fut.result())
            done += 1
            if done % 100 == 0:
                print(f"canonical {done}/{len(ar_posts)}")

    issue_rows = [r for r in rows if r["issue_code"]]
    summary = {
        "ar_posts": len(ar_posts),
        "with_issue": len(issue_rows),
        "en_on_ar_url": sum(1 for r in rows if r["content_language_observed"] == "en"),
        "canonical_points_en": sum(1 for r in rows if "points_to_en" in r.get("issue_code", "") or r["canonical_current"].find("/en/") > 0 and r["ar_url"].find("/en/") < 0),
    }
    # recount canonical_points_en properly
    summary["canonical_points_en"] = sum(
        1
        for r in rows
        if r["canonical_current"]
        and "/en/" in r["canonical_current"]
        and r["ar_url"].rstrip("/") != r["canonical_current"].rstrip("/")
    )
    summary["both_en_content_and_en_canon"] = sum(
        1 for r in rows if r["issue_code"] == "confirmed_en_served_on_ar_url_and_canonical_en"
    )
    summary["ar_content_wrong_canon"] = sum(
        1 for r in rows if r["issue_code"] == "ar_content_but_canonical_en"
    )
    return rows, issue_rows, summary


def verify_off_topic(posts_by_id):
    off = list(csv.DictReader((OUT / "off_topic_refined.csv").open(encoding="utf-8-sig")))
    false_ids = set()
    for r in csv.DictReader((OUT / "article_audit_refined.csv").open(encoding="utf-8-sig")):
        if r.get("confidence") == "منخفض" and (r.get("compatibility") or "").startswith("غير"):
            false_ids.add(int(r["post_id"]))

    rows = []
    extra_ids = {9287, 12716, 10300, 10299}
    ids = {int(x["id"]) for x in off} | false_ids | extra_ids

    for pid in sorted(ids):
        p = posts_by_id.get(pid)
        if not p:
            rows.append({"post_id": pid, "note": "not found in API"})
            continue
        title = unescape(STRIP.sub("", (p.get("title") or {}).get("rendered", "")))
        plain = unescape(STRIP.sub("", (p.get("content") or {}).get("rendered", "")))
        url = p["link"]
        reason = ""
        category = "يحتاج إلى مراجعة بشرية"
        if pid in false_ids:
            category = "مرتبط بالنشاط ويجب الاحتفاظ به"
            reason = "إPositive false: off-topic trigger from boilerplate (e.g. Saudi mention), content is UAE home services"
        elif pid == 9287:
            category = "غير مرتبط بالنشاط فعلًا"
            reason = "Title/body target Makkah (Saudi) — company focuses on UAE"
        elif pid == 12716:
            category = "يحتاج إلى مراجعة بشرية"
            reason = "Mobile car wash — adjacent to cleaning; not core CPT list but may be intentional service"
        elif pid in (10300, 10299):
            category = "مرتبط بالنشاط ويحتاج تحسينًا"
            reason = "Fire alarm/firefighting slugs — likely security/safety services; verify business offering"
        elif "/en/" in url:
            st, html = fetch_html(url)
            seo = parse_seo(html)
            category = "مرتبط بالنشاط ويجب الاحتفاظ به"
            reason = f"English locale page ({seo['content_lang_heuristic']}) — not off-topic; needs hreflang/canonical policy not deletion"
        else:
            # read off csv reason
            o = next((x for x in off if int(x["id"]) == pid), None)
            if o:
                reason = o.get("reason", "")
            if "غسيل" in title or "car" in url:
                category = "يحتاج إلى مراجعة بشرية"
            elif re.search(r"مكة|makkah", title + plain, re.I):
                category = "غير مرتبط بالنشاط فعلًا"
            elif len(plain) < 300:
                category = "مرتبط بالنشاط ويحتاج تحسينًا"
                reason = (reason + "; thin content").strip("; ")
            else:
                category = "مرتبط بالنشاط ويجب الاحتفاظ به"
                reason = reason or "UAE service article"

        rows.append(
            {
                "post_id": pid,
                "title": title,
                "url": url,
                "verified_category": category,
                "specific_reason": reason,
                "evidence": f"REST content read; {len(plain.split())} words approx",
                "confidence": "عالية" if pid in (9287,) else ("متوسطة" if category.startswith("يحتاج") else "عالية"),
                "recommendation": "no deletion in verification phase",
            }
        )
    return rows


def verify_duplicates(posts_by_id):
    clusters = list(csv.DictReader((OUT / "duplicate_template_clusters.csv").open(encoding="utf-8-sig")))
    hash_groups = {}
    for r in csv.DictReader((OUT / "article_audit_full.csv").open(encoding="utf-8-sig")):
        g = r.get("duplicate_group")
        if g:
            hash_groups.setdefault(g, []).append(int(r["post_id"]))

    rows = []

    def body(pid):
        p = posts_by_id.get(pid)
        if not p:
            return ""
        return unescape(STRIP.sub("", (p.get("content") or {}).get("rendered", "")))

    for g, pids in hash_groups.items():
        texts = {pid: body(pid) for pid in pids}
        pid_list = list(pids)
        ratios = []
        if len(pid_list) >= 2:
            ratios.append(SequenceMatcher(None, texts[pid_list[0]], texts[pid_list[1]]).ratio())
        rows.append(
            {
                "group_id": g,
                "group_type": "exact_content_hash",
                "page_count": len(pids),
                "post_ids": "|".join(map(str, pids)),
                "urls": "|".join(posts_by_id[i]["link"] for i in pids if i in posts_by_id),
                "similarity_ratio": round(ratios[0], 3) if ratios else 1.0,
                "similarity_method": "SequenceMatcher full body",
                "service_city_variance": "see URLs",
                "internal_competition": "yes — near duplicate",
                "merge_evidence_sufficient": "yes" if ratios and ratios[0] > 0.95 else "review",
                "recommendation": "merge or 301 to single URL after picking keeper — human approval",
                "confidence": "عالية",
            }
        )

    for c in clusters:
        ids = [int(x) for x in c["post_ids"].split("|") if x]
        texts = [body(i) for i in ids[:3] if body(i)]
        ratio = 0.0
        if len(texts) >= 2:
            ratio = SequenceMatcher(None, texts[0], texts[1]).ratio()
        gtype = "template_skeleton" if ratio < 0.85 else "high_paragraph_overlap"
        if ratio > 0.95:
            gtype = "near_literal_duplicate"
        rows.append(
            {
                "group_id": c["cluster_id"],
                "group_type": gtype,
                "page_count": c["count"],
                "post_ids": c["post_ids"],
                "urls": c["sample_urls"],
                "similarity_ratio": round(ratio, 3),
                "similarity_method": "SequenceMatcher first two members",
                "service_city_variance": "different slugs — check city/service in URL",
                "internal_competition": "possible — same intent template" if int(c["count"]) > 6 else "low",
                "merge_evidence_sufficient": "no — city pages need local value check" if ratio < 0.9 else "maybe",
                "recommendation": "differentiate locally or consolidate only if same intent+duplicate body",
                "confidence": "متوسطة",
            }
        )
    return rows


def verify_plugins_and_gtm():
    rows = []
    st, html = fetch_html(SITE + "/")
    gtm_ids = re.findall(r"GTM-[A-Z0-9]+", html)
    gtm_js = len(re.findall(r"googletagmanager\.com/gtm\.js", html))
    gtag = len(re.findall(r"googletagmanager\.com/gtag", html))
    rows.append(
        {
            "component": "GTM homepage",
            "evidence": f"GTM IDs: {','.join(sorted(set(gtm_ids)))}; gtm.js loads={gtm_js}; gtag loads={gtag}",
            "risk": "متوسطة — duplicate container possible" if gtm_js > 1 or len(set(gtm_ids)) > 1 else "منخفضة",
            "active": "yes",
            "theme_dependency": "unknown — check google-tag-manager + duracelltomi plugins",
            "safe_to_disable": "needs test — do not disable both without checking Site Kit",
            "confidence": "عالية",
            "recommendation": "audit which plugin injects which ID; keep one path",
        }
    )
    rows.append(
        {
            "component": "kayan-dbg",
            "evidence": "Listed active in WPVibe site-info plugins[]; cannot read plugin file (path_traversal blocked)",
            "risk": "عالية — debug on production (inferred from name + prior TEMP Debug Fatal)",
            "active": "yes (plugin list)",
            "theme_dependency": "not found in theme PHP grep — likely standalone",
            "safe_to_disable": "likely yes AFTER staging test and error log review",
            "confidence": "متوسطة",
            "recommendation": "confirm via wp-admin plugin description; disable only after approval",
        }
    )
    rows.append(
        {
            "component": "download-plugin",
            "evidence": "download-plugin/download-plugin.php active",
            "risk": "عالية — known admin attack surface",
            "active": "yes",
            "theme_dependency": "no",
            "safe_to_disable": "yes if not used for backups",
            "confidence": "عالية",
            "recommendation": "remove when no longer needed",
        }
    )
    return rows


def verify_seo_misc():
    checks = [
        ("https://www.rukn-eltatawer.com/services/", "archive_title"),
        ("https://www.rukn-eltatawer.com/category/villa-cleaning/", "category_meta"),
        ("https://www.rukn-eltatawer.com/service/", "sitemap_dup"),
    ]
    rows = []
    for url, kind in checks:
        st, html = fetch_html(url)
        seo = parse_seo(html)
        title = re.search(r"<title[^>]*>([^<]+)</title>", html, re.I)
        title = unescape(title.group(1).strip()) if title else ""
        desc = re.search(r'<meta[^>]+name=["\']description["\'][^>]+content=["\']([^"\']*)', html, re.I)
        note = ""
        status = "confirmed"
        if kind == "archive_title" and "Archive" in title:
            note = "Title contains Archive"
        if kind == "category_meta" and (not desc or not desc.group(1).strip()):
            note = "Empty meta description"
        if kind == "sitemap_dup":
            note = f"HTTP {st}"
        rows.append(
            {
                "check": kind,
                "url": url,
                "http_status": st,
                "title": title[:120],
                "meta_description_empty": not bool(desc and desc.group(1).strip()) if desc else True,
                "finding": note,
                "status": status if note else "ok",
                "confidence": "عالية",
            }
        )
    return rows


def build_final_action_plan(canonical_summary, plugin_rows):
    actions = []
    if canonical_summary.get("canonical_points_en"):
        actions.append(
            {
                "priority": "حرجة",
                "item": "Canonical/language mismatch on non-/en/ URLs",
                "evidence": json.dumps(canonical_summary, ensure_ascii=False),
                "risk_if_fixed_wrong": "Wrong locale indexed; GCC paths affected",
                "test_plan": "Sample 30 AR URLs + Rich Results + GSC URL inspection",
                "needs_approval": "نعم",
            }
        )
    return actions


def main():
    OUT.mkdir(parents=True, exist_ok=True)
    print("load posts...")
    posts = get_all_posts()
    posts_by_id = {p["id"]: p for p in posts}
    print("canonical verify...")
    all_c, issue_c, c_sum = verify_canonical_posts(posts)
    with (OUT / "canonical_issues_verified.csv").open("w", newline="", encoding="utf-8-sig") as f:
        w = csv.DictWriter(f, fieldnames=list(all_c[0].keys()))
        w.writeheader()
        w.writerows(all_c)
    (OUT / "canonical_verification_summary.json").write_text(
        json.dumps(c_sum, ensure_ascii=False, indent=2), encoding="utf-8"
    )

    print("off topic verify...")
    off_rows = verify_off_topic(posts_by_id)
    with (OUT / "off_topic_verified.csv").open("w", newline="", encoding="utf-8-sig") as f:
        w = csv.DictWriter(f, fieldnames=list(off_rows[0].keys()))
        w.writeheader()
        w.writerows(off_rows)

    print("duplicates verify...")
    dup_rows = verify_duplicates(posts_by_id)
    with (OUT / "duplicate_groups_verified.csv").open("w", newline="", encoding="utf-8-sig") as f:
        w = csv.DictWriter(f, fieldnames=list(dup_rows[0].keys()))
        w.writeheader()
        w.writerows(dup_rows)

    print("plugins...")
    plug = verify_plugins_and_gtm()
    with (OUT / "plugin_risk_review.csv").open("w", newline="", encoding="utf-8-sig") as f:
        w = csv.DictWriter(f, fieldnames=list(plug[0].keys()))
        w.writeheader()
        w.writerows(plug)

    seo_misc = verify_seo_misc()
    actions = build_final_action_plan(c_sum, plug)
    # merge with prior action plan as draft
    final = []
    for a in actions:
        final.append({**a, "phase": "verification_confirmed"})
    with (OUT / "final_action_plan.csv").open("w", newline="", encoding="utf-8-sig") as f:
        w = csv.DictWriter(
            f,
            fieldnames=["priority", "item", "evidence", "risk_if_fixed_wrong", "test_plan", "needs_approval", "phase"],
        )
        w.writeheader()
        w.writerows(final)

    print("done", c_sum)


if __name__ == "__main__":
    main()
