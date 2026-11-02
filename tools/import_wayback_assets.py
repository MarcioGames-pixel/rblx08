#!/usr/bin/env python3
"""Mirror CSS, JavaScript and image files linked by the Roblox homepage archived in 2008."""
from html.parser import HTMLParser
from pathlib import Path
from urllib.parse import urljoin, urlparse, unquote
from urllib.request import Request, urlopen
import json, re, sys

ROOT=Path(__file__).resolve().parents[1]
STAMP="20080223140549"
ORIGIN="http://www.roblox.com/"
PAGE=f"https://web.archive.org/web/{STAMP}id_/{ORIGIN}"
OUT=ROOT/"public"/"wayback"
EXTS={".css",".js",".png",".jpg",".jpeg",".gif",".ico",".svg",".webp",".ashx"}
class PageParser(HTMLParser):
 def __init__(self): super().__init__(); self.refs=[]; self.logos=[]
 def handle_starttag(self,tag,attrs):
  a=dict(attrs)
  for k in ("src","href","data-src"):
   if a.get(k): self.refs.append((tag,k,a[k],a))
  hint=" ".join(str(a.get(k,"")) for k in ("src","alt","title","id","class")).lower()
  if tag=="img" and ("logo" in hint or "roblox" in hint): self.logos.append(a.get("src"))
def fetch(url):
 req=Request(url,headers={"User-Agent":"rblx08-wayback-assets/1.0"})
 with urlopen(req,timeout=45) as r:return r.read(),r.headers.get("Content-Type","")
def normalize(value,base):
 value=value.strip()
 if not value or value.startswith(("data:","javascript:","#","mailto:")):return None
 u=urljoin(base,value);p=urlparse(u)
 if p.scheme not in ("http","https"):return None
 h=p.netloc.lower()
 if not (h.endswith("roblox.com") or h.endswith("rbxcdn.com")):return None
 return u
def archive(u):return f"https://web.archive.org/web/{STAMP}id_/{u}"
def target(u,mime=""):
 p=urlparse(u);name=unquote(p.path).lstrip("/") or "index"
 if "text/css" in mime and Path(name).suffix.lower() not in (".css",):name += ".css"
 name=re.sub(r"[^A-Za-z0-9._/-]","_",name)
 if p.query:name+="_q"+str(abs(hash(p.query)))
 return OUT/p.netloc/name
def main():
 try:html,_=fetch(PAGE)
 except Exception as e:print("Could not read archived homepage:",e,file=sys.stderr);return 1
 parser=PageParser();parser.feed(html.decode("utf-8",errors="replace"))
 queue=[];styles=[];scripts=[];logo_candidates=[]
 for tag,key,value,attrs in parser.refs:
  u=normalize(value,ORIGIN)
  if not u:continue
  ext=Path(urlparse(u).path).suffix.lower()
  if ext in EXTS or tag in ("img","script","link"):queue.append(u)
  if tag=="link" and (attrs.get("rel","").lower()=="stylesheet" or ext in (".css",".ashx")):styles.append(u)
  if tag=="script" and ext==".js":scripts.append(u)
 for value in parser.logos:
  if value:
   u=normalize(value,ORIGIN)
   if u:logo_candidates.append(u)
 seen=set();downloaded={};css_sources={}
 while queue:
  u=queue.pop(0)
  if u in seen:continue
  seen.add(u)
  try:data,mime=fetch(archive(u))
  except Exception as e:print("Skipped unavailable archive asset:",u,e);continue
  is_css="text/css" in mime or Path(urlparse(u).path).suffix.lower() in (".css",".ashx") and b"{" in data[:2048]
  dest=target(u,"text/css" if is_css else mime);dest.parent.mkdir(parents=True,exist_ok=True)
  if is_css:
   text=data.decode("utf-8",errors="replace");css_sources[u]=text
   for raw in re.findall(r"url\(\s*['\"]?([^'\")]+)",text,re.I):
    child=normalize(raw,u)
    if child and child not in seen:queue.append(child)
   for raw in re.findall(r"@import\s+(?:url\()?['\"]([^'\"]+)",text,re.I):
    child=normalize(raw,u)
    if child and child not in seen:queue.append(child)
   dest.write_text(text,encoding="utf-8")
  else:dest.write_bytes(data)
  downloaded[u]="/wayback/"+str(dest.relative_to(OUT)).replace("\\","/")
 # Rewrite CSS resource URLs to local mirrored paths.
 for u,text in css_sources.items():
  dest=ROOT/"public"/downloaded[u].lstrip("/")
  def repl(m):
   raw=m.group(2);child=normalize(raw,u)
   return "url("+downloaded.get(child,raw)+")"
  text=re.sub(r"url\(\s*(['\"]?)(.*?)\1\s*\)",repl,text,flags=re.I)
  dest.write_text(text,encoding="utf-8")
 logo=None
 candidates=logo_candidates+[u for u in downloaded if "logo" in urlparse(u).path.lower()]
 for u in candidates:
  local=downloaded.get(u)
  if not local:continue
  src=ROOT/"public"/local.lstrip("/")
  ext=Path(urlparse(u).path).suffix.lower()
  if ext not in (".png",".gif",".jpg",".jpeg",".svg",".webp"):continue
  dest=ROOT/"public"/"images"/("roblox-logo-2008"+ext)
  dest.parent.mkdir(parents=True,exist_ok=True);dest.write_bytes(src.read_bytes())
  logo="/images/"+dest.name;break
 (OUT/"stylesheets.json").write_text(json.dumps([downloaded[u] for u in styles if u in downloaded],indent=2),encoding="utf-8")
 (OUT/"scripts.json").write_text(json.dumps([downloaded[u] for u in scripts if u in downloaded],indent=2),encoding="utf-8")
 (OUT/"manifest.json").write_text(json.dumps({"capture":STAMP,"source":PAGE,"logo":logo,"assets":downloaded},indent=2),encoding="utf-8")
 print(f"Mirrored {len(downloaded)} assets; logo: {logo or 'not identified'}")
 return 0
if __name__=="__main__":raise SystemExit(main())
