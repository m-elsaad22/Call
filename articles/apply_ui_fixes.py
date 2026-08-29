#!/usr/bin/env python3
"""Deploy surgical UI fixes only: header Call off, search, cards, EN chrome."""

from __future__ import annotations

import json
import re
from pathlib import Path

from apply_english_feedback import HEADER_RATES, restore_header, sql
from rukn_rewrite_pipeline import cli


def php_s(blob: str, key: str) -> str:
    m = re.search(re.escape(key) + r'";s:\d+:"([^"]*)"', blob)
    return m.group(1) if m else ""


def build_search_ratings() -> dict[str, list]:
    raw = sql(
        "SELECT p.post_name slug, pm.meta_key k, pm.meta_value v "
        "FROM wp3mdn_posts p "
        "JOIN wp3mdn_postmeta pm ON p.ID=pm.post_id "
        "WHERE p.post_status='publish' AND p.post_type='post' "
        "AND pm.meta_key IN ('YourColor__Rating','defualt__rating',"
        "'TotalRate_v1','RateUserCount_v1')"
    )
    rows = raw.get("results")
    if not isinstance(rows, list):
        payload = raw.get("stdout") or ""
        try:
            parsed = json.loads(payload) if isinstance(payload, str) else {}
        except json.JSONDecodeError:
            parsed = {}
        rows = parsed.get("results") if isinstance(parsed, dict) else []
    if not isinstance(rows, list):
        rows = []
    by_slug: dict[str, dict[str, str]] = {}
    for row in rows:
        slug = str(row.get("slug") or row.get("post_name") or "")
        if not slug:
            continue
        by_slug.setdefault(slug, {})[str(row.get("k") or row.get("meta_key") or "")] = str(
            row.get("v") or row.get("meta_value") or ""
        )
    out: dict[str, list] = {}
    for slug, metas in by_slug.items():
        avg = ""
        cnt = ""

        def take(candidate: str, count: str) -> bool:
            nonlocal avg, cnt
            try:
                num = float(candidate)
            except ValueError:
                return False
            if num <= 0 or num > 5:
                return False
            avg = candidate
            if count and count != "0":
                cnt = count
            return True

        yc = metas.get("YourColor__Rating") or ""
        if yc:
            take(php_s(yc, "RatingValue_def"), php_s(yc, "RatingCount_def"))
        if not avg:
            df = metas.get("defualt__rating") or ""
            users = 0
            for key in ("ratingUsers_1", "ratingUsers_2", "ratingUsers_3", "ratingUsers_4", "ratingUsers_5"):
                try:
                    users += int(php_s(df, key) or 0)
                except ValueError:
                    pass
            take(php_s(df, "ratingValue"), str(users) if users else "")
        if not avg:
            take((metas.get("TotalRate_v1") or "").strip(), (metas.get("RateUserCount_v1") or "").strip())
        try:
            num = float(avg)
        except ValueError:
            continue
        if num <= 0 or num > 5:
            continue
        shown = str(int(num)) if num == int(num) else str(round(num, 1))
        item: list = [shown]
        if cnt and cnt != "0":
            item.append(cnt)
        out[slug] = item
    return out


def write_ratings() -> int:
    rates = build_search_ratings()
    HEADER_RATES.write_text(
        "<script>window.RuknSR=" + json.dumps(rates, ensure_ascii=False, separators=(",", ":")) + ";</script>\n",
        encoding="utf-8",
    )
    print("ratings", len(rates))
    return len(rates)


def main() -> None:
    print("== search ratings map ==")
    write_ratings()
    print("== restore header (UI fixes only) ==")
    restore_header()
    cli("wp litespeed-purge all", write=True)
    cli("wp cache flush", write=True)
    print("done")


if __name__ == "__main__":
    main()
