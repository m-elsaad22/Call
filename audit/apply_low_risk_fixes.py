#!/usr/bin/env python3
"""Apply approved low-risk fixes: media alt text + Rank Math archive/category meta."""
from __future__ import annotations

import base64
import json
import re
import time
import urllib.error
import urllib.parse
import urllib.request
from datetime import datetime, timezone
from pathlib import Path

BASE = "https://www.rukn-eltatawer.com"
STORE = Path("/cursor/stores/self/rukn-app-password.txt")
BACKUP_DIR = Path("/workspace/audit/backups")
REPORT_PATH = Path("/workspace/audit/LOW_RISK_FIXES_REPORT.md")
CHANGELOG_PATH = Path("/workspace/audit/output/low_risk_changes.jsonl")

# Media IDs attached to protected clusters — skip any manual list if needed (none for alt by default).
SKIP_MEDIA_IDS: set[int] = set()
MEDIA_LIMIT = int(__import__("os").environ.get("MEDIA_ALT_LIMIT", "0"))  # 0 = all
SKIP_SEO = __import__("os").environ.get("SKIP_SEO", "") == "1"


def auth_header() -> str:
    text = STORE.read_text(encoding="utf-8")
    creds = dict(line.split(": ", 1) for line in text.splitlines() if ": " in line)
    token = base64.b64encode(
        f"{creds['username']}:{creds['app_password']}".encode()
    ).decode()
    return "Basic " + token


def api_request(method: str, path: str, data: dict | None = None) -> tuple[int, object]:
    url = path if path.startswith("http") else BASE + path
    body = None if data is None else json.dumps(data).encode()
    req = urllib.request.Request(url, data=body, method=method)
    req.add_header("Authorization", auth_header())
    if body is not None:
        req.add_header("Content-Type", "application/json")
    try:
        with urllib.request.urlopen(req, timeout=120) as resp:
            raw = resp.read()
            return resp.status, json.loads(raw) if raw else None
    except urllib.error.HTTPError as e:
        raw = e.read().decode("utf-8", errors="replace")
        try:
            return e.code, json.loads(raw)
        except json.JSONDecodeError:
            return e.code, raw


def wp_cli(command: str) -> dict:
    status, payload = api_request(
        "POST",
        "/wp-json/wpvibe/v1/cli/run",
        {"command": command},
    )
    if status != 200 or not isinstance(payload, dict):
        raise RuntimeError(f"CLI failed ({status}): {payload}")
    if payload.get("exit_code", 0) != 0:
        raise RuntimeError(
            f"CLI {command!r} exit {payload.get('exit_code')}: {payload.get('stderr')}"
        )
    return payload


def fetch_all_media() -> list[dict]:
    items: list[dict] = []
    page = 1
    while True:
        q = urllib.parse.urlencode(
            {
                "per_page": 100,
                "page": page,
                "_fields": "id,title,alt_text,source_url,media_type,mime_type,post",
            }
        )
        status, batch = api_request("GET", f"/wp-json/wp/v2/media?{q}")
        if status == 400:
            break
        if status != 200 or not isinstance(batch, list):
            raise RuntimeError(f"media fetch page {page}: {status} {batch}")
        if not batch:
            break
        items.extend(batch)
        page += 1
        time.sleep(0.15)
    return items


def humanize_filename(url: str) -> str:
    name = url.rsplit("/", 1)[-1]
    name = re.sub(r"\.(webp|jpe?g|png|gif|avif)$", "", name, flags=re.I)
    name = urllib.parse.unquote(name)
    name = re.sub(r"[_\-]+", " ", name)
    name = re.sub(r"\s+", " ", name).strip()
    # Drop long numeric-only camera names
    if re.fullmatch(r"[\d\s]+", name) or len(name) > 80:
        return ""
    return name


def build_alt(item: dict, parent_titles: dict[int, str]) -> str:
    existing = (item.get("alt_text") or "").strip()
    if existing:
        return existing
    title = (item.get("title") or {}).get("rendered", "").strip()
    title = re.sub(r"<[^>]+>", "", title)
    url = (item.get("source_url") or "").lower()
    fname = humanize_filename(item.get("source_url") or "")
    parent_id = item.get("post") or 0
    parent = parent_titles.get(parent_id, "")

    if "logo" in url or "logo" in fname.lower():
        return "شعار شركة ركن التطور للخدمات المنزلية"

    parts: list[str] = []
    generic_titles = {"image", "img", "photo", "untitled", "scaled", "cropped"}
    if title and title.lower() not in generic_titles and not re.fullmatch(r"[\w\-]+", title):
        parts.append(title)
    elif parent:
        parts.append(f"صورة توضيحية لـ{parent}")
    elif fname and re.search(r"[\u0600-\u06FF]", fname):
        parts.append(fname)
    elif fname:
        # Latin slug: short factual caption without keyword stuffing
        parts.append(f"صورة: {fname}")

    if not parts:
        mime = item.get("mime_type") or ""
        if mime.startswith("image/"):
            parts.append("صورة من موقع ركن التطور")
        else:
            return ""

    alt = " ".join(parts)
    alt = re.sub(r"\s+", " ", alt).strip()
    if len(alt) > 125:
        alt = alt[:122].rstrip() + "…"
    return alt


def load_parent_titles(media: list[dict]) -> dict[int, str]:
    parent_ids = sorted({m["post"] for m in media if m.get("post")})
    titles: dict[int, str] = {}
    for pid in parent_ids:
        status, post = api_request(
            "GET",
            f"/wp-json/wp/v2/posts/{pid}?_fields=id,title,type",
        )
        if status != 200:
            status, post = api_request(
                "GET",
                f"/wp-json/wp/v2/pages/{pid}?_fields=id,title,type",
            )
        if status == 200 and isinstance(post, dict):
            t = (post.get("title") or {}).get("rendered", "")
            t = re.sub(r"<[^>]+>", "", t).strip()
            if t:
                titles[pid] = t
        time.sleep(0.05)
    return titles


def update_media_alt(media_id: int, alt: str) -> tuple[bool, str]:
    status, resp = api_request(
        "POST",
        f"/wp-json/wp/v2/media/{media_id}",
        {"alt_text": alt},
    )
    if status in (200, 201):
        return True, "ok"
    return False, str(resp)


def trim_description(text: str, max_len: int = 155) -> str:
    text = re.sub(r"\s+", " ", text).strip()
    if len(text) <= max_len:
        return text
    cut = text[: max_len - 1].rsplit(" ", 1)[0]
    return cut + "…"


def main() -> None:
    ts = datetime.now(timezone.utc).strftime("%Y%m%dT%H%M%SZ")
    BACKUP_DIR.mkdir(parents=True, exist_ok=True)
    CHANGELOG_PATH.parent.mkdir(parents=True, exist_ok=True)

    # --- Backups ---
    media = fetch_all_media()
    titles_opt = json.loads(
        wp_cli("wp option get rank-math-options-titles --format=json")["stdout"]
    )
    sitemap_opt = json.loads(
        wp_cli("wp option get rank-math-options-sitemap --format=json")["stdout"]
    )
    terms = json.loads(
        wp_cli("wp term list category --fields=term_id,slug,name,description --format=json")[
            "stdout"
        ]
    )
    backup_path = BACKUP_DIR / f"pre_low_risk_{ts}.json"
    backup_path.write_text(
        json.dumps(
            {
                "timestamp": ts,
                "media_alt_snapshot": [
                    {
                        "id": m["id"],
                        "alt_text": m.get("alt_text"),
                        "title": (m.get("title") or {}).get("rendered"),
                        "source_url": m.get("source_url"),
                    }
                    for m in media
                ],
                "rank_math_options_titles": titles_opt,
                "rank_math_options_sitemap": sitemap_opt,
                "categories": terms,
                "page_7463_note": "service page — sitemap duplicate documented only",
            },
            ensure_ascii=False,
            indent=2,
        ),
        encoding="utf-8",
    )
    print(f"Backup written: {backup_path}")

    changes: list[dict] = []

    # --- Rank Math: global templates (fix broken category template + services archive) ---
    seo_updates = {
        "tax_category_title": "%term% %sep% %sitename%",
        "tax_category_description": "%term_description%",
        "pt_services_title": "خدماتنا في الإمارات %sep% %sitename%",
        "pt_services_description": (
            "خدمات ركن التطور في الإمارات: تنظيف منازل وفلل، صيانة، عزل وكشف تسربات، "
            "تركيبات، مكافحة حشرات، طاقة شمسية والمزيد. تواصل 0586634710."
        ),
    }
    if SKIP_SEO:
        seo_updates = {}
    for key, val in seo_updates.items():
        old = titles_opt.get(key)
        if old == val:
            continue
        wp_cli(
            f"wp option patch update rank-math-options-titles {key} "
            + json.dumps(val, ensure_ascii=False)
        )
        changes.append(
            {
                "type": "rank_math_option",
                "key": key,
                "before": old,
                "after": val,
                "rollback": f"wp option patch update rank-math-options-titles {key} "
                + json.dumps(old, ensure_ascii=False),
            }
        )

    # --- Media alt (sample batch first, then remainder) ---
    missing = [
        m
        for m in media
        if not (m.get("alt_text") or "").strip() and m["id"] not in SKIP_MEDIA_IDS
    ]
    parent_titles = load_parent_titles(missing)
    proposals = []
    for m in missing:
        alt = build_alt(m, parent_titles)
        if alt:
            proposals.append({"id": m["id"], "alt": alt, "url": m.get("source_url")})

    # Apply in batches of 25 with short pause
    applied = 0
    failed = 0
    if MEDIA_LIMIT > 0:
        proposals = proposals[:MEDIA_LIMIT]
    for i, p in enumerate(proposals):
        ok, msg = update_media_alt(p["id"], p["alt"])
        entry = {
            "type": "media_alt",
            "id": p["id"],
            "after": p["alt"],
            "url": p["url"],
            "success": ok,
            "detail": msg,
            "rollback": f"wp post meta update {p['id']} _wp_attachment_image_alt ''",
        }
        # store previous from backup
        for snap in json.loads(backup_path.read_text())["media_alt_snapshot"]:
            if snap["id"] == p["id"]:
                entry["before"] = snap.get("alt_text") or ""
                if entry["before"]:
                    entry["rollback"] = (
                        f"wp post meta update {p['id']} _wp_attachment_image_alt "
                        + json.dumps(entry["before"], ensure_ascii=False)
                    )
                break
        changes.append(entry)
        if ok:
            applied += 1
        else:
            failed += 1
        if (i + 1) % 25 == 0:
            time.sleep(1)
        else:
            time.sleep(0.12)

    with CHANGELOG_PATH.open("a", encoding="utf-8") as f:
        for c in changes:
            f.write(json.dumps(c, ensure_ascii=False) + "\n")

    summary = {
        "backup": str(backup_path),
        "media_missing_before": len(missing),
        "media_alt_applied": applied,
        "media_alt_failed": failed,
        "seo_option_changes": sum(1 for c in changes if c["type"] == "rank_math_option"),
        "category_meta_changes": 0,
    }
    print(json.dumps(summary, ensure_ascii=False, indent=2))


if __name__ == "__main__":
    main()
