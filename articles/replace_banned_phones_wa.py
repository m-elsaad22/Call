#!/usr/bin/env python3
"""Replace banned UAE phones with +971586634710 everywhere except call buttons.

Call buttons (call_section_phone and similar tel-only fields): banned numbers are
cleared (empty), never set to the WhatsApp-only replacement number.
Also clears call_section_phone if it already holds +971586634710.
"""

from __future__ import annotations

import json
import re
import time
import urllib.request
from copy import deepcopy
from pathlib import Path

from rukn_rewrite_pipeline import (
    WP,
    auth_header,
    cli,
    php_serialize,
    sql_set_meta,
)

REPLACE_INTL = "+971586634710"
REPLACE_LOCAL = "0586634710"
REPLACE_WA = "971586634710"
REPLACE_DISPLAY = "+971 58 663 4710"

# User-provided numbers to remove (intl + local pairs collapsed to 9-digit mobile body)
BANNED_BODIES = {
    "547577159",
    "508456901",
    "502373375",
    "568060309",
    "543331609",
    "522997116",
    "555357629",
    "522881997",
    "526965446",
    "502516205",
    "555110143",
    "528541360",
    "509843301",
    "544404988",
    "509728382",
    "508575165",
    "505460233",
    "525774047",
    "544438542",
    "544787820",
    "564634332",
    "508715513",
    "526151971",
    "504030828",
    "509380804",
    "567925243",
    "502288776",
    "521555782",
    "543533969",
    "582545283",
    "588404027",
    "508783205",
    "543103610",
    "557595736",
    "558422159",
    "566991366",
    "503049830",
    "503627748",
    "504157919",
    "523199437",
    "545103528",
    "545473541",
    "545686650",
    "582355507",
}

# Build one big regex covering intl/local/wa/spaced forms for banned bodies
_BODY_ALT = "|".join(re.escape(b) for b in sorted(BANNED_BODIES))
BANNED_RE = re.compile(
    rf"""(?:
        \+971[\s\-]?0?({_BODY_ALT})
      | (?<!\d)971[\s\-]?0?({_BODY_ALT})(?!\d)
      | (?<!\d)0({_BODY_ALT})(?!\d)
      | (?<!\d)({_BODY_ALT})(?!\d)
    )""",
    re.VERBOSE,
)

# Also match oddly spaced locals like 05 47 577 159 — normalize via digit extract fallback
LOOSE_UAE_RE = re.compile(r"\+?971[\s\-]?0?5[\d\s\-]{8,14}|(?<!\d)0?5[\d\s\-]{8,14}(?!\d)")


def digits(s: str) -> str:
    return re.sub(r"\D", "", s or "")


def body_of(phone_digits: str) -> str | None:
    d = phone_digits
    if d.startswith("971") and len(d) >= 12:
        return d[3:12]
    if d.startswith("05") and len(d) >= 10:
        return d[1:10]
    if d.startswith("5") and len(d) >= 9:
        return d[:9]
    return None


def is_banned_text(text: str) -> bool:
    if not text:
        return False
    if BANNED_RE.search(text):
        return True
    for m in LOOSE_UAE_RE.finditer(text):
        b = body_of(digits(m.group(0)))
        if b in BANNED_BODIES:
            return True
    return False


def contains_replace_number(text: str) -> bool:
    d = digits(text)
    return "586634710" in d or d.endswith("586634710")


def replace_banned_in_text(text: str, style: str = "auto") -> tuple[str, int]:
    """Replace banned phone forms with same-length WA number forms when possible."""
    if not text or not is_banned_text(text):
        return text, 0

    count = 0

    def pick_replacement(raw: str) -> str:
        d = digits(raw)
        b = body_of(d)
        if b not in BANNED_BODIES:
            return raw
        stripped = raw.strip()
        if style == "local":
            return REPLACE_LOCAL
        if style == "wa":
            return REPLACE_WA
        if style == "intl":
            return REPLACE_INTL
        # auto: preserve form / length
        if stripped.startswith("+"):
            return REPLACE_INTL
        if d.startswith("971"):
            return REPLACE_WA
        if stripped.startswith("0") or d.startswith("05"):
            return REPLACE_LOCAL
        # bare 9-digit body
        return "586634710"

    def repl_match(m: re.Match) -> str:
        nonlocal count
        raw = m.group(0)
        b = body_of(digits(raw))
        if b not in BANNED_BODIES:
            return raw
        count += 1
        return pick_replacement(raw)

    text2 = BANNED_RE.sub(repl_match, text)
    text2 = LOOSE_UAE_RE.sub(repl_match, text2)
    return text2, count


def db_query(sql: str) -> list[dict]:
    safe = sql.replace('"', '\\"')
    r = cli(f'db query "{safe}"')
    out = r.get("stdout") or ""
    try:
        data = json.loads(out)
    except json.JSONDecodeError:
        return []
    if isinstance(data, dict) and "results" in data:
        return data["results"]
    if isinstance(data, list):
        return data
    return []


def db_paged(sql_base: str, page_size: int = 100) -> list[dict]:
    rows_all = []
    offset = 0
    while True:
        rows = db_query(f"{sql_base} LIMIT {page_size} OFFSET {offset}")
        rows_all.extend(rows)
        if len(rows) < page_size:
            break
        offset += page_size
    return rows_all


def get_meta(post_id: int, key: str):
    r = cli(f"post meta get {post_id} {key}")
    out = (r.get("stdout") or "").strip()
    if not out:
        return None
    if out.startswith("{") or out.startswith("["):
        try:
            return json.loads(out)
        except json.JSONDecodeError:
            return out
    if (out.startswith('"') and out.endswith('"')) or (
        out.startswith("'") and out.endswith("'")
    ):
        return out[1:-1]
    return out


def set_plain_meta(post_id: int, key: str, value: str) -> None:
    sql_set_meta(post_id, key, value)


CALL_SCALAR_KEYS = {
    # tel / call oriented — clear banned, never set to WA-only number
    "phone",
    "phone_number",
    "memo-meta-phone",
    "contact_number",
}
WA_SCALAR_KEYS = {
    "whatsapp",
    "whatsapp_number",
}
SEO_KEYS = {
    "rank_math_title",
    "_rank_math_title",
    "rank_math_facebook_title",
    "rank_math_description",
    "_yoast_wpseo_title",
    "_yoast_wpseo_metadesc",
}


def process_call_section(post_id: int) -> dict:
    data = get_meta(post_id, "post__call_section__data")
    if not isinstance(data, dict):
        return {"skipped": True}
    data = deepcopy(data)
    changed = False
    cleared_call = False
    replaced_wa = False

    call_ph = str(data.get("call_section_phone") or "")
    wa_ph = str(data.get("call_section_whatsapp") or "")

    # Call button: clear if banned OR if it's the WA-only replacement number
    if is_banned_text(call_ph) or contains_replace_number(call_ph):
        if call_ph != "":
            data["call_section_phone"] = ""
            changed = True
            cleared_call = True

    # WhatsApp button: replace banned with replacement
    if is_banned_text(wa_ph):
        data["call_section_whatsapp"] = REPLACE_INTL
        changed = True
        replaced_wa = True

    if changed:
        sql_set_meta(post_id, "post__call_section__data", data)
    return {
        "changed": changed,
        "cleared_call": cleared_call,
        "replaced_wa": replaced_wa,
    }


def process_yourcolor_service(post_id: int) -> bool:
    data = get_meta(post_id, "YourColor_Service")
    if not isinstance(data, dict):
        return False
    tel = str(data.get("telephone") or "")
    if not is_banned_text(tel):
        return False
    # Schema telephone — use local WA number (not a "call button" UI, but contact schema)
    # User said WA only — put WA number here as contact, OK for schema.
    data = deepcopy(data)
    data["telephone"] = REPLACE_LOCAL
    sql_set_meta(post_id, "YourColor_Service", data)
    return True


def process_scalar_meta(post_id: int, key: str, value: str) -> str | None:
    """Return new value if changed, else None. action depends on key type."""
    if not is_banned_text(value):
        return None
    if key in CALL_SCALAR_KEYS:
        # Do not put WA-only number on call-oriented fields — clear
        return ""
    if key in WA_SCALAR_KEYS or key in SEO_KEYS:
        new, n = replace_banned_in_text(value, style="intl" if key in WA_SCALAR_KEYS else "keep")
        return new if n else None
    # Other metas containing banned phones (price lists, serialized blobs as strings from SQL)
    new, n = replace_banned_in_text(value, style="keep")
    return new if n else None


def process_serialized_meta_via_cli(post_id: int, key: str) -> bool:
    """Load complex meta as JSON via wp-cli, replace phones in JSON dump, write back."""
    data = get_meta(post_id, key)
    if data is None:
        return False
    if isinstance(data, (dict, list)):
        blob = json.dumps(data, ensure_ascii=False)
        if not is_banned_text(blob):
            return False
        # Special nested call section already handled
        if key == "post__call_section__data":
            return False
        new_blob, n = replace_banned_in_text(blob, style="keep")
        if not n:
            return False
        try:
            new_data = json.loads(new_blob)
        except json.JSONDecodeError:
            return False
        # If this is call section-like nested phone fields inside other structures, leave as replaced in JSON
        sql_set_meta(post_id, key, new_data)
        return True
    if isinstance(data, str) and is_banned_text(data):
        if key in CALL_SCALAR_KEYS:
            set_plain_meta(post_id, key, "")
            return True
        new, n = replace_banned_in_text(data, style="keep")
        if n:
            set_plain_meta(post_id, key, new)
            return True
    return False


def update_post_content(post_id: int, content: str) -> int:
    new, n = replace_banned_in_text(content, style="keep")
    if not n:
        return 0
    payload = json.dumps({"content": new}).encode("utf-8")
    req = urllib.request.Request(
        f"{WP}/wp-json/wp/v2/posts/{post_id}",
        data=payload,
        method="POST",
        headers=auth_header(),
    )
    try:
        with urllib.request.urlopen(req, timeout=180) as r:
            json.load(r)
        return n
    except Exception:
        # try pages endpoint
        req = urllib.request.Request(
            f"{WP}/wp-json/wp/v2/pages/{post_id}",
            data=payload,
            method="POST",
            headers=auth_header(),
        )
        with urllib.request.urlopen(req, timeout=180) as r:
            json.load(r)
        return n


def process_options() -> list[dict]:
    results = []
    rows = db_paged(
        "SELECT option_id, option_name, option_value FROM wp3mdn_options "
        "WHERE option_value REGEXP '05[0-9]{8}|9715[0-9]{8}|\\\\+971' "
        "OR option_name REGEXP 'phone|whatsapp|tel|mobile' "
        "ORDER BY option_id"
    )
    for row in rows:
        name = row.get("option_name") or ""
        val = str(row.get("option_value") or "")
        if not is_banned_text(val) and not is_banned_text(name):
            # also clear call options that hold WA-only number? only banned per user list
            continue
        # Call-only options: clear banned (and WA number on phone options)
        is_call_opt = bool(
            re.search(r"(^|_)phone($|_)|phonenumber|tel($|_)", name, re.I)
        ) and not re.search(r"whatsapp|wa_", name, re.I)
        if is_call_opt:
            new, n = replace_banned_in_text(val, style="keep")
            # After replacing banned→586..., if this is call option, clear any 586... 
            # Actually: clear banned without putting WA number
            new2 = val
            # Remove banned by blanking phone-like tokens
            def blank_banned(m):
                b = body_of(digits(m.group(0)))
                if b in BANNED_BODIES:
                    return ""
                return m.group(0)
            new2 = BANNED_RE.sub(blank_banned, val)
            new2 = LOOSE_UAE_RE.sub(
                lambda m: "" if body_of(digits(m.group(0))) in BANNED_BODIES else m.group(0),
                new2,
            )
            if new2 != val:
                # wp option update
                safe = new2.replace("'", "'\\''")
                r = cli(f"option update {name} '{safe}' --format=plaintext", write=True)
                results.append({"option": name, "action": "cleared_call_option", "exit": r.get("exit_code")})
            continue

        new, n = replace_banned_in_text(val, style="keep")
        if n and new != val:
            # Prefer SQL for serialized options to avoid double serialization
            sql_set_meta_option(name, new)
            results.append({"option": name, "action": "replaced", "replacements": n})
    return results


def sql_set_meta_option(option_name: str, value: str) -> None:
    """Update wp_options.option_value via UNHEX to preserve exact string (incl. serialized)."""
    import binascii

    hx = binascii.hexlify(value.encode("utf-8")).decode()
    # ensure option exists
    cli(f"option update {option_name} tmp --format=plaintext", write=True)
    q = (
        f"UPDATE wp3mdn_options SET option_value=UNHEX('{hx}') "
        f"WHERE option_name='{option_name.replace(chr(39), chr(39)+chr(39))}'"
    )
    cmd = f'db query "{q}"'
    try:
        cli(cmd, write=True)
    except Exception as e:
        body = e.read().decode() if hasattr(e, "read") else str(e)
        if "approval_required" not in body and "409" not in body:
            raise
    from rukn_rewrite_pipeline import cli_approved

    r = cli_approved(cmd)
    if r.get("exit_code") not in (0, None):
        raise RuntimeError(f"option SQL failed {option_name}: {r}")


def main() -> None:
    print("Collecting posts/pages with banned phones…", flush=True)
    # Content hits
    content_hits = db_paged(
        "SELECT ID, post_type, post_title, post_content FROM wp3mdn_posts "
        "WHERE post_status='publish' AND post_type IN ('post','page') "
        "AND post_content REGEXP '05[0-9]{8}|9715[0-9]{8}|\\\\+971' "
        "ORDER BY ID"
    )
    content_ids = []
    for row in content_hits:
        if is_banned_text(row.get("post_content") or ""):
            content_ids.append(int(row["ID"]))
    print(f"content with banned: {len(content_ids)}", flush=True)

    meta_hits = db_paged(
        "SELECT DISTINCT post_id FROM wp3mdn_postmeta "
        "WHERE meta_value REGEXP '05[0-9]{8}|9715[0-9]{8}|\\\\+971' "
        "ORDER BY post_id"
    )
    meta_ids = [int(r["post_id"]) for r in meta_hits]
    print(f"posts with phone-like meta: {len(meta_ids)}", flush=True)

    # Focus IDs = union, but only published
    published = {
        int(r["ID"]): r
        for r in db_paged(
            "SELECT ID, post_title, post_type FROM wp3mdn_posts "
            "WHERE post_status='publish' AND post_type IN ('post','page') "
            "ORDER BY ID"
        )
    }

    target_ids = sorted(set(content_ids) | {i for i in meta_ids if i in published})
    print(f"published targets: {len(target_ids)}", flush=True)

    results = []
    for i, pid in enumerate(target_ids, 1):
        info = published.get(pid) or {}
        title = info.get("post_title") or str(pid)
        print(f"\n=== [{i}/{len(target_ids)}] {title[:55]} ({pid}) ===", flush=True)
        row = {"id": pid, "title": title, "type": info.get("post_type"), "actions": []}
        try:
            # call section
            cs = process_call_section(pid)
            if cs.get("changed"):
                row["actions"].append(cs)

            # common scalar keys
            for key in sorted(CALL_SCALAR_KEYS | WA_SCALAR_KEYS | SEO_KEYS):
                val = get_meta(pid, key)
                if not isinstance(val, str):
                    continue
                new = process_scalar_meta(pid, key, val)
                if new is not None and new != val:
                    set_plain_meta(pid, key, new)
                    row["actions"].append({"key": key, "from": val[:80], "to": new[:80]})

            # YourColor_Service
            if process_yourcolor_service(pid):
                row["actions"].append({"key": "YourColor_Service", "telephone": REPLACE_LOCAL})

            # Other complex metas that often hold phones
            for key in (
                "post__price_list__data",
                "post__card__data",
                "post__features__data",
                "post__work_steps__data",
                "yourcolor__faqs",
                "YourColor_Article",
                "YourColor_ImageObject",
                "_aie_schema",
                "references",
            ):
                if process_serialized_meta_via_cli(pid, key):
                    row["actions"].append({"key": key, "replaced": True})

            # content
            if pid in content_ids or True:
                # load raw content
                r = db_query(
                    f"SELECT post_content FROM wp3mdn_posts WHERE ID={pid} LIMIT 1"
                )
                content = (r[0].get("post_content") if r else "") or ""
                if is_banned_text(content):
                    n = update_post_content(pid, content)
                    if n:
                        row["actions"].append({"content_replacements": n})

            results.append(row)
            print("OK actions", len(row["actions"]), flush=True)
        except Exception as e:
            print("FAIL", pid, e, flush=True)
            row["error"] = str(e)
            results.append(row)
        time.sleep(0.05)

    print("\nProcessing options…", flush=True)
    opt_results = process_options()
    print(f"options changed: {len(opt_results)}", flush=True)

    out = {
        "replace_with": REPLACE_INTL,
        "note": "call_section_phone cleared (never set to WA-only number)",
        "posts": results,
        "options": opt_results,
        "stats": {
            "posts_touched": len(results),
            "posts_with_actions": sum(1 for r in results if r.get("actions")),
            "failures": sum(1 for r in results if r.get("error")),
            "options_changed": len(opt_results),
        },
    }
    path = Path("/workspace/articles/phone-replace-banned-to-wa.json")
    path.write_text(json.dumps(out, ensure_ascii=False, indent=2), encoding="utf-8")
    print("Wrote", path)
    print(out["stats"])


if __name__ == "__main__":
    main()
