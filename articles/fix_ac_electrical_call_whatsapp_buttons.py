#!/usr/bin/env python3
"""Set call/WhatsApp FAB button metas on AC + electrical posts to +971556190406."""

from __future__ import annotations

import json
import time
from pathlib import Path

from rukn_rewrite_pipeline import cli
from set_ac_electrical_phone_0556190406 import (
    NEW_PHONE,
    fetch_all_posts,
    get_meta,
    is_target,
    strip_title,
    update_call_section,
    update_service_schema,
    update_simple_phone_metas,
)


def main() -> None:
    posts = fetch_all_posts()
    targets = []
    for p in posts:
        title = strip_title(p)
        if is_target(title, p.get("slug") or ""):
            targets.append({"id": int(p["id"]), "title": title, "link": p.get("link")})
    print(f"Targets: {len(targets)}", flush=True)
    results = []
    for i, item in enumerate(targets, 1):
        pid = item["id"]
        print(f"[{i}/{len(targets)}] {item['title'][:70]} ({pid})", flush=True)
        try:
            update_simple_phone_metas(pid)
            update_call_section(pid)
            schema = update_service_schema(pid)
            phone_number = get_meta(pid, "phone_number")
            whatsapp = get_meta(pid, "whatsapp")
            ok = phone_number == NEW_PHONE and whatsapp == NEW_PHONE
            results.append(
                {
                    "id": pid,
                    "title": item["title"],
                    "link": item["link"],
                    "ok": ok,
                    "phone_number": phone_number,
                    "whatsapp": whatsapp,
                    "schema": schema,
                }
            )
            print("  OK" if ok else "  WARN", phone_number, whatsapp, flush=True)
        except Exception as e:
            print("  FAIL", e, flush=True)
            results.append({"id": pid, "title": item["title"], "error": str(e)})
        time.sleep(0.05)

    out = Path("/workspace/articles/ac-electrical-buttons-0556190406.json")
    out.write_text(json.dumps(results, ensure_ascii=False, indent=2), encoding="utf-8")
    print(f"Wrote {out} ok={sum(1 for r in results if r.get('ok'))}/{len(results)}")
    try:
        r = cli("litespeed-purge all", write=True)
        print("cache", r.get("exit_code"), (r.get("stdout") or "")[:120])
    except Exception as e:
        print("cache skip", e)


if __name__ == "__main__":
    main()
