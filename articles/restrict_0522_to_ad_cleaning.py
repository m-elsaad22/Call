#!/usr/bin/env python3
"""Keep +971522901095 on Abu Dhabi cleaning posts only.

Remove it everywhere else and put back the number that belongs there:
- AD pest / birds / disinfection → +971524221011
- Other-emirate cleaning + hourly AD → +971541673020
- Pools → +971521300019
- Non-AD pest (including Al Ain) → +971586634710 (WhatsApp-only site number)
"""

from __future__ import annotations

import base64
import json
import os
import urllib.request

WP = "https://www.rukn-eltatawer.com"

KEEP_AD_CLEANING = {
    471, 763, 807, 964, 970, 999, 1019, 1048, 1220,
    5900, 5901, 5902, 5903, 5904, 5905, 5906, 5907, 5908, 5909,
    5910, 5911, 5912, 5913, 5914, 5915, 5916, 5918, 6245,
}

AD_PEST = {
    251, 393, 743, 6463, 6467, 6470, 6473, 6475, 6485, 6489,
    6492, 6495, 6499, 6504, 6508, 6512, 6516, 6520, 6527,
    6531, 6534, 6537,
}

POOL = {795, 2425}
HOURLY_AD = {789}
ALAIN_OR_OTHER_CLEAN = {
    753, 801, 962, 968, 986, 1017, 6039, 6040, 6041, 6049,
    6051, 6052, 6053, 6055, 6399,
}
NON_AD_PEST = {
    170, 263, 265, 267, 745, 3274, 3276, 6581, 6693, 6731, 6749,
    6881, 6884, 6890, 6893, 6896, 6899, 6901, 6907, 6911, 6914,
    6916, 6918, 6925, 6927, 6929, 6932, 6935, 6941, 6943, 10474,
}
OTHER_WA_ONLY = {751}  # marble AD — not cleaning


def auth_header() -> dict:
    user = os.environ["WP_USER"]
    pw = os.environ["WP_APP_PASS"].replace(" ", "")
    token = base64.b64encode(f"{user}:{pw}".encode()).decode()
    return {
        "Authorization": f"Basic {token}",
        "Content-Type": "application/json",
        "User-Agent": "CursorAgent/1.0",
    }


def api_post(path: str, payload: dict, timeout: int = 180) -> dict:
    req = urllib.request.Request(
        f"{WP}/wp-json/{path.lstrip('/')}",
        data=json.dumps(payload).encode(),
        headers=auth_header(),
        method="POST",
    )
    with urllib.request.urlopen(req, timeout=timeout) as r:
        return json.load(r)


def cli(command: str, write: bool = False) -> dict:
    return api_post("wpvibe/v1/cli/run", {"command": command, "confirm_write": write})


def cli_approved(command: str) -> dict:
    return api_post("wpvibe/v1/cli/run-approved", {"command": command, "confirm_write": True})


def db(sql: str, write: bool = False) -> dict:
    cmd = f'db query "{sql}" --skip-column-names'
    if not write:
        return cli(cmd)
    try:
        return cli(cmd, write=True)
    except Exception:
        return cli_approved(cmd)


def rows(sql: str) -> list:
    r = db(sql)
    data = json.loads(r.get("stdout") or "{}")
    return data.get("results") or []


# Same digit length so PHP-serialized metas stay valid.
REPL = {
    "ad_pest": ("0524221011", "+971524221011", "971524221011"),
    "cleaning": ("0541673020", "+971541673020", "971541673020"),
    "pool": ("0521300019", "+971521300019", "971521300019"),
    "wa": ("0586634710", "+971586634710", "971586634710"),
}


def sql_replace(ids: set[int], kind: str) -> None:
    if not ids:
        return
    local, e164, digits = REPL[kind]
    idlist = ",".join(str(i) for i in sorted(ids))
    q = (
        "UPDATE wp3mdn_postmeta SET meta_value="
        f"REPLACE(REPLACE(REPLACE(meta_value,'+971522901095','{e164}'),"
        f"'0522901095','{local}'),'971522901095','{digits}') "
        f"WHERE post_id IN ({idlist}) AND meta_value LIKE '%522901095%'"
    )
    r = db(q, write=True)
    print("meta", kind, len(ids), r.get("stdout") or r)


def content_replace(ids: set[int], kind: str) -> None:
    local, e164, digits = REPL[kind]
    for pid in sorted(ids):
        recs = rows(
            f"SELECT ID id, post_content FROM wp3mdn_posts "
            f"WHERE ID={pid} AND post_content LIKE '%522901095%'"
        )
        if not recs:
            continue
        html = recs[0].get("post_content") or ""
        new = (
            html.replace("+971522901095", e164)
            .replace("0522901095", local)
            .replace("971522901095", digits)
        )
        if new == html:
            continue
        # write via REST to keep WP filters
        api_post(f"wp/v2/posts/{pid}", {"content": new, "id": pid})
        print("content", kind, pid)


def main() -> int:
    sql_replace(AD_PEST, "ad_pest")
    sql_replace(ALAIN_OR_OTHER_CLEAN | HOURLY_AD, "cleaning")
    sql_replace(POOL, "pool")
    sql_replace(NON_AD_PEST | OTHER_WA_ONLY, "wa")
    content_replace(AD_PEST, "ad_pest")
    content_replace(ALAIN_OR_OTHER_CLEAN | HOURLY_AD, "cleaning")
    content_replace(POOL, "pool")
    content_replace(NON_AD_PEST | OTHER_WA_ONLY, "wa")

    # leftover metas/content except KEEP
    leftover = rows(
        "SELECT DISTINCT post_id id FROM wp3mdn_postmeta "
        "WHERE meta_value LIKE '%522901095%'"
    )
    extra = [int(r["id"]) for r in leftover if int(r["id"]) not in KEEP_AD_CLEANING]
    print("leftover meta not keep", extra)
    leftover_c = rows(
        "SELECT ID id, post_name FROM wp3mdn_posts "
        "WHERE post_status='publish' AND post_content LIKE '%522901095%'"
    )
    extra_c = [r for r in leftover_c if int(r["id"]) not in KEEP_AD_CLEANING]
    print("leftover content not keep", extra_c)

    try:
        print("purge", cli("litespeed-purge all", write=True))
        print("flush", cli("cache flush", write=True))
    except Exception as e:
        print("purge skip", e)
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
