#!/usr/bin/env python3
"""Publish unique shade + mismatch rewrites to live WordPress."""
from __future__ import annotations

import base64
import json
import os
import re
import sys
import urllib.request
from pathlib import Path

from _php import faqs, features, services, steps
from mismatch_content import ARTICLES as MISMATCH
from shade_content import ARTICLES as SHADE

ARTICLES = {**SHADE, **MISMATCH}

URL = "https://www.rukn-eltatawer.com/wp-json/wpvibe/v1/cli/run"
ROOT = Path("/workspace/rewrites")
FOLDERS = {**{pid: "shade" for pid in SHADE}, **{pid: "mismatch" for pid in MISMATCH}}
FORBIDDEN = (
    "تنسّق العنصر المطلوب",
    "تقدّم هذه الخدمة تحديدًا",
    "اختيار الأنواع المناسبة للموقع حتى الزراعة",
    "نجاح الزراعة حسب التعرض للشمس",
    "طبيعة التربة وشبكة الري",
    "العشب والخشب والحجر يختلفون حاجة ماء",
    "قطعة بديلة",
    "tel:",
    "{city}",
)


def vibe(command: str, write: bool = False, timeout: int = 180) -> dict:
    user = os.environ.get("WPVIBE_USER", "cursor")
    password = os.environ.get("WPVIBE_PASS", "")
    if not password:
        raise SystemExit("Set WPVIBE_PASS")
    token = base64.b64encode(f"{user}:{password}".encode()).decode()
    body = json.dumps({"command": command, "confirm_write": write}, ensure_ascii=False).encode()
    req = urllib.request.Request(
        URL,
        data=body,
        headers={
            "Authorization": f"Basic {token}",
            "Content-Type": "application/json",
            "User-Agent": "Mozilla/5.0 CursorAgent",
        },
    )
    with urllib.request.urlopen(req, timeout=timeout) as r:
        return json.loads(r.read().decode())


def dump_html() -> None:
    for pid, art in ARTICLES.items():
        folder = ROOT / FOLDERS[pid]
        folder.mkdir(parents=True, exist_ok=True)
        (folder / f"{art['slug']}.html").write_text(art["html"].strip() + "\n", encoding="utf-8")


def sq(value: str) -> str:
    if "'" in value:
        raise ValueError("single quote in meta value")
    return "'" + value + "'"


def publish_one(pid: int, art: dict) -> None:
    html = art["html"].strip() + "\n"
    for needle in FORBIDDEN:
        if needle in html:
            raise RuntimeError(f"{pid} contains forbidden {needle!r}")
    if "[post_call]" not in html:
        raise RuntimeError(f"{pid} missing [post_call]")
    if re.search(
        r"في (أبوظبي|دبي|الشارقة|عجمان|رأس الخيمة|الفجيرة|أم القيوين|العين) في \1",
        html,
    ):
        raise RuntimeError(f"{pid} double-city")
    b64 = base64.b64encode(html.encode("utf-8")).decode("ascii")
    out = vibe(f"post update {pid} --post_content_base64={b64}", write=True)
    stdout = (out.get("stdout") or "") + (out.get("stderr") or "")
    if out.get("exit_code") not in (0, "0", None) and "updated" not in stdout.lower():
        raise RuntimeError(f"content {pid}: {json.dumps(out, ensure_ascii=False)[:800]}")

    call = {
        "call_section_title": art["call_title"],
        "call_section_content": art["call_content"],
        "call_section_phone": art.get("call_phone") or "",
        "call_section_whatsapp": art.get("call_wa") or "",
    }
    updates = [
        ("yourcolor__faqs", json.dumps(faqs(art["faqs"]), ensure_ascii=True)),
        ("post__features__data", json.dumps(features(*art["features"]), ensure_ascii=True)),
        ("post__work_steps__data", json.dumps(steps(*art["steps"]), ensure_ascii=True)),
        ("post__services__data", json.dumps(services(*art["services"]), ensure_ascii=True)),
        ("post__call_section__data", json.dumps(call, ensure_ascii=True)),
        ("last_update", "24-11-2026"),
    ]
    for key, val in updates:
        mout = vibe(f"post meta update {pid} {key} {sq(val)}", write=True)
        if mout.get("exit_code") not in (0, "0", None):
            raise RuntimeError(f"meta {pid} {key}: {json.dumps(mout, ensure_ascii=False)[:800]}")


def verify(pid: int, slug: str) -> None:
    raw = json.loads(vibe(f"post get {pid} --fields=post_content")["stdout"])
    content = raw.get("post_content") or ""
    bad = [n for n in FORBIDDEN if n in content]
    if re.search(
        r"في (أبوظبي|دبي|الشارقة|عجمان|رأس الخيمة|الفجيرة|أم القيوين|العين) في \1",
        content,
    ):
        bad.append("double-city")
    if "[post_call]" not in content:
        bad.append("missing post_call")
    print(f"VERIFY {pid} {slug} len={len(content)} bad={bad or 'none'}")
    if bad:
        raise SystemExit(f"verify failed {pid}: {bad}")


def ngram_overlap() -> None:
    """Fail if two articles share a 12-word window."""
    windows = {}
    for pid, art in ARTICLES.items():
        text = re.sub(r"<[^>]+>", " ", art["html"])
        text = re.sub(r"\s+", " ", text).strip()
        words = text.split()
        for i in range(len(words) - 11):
            win = " ".join(words[i : i + 12])
            if win in windows and windows[win] != pid:
                raise SystemExit(f"overlap 12-gram between {windows[win]} and {pid}: {win}")
            windows[win] = pid
    print(f"uniq-ok windows={len(windows)} articles={len(ARTICLES)}")


def main() -> None:
    dump_html()
    ngram_overlap()
    if len(sys.argv) > 1 and sys.argv[1] == "--dump-only":
        print("dumped html")
        return
    only = {int(x) for x in sys.argv[1:]} if len(sys.argv) > 1 else None
    for pid, art in ARTICLES.items():
        if only and pid not in only:
            continue
        print(f"PUBLISH {pid} {art['slug']} ...", flush=True)
        publish_one(pid, art)
        verify(pid, art["slug"])
    print("DONE", len(only) if only else len(ARTICLES))


if __name__ == "__main__":
    main()
