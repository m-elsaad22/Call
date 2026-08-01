#!/usr/bin/env python3
"""Rename call recordings to: {ContactNameOrPhone}_{YYYYMMDD-HHMMSS}.amr

Reads contacts from a VCF file. If the phone number exists in contacts,
the contact display name is used; otherwise the raw phone number is kept.
Files without a phone number become Unknown_{datetime}.amr.
"""

from __future__ import annotations

import argparse
import quopri
import re
import shutil
import unicodedata
import zipfile
from collections import defaultdict
from pathlib import Path

DEFAULT_VCF = "vcards_20260801_183835.vcf"
CALL_DIRS = ["call1", "call2", "call2a", "call3"]


def decode_qp(value: str) -> str:
    value = value.replace("=\r\n", "").replace("=\n", "").replace("=\r", "")
    try:
        return quopri.decodestring(value.encode("latin1", errors="ignore")).decode(
            "utf-8", errors="replace"
        )
    except Exception:
        return value


def unfold_vcard(card: str) -> str:
    card = re.sub(r"\r\n[ \t]", "", card)
    card = re.sub(r"\n[ \t]", "", card)
    return card.replace("=\r\n", "").replace("=\n", "")


def normalize_phone(p: str) -> set[str]:
    d = re.sub(r"\D", "", p or "")
    if not d:
        return set()
    keys = {d}
    if d.startswith("00"):
        d = d[2:]
        keys.add(d)
    if d.startswith("20") and len(d) >= 11:
        keys.add(d)
        keys.add("0" + d[2:])
    elif d.startswith("0") and len(d) >= 10:
        keys.add(d)
        keys.add("20" + d[1:])
    elif d.startswith(("971", "966", "974", "965", "973", "968")):
        keys.add(d)
        keys.add("0" + d[3:])
    if len(d) >= 9:
        keys.add(d[-9:])
    return {k for k in keys if k}


def parse_contacts(vcf_path: Path) -> dict[str, str]:
    text = vcf_path.read_text(encoding="utf-8", errors="replace")
    cards = re.split(r"BEGIN:VCARD\s*", text, flags=re.I)[1:]
    phone_to_name: dict[str, str] = {}
    for card in cards:
        unfolded = unfold_vcard(card)
        fn = None
        for line in unfolded.splitlines():
            if line.upper().startswith("FN"):
                if ":" not in line:
                    continue
                params, val = line.split(":", 1)
                if "QUOTED-PRINTABLE" in params.upper():
                    fn = decode_qp(val)
                else:
                    fn = val.strip()
                break
        if not fn:
            continue
        fn = unicodedata.normalize("NFC", fn).replace("\ufffd", "").strip()
        if not fn:
            continue
        for tel in re.findall(r"^TEL[^:]*:(.+)$", unfolded, flags=re.M | re.I):
            for key in normalize_phone(tel.strip()):
                phone_to_name[key] = fn
    return phone_to_name


def lookup_name(phone: str, phone_to_name: dict[str, str]) -> str | None:
    for key in sorted(normalize_phone(phone), key=len, reverse=True):
        if key in phone_to_name:
            return phone_to_name[key]
    return None


def sanitize_filename(name: str) -> str:
    name = unicodedata.normalize("NFC", name).strip()
    # Drop variation selectors left behind after removing emoji.
    name = name.replace("\ufe0f", "").replace("\ufe0e", "")
    out: list[str] = []
    for c in name:
        cat = unicodedata.category(c)
        if cat in ("Cc", "Cf", "Cn", "Co", "Cs"):
            continue
        if cat.startswith("So") or cat.startswith("Sk"):
            continue
        if c in '\\/:*?"<>|':
            out.append("_")
        elif c.isspace():
            out.append(" ")
        else:
            out.append(c)
    name = re.sub(r"\s+", " ", "".join(out)).strip(" ._")
    name = re.sub(r"_+", "_", name)
    return name or "Unknown"


def parse_filename(fname: str):
    m = re.match(r"^phone_(\d{8}-\d{6})(?:_{1,2}(.+))?\.amr$", fname, flags=re.I)
    if not m:
        return None
    return m.group(1), m.group(2)


def reextract(root: Path) -> None:
    for dname in CALL_DIRS:
        dpath = root / dname
        zpath = root / f"{dname}.zip"
        if not zpath.exists():
            raise FileNotFoundError(zpath)
        if dpath.exists():
            shutil.rmtree(dpath)
        dpath.mkdir()
        with zipfile.ZipFile(zpath, "r") as zf:
            zf.extractall(dpath)


def rename_dir(dpath: Path, phone_to_name: dict[str, str], stats: dict) -> None:
    planned: list[tuple[Path, str]] = []
    for f in sorted(dpath.glob("*.amr")):
        parsed = parse_filename(f.name)
        if not parsed:
            # Already renamed, or unexpected pattern — leave as-is.
            stats["skipped"] += 1
            continue
        dt, phone = parsed
        if phone:
            name = lookup_name(phone, phone_to_name)
            if name:
                label = sanitize_filename(name)
                stats["matched_contact"] += 1
            else:
                label = sanitize_filename(phone)
                stats["kept_phone"] += 1
        else:
            label = "Unknown"
            stats["no_phone"] += 1
        planned.append((f, f"{label}_{dt}"))

    temp_map: list[tuple[Path, str]] = []
    for i, (f, new_base) in enumerate(planned):
        tmp = dpath / f".__renaming_{i:05d}__.amr"
        f.rename(tmp)
        temp_map.append((tmp, new_base))

    used: set[str] = set()
    for tmp, new_base in temp_map:
        base = new_base
        final = dpath / f"{base}.amr"
        n = 2
        while final.name.lower() in used or final.exists():
            base = f"{new_base}_{n}"
            final = dpath / f"{base}.amr"
            n += 1
        used.add(final.name.lower())
        tmp.rename(final)
        stats["renamed"] += 1


def rebuild_zips(root: Path) -> None:
    for dname in CALL_DIRS:
        dpath = root / dname
        zpath = root / f"{dname}.zip"
        zpath.unlink(missing_ok=True)
        with zipfile.ZipFile(zpath, "w", compression=zipfile.ZIP_DEFLATED) as zf:
            for f in sorted(dpath.glob("*.amr")):
                zf.write(f, arcname=f.name)


def main() -> None:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument("--root", type=Path, default=Path.cwd())
    parser.add_argument("--vcf", type=Path, default=None)
    parser.add_argument(
        "--from-zip",
        action="store_true",
        help="Re-extract call*.zip before renaming (expects original phone_*.amr names).",
    )
    parser.add_argument(
        "--rebuild-zip",
        action="store_true",
        help="Rebuild call*.zip from renamed folders.",
    )
    args = parser.parse_args()
    root: Path = args.root
    vcf = args.vcf or (root / DEFAULT_VCF)
    if not vcf.exists():
        raise SystemExit(f"VCF not found: {vcf}")

    if args.from_zip:
        print("Re-extracting archives...")
        reextract(root)

    phone_to_name = parse_contacts(vcf)
    print(
        f"Loaded {len(set(phone_to_name.values()))} contact names, "
        f"{len(phone_to_name)} phone keys from {vcf.name}"
    )

    stats: dict[str, int] = defaultdict(int)
    for dname in CALL_DIRS:
        dpath = root / dname
        if not dpath.is_dir():
            print(f"Skip missing dir: {dname}")
            continue
        rename_dir(dpath, phone_to_name, stats)
        print(f"{dname}: {len(list(dpath.glob('*.amr')))} files")

    print("Stats:", dict(stats))
    if args.rebuild_zip:
        print("Rebuilding zip archives...")
        rebuild_zips(root)
        for dname in CALL_DIRS:
            z = root / f"{dname}.zip"
            print(f"  {z.name}: {z.stat().st_size / 1e6:.1f} MB")


if __name__ == "__main__":
    main()
