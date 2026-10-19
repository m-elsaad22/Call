#!/usr/bin/env python3
"""Read-only theme/plugin file inspection via WPVibe REST."""
import base64
import json
import re
import urllib.parse
import urllib.request
from pathlib import Path

import os

AUTH = os.environ.get("WP_APP_AUTH", "")
API = "https://www.rukn-eltatawer.com/wp-json/wpvibe/v1"
OUT = Path("/workspace/audit/output")


def req(method, path, data=None):
    url = API + path
    body = None
    headers = {
        "Authorization": "Basic " + base64.b64encode(AUTH.encode()).decode(),
        "User-Agent": "RuknAudit/1.0",
    }
    if data is not None:
        body = json.dumps(data).encode()
        headers["Content-Type"] = "application/json"
    r = urllib.request.Request(url, data=body, headers=headers, method=method)
    with urllib.request.urlopen(r, timeout=60) as resp:
        return json.loads(resp.read())


def list_dir(path="."):
    return req("GET", "/file/list?" + urllib.parse.urlencode({"path": path}))


def read_file(path):
    return req("GET", "/file/read?" + urllib.parse.urlencode({"path": path}))


KEY_FILES = [
    "functions.php",
    "components/packs/kayan-cpt/setup.php",
    "components/packs/kayan-seo/setup.php",
    "components/packs/kayan-routing/setup.php",
    "header.php",
    "footer.php",
    "index.php",
]


def scan_php(code, path):
    findings = []
    patterns = [
        (r"\beval\s*\(", "eval() usage", "high"),
        (r"base64_decode\s*\(", "base64_decode", "medium"),
        (r"\$_(GET|POST|REQUEST)\[", "superglobal input", "info"),
        (r"wp_redirect\s*\(", "redirect", "info"),
        (r"noindex", "noindex reference", "info"),
        (r"register_post_type\s*\(", "CPT registration", "info"),
        (r"add_rewrite_rule\s*\(", "rewrite rule", "info"),
        (r"TEMP|FIXME|DEBUG|var_dump|print_r\s*\(", "debug code", "medium"),
    ]
    for pat, label, sev in patterns:
        if re.search(pat, code):
            findings.append({"file": path, "signal": label, "severity": sev})
    return findings


def walk_files(base=".", max_files=200):
    collected = []
    try:
        listing = list_dir(base)
    except Exception as e:
        return collected, str(e)
    for ent in listing.get("files", []):
        p = ent["path"]
        if ent["type"] == "directory":
            sub, _ = walk_files(p, max_files=max_files - len(collected))
            collected.extend(sub)
            if len(collected) >= max_files:
                break
        else:
            if ent.get("extension") in ("php", "js", "css"):
                collected.append(p)
        if len(collected) >= max_files:
            break
    return collected, None


def main():
    OUT.mkdir(parents=True, exist_ok=True)
    site = req("GET", "/site-info")
    findings = []
    file_contents_meta = []
    for rel in KEY_FILES:
        try:
            data = read_file(rel)
            code = data.get("content") or data.get("body") or ""
            if isinstance(code, str) and code:
                findings.extend(scan_php(code, rel))
                file_contents_meta.append({"path": rel, "bytes": len(code)})
        except Exception as e:
            file_contents_meta.append({"path": rel, "error": str(e)})

    php_files, walk_err = walk_files(".", max_files=120)
    report = {
        "theme": site.get("active_theme"),
        "plugins": site.get("plugins"),
        "key_files": file_contents_meta,
        "code_signals": findings,
        "php_files_sampled": len(php_files),
        "walk_error": walk_err,
    }
    (OUT / "theme_audit.json").write_text(json.dumps(report, ensure_ascii=False, indent=2), encoding="utf-8")
    print(json.dumps(report, ensure_ascii=False, indent=2)[:4000])


if __name__ == "__main__":
    main()
