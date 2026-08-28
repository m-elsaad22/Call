#!/usr/bin/env python3
"""Fix call/WhatsApp button metas on Abu Dhabi + Al Ain cleaning/pest posts."""

from __future__ import annotations

import json
import time
from pathlib import Path

from set_cleaning_pest_abudhabi_alain_phone import (
    NEW_PHONE,
    classify,
    cli_set_meta,
    fetch_all_posts,
    get_meta,
    strip_title,
    update_call_section,
    update_schema_telephone,
)
from rukn_rewrite_pipeline import cli

BUTTON_KEYS = (
    "phone",
    "phone_number",
    "contact_number",
    "whatsapp",
    "whatsapp_number",
    "memo-meta-phone",
)


def main() -> None:
    posts = fetch_all_posts()
    targets = []
    for p in posts:
        title = strip_title(p)
        kind = classify(title, p.get("slug") or "")
        if kind:
            targets.append({"id": int(p["id"]), "title": title, "link": p.get("link"), "kind": kind})
    print(f"Targets: {len(targets)}", flush=True)
    results = []
    for i, item in enumerate(targets, 1):
        pid = item["id"]
        print(f"[{i}/{len(targets)}] {item['title'][:60]} ({pid})", flush=True)
        try:
            for key in BUTTON_KEYS:
                cli_set_meta(pid, key, NEW_PHONE)
            update_call_section(pid)
            schema = update_schema_telephone(pid)
            phone_number = get_meta(pid, "phone_number")
            whatsapp = get_meta(pid, "whatsapp")
            ok = phone_number == NEW_PHONE and whatsapp == NEW_PHONE
            results.append(
                {
                    "id": pid,
                    "title": item["title"],
                    "link": item["link"],
                    "kind": item["kind"],
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

    out = Path("/workspace/articles/cleaning-pest-buttons-0522901095.json")
    out.write_text(json.dumps(results, ensure_ascii=False, indent=2), encoding="utf-8")
    print(f"Wrote {out} ok={sum(1 for r in results if r.get('ok'))}/{len(results)}")
    try:
        r = cli("litespeed-purge all", write=True)
        print("cache", r.get("exit_code"), (r.get("stdout") or "")[:120])
    except Exception as e:
        print("cache skip", e)


if __name__ == "__main__":
    main()
