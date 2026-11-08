#!/usr/bin/env python3
"""Publish gypsum-board rewrites and leftover encyclopedia ceiling pages."""
from __future__ import annotations

import base64
import json
import os
import re
import sys
import urllib.request
from pathlib import Path

from _php import faqs, features, services, steps
from gypsum_content import ARTICLES as GYPSUM
from leftover_ceiling_content import ARTICLES as LEFTOVER

ARTICLES = {**GYPSUM, **LEFTOVER}

_user = os.environ.get("WPVIBE_USER", "")
_pass = os.environ.get("WPVIBE_PASS", "")
AUTH = (
    "Basic " + base64.b64encode(f"{_user}:{_pass}".encode()).decode()
    if _user and _pass
    else ""
)
URL = "https://www.rukn-eltatawer.com/wp-json/wpvibe/v1/cli/run"
ROOT = Path("/workspace/rewrites")

FOLDERS = {
    **{pid: "gypsum" for pid in GYPSUM},
    6120: "false-ceiling",
    6137: "suspended-ceiling",
}


def vibe(command: str, timeout: int = 120) -> dict:
    if not AUTH:
        raise SystemExit("Set WPVIBE_USER and WPVIBE_PASS")
    req = urllib.request.Request(
        URL,
        data=json.dumps({"command": command}).encode(),
        headers={"Authorization": AUTH, "Content-Type": "application/json"},
    )
    with urllib.request.urlopen(req, timeout=timeout) as r:
        return json.loads(r.read().decode())


def dump_html():
    for pid, art in ARTICLES.items():
        folder = ROOT / FOLDERS[pid]
        folder.mkdir(parents=True, exist_ok=True)
        (folder / f"{art['slug']}.html").write_text(art["html"], encoding="utf-8")


def sq(value: str) -> str:
    if "'" in value:
        raise ValueError("single quote in meta value")
    return "'" + value + "'"


def publish_one(pid: int, art: dict) -> None:
    html = art["html"].strip() + "\n"
    b64 = base64.b64encode(html.encode("utf-8")).decode("ascii")
    out = vibe(f"post update {pid} --post_content_base64={b64}")
    if out.get("exit_code") not in (0, None):
        stdout = out.get("stdout") or ""
        if "updated" not in stdout.lower() and "success" not in stdout.lower():
            raise RuntimeError(f"content {pid}: {json.dumps(out, ensure_ascii=False)[:800]}")

    call = {
        "call_section_title": art["call_title"],
        "call_section_content": art["call_content"],
        "call_section_phone": art["call_phone"],
        "call_section_whatsapp": art["call_wa"],
    }
    updates = [
        ("yourcolor__faqs", json.dumps(faqs(art["faqs"]), ensure_ascii=True)),
        ("post__features__data", json.dumps(features(*art["features"]), ensure_ascii=True)),
        ("post__work_steps__data", json.dumps(steps(*art["steps"]), ensure_ascii=True)),
        ("post__services__data", json.dumps(services(*art["services"]), ensure_ascii=True)),
        ("post__call_section__data", json.dumps(call, ensure_ascii=True)),
        ("last_update", "08-11-2026"),
    ]
    for key, val in updates:
        mout = vibe(f"post meta update {pid} {key} {sq(val)}")
        if mout.get("exit_code") not in (0, None):
            raise RuntimeError(f"meta {pid} {key}: {json.dumps(mout, ensure_ascii=False)[:800]}")


def verify(pid: int, slug: str) -> None:
    raw = json.loads(vibe(f"post get {pid} --fields=post_content")["stdout"])
    content = raw.get("post_content") or ""
    bad = []
    for needle in (
        "تنفّذ العمل المطلوب",
        "قطعة بديلة",
        "تشخيص العطل",
        "تجربة التشغيل",
        "{city}",
        "tel:",
        "فقس بيض",
        "ما هي شركة تركيب",
    ):
        if needle in content:
            bad.append(needle)
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


def main():
    dump_html()
    if len(sys.argv) > 1 and sys.argv[1] == "--dump-only":
        print("dumped html")
        return
    only = {int(x) for x in sys.argv[1:]} if len(sys.argv) > 1 else None
    for pid, art in ARTICLES.items():
        if only and pid not in only:
            continue
        print(f"PUBLISH {pid} {art['slug']} ...")
        publish_one(pid, art)
        verify(pid, art["slug"])
    print("DONE", len(only) if only else len(ARTICLES))


if __name__ == "__main__":
    main()
