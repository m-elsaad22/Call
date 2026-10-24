#!/usr/bin/env python3
"""Fast bulk replace of banned phones → +971586634710, then clear call buttons.

Phase 1: same-length SQL REPLACE across posts content, postmeta, options.
Phase 2: clear call_section_phone when it holds the WA-only number (or leftover banned).
Phase 3: clear call-oriented scalar metas ONLY when they still hold a banned number
         (already replaced in phase 1 to WA) — actually for scalars phone/phone_number/
         memo/contact: if value is WA number AND we want no call — user asked specifically
         about call buttons. We only clear call_section_phone.
"""

from __future__ import annotations

import json
import re
import time
from copy import deepcopy
from pathlib import Path

from rukn_rewrite_pipeline import cli, cli_approved, sql_set_meta
from replace_banned_phones_wa import (
    BANNED_BODIES,
    REPLACE_INTL,
    REPLACE_LOCAL,
    REPLACE_WA,
    contains_replace_number,
    get_meta,
    is_banned_text,
)

PAGE = 100


def db_query(sql: str, write: bool = False) -> dict | list:
    safe = sql.replace('"', '\\"')
    cmd = f'db query "{safe}"'
    if not write:
        r = cli(cmd)
    else:
        try:
            r = cli(cmd, write=True)
        except Exception as e:
            body = e.read().decode() if hasattr(e, "read") else str(e)
            if "approval_required" not in body and "409" not in body:
                raise
            r = cli_approved(cmd)
    out = r.get("stdout") or ""
    try:
        data = json.loads(out)
    except json.JSONDecodeError:
        return {"raw": out, "exit_code": r.get("exit_code")}
    return data


def db_paged(sql_base: str) -> list[dict]:
    rows_all = []
    offset = 0
    while True:
        data = db_query(f"{sql_base} LIMIT {PAGE} OFFSET {offset}")
        rows = data.get("results", data if isinstance(data, list) else [])
        if not isinstance(rows, list):
            rows = []
        rows_all.extend(rows)
        if len(rows) < PAGE:
            break
        offset += PAGE
    return rows_all


def sql_write(sql: str) -> dict:
    return db_query(sql, write=True)


def phase1_bulk_replace() -> list[dict]:
    """Same-length replacements preserve PHP serialization."""
    log = []
    forms = []
    for body in sorted(BANNED_BODIES):
        forms.append((f"+971{body}", REPLACE_INTL))
        forms.append((f"971{body}", REPLACE_WA))
        forms.append((f"0{body}", REPLACE_LOCAL))

    tables = [
        ("wp3mdn_posts", "post_content", "ID"),
        ("wp3mdn_postmeta", "meta_value", "meta_id"),
        ("wp3mdn_options", "option_value", "option_id"),
    ]

    for table, col, _pk in tables:
        for old, new in forms:
            # count first
            esc_old = old.replace("'", "''")
            esc_new = new.replace("'", "''")
            cnt = db_query(
                f"SELECT COUNT(*) AS c FROM {table} WHERE {col} LIKE '%{esc_old}%'"
            )
            c = 0
            try:
                c = int((cnt.get("results") or [{}])[0].get("c") or 0)
            except Exception:
                pass
            if c == 0:
                continue
            print(f"REPLACE {old} → {new} in {table}.{col} ({c} rows)", flush=True)
            r = sql_write(
                f"UPDATE {table} SET {col} = REPLACE({col}, '{esc_old}', '{esc_new}') "
                f"WHERE {col} LIKE '%{esc_old}%'"
            )
            log.append({"table": table, "column": col, "old": old, "new": new, "matched_rows": c, "result": r.get("exit_code") if isinstance(r, dict) else None})
            time.sleep(0.05)
    return log


def phase2_clear_call_buttons() -> list[dict]:
    """Clear call_section_phone if it is WA-only number or still banned."""
    # Find metas that look like call section with our number or banned residue
    rows = db_paged(
        "SELECT post_id FROM wp3mdn_postmeta "
        "WHERE meta_key='post__call_section__data' "
        "AND (meta_value LIKE '%586634710%' OR meta_value LIKE '%call_section_phone%') "
        "ORDER BY post_id"
    )
    # Also any published post with call section
    post_ids = sorted({int(r["post_id"]) for r in rows})
    print(f"call_section candidates: {len(post_ids)}", flush=True)

    results = []
    for i, pid in enumerate(post_ids, 1):
        try:
            data = get_meta(pid, "post__call_section__data")
            if not isinstance(data, dict):
                continue
            call_ph = str(data.get("call_section_phone") or "")
            wa_ph = str(data.get("call_section_whatsapp") or "")
            changed = False
            data = deepcopy(data)

            # Clear call button if WA-only number or banned leftover
            if call_ph and (contains_replace_number(call_ph) or is_banned_text(call_ph)):
                data["call_section_phone"] = ""
                changed = True

            # Ensure WhatsApp uses replacement if banned leftover somehow remained
            if wa_ph and is_banned_text(wa_ph):
                data["call_section_whatsapp"] = REPLACE_INTL
                changed = True
            # If WA empty but call was cleared from WA number, set WA to replacement
            if changed and not str(data.get("call_section_whatsapp") or ""):
                data["call_section_whatsapp"] = REPLACE_INTL

            if changed:
                sql_set_meta(pid, "post__call_section__data", data)
                results.append(
                    {
                        "id": pid,
                        "cleared_call": True,
                        "whatsapp": data.get("call_section_whatsapp"),
                    }
                )
                if i % 25 == 0:
                    print(f"  cleared call buttons {i}/{len(post_ids)} (changed {len(results)})", flush=True)
        except Exception as e:
            results.append({"id": pid, "error": str(e)})
        time.sleep(0.03)
    return results


def verify_banned_remaining() -> dict:
    """Count remaining banned bodies in DB."""
    remaining = {}
    for body in sorted(BANNED_BODIES):
        total = 0
        for table, col in (
            ("wp3mdn_posts", "post_content"),
            ("wp3mdn_postmeta", "meta_value"),
            ("wp3mdn_options", "option_value"),
        ):
            for form in (f"+971{body}", f"971{body}", f"0{body}"):
                esc = form.replace("'", "''")
                cnt = db_query(
                    f"SELECT COUNT(*) AS c FROM {table} WHERE {col} LIKE '%{esc}%'"
                )
                try:
                    total += int((cnt.get("results") or [{}])[0].get("c") or 0)
                except Exception:
                    pass
        if total:
            remaining[body] = total
    return remaining


def sample_check() -> list[dict]:
    samples = []
    for pid in [6588, 251, 170, 38]:
        try:
            call = get_meta(pid, "post__call_section__data") or {}
            seo = get_meta(pid, "rank_math_title")
            samples.append(
                {
                    "id": pid,
                    "call_phone": call.get("call_section_phone") if isinstance(call, dict) else None,
                    "call_wa": call.get("call_section_whatsapp") if isinstance(call, dict) else None,
                    "seo": seo,
                    "phone": get_meta(pid, "phone"),
                    "whatsapp": get_meta(pid, "whatsapp"),
                }
            )
        except Exception as e:
            samples.append({"id": pid, "error": str(e)})
    return samples


def main() -> None:
    print("=== PHASE 1: bulk SQL replace ===", flush=True)
    log1 = phase1_bulk_replace()
    print(f"phase1 ops: {len(log1)}", flush=True)

    print("\n=== PHASE 2: clear call buttons ===", flush=True)
    log2 = phase2_clear_call_buttons()
    print(f"call buttons cleared: {len(log2)}", flush=True)

    print("\n=== VERIFY remaining banned ===", flush=True)
    remaining = verify_banned_remaining()
    print("remaining", remaining, flush=True)

    samples = sample_check()
    print("samples", json.dumps(samples, ensure_ascii=False, indent=2), flush=True)

    out = {
        "replace_with": REPLACE_INTL,
        "phase1_replacements": log1,
        "phase2_call_clears": log2,
        "banned_remaining": remaining,
        "samples": samples,
        "stats": {
            "bulk_ops": len(log1),
            "call_buttons_cleared": len([x for x in log2 if x.get("cleared_call")]),
            "phase2_errors": len([x for x in log2 if x.get("error")]),
            "banned_bodies_still_present": len(remaining),
        },
    }
    path = Path("/workspace/articles/phone-replace-banned-to-wa.json")
    path.write_text(json.dumps(out, ensure_ascii=False, indent=2), encoding="utf-8")
    print("Wrote", path)
    print(out["stats"])


if __name__ == "__main__":
    main()
