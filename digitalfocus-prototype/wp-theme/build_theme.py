"""Regenerate (python3 wp-theme/build_theme.py) after changing listed*.html.

Generate the data parts of the df-listed WordPress theme from the prototype pages:
CSS for both colour options, seed content (pages, news) in Thai and English, and images."""
import re,json,os,shutil
HERE=os.path.dirname(os.path.abspath(__file__))
SP=os.path.join(HERE,'..')+'/'   # prototype folder: listed.html, listed-en.html, listed-df.html, img/
T=os.path.join(HERE,'df-listed')+'/'
os.makedirs(T+'assets/css',exist_ok=True); os.makedirs(T+'assets/img',exist_ok=True); os.makedirs(T+'data',exist_ok=True)
SLUG={'home':'','about':'about','business':'business','solutions':'solutions','brands':'products','projects':'projects','esg':'sustainability','news':'news','contact':'contact'}
SUB={'biz-dist':'business','biz-si':'business','biz-platform':'business','biz-service':'business','ir':'contact','partner':'contact','showcase':'solutions'}
WP_CSS='''
/* WordPress additions */
.nav ul li,.mnav li{list-style:none}
.mnav ul{list-style:none;margin:0;padding:0;display:grid;gap:4px}
.nav .current-menu-item>a,.nav .current_page_item>a{color:var(--accent);background:var(--accent-soft);font-weight:600}
.mnav .current-menu-item>a,.mnav .current_page_item>a{background:var(--accent-soft);color:var(--accent);font-weight:600}
body.admin-bar .hdr{top:32px}
@media(max-width:782px){body.admin-bar .hdr{top:46px}}
.screen-reader-text{position:absolute!important;width:1px;height:1px;overflow:hidden;clip:rect(1px,1px,1px,1px);white-space:nowrap}
.fsend[disabled]{opacity:.6;pointer-events:none}
.hp-field{position:absolute!important;left:-9999px!important}
.entry{max-width:780px}
.entry h2{font-size:clamp(24px,3vw,32px);color:var(--navy-800);margin:36px 0 14px}
.entry p{margin:0 0 16px;color:var(--ink-70);font-size:17px}
.entry img{border-radius:var(--r);margin:22px 0}
.post-meta{font-size:14px;color:var(--ink-45);margin-bottom:24px}
.pager{display:flex;gap:10px;justify-content:center;margin-top:36px}
'''
def css_of(f):
    s=open(SP+f,encoding='utf-8').read()
    c=s[s.index('<style>')+7:s.index('</style>')]
    c=c.replace('body[data-page="solutions"] #solutions','.df-page-solutions #solutions')
    return c.replace('url(img/','url(../img/')+WP_CSS
open(T+'assets/css/site-a.css','w',encoding='utf-8').write(css_of('listed.html'))
open(T+'assets/css/site-b.css','w',encoding='utf-8').write(css_of('listed-df.html'))

def strip_data_only(html,page):
    out=html
    while True:
        m=re.search(r'<(\w+)([^>]*?)\sdata-only="([^"]*)"([^>]*)>',out)
        if not m: return out
        tag=m.group(1); start=m.start(); i=m.end(); depth=1
        pat=re.compile(r'<(/?)'+tag+r'\b[^>]*>')
        for t in pat.finditer(out,i):
            depth+= -1 if t.group(1) else 1
            if depth==0: end=t.end(); break
        if m.group(3)==page:
            out=out[:start]+f'<{tag}{m.group(2)}{m.group(4)}>'+out[i:]
        else:
            out=out[:start]+out[end:]

def links(html):
    def r(m):
        k=m.group(1)
        if k=='home': return 'href="{{url:home}}"'
        if k in SLUG: return 'href="{{url:%s}}"'%k
        if k in SUB: return 'href="{{url:%s}}#%s"'%(SUB[k],k)
        return m.group(0)
    html=re.sub(r'href="#([a-z-]+)"',r,html)
    html=re.sub(r'href="\{\{url:solutions\}\}" data-sector="([a-z]+)"',r'href="{{url:solutions}}?sector=\1#showcase" data-sector="\1"',html)
    html=html.replace('src="img/','src="{{img}}/')
    # replies are filled in by site.js; drop the prototype notes
    html=re.sub(r'(<p class="fok" id="pfOk" hidden>)[^<]*(</p>)',r'\1\2',html)
    html=re.sub(r'(<p class="ok" id="irOk" hidden>)[^<]*(</p>)',r'\1\2',html)
    html=html.replace('<dd>2545 (2002)</dd></div>','<dd>{{est}}</dd></div>')
    return html

def pages_of(f,lang):
    s=open(SP+f,encoding='utf-8').read()
    main=s[s.index('<main id="top">')+15:s.index('</main>')]
    secs=re.findall(r'<section\b[^>]*>.*?</section>',main,flags=re.S)
    js=s[s.index('var PAGES={'):s.index('};',s.index('var PAGES={'))]
    PAGES={}
    for m in re.finditer(r"(\w+):\['((?:[^'\\]|\\.)*)','((?:[^'\\]|\\.)*)','((?:[^'\\]|\\.)*)'(,1)?\]",js):
        PAGES[m.group(1)]=[x.replace("\\'","'").replace('\u2060','') for x in m.group(2,3,4)]+[bool(m.group(5))]
    out=[]
    est='2545 (2002)' if lang=='th' else '2002'
    for key,slug in SLUG.items():
        parts=[]
        for sec in secs:
            dp=re.search(r'data-pages="([^"]*)"',sec)
            if not dp or 'id="phero"' in sec: continue
            if key not in dp.group(1).split(): continue
            sec=sec.replace(dp.group(0)+' ','').replace(' '+dp.group(0),'').replace(dp.group(0),'')
            sec=strip_data_only(sec,key)
            parts.append(links(sec).replace('{{est}}',est))
        nav_label=PAGES.get(key,['หน้าแรก' if lang=='th' else 'Home'])[0]
        hero=PAGES.get(key)
        out.append({'key':key,'slug':slug,'title':nav_label,
                    'hero_title':hero[1] if hero else '','hero_lead':hero[2] if hero else '','hero_lead_style':hero[3] if hero else False,
                    'content':'\n\n'.join('<!-- wp:html -->\n'+p+'\n<!-- /wp:html -->' for p in parts)})
    return out
th=pages_of('listed.html','th'); en=pages_of('listed-en.html','en')
for p in th+en:
    assert '{{url:' in p['content'] or p['key'] in ('news',), p['key']
for p in th:
    if p['key']=='news':
        p['content']=re.sub(r'<!-- wp:html -->\n<section class="sec" id="news".*?<!-- /wp:html -->\n*','',p['content'],flags=re.S)
json.dump({'th':th,'en':en},open(T+'data/pages.json','w',encoding='utf-8'),ensure_ascii=False,indent=1)

# news posts (Thai): featured award + gallery items
s=open(SP+'listed.html',encoding='utf-8').read()
news=s[s.index('<section class="sec" id="news"'):]; news=news[:news.index('</section>')]
posts=[]
m=re.search(r'<div class="proj"><div class="im"><img src="img/([^"]+)"[^>]*></div><div class="bd">.*?<h3>(.*?)</h3><p class="sub">(.*?)</p><p>(.*?)</p><a class="more" href="([^"]+)"',news,re.S)
posts.append({'title':'คุณสมชาย ประจักษ์สูตร ได้รับรางวัล '+m.group(2),'img':m.group(1),'excerpt':m.group(4),'content':'<p>'+m.group(4)+'</p><p>'+m.group(3)+'</p><p><a href="'+m.group(5)+'" target="_blank" rel="noopener">ดูประกาศรางวัลบนเว็บไซต์ APEA</a></p>','date':'2026-08-24 09:00:00','sticky':True})
for i,(img,cap) in enumerate(re.findall(r'<figure><div class="im"><img src="img/([^"]+)"[^>]*></div><figcaption><span>[^<]*</span>([^<]+)</figcaption></figure>',news)):
    posts.append({'title':cap,'img':img,'excerpt':cap,'content':'<p>'+cap+'</p>','date':['2026-07-15 09:00:00','2025-11-20 09:00:00','2025-09-10 09:00:00','2025-06-05 09:00:00'][i],'sticky':False})
json.dump(posts,open(T+'data/news-th.json','w',encoding='utf-8'),ensure_ascii=False,indent=1)

# images used anywhere
used=set(re.findall(r'img/([A-Za-z0-9_.-]+\.(?:jpg|png))',open(SP+'listed.html',encoding='utf-8').read()))
for f in used: shutil.copy(SP+'img/'+f,T+'assets/img/'+f)
print('pages',len(th),len(en),'posts',len(posts),'images',len(used))
