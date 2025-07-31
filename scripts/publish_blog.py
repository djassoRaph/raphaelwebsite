from datetime import datetime, timezone
from email.utils import format_datetime
import xml.etree.ElementTree as ET
import os
import re
import json

index_path = 'blog/index.html'

with open(index_path, 'r', encoding='utf-8') as f:
    html = f.read()

articles = re.findall(r'(<article class="blog-post">.*?</article>)', html, flags=re.DOTALL)
print("Generating feeds with", len(articles), "articles")

def get_date(block):
    m = re.search(r'<time[^>]*datetime="(.*?)"', block)
    return m.group(1) if m else ''

articles.sort(key=get_date, reverse=True)

# Update blog/index.html order (optional)
new_html = re.sub(
    r'(<div class="blog-posts">).*?(</div>)',
    lambda m: m.group(1) + '\n' + '\n'.join(articles) + '\n' + m.group(2),
    html,
    flags=re.DOTALL,
)
with open(index_path, 'w', encoding='utf-8') as f:
    f.write(new_html)

os.makedirs('public', exist_ok=True)

# ========== RSS ==========
ET.register_namespace('atom', 'http://www.w3.org/2005/Atom')
rss_root = ET.Element('rss', {'version': '2.0'})
channel = ET.SubElement(rss_root, 'channel')
ET.SubElement(channel, 'title').text = 'Raphael Reck Blog'
ET.SubElement(channel, 'link').text = 'https://raphaelreck.com/blog/'
ET.SubElement(channel, 'description').text = 'Latest posts'
ET.SubElement(channel, 'language').text = 'en-US'
ET.SubElement(channel, '{http://www.w3.org/2005/Atom}link', {
    'href': 'https://raphaelreck.com/feed.xml',
    'rel': 'self',
    'type': 'application/rss+xml'
})

for block in articles[:20]:
    date_str = get_date(block)
    if not date_str:
        continue
    dt = datetime.fromisoformat(date_str).replace(tzinfo=timezone.utc)
    item = ET.SubElement(channel, 'item')
    title = re.search(r'<h2>(.*?)</h2>', block, re.DOTALL).group(1).strip()
    ET.SubElement(item, 'title').text = title
    ET.SubElement(item, 'link').text = 'https://raphaelreck.com/blog/'
    ET.SubElement(item, 'guid').text = f'https://raphaelreck.com/blog/#{date_str}'
    ET.SubElement(item, 'pubDate').text = format_datetime(dt, usegmt=True)
    snippet = ' '.join(re.sub(r'<.*?>', '', block).split())
    ET.SubElement(item, 'description').text = snippet

ET.ElementTree(rss_root).write('public/feed.xml', encoding='utf-8', xml_declaration=True)
with open('public/feed.xml', 'ab') as f:
    f.write(b'\n<!-- regenerated: %s -->\n' % datetime.now().isoformat().encode())
print("✅ Wrote RSS feed.xml")

# ========== ATOM ==========
atom_root = ET.Element('feed', {'xmlns': 'http://www.w3.org/2005/Atom'})
ET.SubElement(atom_root, 'title').text = 'Raphael Reck Blog'
ET.SubElement(atom_root, 'link', {'href': 'https://raphaelreck.com/blog/'})
ET.SubElement(atom_root, 'link', {
    'href': 'https://raphaelreck.com/atom.xml',
    'rel': 'self',
    'type': 'application/atom+xml'
})
ET.SubElement(atom_root, 'updated').text = datetime.now(timezone.utc).isoformat()
ET.SubElement(atom_root, 'id').text = 'https://raphaelreck.com/blog/'

for block in articles[:20]:
    date_str = get_date(block)
    if not date_str:
        continue
    dt = datetime.fromisoformat(date_str).replace(tzinfo=timezone.utc)
    entry = ET.SubElement(atom_root, 'entry')
    title = re.search(r'<h2>(.*?)</h2>', block, re.DOTALL).group(1).strip()
    ET.SubElement(entry, 'title').text = title
    ET.SubElement(entry, 'link', {'href': 'https://raphaelreck.com/blog/'})
    ET.SubElement(entry, 'id').text = f'https://raphaelreck.com/blog/#{date_str}'
    ET.SubElement(entry, 'updated').text = dt.isoformat()
    snippet = ' '.join(re.sub(r'<.*?>', '', block).split())
    ET.SubElement(entry, 'summary').text = snippet

ET.ElementTree(atom_root).write('public/atom.xml', encoding='utf-8', xml_declaration=True)
print("✅ Wrote Atom atom.xml")

# ========== JSON ==========
json_feed = {
    "version": "https://jsonfeed.org/version/1",
    "title": "Raphael Reck Blog",
    "home_page_url": "https://raphaelreck.com/blog/",
    "feed_url": "https://raphaelreck.com/feed.json",
    "language": "en-US",
    "items": []
}

for block in articles[:20]:
    date_str = get_date(block)
    if not date_str:
        continue
    dt = datetime.fromisoformat(date_str).replace(tzinfo=timezone.utc)
    title = re.search(r'<h2>(.*?)</h2>', block, re.DOTALL).group(1).strip()
    snippet = ' '.join(re.sub(r'<.*?>', '', block).split())
    json_feed["items"].append({
        "id": f"https://raphaelreck.com/blog/#{date_str}",
        "url": "https://raphaelreck.com/blog/",
        "title": title,
        "content_text": snippet,
        "date_published": dt.isoformat()
    })

with open('public/feed.json', 'w', encoding='utf-8') as f:
    json.dump(json_feed, f, ensure_ascii=False, indent=2)
print("✅ Wrote JSON feed.json")
