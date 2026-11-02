#!/usr/bin/env python3
"""Download the ROBLOX logo referenced by the 2008-02-23 Wayback homepage."""
from pathlib import Path
from html.parser import HTMLParser
from urllib.parse import urljoin, urlparse
from urllib.request import Request, urlopen
import sys
ROOT = Path(__file__).resolve().parents[1]
STAMP = "20080223140549"
PAGE = "http://www.roblox.com/"
ARCHIVE = "https://web.archive.org/web/" + STAMP + "id_/" + PAGE
OUT = ROOT / "images" / "roblox_logo_2008.png"
class Parser(HTMLParser):
    def __init__(self):
        super().__init__()
        self.images = []
    def handle_starttag(self, tag, attrs):
        attrs = dict(attrs)
        if tag.lower() == "img" and attrs.get("src"): self.images.append(attrs)
def fetch(url):
    req = Request(url, headers={"User-Agent": "rblx08-wayback-logo-importer/1.0"})
    with urlopen(req, timeout=45) as response:
        return response.read(), response.headers.get("Content-Type", "")
def main():
    try: html, _ = fetch(ARCHIVE)
    except Exception as exc:
        print("Wayback homepage request failed:", exc, file=sys.stderr)
        return 1
    parser = Parser()
    parser.feed(html.decode("utf-8", errors="replace"))
    ranked = []
    for item in parser.images:
        words = " ".join(str(item.get(k, "")) for k in ("src", "alt", "title", "id", "class")).lower()
        score = (12 if "logo" in words else 0) + (8 if "roblox" in words else 0)
        score += 3 if "header" in words else 0
        score -= 10 if any(x in words for x in ("goodblox", "signup", "banner", "button")) else 0
        if score > 0: ranked.append((score, item))
    ranked.sort(key=lambda pair: pair[0], reverse=True)
    if not ranked:
        print("No logo-like image found in archived HTML.", file=sys.stderr)
        for item in parser.images: print(item.get('src'), item.get('alt', ''), file=sys.stderr)
        return 2
    for score, item in ranked:
        src = item["src"]
        if src.startswith("//"): src = "http:" + src
        if "web.archive.org/web/" in src: url = src
        else:
            absolute = urljoin(PAGE, src)
            parsed = urlparse(absolute)
            if parsed.netloc.endswith("roblox.com"): url = "https://web.archive.org/web/" + STAMP + "id_/" + absolute
            else: url = absolute
        try: data, mime = fetch(url)
        except Exception as exc:
            print("Candidate failed:", src, exc, file=sys.stderr)
            continue
        if not mime.startswith("image/") and not data.startswith((bytes.fromhex("89504e47"), bytes.fromhex("ffd8ff"), b"GIF8")): continue
        OUT.parent.mkdir(parents=True, exist_ok=True)
        OUT.write_bytes(data)
        print("Saved:", OUT.relative_to(ROOT))
        print("Source:", url)
        return 0
    print("No logo candidate could be downloaded as an image.", file=sys.stderr)
    return 3
if __name__ == "__main__": raise SystemExit(main())
