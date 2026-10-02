#!/usr/bin/env python3
"""Call+WhatsApp +971524221011 on listed Abu Dhabi pest/disinfection posts only."""

from __future__ import annotations

import binascii
import json
from urllib.error import HTTPError

from rukn_rewrite_pipeline import api_get, api_patch, cli, cli_approved

TEL = "+971524221011"
TEL_LOCAL = "0524221011"
WA_DIGITS = "971524221011"

OLD = "+971522901095"
OLD_LOCAL = "0522901095"
OLD_WA = "971522901095"

CALL_KEYS = ("phone_number", "contact_number", "phone", "memo-meta-phone")
WA_KEYS = ("whatsapp", "whatsapp_number")
PHONE_KEYS = CALL_KEYS + WA_KEYS

SLUGS = (
    "insect-control-in-abu-dhabi",
    "crawling-pest-control-abu-dhabi",
    "flying-pest-control-abu-dhabi",
    "termite-control-abu-dhabi",
    "ant-control-abu-dhabi",
    "cockroach-control-abu-dhabi",
    "bed-bug-control-abu-dhabi",
    "rodent-control-abu-dhabi",
    "snake-control-abu-dhabi",
    "scorpion-control-abu-dhabi",
    "flea-control-abu-dhabi",
    "lizard-control-abu-dhabi",
    "bird-control-abu-dhabi",
    "mosquito-fly-control-abu-dhabi",
    "pigeon-net-installation-abu-dhabi",
    "pigeon-spikes-installation-abu-dhabi",
    "bird-sound-device-abu-dhabi",
    "steam-disinfection-abu-dhabi",
    "chlorine-disinfection-abu-dhabi",
    "ozone-disinfection-abu-dhabi",
)

CONTENT_REPLACES = (
    (f"tel:{OLD}", f"tel:{TEL}"),
    (f"tel:{OLD_WA}", f"tel:{TEL}"),
    (f"https://wa.me/{OLD_WA}", f"https://wa.me/{WA_DIGITS}"),
    (f"https://wa.me/+{OLD_WA}", f"https://wa.me/{WA_DIGITS}"),
    (f"https://wa.me/{OLD}", f"https://wa.me/{WA_DIGITS}"),
    (OLD, TEL),
    (OLD_LOCAL, TEL_LOCAL),
    (OLD_WA, WA_DIGITS),
)


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


def target_ids() -> list[int]:
    inlist = ",".join("'" + s + "'" for s in SLUGS)
    rows = q(
        "SELECT ID, post_name FROM wp3mdn_posts "
        f"WHERE post_type='post' AND post_status='publish' AND post_name IN ({inlist})"
    ).get("results") or []
    found = {row["post_name"]: int(row["ID"]) for row in rows}
    missing = [s for s in SLUGS if s not in found]
    if missing:
        raise RuntimeError("missing slugs: " + ", ".join(missing))
    extra = [name for name in found if "abu-dhabi" not in name]
    if extra:
        raise RuntimeError("non-AD slug: " + ", ".join(extra))
    return [found[s] for s in SLUGS]


def set_phones(ids: list[int]) -> None:
    id_list = ",".join(str(i) for i in ids)
    keys = ",".join("'" + k + "'" for k in PHONE_KEYS)
    sql(f"DELETE FROM wp3mdn_postmeta WHERE post_id IN ({id_list}) AND meta_key IN ({keys})")
    rows = []
    for pid in ids:
        for k in PHONE_KEYS:
            rows.append(f"({pid},'{k}','{TEL}')")
    sql("INSERT INTO wp3mdn_postmeta (post_id, meta_key, meta_value) VALUES " + ",".join(rows))
    print("phone metas", len(ids))


def set_seo_and_call(ids: list[int]) -> None:
    id_list = ",".join(str(i) for i in ids)
    sql(
        "UPDATE wp3mdn_postmeta SET meta_value="
        f"REPLACE(REPLACE(meta_value,'{OLD_LOCAL}','{TEL_LOCAL}'),'{OLD}','{TEL}') "
        f"WHERE post_id IN ({id_list}) AND meta_key='rank_math_title'"
    )
    sql(
        "UPDATE wp3mdn_postmeta SET meta_value="
        f"REPLACE(REPLACE(REPLACE(meta_value,'{OLD}','{TEL}'),'{OLD_LOCAL}','{TEL_LOCAL}'),"
        f"'{OLD_WA}','{WA_DIGITS}') "
        f"WHERE post_id IN ({id_list}) AND meta_key IN ("
        "'rank_math_description','rank_math_facebook_description',"
        "'rank_math_twitter_description')"
    )
    sql(f"DELETE FROM wp3mdn_postmeta WHERE post_id IN ({id_list}) AND meta_key='post__call_section__data'")
    section = json.dumps(
        {
            "call_section_phone": TEL,
            "call_section_whatsapp": WA_DIGITS,
            "call_section_title": "مكافحة حشرات أبوظبي",
            "call_section_subtitle": "اتصال وواتساب " + TEL_LOCAL,
        },
        ensure_ascii=False,
    )
    rows = ",".join(f"({pid},'post__call_section__data',{hex_sql(section)})" for pid in ids)
    sql(f"INSERT INTO wp3mdn_postmeta (post_id, meta_key, meta_value) VALUES {rows}")
    print("seo + call section", len(ids))


def rewrite_content(ids: list[int]) -> None:
    for pid in ids:
        raw = api_get(f"wp/v2/posts/{pid}?context=edit")["content"]["raw"]
        new = raw
        for old, repl in CONTENT_REPLACES:
            new = new.replace(old, repl)
        if new != raw:
            api_patch(f"wp/v2/posts/{pid}", {"content": new})
            print(" content", pid)
        else:
            print(" content skip", pid)


def verify(ids: list[int]) -> None:
    sample = ids[:3] + [265, 471, 789, 795]
    id_list = ",".join(str(i) for i in sample)
    r = q(
        "SELECT post_id, meta_key, LEFT(meta_value,80) v FROM wp3mdn_postmeta "
        f"WHERE post_id IN ({id_list}) AND meta_key IN "
        "('phone_number','whatsapp','rank_math_title')"
    )
    print("verify", json.dumps(r.get("results"), ensure_ascii=False)[:3500])


def main() -> None:
    ids = target_ids()
    print("targets", len(ids), ids)
    if len(ids) != 20:
        raise RuntimeError("expected 20 posts")
    set_phones(ids)
    set_seo_and_call(ids)
    rewrite_content(ids)
    cli("wp litespeed-purge all", write=True)
    cli("wp cache flush", write=True)
    verify(ids)
    print("done")


if __name__ == "__main__":
    main()
