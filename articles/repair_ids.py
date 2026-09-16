#!/usr/bin/env python3
"""Force re-rewrite specific post IDs using the current pipeline."""
from __future__ import annotations

import json
import sys

from rukn_rewrite_pipeline import rewrite_post

QUEUE = "/workspace/articles/under1000-priority-queue.json"


def main() -> None:
    ids = [int(x) for x in sys.argv[1:]]
    if not ids:
        raise SystemExit("usage: repair_ids.py ID [ID ...]")
    queue = {int(q["id"]): q for q in json.load(open(QUEUE, encoding="utf-8"))}
    results = []
    for pid in ids:
        item = queue.get(pid)
        if not item:
            # minimal fallback from live title not required; skip unknown
            results.append({"id": pid, "error": "not in queue"})
            print("SKIP unknown", pid)
            continue
        print(f"\n=== REPAIR {pid} {item['title']} ===")
        try:
            res = rewrite_post(item)
            results.append(res)
            print("OK", res)
        except Exception as e:
            results.append({"id": pid, "title": item["title"], "error": str(e)})
            print("FAIL", pid, e)
    out = "/workspace/articles/batch-results-repair.json"
    open(out, "w", encoding="utf-8").write(json.dumps(results, ensure_ascii=False, indent=2))
    print("Wrote", out)


if __name__ == "__main__":
    main()
