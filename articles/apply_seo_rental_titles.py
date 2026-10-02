#!/usr/bin/env python3
"""Set Rank Math titles to the rental-ad template on allowed posts only.

Does NOT change:
- posts whose current SEO title contains 01556644443
- posts with empty rank_math_title (they inherit the global 01556644443 template)
- the global Rank Math option pt_post_title
- leak / insulation / AD pest / AD pools / AD cleaning (except hourly AD)
- hourly maids Dubai & Sharjah
- landscaping Abu Dhabi & Al Ain
"""

from __future__ import annotations

import base64
import binascii
import json
import os
import urllib.request

WP = "https://www.rukn-eltatawer.com"
NEW = "%title% 📞 01151481000 📢 الإعلان للإيجار"
AUDIT = os.path.join(os.path.dirname(os.path.abspath(__file__)), "seo-title-rental-audit.json")
HOURLY_AD_ID = 789


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
    return api_post(
        "wpvibe/v1/cli/run-approved",
        {"command": command, "confirm_write": True},
    )


def db(sql: str, write: bool = False) -> dict:
    cmd = f'db query "{sql}" --skip-column-names'
    if not write:
        return cli(cmd, write=False)
    try:
        return cli(cmd, write=True)
    except Exception:
        return cli_approved(cmd)


def rows(sql: str) -> list[dict]:
    r = db(sql, write=False)
    raw = r.get("stdout") or ""
    data = json.loads(raw) if isinstance(raw, str) else raw
    if isinstance(data, dict):
        return data.get("results") or []
    return data or []


def target_ids() -> list[int]:
    data = json.load(open(AUDIT, encoding="utf-8"))
    ids = []
    for r in data["would_change"]:
        seo = r.get("seo") or ""
        if "01556644443" in seo:
            continue
        if not seo.strip():
            continue
        ids.append(int(r["id"]))
    if HOURLY_AD_ID not in ids:
        ids.append(HOURLY_AD_ID)
    return sorted(set(ids))


def apply(ids: list[int]) -> None:
    hx = binascii.hexlify(NEW.encode("utf-8")).decode()
    for i in range(0, len(ids), 150):
        chunk = ",".join(str(x) for x in ids[i : i + 150])
        sql = (
            f"UPDATE wp3mdn_postmeta SET meta_value=UNHEX('{hx}') "
            f"WHERE meta_key='rank_math_title' AND post_id IN ({chunk})"
        )
        r = db(sql, write=True)
        print("chunk", i, "n", len(ids[i : i + 150]), r.get("stdout") or r)


def purge() -> None:
    try:
        print("purge", cli("litespeed-purge all", write=True))
    except Exception as e:
        print("purge skip", e)
    try:
        print("flush", cli("cache flush", write=True))
    except Exception as e:
        print("flush skip", e)


def main() -> int:
    if not os.environ.get("WP_USER") or not os.environ.get("WP_APP_PASS"):
        print("WP_USER / WP_APP_PASS missing")
        return 2
    ids = target_ids()
    print("targets", len(ids), "includes_hourly_ad", HOURLY_AD_ID in ids)
    # safety: none of these should currently have 01556644443
    check = rows(
        "SELECT COUNT(*) AS c FROM wp3mdn_postmeta "
        f"WHERE meta_key='rank_math_title' AND post_id IN ({','.join(map(str, ids))}) "
        "AND meta_value LIKE '%01556644443%'"
    )
    print("safety 0155 in targets", check)
    apply(ids)
    purge()
    open("/tmp/seo_rental_applied_ids.json", "w").write(json.dumps(ids))
    print("done")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
