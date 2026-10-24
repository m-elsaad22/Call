#!/usr/bin/env python3
"""Drain articles: remove +971526965446, WhatsApp-only +971586634710, hide Call."""

from __future__ import annotations

import binascii
import json
import re
from pathlib import Path
from urllib.error import HTTPError

from rukn_rewrite_pipeline import api_get, api_patch, cli, cli_approved

ROOT = Path(__file__).resolve().parent
WA = "+971586634710"
WA_DIGITS = "971586634710"
WA_LOCAL = "0586634710"
DRAIN_NUM = "526965446"
HEADER_SNIPPET = ROOT / "header_wa_only_call_hide.html"

CALL_KEYS = ("phone_number", "contact_number", "phone", "memo-meta-phone")
WA_KEYS = ("whatsapp", "whatsapp_number")

DRAIN_IDS = [448, 451, 454, 458, 461, 464, 1783, 1785, 3182, 10281]


def sql(cmd_sql: str) -> dict:
    cmd = "wp db query " + json.dumps(cmd_sql)
    try:
        return cli(cmd, write=True)
    except HTTPError as e:
        body = e.read().decode() if hasattr(e, "read") else str(e)
        if "approval_required" not in body and "409" not in body:
            raise
        return cli_approved(cmd)


def q(cmd_sql: str) -> dict:
    r = cli("wp db query " + json.dumps(cmd_sql))
    raw = r.get("stdout") or ""
    try:
        return json.loads(raw)
    except json.JSONDecodeError:
        return r


def hex_sql(value: str) -> str:
    return "UNHEX('" + binascii.hexlify(value.encode("utf-8")).decode() + "')"


def replace_drain_number_in_metas() -> None:
    """Same-length replaces so PHP-serialized metas stay valid."""
    pairs = (
        ("+971526965446", "+971586634710"),
        ("971526965446", "971586634710"),
        ("0526965446", "0586634710"),
        ("526965446", "586634710"),
    )
    for old, new in pairs:
        r = sql(
            "UPDATE wp3mdn_postmeta SET meta_value=REPLACE(meta_value,"
            f"'{old}','{new}') WHERE meta_value LIKE '%{old}%'"
        )
        print("meta replace", old, r.get("stdout") or r)


def empty_call_metas(ids: list[int]) -> None:
    if not ids:
        return
    id_list = ",".join(str(int(i)) for i in ids)
    keys = ",".join("'" + k + "'" for k in CALL_KEYS)
    sql(
        f"UPDATE wp3mdn_postmeta SET meta_value='' "
        f"WHERE post_id IN ({id_list}) AND meta_key IN ({keys})"
    )
    print("emptied call metas", len(ids))


def set_wa_metas(ids: list[int]) -> None:
    if not ids:
        return
    id_list = ",".join(str(int(i)) for i in ids)
    keys = ",".join("'" + k + "'" for k in WA_KEYS)
    sql(f"DELETE FROM wp3mdn_postmeta WHERE post_id IN ({id_list}) AND meta_key IN ({keys})")
    rows = []
    for pid in ids:
        for k in WA_KEYS:
            rows.append(f"({int(pid)},'{k}','{WA}')")
    sql("INSERT INTO wp3mdn_postmeta (post_id, meta_key, meta_value) VALUES " + ",".join(rows))
    print("whatsapp metas", len(ids))


def rewrite_call_section(ids: list[int]) -> None:
    payload = json.dumps(
        {
            "call_section_phone": "",
            "call_section_whatsapp": WA_DIGITS,
            "call_section_title": "واتساب الآن",
            "call_section_subtitle": "تسليك مجاري — واتساب فقط " + WA_LOCAL,
        },
        ensure_ascii=False,
    )
    id_list = ",".join(str(int(i)) for i in ids)
    sql(
        f"DELETE FROM wp3mdn_postmeta WHERE post_id IN ({id_list}) "
        "AND meta_key='post__call_section__data'"
    )
    rows = [f"({int(pid)},'post__call_section__data',{hex_sql(payload)})" for pid in ids]
    sql("INSERT INTO wp3mdn_postmeta (post_id, meta_key, meta_value) VALUES " + ",".join(rows))
    print("call section rewritten")


def main_wa_post_ids() -> list[int]:
    rows = (
        q(
            "SELECT DISTINCT post_id FROM wp3mdn_postmeta "
            "WHERE meta_key IN ('whatsapp','whatsapp_number') "
            "AND meta_value LIKE '%586634710%'"
        ).get("results")
        or []
    )
    return sorted({int(r["post_id"]) for r in rows} | set(DRAIN_IDS))


def patch_drain_content() -> None:
    for pid in DRAIN_IDS:
        raw = api_get(f"wp/v2/posts/{pid}?context=edit")["content"]["raw"]
        new = raw
        new = new.replace("tel:+971526965446", f"https://wa.me/{WA_DIGITS}")
        new = new.replace("tel:971526965446", f"https://wa.me/{WA_DIGITS}")
        new = new.replace("https://wa.me/971526965446", f"https://wa.me/{WA_DIGITS}")
        new = new.replace("https://wa.me/+971526965446", f"https://wa.me/{WA_DIGITS}")
        new = new.replace("+971526965446", WA)
        new = new.replace("0526965446", WA_LOCAL)
        new = new.replace("tel:+971000000000", f"https://wa.me/{WA_DIGITS}")
        new = new.replace("https://wa.me/971000000000", f"https://wa.me/{WA_DIGITS}")
        new = new.replace("+971000000000", WA)
        if "tel:" in new and DRAIN_NUM in new:
            new = re.sub(r'href="tel:[^"]*526965446[^"]*"', f'href="https://wa.me/{WA_DIGITS}"', new)
        if new != raw:
            api_patch(f"wp/v2/posts/{pid}", {"content": new})
            print(" content", pid)
        else:
            print(" content unchanged", pid)


def patch_header() -> None:
    snippet = HEADER_SNIPPET.read_text(encoding="utf-8")
    r = q("SELECT option_value FROM wp3mdn_options WHERE option_name='ihaf_insert_header'")
    current = ((r.get("results") or [{}])[0].get("option_value")) or ""
    if not current:
        raise RuntimeError("ihaf_insert_header empty")
    start = current.find('<style id="rukn-page-call-css">')
    end = current.find('<style id="rukn-lc-css">')
    if start < 0 or end < 0 or end <= start:
        raise RuntimeError("cannot locate page-call header block")
    updated = current[:start] + snippet + current[end:]
    if "rukn-wa-only" not in updated:
        raise RuntimeError("header patch missing rukn-wa-only")
    hx = binascii.hexlify(updated.encode("utf-8")).decode()
    sql(f"UPDATE wp3mdn_options SET option_value=UNHEX('{hx}') WHERE option_name='ihaf_insert_header'")
    print("header patched", len(current), "->", len(updated))


def verify() -> None:
    r = q(
        "SELECT post_id, meta_key, LEFT(meta_value,40) v FROM wp3mdn_postmeta "
        "WHERE post_id IN (448,1783,10281,69) AND meta_key IN "
        "('phone_number','whatsapp','whatsapp_number')"
    )
    print("verify metas", json.dumps(r.get("results"), ensure_ascii=False)[:2000])
    left = q(
        "SELECT COUNT(*) c FROM wp3mdn_postmeta WHERE meta_value LIKE '%526965446%'"
    )
    print("remaining 526965446 metas", left.get("results"))


def main() -> None:
    print("== replace drain number in metas ==")
    replace_drain_number_in_metas()
    print("== drain WhatsApp + empty Call ==")
    set_wa_metas(DRAIN_IDS)
    empty_call_metas(DRAIN_IDS)
    rewrite_call_section(DRAIN_IDS)
    print("== empty Call on every main-WhatsApp article ==")
    empty_call_metas(main_wa_post_ids())
    print("== drain content ==")
    patch_drain_content()
    print("== header hide Call when page WA is 0586634710 ==")
    patch_header()
    cli("wp litespeed-purge all", write=True)
    cli("wp cache flush", write=True)
    print("== verify ==")
    verify()
    print("done")


if __name__ == "__main__":
    main()
