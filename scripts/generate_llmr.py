#!/usr/bin/env python3
"""
Build llmr.json (LLMR v2.1) and llms.txt for raphaelreck.com.

CI runs this before every deploy (.github/workflows/publish.yml), from the repo
root, with the llmr-generator package and its build tools pinned there:

    pip install "setuptools==<version>" "wheel==<version>"
    pip install --no-build-isolation "git+https://gitlab.com/open-llmr/python.git@<commit>"
    python3 scripts/generate_llmr.py

Both files are written to the repo root and link to each other: llmr.json
through s.llms_txt, llms.txt through its "## Optional" line. They list the
same pages: every public content page, the homepage as "/". Private areas,
tooling folders and noindex pages are left out. A page the generator fails
on is skipped with the reason printed; only a failure on index.html stops
the build (exit status 1).

A page's summary is its <meta name="description">; og:description is used
only when a page has none.

llms.txt is only regenerated while it carries the generator marker line;
delete that line to edit the file by hand (or pass --force to take it back).

Tests: python3 -m unittest discover -s scripts -p "test_*.py"
"""

import argparse
import json
import os
import sys
import unicodedata
from pathlib import Path

from llmr.generator import build_page, extract_keywords, generate, is_noindex
from llmr.llms_txt import is_generated, links_to_llmr, llmr_link_line, render_llms_txt, write_llms_txt
from llmr.parser import HTMLMetadataExtractor

DOMAIN = "raphaelreck.com"
SITE_URL = "https://" + DOMAIN
LLMR_URL = SITE_URL + "/llmr.json"
LLMS_TXT_URL = SITE_URL + "/llms.txt"

ROOT = Path(__file__).resolve().parent.parent

# Folders that never hold public content pages: private or noindex areas
# (public/, secret/, php-test/), tooling and dependencies. Hidden folders
# (.git, .github, .claude, ...) are skipped as well.
EXCLUDED_DIRS = {"public", "secret", "php-test", "scripts", "node_modules"}


class PageParser(HTMLMetadataExtractor):
    """The generator's parser, also noting <meta name="description"> on its own.

    llmr-generator 2.1.0 kept whichever of meta description and og:description
    came last (here mostly the og:description written for social cards); 2.3.0
    already prefers the meta description, so this override is a no-op there.
    """

    def __init__(self):
        super().__init__()
        self.meta_description = ""

    def handle_starttag(self, tag, attrs):
        super().handle_starttag(tag, attrs)
        if tag == "meta":
            a = dict(attrs)
            if (a.get("name") or "").lower() == "description" and a.get("content"):
                self.meta_description = a["content"]


def parse_page(html):
    """parse_html(), except that <meta name="description"> is the description.

    The parser's own value (og:description) stays as the fallback.
    """
    p = PageParser()
    p.feed(html)
    if p.meta_description:
        p.description = p.meta_description
    return p


def find_pages(root):
    """Every .html file outside the excluded folders, sorted by path."""
    found = []
    for dirpath, dirnames, filenames in os.walk(root):
        dirnames[:] = [d for d in dirnames if d not in EXCLUDED_DIRS and not d.startswith(".")]
        found.extend(Path(dirpath) / name for name in filenames if name.endswith(".html"))
    return sorted(found, key=lambda p: p.relative_to(root).as_posix())


def page_url(rel_path):
    """'index.html' -> '/', 'blog/index.html' -> '/blog/', 'cv.html' -> '/cv.html'."""
    if rel_path == "index.html":
        return "/"
    if rel_path.endswith("/index.html"):
        return "/" + rel_path[:-len("index.html")]
    return "/" + rel_path


def page_id(rel_path):
    """Slug: the file name without .html; an index page takes its folder name."""
    parts = rel_path.split("/")
    if parts[-1] == "index.html":
        return parts[-2] if len(parts) > 1 else "index"
    return parts[-1][:-len(".html")]


def stable_keywords(parsed, limit=10):
    """Keywords in a fixed order: the page's meta keywords, then content words.

    In llmr-generator 2.1.0, extract_keywords() returned list(set(...))[:limit],
    so with string hashing randomised its pick and order changed between runs
    (2.3.0 fixed that). Same sources here, in an order that never changes.
    """
    meta = list(dict.fromkeys(parsed.keywords))
    saved, parsed.keywords = parsed.keywords, []
    try:
        content = extract_keywords(parsed, max_kw=limit)
    finally:
        parsed.keywords = saved
    return (meta + sorted(w for w in content if w not in meta))[:limit]


def order_pages(pages):
    """Section landing pages ('/', '/blog/') first, then newest first, then undated.

    Ties keep URL order, so the output is the same on every run.
    """
    pages = sorted(pages, key=lambda p: p["u"])
    pages.sort(key=lambda p: p.get("d", ""), reverse=True)
    pages.sort(key=lambda p: not p["u"].endswith("/"))
    return pages


def same_name(a, b):
    """'Raphaël Reck' and 'Raphael Reck' name the same person."""
    def fold(s):
        return "".join(c for c in unicodedata.normalize("NFKD", s) if not unicodedata.combining(c)).casefold()
    return fold(a) == fold(b)


def build_llmr(root=ROOT):
    homepage = None
    pages = []
    skipped = []
    used_ids = set()

    for path in find_pages(root):
        rel = path.relative_to(root).as_posix()
        # If the generator fails on a page (2.1.0 did on a JSON-LD array or a bare
        # <html lang>), that page is left out, not the deploy.
        try:
            parsed = parse_page(path.read_text(encoding="utf-8", errors="replace"))
            if is_noindex(parsed):
                skipped.append((rel, "noindex"))
                continue
            if not parsed.title:
                skipped.append((rel, "no <title>, not a content page"))
                continue

            pid = base_id = page_id(rel)
            n = 2
            while pid in used_ids:
                pid = "{}-{}".format(base_id, n)
                n += 1

            page = build_page(parsed, page_url(rel), pid)
            page.pop("emb", None)  # hash placeholder, not a real embedding
            if "kw" in page:
                page["kw"] = stable_keywords(parsed)
        except Exception as e:
            reason = "generator failed ({}: {})".format(type(e).__name__, e)
            if rel == "index.html":
                sys.exit("Error: index.html: {}; it gives the site title and description.".format(reason))
            skipped.append((rel, reason))
            continue
        used_ids.add(pid)
        pages.append(page)
        if rel == "index.html":
            homepage = parsed

    if homepage is None:
        sys.exit("Error: index.html is missing or noindex; it gives the site title and description.")

    # Per-page author only when it differs from the site author (LLMR spec).
    for page in pages:
        if "a" in page and same_name(page["a"], homepage.author):
            del page["a"]

    data = generate(
        order_pages(pages),
        domain=DOMAIN,
        title=homepage.title,
        description=homepage.description,
        author_name=homepage.author,
        llms_txt_url=LLMS_TXT_URL,
    )
    return data, skipped


def write_json(data, path):
    """UTF-8, LF line endings and a trailing newline on every OS."""
    with open(path, "w", encoding="utf-8", newline="\n") as f:
        f.write(json.dumps(data, indent=2, ensure_ascii=False) + "\n")


def main():
    ap = argparse.ArgumentParser(description="Build llmr.json and llms.txt for " + DOMAIN)
    ap.add_argument("--force", action="store_true",
                    help="overwrite llms.txt even if it has no generator marker (hand-written)")
    args = ap.parse_args()

    data, skipped = build_llmr()

    llmr_path = ROOT / "llmr.json"
    write_json(data, llmr_path)

    llms_path = ROOT / "llms.txt"
    existing = llms_path.read_text(encoding="utf-8") if llms_path.exists() else None
    if existing is not None and not args.force and not is_generated(existing):
        llms_status = "left unchanged (no generator marker: edited by hand; --force overwrites)"
        if not links_to_llmr(existing):
            llms_status += "\n  add under '## Optional': " + llmr_link_line(LLMR_URL)
    else:
        write_llms_txt(render_llms_txt(data, llmr_url=LLMR_URL), llms_path)
        llms_status = "written"

    print("LLMR v{}: {} pages ({})".format(
        data["v"], len(data["p"]),
        ", ".join("{} {}".format(n, t) for t, n in sorted(data["s"]["ct"].items()))))
    for rel, reason in skipped:
        print("  skipped {}: {}".format(rel, reason))
    print("  llmr.json: written")
    print("  llms.txt:  " + llms_status)


if __name__ == "__main__":
    main()
