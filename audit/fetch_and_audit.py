#!/usr/bin/env python3
"""Read-only site audit data collection for rukn-eltatawer.com"""
import base64
import csv
import hashlib
import json
import re
import ssl
import time
import urllib.error
import urllib.parse
import urllib.request
from collections import Counter, defaultdict
from html import unescape
from pathlib import Path

BASE = "https://www.rukn-eltatawer.com/wp-json"
import os

AUTH = os.environ.get("WP_APP_AUTH", "")
OUT = Path("/workspace/audit/output")
OUT.mkdir(parents=True, exist_ok=True)

UA_CITIES = {
    "dubai": "دبي",
    "abu-dhabi": "أبوظبي",
    "abudhabi": "أبوظبي",
    "sharjah": "الشارقة",
    "ajman": "عجمان",
    "fujairah": "الفجيرة",
    "ras-al-khaimah": "رأس الخيمة",
    "umm-al-quwain": "أم القيوين",
    "al-ain": "العين",
    "umm-al-quwain": "أم القيوين",
}

SERVICE_SLUG_HINTS = [
    ("water-leak", "كشف تسربات المياه"),
    ("roof-insulation", "عزل الأسطح"),
    ("tank-insulation", "عزل الخزانات"),
    ("bathroom-insulation", "عزل الحمامات"),
    ("thermal-insulation", "العزل الحراري"),
    ("waterproof", "العزل المائي"),
    ("plumb", "السباكة"),
    ("sewage", "تسليك مجاري/صرف صحي"),
    ("sewer", "تسليك مجاري"),
    ("drain", "تسليك/صرف"),
    ("ac-", "المكيفات"),
    ("split-ac", "صيانة مكيفات"),
    ("pest", "مكافحة حشرات"),
    ("termite", "مكافحة النمل"),
    ("rodent", "مكافحة القوارض"),
    ("clean", "تنظيف"),
    ("pool", "المسابح"),
    ("electric", "الكهرباء"),
    ("paint", "الدهانات"),
    ("renovation", "الترميم"),
    ("gypsum", "الجبس"),
    ("parquet", "الأرضيات"),
    ("kitchen", "المطابخ"),
    ("bathroom", "الحمامات"),
    ("garden", "تنسيق حدائق"),
    ("landscap", "تنسيق حدائق"),
    ("furniture-moving", "نقل عفش"),
    ("disinfection", "تعقيم"),
    ("duct-clean", "تنظيف مجاري"),
    ("glass", "زجاج/ألمنيوم"),
    ("bird", "طارد حمام"),
    ("snake", "مكافحة ثعابين"),
    ("flea", "مكافحة براغيث"),
    ("mosquito", "مكافحة بعوض"),
]

OFF_TOPIC_PATTERNS = [
    (r"\b(crypto|bitcoin|forex|casino|betting)\b", "مال/قمار/عملات"),
    (r"\b(diet|weight loss|supplement|viagra)\b", "صحة/دواء غير مرتبط"),
    (r"\b(university|scholarship|IELTS)\b", "تعليم عام"),
    (r"مكة|الرياض|جدة|السعودية", "استهداف جغرافي خارج الإمارات"),
    (r"makkah|riyadh|jeddah", "استهداف جغرافي خارج الإمارات"),
    (r"lorem ipsum|test post|TODO:|FIXME:", "نص تجريبي/داخلي"),
    (r"ChatGPT|مولّد|محتوى آلي", "إشارة توليد آلي"),
]

INTERNAL_BRAND_WRONG = re.compile(
    r"(?i)(شركة\s+(?!ركن|التطور)[\u0600-\u06FFa-z]{3,30}\s+(?:في|بدبي))",
)

STRIP_TAGS = re.compile(r"<[^>]+>")


def api_get(path, params=None, headers=None):
    url = BASE + path
    if params:
        url += "?" + urllib.parse.urlencode(params)
    req = urllib.request.Request(url)
    req.add_header(
        "Authorization",
        "Basic " + base64.b64encode(AUTH.encode()).decode(),
    )
    req.add_header("User-Agent", "RuknAudit/1.0 (read-only)")
    if headers:
        for k, v in headers.items():
            req.add_header(k, v)
    for attempt in range(5):
        try:
            with urllib.request.urlopen(req, timeout=120) as resp:
                total = resp.headers.get("X-WP-Total")
                pages = resp.headers.get("X-WP-TotalPages")
                body = resp.read()
                data = json.loads(body) if body else None
                return data, total, pages
        except urllib.error.HTTPError as e:
            if e.code == 429 or e.code >= 500:
                time.sleep(2 ** attempt)
                continue
            raise
    raise RuntimeError(f"Failed GET {url}")


def fetch_all(rest_base, status="publish", extra=None):
    items = []
    page = 1
    while True:
        params = {
            "per_page": 100,
            "page": page,
            "status": status,
            "_fields": "id,slug,link,title,content,excerpt,status,type,categories,tags,meta,date,modified",
        }
        if extra:
            params.update(extra)
        data, total, total_pages = api_get(f"/wp/v2/{rest_base}", params)
        if not data:
            break
        items.extend(data)
        if total_pages and page >= int(total_pages):
            break
        page += 1
        time.sleep(0.15)
    return items


def strip_html(html):
    if not html:
        return ""
    text = unescape(STRIP_TAGS.sub(" ", html or ""))
    text = re.sub(r"\s+", " ", text).strip()
    return text


def word_count_ar_en(text):
    if not text:
        return 0
    parts = re.findall(r"[\w\u0600-\u06FF]+", text)
    return len(parts)


def detect_city(slug, title, text):
    s = (slug or "") + " " + (title or "") + " " + (text or "")[:500]
    s = s.lower()
    for key, name in UA_CITIES.items():
        if key in s or name in (title or ""):
            return name
    if "category" in s:
        return ""
    return "غير محدد/عام"


def detect_service(slug, title):
    s = ((slug or "") + " " + (title or "")).lower()
    for hint, label in SERVICE_SLUG_HINTS:
        if hint in s:
            return label
    return "غير محدد — يحتاج مراجعة"


def content_fingerprint(text):
    norm = re.sub(r"\s+", " ", text.lower())[:8000]
    return hashlib.sha256(norm.encode("utf-8", errors="ignore")).hexdigest()


def classify_post(item, plain, title):
    slug = item.get("slug") or ""
    issues = []
    action = "مناسب وقوي: يُحافظ عليه"
    compat = "مناسب"
    quality = "جيد"
    intent = "جيد"
    confidence = "متوسط"

    wc = word_count_ar_en(plain)
    if wc < 250:
        quality = "ضعيف/سطحي"
        issues.append("محتوى قصير (<250 كلمة)")
    if wc > 6000:
        issues.append("محتوى طويل جداً — راجع التكرار")

    for pat, label in OFF_TOPIC_PATTERNS:
        if re.search(pat, plain, re.I) or re.search(pat, title, re.I):
            compat = "غير مرتبط أو مشبوه"
            issues.append(f"إشارة off-topic: {label}")
            action = "غير مرتبط بالنشاط: يُراجع للحذف أو الإزالة من الفهرسة"
            confidence = "عالي"

    if "makka" in slug or "makkah" in slug or "water-leak-detection-makkah" in slug:
        compat = "غير مرتبط"
        issues.append("صفحة تستهدف مكة — خارج نطاق الإمارات")
        action = "مشبوه أو مضلل: مراجعة بشرية"
        confidence = "عالي"

    kw_density = 0
    if title:
        t_words = re.findall(r"[\u0600-\u06FFa-z]+", title.lower())
        if t_words:
            hits = sum(plain.lower().count(w) for w in t_words if len(w) > 3)
            kw_density = hits / max(wc, 1)
            if kw_density > 0.08 and wc > 400:
                issues.append("تكرار كلمات العنوان بكثافة")
                quality = "متوسط"

    if re.search(r"(\b\w+\b)(\s+\1){5,}", plain[:2000], re.I):
        issues.append("تكرار لفظي محتمل")
        quality = "ضعيف/مكرر"

    if "شركة" in title and detect_service(slug, title).startswith("غير محدد"):
        if wc < 400:
            issues.append("عنوان خدمة دون محتوى خدمة واضح")
            action = "ضعيف ويحتاج إعادة كتابة"
            compat = "مناسب جزئياً"

    if not issues and quality == "جيد":
        action = "مناسب وقوي: يُحافظ عليه"
    elif "off-topic" in str(issues) or compat.startswith("غير"):
        pass
    elif quality.startswith("ضعيف") and compat == "مناسب":
        action = "مناسب ويحتاج تحسينًا"
    elif "تكرار" in str(issues):
        action = "مناسب لكنه مكرر"

    return {
        "word_count": wc,
        "compatibility": compat,
        "quality": quality,
        "search_intent": intent,
        "seo_issues": "; ".join(issues) if issues else "",
        "suggested_action": action,
        "confidence": confidence,
        "priority": "عالية" if compat.startswith("غير") or "مشبوه" in action else ("متوسطة" if issues else "منخفضة"),
    }


def main():
    print("Fetching posts...")
    posts = fetch_all("posts", status="publish")
    print(f"Fetched {len(posts)} published posts")

    print("Fetching pages...")
    pages = fetch_all("pages", status="publish")
    print(f"Fetched {len(pages)} pages")

    cpts = {}
    for rb in ("services", "faqs", "reviews", "pricing", "portfolio", "before-after"):
        try:
            cpts[rb] = fetch_all(rb, status="publish")
            print(f"CPT {rb}: {len(cpts[rb])}")
        except Exception as e:
            print(f"CPT {rb} failed: {e}")
            cpts[rb] = []

    # Taxonomies
    tax_data = {}
    for tax in ("categories", "tags", "cities", "service_categories"):
        terms, total, _ = api_get(f"/wp/v2/{tax}", {"per_page": 100})
        all_terms = list(terms or [])
        page = 2
        while total and len(all_terms) < int(total):
            t, _, tp = api_get(f"/wp/v2/{tax}", {"per_page": 100, "page": page})
            all_terms.extend(t or [])
            if tp and page >= int(tp):
                break
            page += 1
        tax_data[tax] = all_terms
        print(f"Taxonomy {tax}: {len(all_terms)}")

    rows = []
    fp_map = defaultdict(list)

    for item in posts + pages:
        for cpt_items in cpts.values():
            pass
    all_content = []
    for item in posts:
        all_content.append(("post", item))
    for item in pages:
        all_content.append(("page", item))
    for rb, items in cpts.items():
        for item in items:
            all_content.append((rb, item))

    for ctype, item in all_content:
        title = (item.get("title") or {}).get("rendered") or ""
        title = unescape(re.sub(r"<[^>]+>", "", title))
        plain = strip_html((item.get("content") or {}).get("rendered") or "")
        slug = item.get("slug") or ""
        svc = detect_service(slug, title)
        city = detect_city(slug, title, plain)
        cls = classify_post(item, plain, title)
        fp = content_fingerprint(plain)
        fp_map[fp].append(item.get("id"))

        rows.append(
            {
                "post_id": item.get("id"),
                "title": title,
                "url": item.get("link"),
                "content_type": ctype,
                "status": item.get("status"),
                "target_service": svc,
                "target_city": city,
                "focus_keyword": "",
                "actual_topic": plain[:200],
                "compatibility": cls["compatibility"],
                "content_quality": cls["quality"],
                "search_intent_fit": cls["search_intent"],
                "duplicate_group": "",
                "seo_issues": cls["seo_issues"],
                "internal_links_note": "",
                "word_count_approx": cls["word_count"],
                "suggested_action": cls["suggested_action"],
                "action_rationale": cls["seo_issues"] or "ضمن نطاق الخدمات المنزلية",
                "priority": cls["priority"],
                "confidence": cls["confidence"],
                "content_fingerprint": fp,
            }
        )

    # duplicate groups
    dup_groups = {fp: ids for fp, ids in fp_map.items() if len(ids) > 1 and fp}
    dup_id_to_group = {}
    for i, (fp, ids) in enumerate(dup_groups.items(), 1):
        gid = f"DUP-{i}"
        for pid in ids:
            dup_id_to_group[pid] = gid

    for r in rows:
        r["duplicate_group"] = dup_id_to_group.get(r["post_id"], "")

    # near-duplicate by first 500 chars hash
    short_fp = defaultdict(list)
    for r in rows:
        if r["word_count_approx"] < 100:
            continue
        item_plain = r["actual_topic"]
        sh = hashlib.md5(item_plain.encode()).hexdigest()[:12]
        short_fp[sh].append(r["post_id"])

    csv_path = OUT / "article_audit_full.csv"
    fields = list(rows[0].keys()) if rows else []
    with csv_path.open("w", newline="", encoding="utf-8-sig") as f:
        w = csv.DictWriter(f, fieldnames=fields)
        w.writeheader()
        w.writerows(rows)

    summary = {
        "posts_fetched": len(posts),
        "pages_fetched": len(pages),
        "cpts": {k: len(v) for k, v in cpts.items()},
        "taxonomies": {k: len(v) for k, v in tax_data.items()},
        "audit_rows": len(rows),
        "exact_duplicate_groups": len(dup_groups),
        "action_counts": dict(Counter(r["suggested_action"] for r in rows)),
        "compat_counts": dict(Counter(r["compatibility"] for r in rows)),
        "quality_counts": dict(Counter(r["content_quality"] for r in rows)),
        "low_word_count_under_250": sum(1 for r in rows if r["word_count_approx"] < 250),
    }
    (OUT / "inventory_summary.json").write_text(
        json.dumps(summary, ensure_ascii=False, indent=2), encoding="utf-8"
    )
    print(json.dumps(summary, ensure_ascii=False, indent=2))


if __name__ == "__main__":
    main()
