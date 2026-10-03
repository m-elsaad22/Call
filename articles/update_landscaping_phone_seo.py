#!/usr/bin/env python3
"""Set Call+WhatsApp +971566556017, show Call, and match AD/Al Ain SEO titles.

Applies only to the named Dubai / Sharjah / Ajman / RAK / Fujairah / UAQ
landscaping articles. Does not touch Abu Dhabi or Al Ain posts.
"""
from __future__ import annotations

import json
import re
import time
import urllib.error
import urllib.parse
import urllib.request
from concurrent.futures import ThreadPoolExecutor, as_completed

SITE = "https://www.rukn-eltatawer.com"
USER = "cursor"
APP_PASS = "QQEGjJcChwu4SvYYYofJcOJT"
TOKEN = __import__("base64").b64encode(f"{USER}:{APP_PASS}".encode()).decode()
HEADERS = {
    "Authorization": f"Basic {TOKEN}",
    "User-Agent": "Mozilla/5.0 CursorAgent",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

NEW_E164 = "+971566556017"
SEO_TITLE = "%title% 📞 0566556017 📢 الإعلان للإيجار"

# User URLs that 404; live hub slugs used instead.
SLUG_ALIASES = {
    "landscaping-sharjah": "sharjah-landscaping-company",
    "landscaping-ajman": "landscaping-in-ajman",
}

LISTED_SLUGS = [
    "landscaping-company-in-dubai",
    "indoor-garden-design-dubai",
    "roof-garden-design-dubai",
    "natural-grass-cutting-dubai",
    "palm-tree-planting-dubai",
    "garden-cleaning-dubai",
    "modern-irrigation-dubai",
    "automatic-irrigation-dubai",
    "artificial-grass-dubai",
    "wall-grass-dubai",
    "garden-seating-dubai",
    "pergola-installation-dubai",
    "wooden-arbor-installation-dubai",
    "stone-pathway-dubai",
    "artificial-waterfall-dubai",
    "wall-fountain-dubai",
    "landscaping-sharjah",
    "indoor-garden-design-sharjah",
    "roof-garden-design-sharjah",
    "natural-grass-cutting-sharjah",
    "palm-tree-planting-sharjah",
    "garden-cleaning-sharjah",
    "modern-irrigation-sharjah",
    "automatic-irrigation-sharjah",
    "artificial-grass-sharjah",
    "wall-grass-sharjah",
    "garden-seating-sharjah",
    "pergola-installation-sharjah",
    "wooden-arbor-installation-sharjah",
    "stone-pathway-sharjah",
    "artificial-waterfall-sharjah",
    "wall-fountain-sharjah",
    "landscaping-ajman",
    "indoor-garden-design-ajman",
    "roof-garden-design-ajman",
    "natural-grass-cutting-ajman",
    "palm-tree-planting-ajman",
    "garden-cleaning-ajman",
    "modern-irrigation-ajman",
    "automatic-irrigation-ajman",
    "artificial-grass-ajman",
    "wall-grass-ajman",
    "garden-seating-ajman",
    "pergola-installation-ajman",
    "wooden-arbor-installation-ajman",
    "stone-pathway-ajman",
    "artificial-waterfall-ajman",
    "wall-fountain-ajman",
    "landscaping-ras-al-khaimah",
    "indoor-garden-design-ras-al-khaimah",
    "roof-garden-design-ras-al-khaimah",
    "natural-grass-cutting-ras-al-khaimah",
    "palm-tree-planting-ras-al-khaimah",
    "garden-cleaning-ras-al-khaimah",
    "modern-irrigation-ras-al-khaimah",
    "automatic-irrigation-ras-al-khaimah",
    "artificial-grass-ras-al-khaimah",
    "wall-grass-ras-al-khaimah",
    "garden-seating-ras-al-khaimah",
    "pergola-installation-ras-al-khaimah",
    "wooden-arbor-installation-ras-al-khaimah",
    "stone-pathway-ras-al-khaimah",
    "artificial-waterfall-ras-al-khaimah",
    "wall-fountain-ras-al-khaimah",
    "landscaping-fujairah",
    "indoor-garden-design-fujairah",
    "roof-garden-design-fujairah",
    "natural-grass-cutting-fujairah",
    "palm-tree-planting-fujairah",
    "garden-cleaning-fujairah",
    "modern-irrigation-fujairah",
    "automatic-irrigation-fujairah",
    "artificial-grass-fujairah",
    "wall-grass-fujairah",
    "garden-seating-fujairah",
    "pergola-installation-fujairah",
    "wooden-arbor-installation-fujairah",
    "stone-pathway-fujairah",
    "artificial-waterfall-fujairah",
    "wall-fountain-fujairah",
    "landscaping-umm-al-quwain",
    "indoor-garden-design-umm-al-quwain",
    "roof-garden-design-umm-al-quwain",
    "natural-grass-cutting-umm-al-quwain",
    "palm-tree-planting-umm-al-quwain",
    "garden-cleaning-umm-al-quwain",
    "modern-irrigation-umm-al-quwain",
    "automatic-irrigation-umm-al-quwain",
    "artificial-grass-umm-al-quwain",
    "wall-grass-umm-al-quwain",
    "garden-seating-umm-al-quwain",
    "pergola-installation-umm-al-quwain",
    "wooden-arbor-installation-umm-al-quwain",
    "stone-pathway-umm-al-quwain",
    "artificial-waterfall-umm-al-quwain",
    "wall-fountain-umm-al-quwain",
]

PHONE_KEYS = (
    "rukn_call_number",
    "rukn_wa_number",
    "phone_number",
    "whatsapp_number",
    "phone",
    "whatsapp",
    "contact_number",
)


def request(url: str, data=None, timeout: int = 180):
    body = None if data is None else json.dumps(data, ensure_ascii=False).encode("utf-8")
    req = urllib.request.Request(url, data=body, headers=HEADERS, method="POST" if data else "GET")
    with urllib.request.urlopen(req, timeout=timeout) as r:
        raw = r.read().decode("utf-8")
        return json.loads(raw) if raw else {}


def cli(cmd: str, write: bool = False):
    return request(SITE + "/wp-json/wpvibe/v1/cli/run", {"command": cmd, "confirm_write": write})


def rest_posts_by_slug(slugs: list[str]) -> dict[str, dict]:
    found: dict[str, dict] = {}
    for i in range(0, len(slugs), 20):
        batch = slugs[i : i + 20]
        url = (
            SITE
            + "/wp-json/wp/v2/posts?per_page=20&_fields=id,slug,status,title,link&slug="
            + ",".join(batch)
        )
        req = urllib.request.Request(url, headers=HEADERS)
        with urllib.request.urlopen(req, timeout=60) as r:
            rows = json.loads(r.read().decode())
        for p in rows:
            found[p["slug"]] = p
        time.sleep(0.1)
    return found


def resolve_targets() -> list[dict]:
    lookup = [SLUG_ALIASES.get(s, s) for s in LISTED_SLUGS]
    found = rest_posts_by_slug(sorted(set(lookup)))
    targets = []
    for listed in LISTED_SLUGS:
        live = SLUG_ALIASES.get(listed, listed)
        post = found.get(live)
        if not post:
            targets.append({"listed_slug": listed, "live_slug": live, "id": None, "error": "not_found"})
            continue
        targets.append(
            {
                "listed_slug": listed,
                "live_slug": post["slug"],
                "id": post["id"],
                "status": post.get("status"),
                "title": post["title"]["rendered"],
                "link": post.get("link"),
            }
        )
    return targets


def meta_get(post_id: int, key: str) -> str:
    out = cli(f"post meta get {post_id} {key}")
    return (out.get("stdout") or "").strip()


def cli_quote(value: str) -> str:
    return "'" + value.replace("'", "'\\''") + "'"


def meta_update(post_id: int, key: str, value: str) -> dict:
    quoted = cli_quote(value)
    out = cli(f"post meta update {post_id} {key} {quoted}", write=True)
    if out.get("exit_code") == 0:
        return out
    add = cli(f"post meta add {post_id} {key} {quoted}", write=True)
    return add


def parse_call_section(stdout: str) -> dict:
    stdout = (stdout or "").strip()
    data = {
        "call_section_title": "",
        "call_section_content": "",
        "call_section_phone": NEW_E164,
        "call_section_whatsapp": NEW_E164,
    }
    if not stdout:
        return data
    try:
        parsed = json.loads(stdout)
        if isinstance(parsed, dict):
            data["call_section_title"] = parsed.get("call_section_title") or ""
            data["call_section_content"] = parsed.get("call_section_content") or ""
            return data
    except Exception:
        pass
    for key in ("call_section_title", "call_section_content"):
        match = re.search(rf's:\d+:"{key}";s:\d+:"(.*?)";', stdout, re.S)
        if match:
            data[key] = match.group(1)
    return data


def update_call_section(post_id: int) -> dict:
    current = parse_call_section(meta_get(post_id, "post__call_section__data"))
    payload = json.dumps(current, ensure_ascii=False)
    return cli(
        f"post meta update {post_id} post__call_section__data --format=json {cli_quote(payload)}",
        write=True,
    )


def update_seo_title(post_id: int) -> dict:
    return request(
        SITE + "/wp-json/rankmath/v1/updateMeta",
        {"objectType": "post", "objectID": post_id, "meta": {"rank_math_title": SEO_TITLE}},
    )


def update_post(target: dict) -> dict:
    post_id = target["id"]
    if not post_id:
        return {**target, "ok": False}
    before = {
        "rank_math_title": meta_get(post_id, "rank_math_title"),
        "phone_number": meta_get(post_id, "phone_number"),
        "whatsapp_number": meta_get(post_id, "whatsapp_number"),
        "rukn_call_state": meta_get(post_id, "rukn_call_state"),
        "rukn_call_number": meta_get(post_id, "rukn_call_number"),
    }
    writes = {}
    for key in PHONE_KEYS:
        writes[key] = meta_update(post_id, key, NEW_E164).get("exit_code")
    writes["rukn_call_state"] = meta_update(post_id, "rukn_call_state", "show").get("exit_code")
    writes["rukn_wa_state"] = meta_update(post_id, "rukn_wa_state", "show").get("exit_code")
    writes["rank_math_title"] = 0 if update_seo_title(post_id) else 1
    writes["post__call_section__data"] = update_call_section(post_id).get("exit_code")

    after = {
        "rank_math_title": meta_get(post_id, "rank_math_title"),
        "phone_number": meta_get(post_id, "phone_number"),
        "whatsapp_number": meta_get(post_id, "whatsapp_number"),
        "rukn_call_state": meta_get(post_id, "rukn_call_state"),
        "rukn_call_number": meta_get(post_id, "rukn_call_number"),
    }
    ok = (
        after["phone_number"] == NEW_E164
        and after["whatsapp_number"] == NEW_E164
        and after["rukn_call_state"] == "show"
        and after["rukn_call_number"] == NEW_E164
        and "0566556017" in after["rank_math_title"]
        and "📞" in after["rank_math_title"]
    )
    return {**target, "ok": ok, "before": before, "after": after, "writes": writes}


def main() -> int:
    targets = resolve_targets()
    missing = [t for t in targets if not t.get("id")]
    ready = [t for t in targets if t.get("id")]
    print(f"resolved {len(ready)} missing {len(missing)}", flush=True)
    results = []
    with ThreadPoolExecutor(max_workers=4) as pool:
        futs = {pool.submit(update_post, t): t["id"] for t in ready}
        for fut in as_completed(futs):
            row = fut.result()
            results.append(row)
            print(f"{'OK' if row['ok'] else 'FAIL'} {row['id']} {row['live_slug']}", flush=True)
    results.sort(key=lambda r: r.get("id") or 0)
    report = {
        "seo_title": SEO_TITLE,
        "phone": NEW_E164,
        "updated": sum(1 for r in results if r.get("ok")),
        "failed": [r for r in results if not r.get("ok")] + missing,
        "results": results,
        "aliases": SLUG_ALIASES,
    }
    out_path = "articles/landscaping-phone-seo-results.json"
    with open(out_path, "w", encoding="utf-8") as fh:
        json.dump(report, fh, ensure_ascii=False, indent=2)
    print(json.dumps({"updated": report["updated"], "failed": len(report["failed"])}, ensure_ascii=False))
    return 0 if not report["failed"] else 1


if __name__ == "__main__":
    raise SystemExit(main())
