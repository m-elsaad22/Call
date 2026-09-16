#!/usr/bin/env python3
"""Revert pest phone (+971506603374) and SEO title (0506603374) changes.

Restores Rank Math titles from seo-title-pest-phone.json "before" values,
and restores call/WhatsApp metas to the best-known prior numbers.
"""

from __future__ import annotations

import json
import re
import time
import urllib.request
from copy import deepcopy
from pathlib import Path

from rukn_rewrite_pipeline import WP, api_get, auth_header, cli, sql_set_meta
from update_pest_phones_all import get_meta, update_content
from update_pest_seo_titles import get_title, set_title as _set_title


def set_title(post_id: int, title: str) -> None:
    """Allow empty Rank Math titles (wp-cli rejects empty value args)."""
    if title is None:
        title = ""
    if title == "":
        sql_set_meta(post_id, "rank_math_title", "")
        return
    _set_title(post_id, title)

# Prior numbers (state immediately before +971506603374 rollout)
AD_AIN_PHONE = "+971561285605"  # from prior pest AD/Ain update
V2_PHONE = "+971586634710"  # rewrite pipeline default
CITY_DEFAULT = "+971547577159"  # sampled on non-AD city pest pages before change

HUB_PHONES = {
    170: "+971568060309",
    263: "+971528541360",
    3274: "+971522881997",
    3276: "+971522881997",
}

SEO_OVERRIDES = {
    # tested before full SEO batch, so "before" in JSON is already the new title
    251: "%title% 🪲 0543331609 🪳 إختيارك الصحيح",
    6693: "%title% | خدمة معتمدة في عجمان",
}


def to_local(intl: str) -> str:
    d = re.sub(r"\D", "", intl)
    if d.startswith("971") and len(d) >= 12:
        return "0" + d[3:]
    if d.startswith("5") and len(d) == 9:
        return "0" + d
    return intl


def to_intl(phone: str) -> str:
    d = re.sub(r"\D", "", phone)
    if d.startswith("05") and len(d) == 10:
        return "+971" + d[1:]
    if d.startswith("971"):
        return "+" + d
    if d.startswith("5") and len(d) == 9:
        return "+971" + d
    if phone.startswith("+"):
        return phone
    return "+" + d if d else phone


def load_ad_ain_ids() -> set[int]:
    p = Path("/tmp/ad-ain-pest-ids.json")
    if p.exists():
        return set(json.loads(p.read_text()))
    # fallback embed from previous update file via git if needed
    return set()


def load_v2_ids() -> set[int]:
    import subprocess

    raw = subprocess.check_output(
        [
            "git",
            "show",
            "origin/cursor/service-specific-rewrites-ddfc:articles/v2-done-ids.json",
        ]
    )
    return set(int(x) for x in json.loads(raw))


def prior_phone(post_id: int, ad: set[int], v2: set[int]) -> str:
    if post_id in HUB_PHONES:
        return HUB_PHONES[post_id]
    if post_id in ad:
        return AD_AIN_PHONE
    if post_id in v2:
        return V2_PHONE
    return CITY_DEFAULT


def restore_call_metas(post_id: int, phone: str) -> None:
    intl = to_intl(phone)
    local = to_local(intl)

    data = get_meta(post_id, "post__call_section__data")
    if not isinstance(data, dict):
        data = {
            "call_section_title": "تواصل معنا الآن",
            "call_section_content": "تواصل معنا لطلب الخدمة.",
            "call_section_phone": intl,
            "call_section_whatsapp": intl,
        }
    else:
        data = deepcopy(data)
        data["call_section_phone"] = intl
        data["call_section_whatsapp"] = intl
    sql_set_meta(post_id, "post__call_section__data", data)

    for key in (
        "memo-meta-phone",
        "phone",
        "phone_number",
        "whatsapp",
        "whatsapp_number",
    ):
        sql_set_meta(post_id, key, intl)

    svc = get_meta(post_id, "YourColor_Service")
    if isinstance(svc, dict):
        svc = deepcopy(svc)
        # Prefer local format as before for many posts; AD used intl sometimes
        svc["telephone"] = intl if post_id in load_ad_ain_ids() else local
        # For AD we observed intl +971561285605 in YourColor before
        if post_id in load_ad_ain_ids():
            svc["telephone"] = intl
        sql_set_meta(post_id, "YourColor_Service", svc)


def restore_content_phones(post_id: int, phone: str) -> int:
    """Replace our new numbers in content with the restored phone."""
    try:
        full = api_get(f"wp/v2/posts/{post_id}?context=edit&_fields=content")
        raw = (full.get("content") or {}).get("raw") or ""
    except Exception:
        return 0
    if not raw:
        return 0
    intl = to_intl(phone)
    local = to_local(intl)
    wa = re.sub(r"\D", "", intl)

    new = raw
    n = 0

    def sub_count(pattern, repl, text):
        nonlocal n
        text2, c = re.subn(pattern, repl, text)
        n += c
        return text2

    new = sub_count(r"\+971[\s\-]?506603374", intl, new)
    new = sub_count(r"\+971[\s\-]?50[\s\-]?660[\s\-]?3374", intl, new)
    new = sub_count(r"(?<!\d)0506603374(?!\d)", local, new)
    new = sub_count(r"(?<!\d)971506603374(?!\d)", wa, new)

    if new == raw:
        return 0
    payload = json.dumps({"content": new}).encode("utf-8")
    req = urllib.request.Request(
        f"{WP}/wp-json/wp/v2/posts/{post_id}",
        data=payload,
        method="POST",
        headers=auth_header(),
    )
    with urllib.request.urlopen(req, timeout=180) as r:
        json.load(r)
    return n


def main() -> None:
    seo_rows = json.loads(Path("/workspace/articles/seo-title-pest-phone.json").read_text())
    phone_rows = json.loads(Path("/workspace/articles/phone-update-pest-all.json").read_text())
    ad = load_ad_ain_ids()
    v2 = load_v2_ids()

    seo_before = {int(r["id"]): r.get("before", "") for r in seo_rows}
    for pid, title in SEO_OVERRIDES.items():
        seo_before[pid] = title

    results = []
    for i, item in enumerate(phone_rows, 1):
        pid = int(item["id"])
        phone = prior_phone(pid, ad, v2)
        seo = seo_before.get(pid, "%title% 🪲 0543331609 🪳 إختيارك الصحيح")
        print(f"\n=== [{i}/{len(phone_rows)}] {item['title'][:50]} ({pid}) ===", flush=True)
        print(f"  restore phone={phone} seo={seo!r}", flush=True)
        try:
            restore_call_metas(pid, phone)
            crepl = restore_content_phones(pid, phone)
            set_title(pid, seo if seo is not None else "")
            # verify
            call = get_meta(pid, "post__call_section__data") or {}
            title_now = get_title(pid)
            ok_phone = (
                isinstance(call, dict)
                and call.get("call_section_phone") == to_intl(phone)
                and call.get("call_section_whatsapp") == to_intl(phone)
            )
            ok_seo = (title_now == seo) or (
                not seo and title_now in ("", "null", None)
            )
            # empty SEO: wp may return empty string
            if not seo:
                ok_seo = title_now in ("", '""', "null", None) or title_now == seo
            row = {
                "id": pid,
                "title": item["title"],
                "restored_phone": to_intl(phone),
                "restored_seo": seo,
                "call_phone": call.get("call_section_phone") if isinstance(call, dict) else None,
                "seo_now": title_now,
                "content_replacements": crepl,
                "ok_phone": ok_phone,
                "ok_seo": ok_seo,
            }
            results.append(row)
            print(
                "OK" if ok_phone and ok_seo else "WARN",
                {
                    "call": row["call_phone"],
                    "seo": title_now,
                    "content_replacements": crepl,
                },
                flush=True,
            )
        except Exception as e:
            print("FAIL", pid, e, flush=True)
            results.append({"id": pid, "title": item["title"], "error": str(e)})
        time.sleep(0.08)

    out = Path("/workspace/articles/phone-seo-pest-revert.json")
    out.write_text(json.dumps(results, ensure_ascii=False, indent=2), encoding="utf-8")
    ok_p = sum(1 for r in results if r.get("ok_phone"))
    ok_s = sum(1 for r in results if r.get("ok_seo"))
    fail = sum(1 for r in results if r.get("error"))
    print(f"\nWrote {out}")
    print(f"ok_phone={ok_p} ok_seo={ok_s} fail={fail} total={len(results)}")


if __name__ == "__main__":
    main()
