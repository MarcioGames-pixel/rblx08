#!/usr/bin/env python3
"""Import static assets from a local Goodblox checkout or ZIP into rblx08.

This intentionally imports only static files, never PHP/application code.
Use it only with assets you own or are authorized to reuse.
"""
from __future__ import annotations

import argparse
import hashlib
import json
import shutil
import sys
import zipfile
from pathlib import Path, PurePosixPath

ALLOWED_EXTENSIONS = {
    ".css", ".js", ".png", ".jpg", ".jpeg", ".gif", ".webp", ".bmp",
    ".ico", ".svg", ".woff", ".woff2", ".ttf", ".otf", ".eot"
}
ALLOWED_TOP_LEVEL = {
    "images", "image", "css", "js", "scripts", "fonts", "content",
    "favicon.ico"
}


def sha256(data: bytes) -> str:
    return hashlib.sha256(data).hexdigest()


def safe_relative(name: str) -> Path | None:
    normalized = name.replace("\\", "/").lstrip("/")
    parts = PurePosixPath(normalized).parts
    if not parts or any(part in ("", ".", "..") for part in parts):
        return None
    if any(part in ("", ".", "..") for part in parts):
        return None
    start = next(
        (i for i, part in enumerate(parts)
         if part.lower() in ALLOWED_TOP_LEVEL),
        None
    )
    if start is None:
        return None
    relative = parts[start:]
    if len(relative) == 1 and not relative[0].lower().endswith(".ico"):
        return None
    if Path(relative[-1]).suffix.lower() not in ALLOWED_EXTENSIONS:
        return None
    return Path(*relative)


def import_directory(source: Path, destination: Path, overwrite: bool) -> list[dict]:
    manifest = []
    for file in source.rglob("*"):
        if not file.is_file() or file.is_symlink():
            continue
        try:
            relative = file.relative_to(source)
        except ValueError:
            continue
        rel = safe_relative(relative.as_posix())
        if rel is None:
            continue
        data = file.read_bytes()
        target = destination / rel
        if target.exists() and not overwrite:
            print(f"SKIP existing: {rel}")
            continue
        target.parent.mkdir(parents=True, exist_ok=True)
        target.write_bytes(data)
        manifest.append({
            "path": rel.as_posix(),
            "size": len(data),
            "sha256": sha256(data),
        })
        print(f"IMPORTED {rel} ({len(data)} bytes)")
    return manifest


def import_zip(source: Path, destination: Path, overwrite: bool) -> list[dict]:
    manifest = []
    with zipfile.ZipFile(source) as archive:
        for info in archive.infolist():
            if info.is_dir():
                continue
            rel = safe_relative(info.filename)
            if rel is None:
                continue
            data = archive.read(info)
            target = destination / rel
            if target.exists() and not overwrite:
                print(f"SKIP existing: {rel}")
                continue
            target.parent.mkdir(parents=True, exist_ok=True)
            target.write_bytes(data)
            manifest.append({
                "path": rel.as_posix(),
                "size": len(data),
                "sha256": sha256(data),
            })
            print(f"IMPORTED {rel} ({len(data)} bytes)")
    return manifest


def main() -> int:
    parser = argparse.ArgumentParser(
        description="Copy authorized Goodblox static assets into rblx08."
    )
    parser.add_argument("source", type=Path, help="Local Goodblox folder or ZIP file")
    parser.add_argument(
        "--destination", type=Path, default=Path("assets/goodblox"),
        help="Destination folder (default: assets/goodblox)"
    )
    parser.add_argument(
        "--overwrite", action="store_true",
        help="Replace files already present in the destination"
    )
    args = parser.parse_args()

    source = args.source.expanduser().resolve()
    destination = args.destination
    if not source.exists():
        print(f"Source not found: {source}", file=sys.stderr)
        return 2
    if source.is_file() and zipfile.is_zipfile(source):
        manifest = import_zip(source, destination, args.overwrite)
    elif source.is_dir():
        manifest = import_directory(source, destination, args.overwrite)
    else:
        print("Source must be a directory or a ZIP archive.", file=sys.stderr)
        return 2

    destination.mkdir(parents=True, exist_ok=True)
    manifest_path = destination / "assets-manifest.json"
    manifest_path.write_text(
        json.dumps(manifest, indent=2, ensure_ascii=False) + "\n",
        encoding="utf-8"
    )
    print(f"Done: {len(manifest)} asset(s) imported.")
    print(f"Manifest: {manifest_path}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
