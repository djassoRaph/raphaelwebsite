import re, xml.etree.ElementTree as ET
from datetime import datetime, timezone
from email.utils import format_datetime
import os

index_path = 'blog/index.html'
with open(index_path, 'r', encoding='utf-8') as f:
    html = f.read()

articles = re.findall(r'(<article class="blog-post">.*?</article>)', html, flags=re.DOTALL)

def get_date(block):
    m = re.search(r'<time[^>]*datetime="(.*?)"', block)
    return m.group(1) if m else ''

articles.sort(key=get_date, reverse=True)

new_html = re.sub(
    r'(<div class="blog-posts">).*?(</div>)',
    lambda m: m.group(1) + '\n' + '\n'.join(articles) + '\n' + m.group(2),
    html,
    flags=re.DOTALL,
)

with open(index_path, 'w', encoding='utf-8') as f:
    f.write(new_html)

rss_root = ET.Element('rss', {'version': '2.0', 'xmlns:atom': 'http://www.w3.org/2005/Atom'})
channel = ET.SubElement(rss_root, 'channel')
ET.SubElement(channel, '{http://www.w3.org/2005/Atom}link', {
    'href': 'https://raphaelreck.com/feed.xml',
    'rel': 'self',
    'type': 'application/rss+xml'
})
ET.SubElement(channel, 'title').text = 'Raphael Reck Blog'
ET.SubElement(channel, 'link').text = 'https://raphaelreck.com/blog/'
ET.SubElement(channel, 'description').text = 'Latest posts'

for block in articles[:20]:
    date_str = get_date(block)
    dt = datetime.fromisoformat(date_str).replace(tzinfo=timezone.utc)
    item = ET.SubElement(channel, 'item')
    title = re.search(r'<h2>(.*?)</h2>', block, re.DOTALL).group(1).strip()
    ET.SubElement(item, 'title').text = title
    link = 'https://raphaelreck.com/blog/'
    ET.SubElement(item, 'link').text = link
    ET.SubElement(item, 'guid').text = link + '#' + date_str
    ET.SubElement(item, 'pubDate').text = format_datetime(dt, usegmt=True)
    text = re.sub(r'<.*?>', '', block)
    snippet = ' '.join(text.split())
    ET.SubElement(item, 'description').text = snippet

os.makedirs('public', exist_ok=True)
ET.ElementTree(rss_root).write('public/feed.xml', encoding='utf-8', xml_declaration=True)
