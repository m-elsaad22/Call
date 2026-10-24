#!/usr/bin/env python3
"""Full paginated inventory of phone numbers on rukn-eltatawer.com."""

from __future__ import annotations

import json
import re
from collections import Counter, defaultdict
from pathlib import Path

from rukn_rewrite_pipeline import cli

PHONE_RE = re.compile(
    r"(?:\+971[\s\-]?0?5\d{8}|(?<!\d)971[\s\-]?0?5\d{8}(?!\d)|(?<!\d)0?5\d{8}(?!\d)|9715\d{8}\+)"
)
PAGE = 100


def normalize(raw: str) -> str:
    s = raw.strip()
    if s.endswith("+") and re.sub(r"\D", "", s).isdigit():
        s = "+" + re.sub(r"\D", "", s)
    digits = re.sub(r"\D", "", s)
    if digits.startswith("971") and len(digits) >= 12:
        return "+" + digits[:12]
    if digits.startswith("05") and len(digits) == 10:
        return "+971" + digits[1:]
    if digits.startswith("5") and len(digits) == 9:
        return "+971" + digits
    return s


def find_phones(text: str) -> list[str]:
    if not text:
        return []
    return [normalize(m.group(0)) for m in PHONE_RE.finditer(text)]


def db_query(sql: str) -> list[dict]:
    safe = sql.replace('"', '\\"')
    r = cli(f'db query "{safe}"')
    out = r.get("stdout") or ""
    try:
        data = json.loads(out)
    except json.JSONDecodeError:
        print("WARN non-json", out[:200])
        return []
    if isinstance(data, dict) and "results" in data:
        return data["results"]
    if isinstance(data, list):
        return data
    return []


def db_paged(sql_base: str, page_size: int = PAGE) -> list[dict]:
    """Append ORDER BY + LIMIT/OFFSET. sql_base must NOT include limit."""
    all_rows = []
    offset = 0
    # Prefer ordering by a stable column if present
    while True:
        sql = f"{sql_base} LIMIT {page_size} OFFSET {offset}"
        rows = db_query(sql)
        all_rows.extend(rows)
        print(f"   fetched {len(all_rows)} (+{len(rows)})", flush=True)
        if len(rows) < page_size:
            break
        offset += page_size
    return all_rows


def local_fmt(phone: str) -> str:
    if phone.startswith("+971") and len(re.sub(r"\D", "", phone)) >= 12:
        return "0" + re.sub(r"\D", "", phone)[3:12]
    return phone


def main() -> None:
    index = defaultdict(list)
    titles = {}

    print("1) Titles…", flush=True)
    for row in db_paged(
        "SELECT ID, post_title, post_type FROM wp3mdn_posts "
        "WHERE post_status='publish' AND post_type IN ('post','page') "
        "ORDER BY ID"
    ):
        titles[int(row["ID"])] = {
            "title": row.get("post_title") or "",
            "type": row.get("post_type") or "post",
        }
    print(f"   titles={len(titles)}", flush=True)

    print("2) Content…", flush=True)
    content_rows = db_paged(
        "SELECT ID, post_title, post_type, post_content FROM wp3mdn_posts "
        "WHERE post_status='publish' AND post_type IN ('post','page') "
        "AND post_content REGEXP '05[0-9]{8}|9715[0-9]{8}|\\\\+971' "
        "ORDER BY ID"
    )
    for row in content_rows:
        pid = int(row["ID"])
        for ph in set(find_phones(row.get("post_content") or "")):
            index[ph].append(
                {
                    "where": "content",
                    "id": pid,
                    "type": row.get("post_type"),
                    "title": row.get("post_title"),
                }
            )

    print("3) Postmeta…", flush=True)
    meta_rows = db_paged(
        "SELECT post_id, meta_key, meta_value FROM wp3mdn_postmeta "
        "WHERE meta_value REGEXP '05[0-9]{8}|9715[0-9]{8}|\\\\+971' "
        "ORDER BY meta_id"
    )
    for row in meta_rows:
        pid = int(row["post_id"])
        info = titles.get(pid, {})
        for ph in set(find_phones(row.get("meta_value") or "")):
            index[ph].append(
                {
                    "where": "meta",
                    "id": pid,
                    "type": info.get("type") or "?",
                    "title": info.get("title") or f"(id {pid})",
                    "key": row.get("meta_key"),
                }
            )

    print("4) Options…", flush=True)
    opt_rows = db_paged(
        "SELECT option_id, option_name, option_value FROM wp3mdn_options "
        "WHERE option_value REGEXP '05[0-9]{8}|9715[0-9]{8}|\\\\+971' "
        "OR option_name REGEXP 'phone|whatsapp|tel|mobile' "
        "ORDER BY option_id"
    )
    for row in opt_rows:
        blob = f"{row.get('option_name')} {row.get('option_value')}"
        for ph in set(find_phones(blob)):
            index[ph].append(
                {
                    "where": "option",
                    "key": row.get("option_name"),
                    "sample": str(row.get("option_value") or "")[:220],
                }
            )

    report = []
    for phone, locs in sorted(index.items(), key=lambda x: (-len(x[1]), x[0])):
        content_ids = sorted({x["id"] for x in locs if x["where"] == "content" and "id" in x})
        meta_ids = sorted({x["id"] for x in locs if x["where"] == "meta"})
        meta_keys = Counter(x.get("key") for x in locs if x["where"] == "meta")
        options = [x for x in locs if x["where"] == "option"]
        published_meta_ids = [i for i in meta_ids if i in titles]

        by_key = defaultdict(list)
        for x in locs:
            if x["where"] == "meta" and x["id"] in titles:
                by_key[x.get("key")].append(
                    {"id": x["id"], "title": x.get("title"), "type": x.get("type")}
                )

        content_unique = []
        seen = set()
        for x in locs:
            if x["where"] != "content":
                continue
            if x["id"] in seen:
                continue
            seen.add(x["id"])
            content_unique.append(
                {"id": x["id"], "title": x.get("title"), "type": x.get("type")}
            )

        report.append(
            {
                "phone": phone,
                "local": local_fmt(phone),
                "total_hits": len(locs),
                "in_content_count": len(content_ids),
                "in_meta_count": len(published_meta_ids),
                "in_meta_including_unpublished": len(meta_ids),
                "in_options_count": len(options),
                "meta_keys": dict(meta_keys.most_common()),
                "options": options,
                "content_posts": content_unique[:40],
                "content_all_ids": content_ids,
                "meta_by_key": {
                    k: {
                        "count": len(v),
                        "unique_posts": len({i["id"] for i in v}),
                        "examples": v[:12],
                        "all_ids": sorted({i["id"] for i in v}),
                    }
                    for k, v in sorted(by_key.items(), key=lambda kv: -len(kv[1]))
                },
                "meta_published_ids": published_meta_ids,
            }
        )

    out_json = Path("/workspace/articles/site-phone-inventory.json")
    out_json.write_text(json.dumps(report, ensure_ascii=False, indent=2), encoding="utf-8")

    md = [
        "# جرد أرقام الهاتف في موقع ركن التطور",
        "",
        f"**عدد الأرقام الفريدة:** {len(report)}",
        "",
        "| الرقم الدولي | المحلي | في المحتوى | في الميتا (منشور) | إعدادات | إجمالي |",
        "|---|---|---:|---:|---:|---:|",
    ]
    for row in report:
        md.append(
            f"| `{row['phone']}` | `{row['local']}` | {row['in_content_count']} | "
            f"{row['in_meta_count']} | {row['in_options_count']} | {row['total_hits']} |"
        )
    md.append("")

    for row in report:
        md.append(f"## `{row['phone']}` — محلي `{row['local']}`")
        md.append("")
        md.append(f"- إجمالي الظهور في قاعدة البيانات: **{row['total_hits']}**")
        md.append(f"- مقالات/صفحات منشور محتواها يحتوي الرقم: **{row['in_content_count']}**")
        md.append(f"- مقالات/صفحات منشورة في الميتا: **{row['in_meta_count']}**")
        md.append(f"- إعدادات عامة: **{row['in_options_count']}**")
        md.append("")
        if row["options"]:
            md.append("### إعدادات الموقع (wp_options)")
            seen_k = set()
            for o in row["options"]:
                k = o["key"]
                if k in seen_k:
                    continue
                seen_k.add(k)
                md.append(f"- `{k}`")
            md.append("")
        if row["meta_keys"]:
            md.append("### الميتا (الحقول)")
            for k, c in list(row["meta_keys"].items())[:25]:
                info = row["meta_by_key"].get(k) or {}
                md.append(
                    f"- **`{k}`** — {info.get('unique_posts', 0)} مقال/صفحة منشورة"
                )
                for ex in (info.get("examples") or [])[:6]:
                    md.append(f"  - {ex.get('title', '')[:75]} `(id {ex.get('id')})`")
                extra = info.get("unique_posts", 0) - min(6, info.get("unique_posts", 0))
                if extra > 0:
                    md.append(f"  - … و {extra} أخرى")
            md.append("")
        if row["content_posts"]:
            md.append("### المحتوى")
            for ex in row["content_posts"][:20]:
                md.append(
                    f"- {ex.get('title', '')[:80]} `(id {ex.get('id')}, {ex.get('type')})`"
                )
            if row["in_content_count"] > 20:
                md.append(f"- … و {row['in_content_count'] - 20} أخرى")
            md.append("")

    out_md = Path("/workspace/articles/site-phone-inventory.md")
    out_md.write_text("\n".join(md), encoding="utf-8")
    print(f"Wrote {out_json}")
    print(f"Wrote {out_md}")
    print(f"Unique phones: {len(report)}")
    for row in report:
        print(
            f"{row['phone']} / {row['local']} | content={row['in_content_count']} "
            f"meta={row['in_meta_count']} opts={row['in_options_count']} hits={row['total_hits']}"
        )


if __name__ == "__main__":
    main()
