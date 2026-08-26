#!/usr/bin/env python3
"""Set +971522901095 / 0522901095 on cleaning + pest/bird posts in Abu Dhabi and Al Ain."""

from __future__ import annotations

import json
import re
import time
from html import unescape
from pathlib import Path

from rukn_rewrite_pipeline import api_get, api_post, cli

NEW_PHONE = "+971522901095"
NEW_LOCAL = "0522901095"
NEW_WA = "971522901095"

SEO_TITLE_CLEAN = f"%title% 🧹 {NEW_LOCAL} 🧴 إختيارك الصحيح"
SEO_TITLE_PEST = f"%title% 🪲 {NEW_LOCAL} 🪳 إختيارك الصحيح"

CONTACT_BLOCK = (
    '<div class="rukn-service-phone" style="background:#0A1F4E;color:#fff;'
    'padding:14px 18px;border-radius:10px;margin:16px 0;text-align:center;">'
    '<p style="margin:0;font-size:18px;"><strong>اطلب الخدمة الآن</strong>'
    " — اتصل أو واتساب "
    f'<a href="tel:{NEW_PHONE}" style="color:#fff;font-weight:700;">{NEW_LOCAL}</a>'
    " | "
    f'<a href="https://wa.me/{NEW_WA}" style="color:#fff;font-weight:700;" '
    'target="_blank" rel="noopener">'
    f"{NEW_PHONE}</a></p></div>\n"
)

CITIES = ["أبوظبي", "ابوظبي", "العين"]
CITY_SLUG = ["abu-dhabi", "abudhabi", "al-ain", "alain"]

CLEAN_TITLE = [
    "تنظيف",
    "تعقيم",
    "جلي",
    "تلميع الرخام",
    "عاملات تنظيف",
    "عاملاته",
    "خادمات",
]
PEST_TITLE = [
    "مكافحة",
    "وزغ",
    "برص",
    "فئران",
    "قوارض",
    "طيور",
    "أفاعي",
    "ثعابين",
    "عقارب",
    "صراصير",
    "بق الفراش",
    "رمة",
    "النمل",
    "نمل",
    "براغيث",
    "بعوض",
    "ذباب",
    "حشرات",
    "طارد للحمام",
    "طارد حمام",
    "مسامير طاردة",
    "شبك طارد",
    "أجهزة صوتية لطرد",
]
CLEAN_SLUG = [
    "cleaning",
    "steriliz",
    "disinfect",
    "marble-polish",
    "hourly-cleaning",
    "maid",
    "tank-cleaning",
    "sofa",
    "villa-clean",
    "majlis",
    "moquette",
    "curtain-cleaning",
    "mattress-cleaning",
    "palace-cleaning",
    "office-cleaning",
    "school-nursery-cleaning",
    "hospital-clinic-cleaning",
    "mall-cleaning",
    "bathroom-cleaning",
    "kitchen-cleaning",
    "home-cleaning",
    "glass-facade-cleaning",
    "stone-facade-cleaning",
    "garage-cleaning",
    "garden-cleaning",
    "deep-cleaning",
    "duct-cleaning",
    "chimney-cleaning",
    "air-conditioner-cleaning",
    "swimming-pool-cleaning",
]
PEST_SLUG = [
    "pest",
    "insect",
    "termite",
    "rodent",
    "snake",
    "scorpion",
    "cockroach",
    "bed-bug",
    "lizard",
    "flea",
    "ant-control",
    "bird",
    "pigeon",
    "mosquito",
    "flying-pest",
    "crawling-pest",
    "white-ant",
    "ozone",
]
EXCLUDE_TITLE = [
    "مكافحة الحريق",
    "فاير",
    "أنظمة حريق",
    "كشف تسربات",
    "تركيب سجاد",
    "صيانة مكانس",
]


def strip_title(p: dict) -> str:
    t = p["title"]["rendered"] if isinstance(p.get("title"), dict) else (p.get("title") or "")
    return unescape(re.sub(r"<[^>]+>", "", t)).strip()


def in_city(title: str, slug: str) -> bool:
    if any(c in title for c in CITIES):
        return True
    sl = (slug or "").lower()
    return any(c in sl for c in CITY_SLUG)


def is_clean(title: str, slug: str) -> bool:
    if any(x in title for x in EXCLUDE_TITLE):
        return False
    if any(k in title for k in CLEAN_TITLE):
        return True
    sl = (slug or "").lower()
    if "%" in sl:
        return False
    return any(k in sl for k in CLEAN_SLUG)


def is_pest(title: str, slug: str) -> bool:
    if any(x in title for x in EXCLUDE_TITLE) or "حريق" in title or "فاير" in title:
        return False
    if any(k in title for k in PEST_TITLE):
        return True
    sl = (slug or "").lower()
    if "%" in sl:
        return False
    return any(k in sl for k in PEST_SLUG)


def classify(title: str, slug: str) -> str | None:
    if not in_city(title, slug):
        return None
    pest = is_pest(title, slug)
    clean = is_clean(title, slug)
    if pest and not clean:
        return "pest"
    if clean:
        return "clean"
    return None


def fetch_all_posts() -> list[dict]:
    posts: list[dict] = []
    page = 1
    while True:
        batch = api_get(
            f"wp/v2/posts?per_page=100&page={page}&status=publish&_fields=id,title,link,slug"
        )
        if not batch:
            break
        posts.extend(batch)
        if len(batch) < 100:
            break
        page += 1
    return posts


def get_meta(post_id: int, key: str):
    r = cli(f"post meta get {post_id} {key}")
    out = (r.get("stdout") or "").strip()
    if not out or out.lower() in ("null", "false"):
        return None
    if out.startswith("{") or out.startswith("["):
        try:
            return json.loads(out)
        except json.JSONDecodeError:
            return out
    return out


def cli_set_meta(post_id: int, key: str, value: str) -> None:
    safe = value.replace("'", "'\\''")
    r = cli(f"post meta update {post_id} {key} '{safe}' --force", write=True)
    if r.get("exit_code") not in (0, None):
        raise RuntimeError(f"meta update {key} failed: {r}")


def make_seo_description(title: str) -> str:
    clean = re.sub(r"\s+", " ", title).strip()
    desc = (
        f"{clean} من ركن التطور. خدمة معتمدة وضمان مكتوب في أبوظبي والعين. "
        f"اتصل أو واتساب {NEW_LOCAL}."
    )
    if len(desc) > 160:
        desc = f"{clean}. خدمة معتمدة وضمان مكتوب من ركن التطور. اتصل {NEW_LOCAL}."
    if len(desc) > 160:
        desc = f"ركن التطور — {clean}. اتصل أو واتساب {NEW_LOCAL}."
    if len(desc) > 160:
        desc = f"{clean}. اتصل {NEW_LOCAL}."
    return desc[:160]


def replace_phones_in_text(text: str) -> tuple[str, int]:
    if not text:
        return text, 0
    count = 0

    def skip_or_replace(raw_digits: str, replacement: str, original: str) -> str:
        nonlocal count
        if "522901095" in re.sub(r"\D", "", raw_digits):
            return original
        count += 1
        return replacement

    def repl_plus(m: re.Match) -> str:
        return skip_or_replace(m.group(0), NEW_PHONE, m.group(0))

    def repl_local(m: re.Match) -> str:
        return skip_or_replace(m.group(0), NEW_LOCAL, m.group(0))

    def repl_wa(m: re.Match) -> str:
        return skip_or_replace(m.group(0), NEW_WA, m.group(0))

    def repl_eg(m: re.Match) -> str:
        return skip_or_replace(m.group(0), NEW_LOCAL, m.group(0))

    text2 = text.replace("{PHONE_UAE}", NEW_PHONE).replace("{PHONE}", NEW_PHONE)
    if "{PHONE_UAE}" in text or "{PHONE}" in text:
        count += text.count("{PHONE_UAE}") + text.count("{PHONE}")

    text2 = re.sub(r"https://wa\.me/\+?9715\d{8}", f"https://wa.me/{NEW_WA}", text2)
    text2 = re.sub(r"https://wa\.me/20\d+", f"https://wa.me/{NEW_WA}", text2)
    text2 = re.sub(r"\+971[\s\-]?0?5\d{8}", repl_plus, text2)
    text2 = re.sub(r"(?<!\d)9715\d{8}(?!\d)", repl_wa, text2)
    text2 = re.sub(r"(?<!\d)05\d{8}(?!\d)", repl_local, text2)
    text2 = re.sub(r"(?<!\d)015\d{8,11}(?!\d)", repl_eg, text2)
    text2 = re.sub(r"الإعلان للإيجار|إعلان للإيجار|للإيجار 📢|📢 الإعلان للإيجار", "", text2)
    return text2, count


def ensure_contact_block(html: str) -> tuple[str, bool]:
    if "rukn-service-phone" in html and NEW_LOCAL in html:
        return html, False
    html2, _ = replace_phones_in_text(html or "")
    if "rukn-service-phone" in html2:
        # Refresh number inside existing box via replace_phones; still prepend if local missing
        if NEW_LOCAL in html2:
            return html2, html2 != (html or "")
    inserted = True
    html2 = CONTACT_BLOCK + (html2.lstrip() if html2 else "")
    return html2, inserted


def update_content(post_id: int, raw: str) -> tuple[int, bool]:
    html, n = replace_phones_in_text(raw or "")
    html2, inserted = ensure_contact_block(html)
    if html2 == (raw or ""):
        return n, False
    api_post(f"wp/v2/posts/{post_id}", {"content": html2})
    return n + (1 if inserted else 0), inserted


def update_call_section(post_id: int) -> dict:
    data = {
        "call_section_title": "تواصل معنا الآن",
        "call_section_content": f"اتصل أو واتساب {NEW_LOCAL} لطلب الخدمة.",
        "call_section_phone": NEW_PHONE,
        "call_section_whatsapp": NEW_PHONE,
    }
    payload = json.dumps(data, ensure_ascii=False).replace("'", "'\\''")
    r = cli(
        f"post meta update {post_id} post__call_section__data '{payload}' --format=json --force",
        write=True,
    )
    if r.get("exit_code") not in (0, None):
        raise RuntimeError(f"call section failed: {r}")
    return data


def main() -> None:
    print("Fetching posts…", flush=True)
    posts = fetch_all_posts()
    targets = []
    for p in posts:
        title = strip_title(p)
        slug = p.get("slug") or ""
        kind = classify(title, slug)
        if kind:
            targets.append(
                {
                    "id": int(p["id"]),
                    "title": title,
                    "link": p.get("link"),
                    "slug": slug,
                    "kind": kind,
                }
            )

    print(f"Matched cleaning/pest Abu Dhabi+Al Ain posts: {len(targets)}", flush=True)
    results = []
    for i, item in enumerate(targets, 1):
        pid = item["id"]
        seo_title = SEO_TITLE_PEST if item["kind"] == "pest" else SEO_TITLE_CLEAN
        print(f"\n=== [{i}/{len(targets)}] {item['kind']} {item['title']} ({pid}) ===", flush=True)
        try:
            seo_desc = make_seo_description(item["title"])
            cli_set_meta(pid, "rank_math_title", seo_title)
            cli_set_meta(pid, "rank_math_description", seo_desc)
            cli_set_meta(pid, "phone", NEW_PHONE)
            cli_set_meta(pid, "whatsapp_number", NEW_PHONE)
            cli_set_meta(pid, "memo-meta-phone", NEW_PHONE)
            update_call_section(pid)

            raw = ""
            try:
                full = api_get(f"wp/v2/posts/{pid}?context=edit&_fields=content")
                raw = (full.get("content") or {}).get("raw") or ""
            except Exception as e:
                print("  raw content warn:", e, flush=True)
            replaced, inserted = update_content(pid, raw)

            verify_title = get_meta(pid, "rank_math_title") or ""
            ok = NEW_LOCAL in str(verify_title)
            row = {
                "id": pid,
                "title": item["title"],
                "link": item["link"],
                "slug": item["slug"],
                "kind": item["kind"],
                "ok": ok,
                "seo_title_after": verify_title,
                "seo_desc_after": seo_desc,
                "call_phone": NEW_PHONE,
                "content_replacements": replaced,
                "contact_block_inserted": inserted,
            }
            results.append(row)
            print("OK" if ok else "WARN", {"seo_title": verify_title, "inserted": inserted}, flush=True)
        except Exception as e:
            print("FAIL", pid, e, flush=True)
            results.append({"id": pid, "title": item["title"], "link": item.get("link"), "error": str(e)})
        time.sleep(0.08)

    out = Path("/workspace/articles/cleaning-pest-abudhabi-alain-phone-0522901095.json")
    out.write_text(json.dumps(results, ensure_ascii=False, indent=2), encoding="utf-8")
    ok_n = sum(1 for r in results if r.get("ok"))
    fail_n = sum(1 for r in results if r.get("error"))
    print(f"\nWrote {out}")
    print(f"ok={ok_n} fail={fail_n} total={len(results)}")

    try:
        r = cli("litespeed-purge all", write=True)
        print("cache purge:", r.get("exit_code"), (r.get("stdout") or "")[:200], flush=True)
    except Exception as e:
        print("cache purge skip:", e, flush=True)


if __name__ == "__main__":
    main()
