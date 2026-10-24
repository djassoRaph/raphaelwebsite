#!/usr/bin/env python3
"""
Tests for generate_llmr.py.

Needs the llmr-generator commit pinned in .github/workflows/publish.yml.
From the repo root:

    python3 -m unittest discover -s scripts -p "test_*.py"
"""

import json
import shutil
import subprocess
import sys
import tempfile
import unittest
from pathlib import Path

SCRIPTS = Path(__file__).resolve().parent
sys.path.insert(0, str(SCRIPTS))

import generate_llmr  # noqa: E402

HOME_HEAD = ('<meta name="description" content="Site description.">'
             '<meta property="og:description" content="Site card text.">'
             '<meta name="author" content="Raphael Reck">')

# Valid markup the pinned generator raises on.
JSON_LD_ARRAY = '<script type="application/ld+json">[{"@type": "Article"}]</script>'
BARE_LANG = "lang"


def page(title, head="", html_attrs='lang="en"'):
    return ('<!DOCTYPE html><html {}><head><title>{}</title>{}</head>'
            '<body><h1>{}</h1><p>Some words about the page.</p></body></html>').format(
                html_attrs, title, head, title)


class SiteTestCase(unittest.TestCase):
    """A throwaway site: index.html plus whatever a test writes."""

    def setUp(self):
        tmp = tempfile.TemporaryDirectory()
        self.addCleanup(tmp.cleanup)
        self.root = Path(tmp.name)
        self.write("index.html", page("Home", HOME_HEAD))

    def write(self, rel, html):
        path = self.root / rel
        path.parent.mkdir(parents=True, exist_ok=True)
        path.write_text(html, encoding="utf-8")

    def build(self):
        data, skipped = generate_llmr.build_llmr(self.root)
        return {p["u"]: p for p in data["p"]}, dict(skipped), data


class SummaryTest(SiteTestCase):
    """F1: <meta name="description"> is the summary, og:description the fallback."""

    def test_meta_description_wins_over_a_later_og_description(self):
        self.write("a.html", page("A", '<meta name="description" content="Meta A.">'
                                       '<meta property="og:description" content="Card A.">'))
        pages, _, _ = self.build()
        self.assertEqual(pages["/a.html"]["sum"], "Meta A.")

    def test_meta_description_wins_over_an_earlier_og_description(self):
        self.write("b.html", page("B", '<meta property="og:description" content="Card B.">'
                                       '<meta name="description" content="Meta B.">'))
        pages, _, _ = self.build()
        self.assertEqual(pages["/b.html"]["sum"], "Meta B.")

    def test_og_description_is_used_when_there_is_no_meta_description(self):
        self.write("c.html", page("C", '<meta property="og:description" content="Card C.">'))
        pages, _, _ = self.build()
        self.assertEqual(pages["/c.html"]["sum"], "Card C.")

    def test_empty_meta_description_falls_back_to_og_description(self):
        self.write("d.html", page("D", '<meta name="description" content="">'
                                       '<meta property="og:description" content="Card D.">'))
        pages, _, _ = self.build()
        self.assertEqual(pages["/d.html"]["sum"], "Card D.")

    def test_site_description_is_the_homepage_meta_description(self):
        pages, _, data = self.build()
        self.assertEqual(data["s"]["desc"], "Site description.")
        self.assertEqual(pages["/"]["sum"], "Site description.")


class BrokenPageTest(SiteTestCase):
    """F2: a page the generator fails on is skipped; only index.html is fatal."""

    def test_page_failing_in_build_page_is_skipped_with_its_reason(self):
        self.write("blog/bad.html", page("Bad", JSON_LD_ARRAY))
        self.write("blog/good.html", page("Good"))
        pages, skipped, _ = self.build()
        self.assertNotIn("/blog/bad.html", pages)
        self.assertIn("/blog/good.html", pages)
        self.assertIn("AttributeError", skipped["blog/bad.html"])

    def test_page_failing_in_the_parser_is_skipped_with_its_reason(self):
        self.write("bad.html", page("Bad", html_attrs=BARE_LANG))
        pages, skipped, _ = self.build()
        self.assertNotIn("/bad.html", pages)
        self.assertIn("generator failed", skipped["bad.html"])

    def test_skipped_page_does_not_take_an_id(self):
        self.write("a/post.html", page("Broken", JSON_LD_ARRAY))
        self.write("b/post.html", page("Fine"))
        pages, _, _ = self.build()
        self.assertEqual(pages["/b/post.html"]["id"], "post")

    def test_broken_index_html_stops_the_build(self):
        self.write("index.html", page("Home", HOME_HEAD + JSON_LD_ARRAY))
        with self.assertRaises(SystemExit) as cm:
            self.build()
        self.assertIn("index.html", str(cm.exception.code))

    def run_script(self):
        (self.root / "scripts").mkdir()
        shutil.copy(SCRIPTS / "generate_llmr.py", self.root / "scripts")
        return subprocess.run([sys.executable, "scripts/generate_llmr.py"], cwd=self.root,
                              capture_output=True, text=True, encoding="utf-8")

    def test_script_exits_0_and_writes_the_files_without_the_broken_page(self):
        self.write("bad.html", page("Bad", JSON_LD_ARRAY))
        self.write("good.html", page("Good"))
        run = self.run_script()
        self.assertEqual(run.returncode, 0, run.stderr)
        self.assertIn("skipped bad.html: generator failed", run.stdout)
        urls = [p["u"] for p in json.loads((self.root / "llmr.json").read_text(encoding="utf-8"))["p"]]
        self.assertEqual(sorted(urls), ["/", "/good.html"])
        self.assertTrue((self.root / "llms.txt").is_file())

    def test_script_exits_non_zero_when_index_html_fails(self):
        self.write("index.html", page("Home", HOME_HEAD, html_attrs=BARE_LANG))
        run = self.run_script()
        self.assertNotEqual(run.returncode, 0)
        self.assertIn("index.html", run.stderr)
        self.assertFalse((self.root / "llmr.json").exists())


if __name__ == "__main__":
    unittest.main()
