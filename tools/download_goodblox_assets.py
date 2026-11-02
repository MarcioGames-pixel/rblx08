#!/usr/bin/env python3
"""Download Goodblox's public static image assets and legacy stylesheet.

Run from the rblx08 repository root:
  python3 tools/download_goodblox_assets.py
  python3 tools/download_goodblox_assets.py --dry-run
  python3 tools/download_goodblox_assets.py --overwrite

Only static files under images/ and AllCSSnew.css are downloaded.
The original directory structure is preserved.
"""
from __future__ import annotations

import argparse
import hashlib
import json
import sys
import time
from pathlib import Path, PurePosixPath
from urllib.error import HTTPError, URLError
from urllib.request import Request, urlopen

OWNER = "g1-1-1"
REPOSITORY = "goodblox"
BRANCH = "main"
API_TREE = f"https://api.github.com/repos/{OWNER}/{REPOSITORY}/git/trees/{BRANCH}?recursive=1"
RAW_BASE = f"https://raw.githubusercontent.com/{OWNER}/{REPOSITORY}/{BRANCH}/"
ROOT = Path(__file__).resolve().parents[1]
MANIFEST = ROOT / "assets" / "goodblox-manifest.json"
USER_AGENT = "rblx08-static-asset-importer/1.0"


def request_bytes(url: str) -> bytes:
    req = Request(url, headers={"User-Agent": USER_AGENT, "Accept": "application/vnd.github+json"})
    with urlopen(req, timeout=40) as response:
        return response.read()


def safe_target(relative: str) -> Path:
    parts = PurePosixPath(relative).parts
    if not parts or any(part in ("", ".", "..") for part in parts):
        raise ValueError(f"Unsafe repository path: {relative}")
    if relative != "AllCSSnew.css" and not relative.startswith("images/"):
        raise ValueError(f"Unexpected asset path: {relative}")
    return ROOT.joinpath(*parts)


def main() -> int:
    parser = argparse.ArgumentParser(description="Download Goodblox images and AllCSSnew.css")
    parser.add_argument("--overwrite", action="store_true", help="Replace existing files")
    parser.add_argument("--dry-run", action="store_true", help="List files without downloading")
    args = parser.parse_args()

    try:
        tree = json.loads(request_bytes(API_TREE).decode("utf-8"))
    except (HTTPError, URLError, TimeoutError, json.JSONDecodeError) as exc:
        print(f"Could not read GitHub tree: {exc}", file=sys.stderr)
        return 1

    if tree.get("truncated"):
        print("GitHub returned a truncated tree; stopping to avoid an incomplete import.", file=sys.stderr)
        return 1

    paths = sorted(
        item["path"] for item in tree.get("tree", [])
        if item.get("type") == "blob"
        and (item["path"] == "AllCSSnew.css" or item["path"].startswith("images/"))
        and not item["path"].endswith("/.htaccess")
    )

    manifest = []
    downloaded = 0
    skipped = 0
    for index, relative in enumerate(paths, 1):
        target = safe_target(relative)
        if target.exists() and not args.overwrite:
            print(f"[{index}/{len(paths)}] SKIP {relative}")
            skipped += 1
            continue
        if args.dry_run:
            print(f"[{index}/{len(paths)}] WOULD DOWNLOAD {relative}")
            continue
        try:
            data = request_bytes(RAW_BASE + relative)
            target.parent.mkdir(parents=True, exist_ok=True)
            target.write_bytes(data)
        except (HTTPError, URLError, TimeoutError, OSError) as exc:
            print(f"FAILED {relative}: {exc}", file=sys.stderr)
            continue
        manifest.append({
            "source": f"https://github.com/{OWNER}/{REPOSITORY}/blob/{BRANCH}/{relative}",
            "path": relative,
            "bytes": len(data),
            "sha256": hashlib.sha256(data).hexdigest(),
        })
        downloaded += 1
        print(f"[{index}/{len(paths)}] OK {relative} ({len(data)} bytes)")
        time.sleep(0.03)

    if not args.dry_run:
        MANIFEST.parent.mkdir(parents=True, exist_ok=True)
        MANIFEST.write_text(json.dumps(manifest, indent=2, ensure_ascii=False) + "\n", encoding="utf-8")
        print(f"Manifest saved: {MANIFEST.relative_to(ROOT)}")
    print(f"Finished: {downloaded} downloaded, {skipped} already present, {len(paths)} found.")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
