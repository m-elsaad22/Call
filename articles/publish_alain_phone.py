#!/usr/bin/env python3
"""Set floating Call/WhatsApp + Rank Math title phone on Al Ain cleaning/pest/pigeon/marble posts."""
from __future__ import annotations

import base64
import json
import os
import re
import urllib.request

SITE = "https://www.rukn-eltatawer.com"
USER = os.environ.get("WP_USER", "cursor")
APP_PASS = os.environ.get("WP_APP_PASS", "QQEG jJcC hwu4 SvYY YofJ cOJT").replace(" ", "")
TOKEN = base64.b64encode(f"{USER}:{APP_PASS}".encode()).decode()
HEADERS = {
    "Authorization": f"Basic {TOKEN}",
    "User-Agent": "Mozilla/5.0 CursorAgent",
    "Accept": "application/json",
    "Content-Type": "application/json",
}

NEW_E164 = "+971565619644"
NEW_WA = "971565619644"
NEW_LOCAL = "0565619644"
SEO_TITLE = f"%title% {NEW_LOCAL}"

# Unique live posts covering the 55 Al Ain titles (combined pages counted once).
POST_IDS = [
    6036,  # تنظيف منازل
    968,   # تنظيف شقق
    962,   # تنظيف فلل
    6037,  # تنظيف قصور
    6038,  # تنظيف مكاتب
    801,   # تنظيف مجالس
    6053,  # تنظيف وتعقيم شامل / تعقيم منازل
    6042,  # تنظيف مطابخ
    6043,  # تنظيف حمامات
    6044,  # تنظيف خزانات مياه
    6045,  # تنظيف خزانات ديزل
    1017,  # تنظيف سجاد
    6046,  # تنظيف موكيت
    986,   # تنظيف كنب
    6047,  # تنظيف ستائر
    6048,  # تنظيف مراتب
    6050,  # تنظيف واجهات زجاجية
    6051,  # تنظيف واجهات حجرية
    6039,  # تنظيف مدارس + حضانات
    6040,  # تنظيف مستشفيات + عيادات
    6041,  # تنظيف محلات + مولات
    6052,  # تنظيف كراجات ومواقف
    6049,  # تنظيف أرضيات رخام + جلي رخام
    753,   # جلي وتلميع الرخام (مقال مستقل)
    6055,  # تنظيف المداخن والشفاطات
    6054,  # تنظيف وصيانة دكتات المكيفات
    265,   # مكافحة حشرات
    6896,  # مكافحة الصراصير
    6881,  # مكافحة الحشرات الزاحفة
    6884,  # مكافحة الحشرات الطائرة
    6890,  # مكافحة الرمة + النمل الأبيض
    6893,  # مكافحة النمل الأسود والأحمر
    6925,  # مكافحة الذباب + البعوض
    6899,  # مكافحة بق الفراش
    6901,  # مكافحة الفئران + القوارض
    6907,  # مكافحة الأفاعي
    6911,  # مكافحة العقارب
    6914,  # مكافحة البراغيث
    6916,  # مكافحة الوزغ + البرص
    6918,  # مكافحة الطيور
    745,   # تركيب طارد حمام
    6927,  # شبك طارد للحمام
    6929,  # مسامير طاردة للحمام
    6932,  # أجهزة صوتية لطرد الطيور
    6935,  # التعقيم بالبخار
    6941,  # التعقيم بالكلور
    6943,  # التعقيم بالأوزون
]

OLD_NUMBERS = [
    "01151481000",
    "0541673020",
    "0586634710",
    "+971541673020",
    "+971586634710",
    "971541673020",
    "971586634710",
]


def php_serialize(val) -> str:
    if val is None:
        return "N;"
    if isinstance(val, bool):
        return f"b:{1 if val else 0};"
    if isinstance(val, int) and not isinstance(val, bool):
        return f"i:{val};"
    if isinstance(val, float):
        return f"d:{val};"
    if isinstance(val, str):
        raw = val.encode("utf-8")
        return f's:{len(raw)}:"{val}";'
    if isinstance(val, list):
        inner = "".join(php_serialize(i) + php_serialize(v) for i, v in enumerate(val))
        return f"a:{len(val)}:{{{inner}}}"
    if isinstance(val, dict):
        inner = "".join(php_serialize(k) + php_serialize(v) for k, v in val.items())
        return f"a:{len(val)}:{{{inner}}}"
    raise TypeError(type(val))


def request(url: str, data=None, method: str | None = None, timeout: int = 120):
    body = None
    if data is not None:
        body = json.dumps(data, ensure_ascii=False).encode("utf-8")
        method = method or "POST"
    req = urllib.request.Request(url, data=body, headers=HEADERS, method=method)
    with urllib.request.urlopen(req, timeout=timeout) as r:
        raw = r.read().decode("utf-8")
        return json.loads(raw) if raw else {}


def cli(cmd: str, approved: bool = False, confirm_write: bool = False):
    endpoint = "/wp-json/wpvibe/v1/cli/run-approved" if approved else "/wp-json/wpvibe/v1/cli/run"
    payload = {"command": cmd}
    if approved:
        payload["confirm_write"] = confirm_write
    return request(SITE + endpoint, payload)


def sql_escape(s: str) -> str:
    return s.replace("\\", "\\\\").replace("'", "\\'")


def db_query(sql: str, approved: bool = True):
    return cli(f'db query "{sql}"', approved=approved, confirm_write=approved)


def upsert_meta(post_id: int, key: str, value: str):
    b64 = base64.b64encode(value.encode("utf-8")).decode("ascii")
    db_query(
        f"DELETE FROM wp3mdn_postmeta WHERE post_id = {int(post_id)} "
        f"AND meta_key = '{sql_escape(key)}'"
    )
    return db_query(
        f"INSERT INTO wp3mdn_postmeta (post_id, meta_key, meta_value) "
        f"VALUES ({int(post_id)}, '{sql_escape(key)}', FROM_BASE64('{b64}'))"
    )


def replace_old_phones(text: str) -> str:
    if not text:
        return text
    out = text
    for old in sorted(OLD_NUMBERS, key=len, reverse=True):
        out = out.replace(old, NEW_LOCAL if old.startswith("0") or old.startswith("01") else NEW_E164 if old.startswith("+") else NEW_WA if old.startswith("971") else NEW_LOCAL)
    # Egyptian junk leftover
    out = out.replace("📢 الإعلان للإيجار", "").replace("الإعلان للإيجار", "")
    out = re.sub(r"\s{2,}", " ", out).strip()
    return out


def parse_meta_json(raw: str):
    raw = (raw or "").strip()
    if not raw:
        return None
    try:
        return json.loads(raw)
    except json.JSONDecodeError:
        return raw


def get_meta(post_id: int, key: str):
    out = cli(f"post meta get {post_id} {key}")
    if out.get("exit_code") not in (0, None):
        return None
    return parse_meta_json(out.get("stdout") or "")


def patch_call_section(data):
    if not isinstance(data, dict) or not data:
        return None
    changed = False
    mapping = {
        "call_section_phone": NEW_E164,
        "call_section_whatsapp": NEW_WA,
    }
    for k, v in mapping.items():
        if k in data:
            data[k] = v
            changed = True
    for k in ("call_section_subtitle", "call_section_content", "call_section_title"):
        if k in data and isinstance(data[k], str) and data[k]:
            nxt = replace_old_phones(data[k])
            if nxt != data[k]:
                data[k] = nxt
                changed = True
    return data if changed else data


def update_post(post_id: int):
    scalars = {
        "phone_number": NEW_E164,
        "whatsapp_number": NEW_E164,
        "phone": NEW_E164,
        "contact_number": NEW_E164,
        "whatsapp": NEW_E164,
        "rank_math_title": SEO_TITLE,
        "hide__floating__call": "",
    }
    desc = get_meta(post_id, "rank_math_description")
    if isinstance(desc, str) and desc:
        scalars["rank_math_description"] = replace_old_phones(desc)
    for key, val in scalars.items():
        upsert_meta(post_id, key, val)

    call = get_meta(post_id, "post__call_section__data")
    if isinstance(call, dict) and call:
        patched = patch_call_section(call)
        upsert_meta(post_id, "post__call_section__data", php_serialize(patched))


def flush_cache():
    for cmd in ["cache flush", "litespeed-purge all"]:
        try:
            print(cmd, cli(cmd, approved=True, confirm_write=True))
        except Exception as e:
            print("cache failed", cmd, e)


if __name__ == "__main__":
    print("posts", len(POST_IDS))
    for pid in POST_IDS:
        print("update", pid)
        update_post(pid)
    flush_cache()
    print("DONE")
