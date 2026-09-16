#!/usr/bin/env python3
"""Set Rank Math SEO titles on pest posts to include 0506603374."""

from __future__ import annotations

import json
import re
import time
from pathlib import Path

from rukn_rewrite_pipeline import cli
from update_pest_phones_all import fetch_all_posts, is_pest_post

SEO_PHONE = "0506603374"
SEO_TITLE = f"%title% 🪲 {SEO_PHONE} 🪳 إختيارك الصحيح"


def get_title(post_id: int) -> str:
    r = cli(f"post meta get {post_id} rank_math_title")
    return (r.get("stdout") or "").strip().strip('"')


def set_title(post_id: int, title: str) -> None:
    # Escape for shell: wrap in single quotes, escape existing singles
    safe = title.replace("'", "'\\''")
    r = cli(f"post meta update {post_id} rank_math_title '{safe}' --force", write=True)
    if r.get("exit_code") not in (0, None):
        raise RuntimeError(f"meta update failed: {r}")


def desired_title(current: str) -> str:
    if not current or current.lower() in ("null", "false"):
        return SEO_TITLE
    # Replace any UAE mobile already embedded in the SEO title
    updated = re.sub(r"\+971[\s\-]?0?5\d{8}", SEO_PHONE, current)
    updated = re.sub(r"(?<!\d)0?5\d{8}(?!\d)", SEO_PHONE, updated)
    if SEO_PHONE in updated:
        return updated
    # No phone present (e.g. "%title% | خدمة معتمدة في …") → use standard pest template
    return SEO_TITLE


def main() -> None:
    posts = fetch_all_posts()
    targets = []
    for p in posts:
        title = p["title"]["rendered"] if isinstance(p["title"], dict) else p["title"]
        title = re.sub(r"<[^>]+>", "", title)
        if is_pest_post(title, p.get("slug") or ""):
            targets.append({"id": int(p["id"]), "title": title, "link": p.get("link")})

    print(f"Pest posts: {len(targets)}", flush=True)
    results = []
    for i, item in enumerate(targets, 1):
        pid = item["id"]
        print(f"=== [{i}/{len(targets)}] {item['title'][:50]} ({pid}) ===", flush=True)
        try:
            before = get_title(pid)
            after = desired_title(before)
            if before != after:
                set_title(pid, after)
            verify = get_title(pid)
            ok = SEO_PHONE in verify
            results.append(
                {
                    "id": pid,
                    "title": item["title"],
                    "link": item["link"],
                    "before": before,
                    "after": verify,
                    "ok": ok,
                }
            )
            print("OK" if ok else "WARN", {"before": before, "after": verify}, flush=True)
        except Exception as e:
            print("FAIL", pid, e, flush=True)
            results.append({"id": pid, "title": item["title"], "error": str(e)})
        time.sleep(0.05)

    out = Path("/workspace/articles/seo-title-pest-phone.json")
    out.write_text(json.dumps(results, ensure_ascii=False, indent=2), encoding="utf-8")
    ok_n = sum(1 for r in results if r.get("ok"))
    print(f"Wrote {out} ok={ok_n}/{len(results)}")


if __name__ == "__main__":
    main()
