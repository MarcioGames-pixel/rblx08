from pathlib import Path
from urllib.request import Request, urlopen
from urllib.error import URLError, HTTPError
import hashlib, re, time, zipfile

OUT = Path("wayback-build")
PAGES = {
 "Default.aspx.html": ("20080115211947", "http://www.roblox.com/Default.aspx"),
 "Games.aspx.html": ("20080108011601", "http://roblox.com/Games.aspx"),
 "Browse.aspx.html": ("20080116125715", "http://roblox.com/Browse.aspx"),
 "Catalog.aspx.html": ("20080108225907", "http://www.roblox.com/Catalog.aspx"),
 "Item.aspx.html": ("20080107041042", "http://www.roblox.com/Item.aspx?ID=130991"),
 "User.aspx.html": ("20080103082717", "http://www.roblox.com/User.aspx?ID=16"),
 "Place.aspx.html": ("20080111082826", "http://www.roblox.com/Place.aspx"),
 "ShowForum.aspx.html": ("20080115072721", "http://www.roblox.com/ShowForum.aspx"),
 "ShowPost.aspx.html": ("20080102233447", "http://www.roblox.com/ShowPost.aspx"),
 "Parents.aspx.html": ("20071229054922", "http://www.roblox.com/Parents.aspx"),
 "www.roblox.com.html": ("20080820125340", "http://www.roblox.com/"),
}
OUT.mkdir(exist_ok=True)
(OUT/"pages").mkdir(exist_ok=True)
(OUT/"assets").mkdir(exist_ok=True)

def get(url):
    req=Request(url, headers={"User-Agent":"Mozilla/5.0 (compatible; rblx08-archive-import/1.0)"})
    with urlopen(req, timeout=35) as r:
        return r.read()

def save_asset(url):
    raw=url.replace("&amp;","&")
    if raw.startswith("/web/"):
        raw="https://web.archive.org"+raw
    if not raw.startswith("https://web.archive.org/web/"):
        return
    key=hashlib.sha256(raw.encode()).hexdigest()[:20]
    ext=".bin"
    m=re.search(r"\.(css|js|png|jpe?g|gif|ico|swf)(?:[?#]|$)",raw,re.I)
    if m: ext="."+m.group(1).lower()
    dest=OUT/"assets"/(key+ext)
    if dest.exists(): return
    try:
        data=get(raw)
        if len(data)>8_000_000: return
        dest.write_bytes(data)
        time.sleep(.15)
    except (HTTPError,URLError,TimeoutError,ValueError) as e:
        print("asset skipped:",raw,str(e)[:120])

all_pages=[]
for name,(stamp,original) in PAGES.items():
    replay=f"https://web.archive.org/web/{stamp}/{original}"
    try:
        data=get(replay)
        (OUT/"pages"/name).write_bytes(data)
        html=data.decode("utf-8","replace")
        all_pages.append((name,html))
        print("page OK",name,len(data))
    except Exception as e:
        print("page FAILED",name,str(e)[:160])

pattern=re.compile(r'''(?:https?://web\.archive\.org)?/web/\d{6,14}(?:im_|cs_|js_|oe_)/[^"'<> )]+''',re.I)
seen=set()
for name,html in all_pages:
    for u in pattern.findall(html):
        if u not in seen:
            seen.add(u); save_asset(u)
for name,(stamp,original) in PAGES.items():
    host="www.roblox.com" if "www." in original else "roblox.com"
    save_asset(f"https://web.archive.org/web/{stamp}cs_/http://{host}/CSS/AllCSS.ashx")

(OUT/"CAPTURES.txt").write_text(
 "Wayback source captures for the Roblox 2008 revival.\n"+
 "\n".join(f"{name} | {stamp} | {url}" for name,(stamp,url) in PAGES.items())+
 "\nPages are archival reference material, not executable application routes.\n"
 "Some Wayback captures/assets may be unavailable or incomplete.\n",encoding="utf-8")
with zipfile.ZipFile("rblx08-wayback-originals.zip","w",zipfile.ZIP_DEFLATED) as z:
    for p in OUT.rglob("*"):
        if p.is_file(): z.write(p,p.relative_to(OUT))
print("ZIP created: rblx08-wayback-originals.zip")
