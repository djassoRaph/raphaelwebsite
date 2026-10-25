#!/usr/bin/env python3
"""
Tests for generate_llmr.py and the deploy step that runs it.

Needs the llmr-generator commit pinned in .github/workflows/publish.yml.
From the repo root:

    python3 -m unittest discover -s scripts -p "test_*.py"
"""

import json
import re
import shutil
import subprocess
import sys
import tempfile
import unittest
from pathlib import Path
from unittest import mock

SCRIPTS = Path(__file__).resolve().parent
sys.path.insert(0, str(SCRIPTS))

import generate_llmr  # noqa: E402

WORKFLOW = SCRIPTS.parent / ".github" / "workflows" / "publish.yml"

HOME_HEAD = ('<meta name="description" content="Site description.">'
             '<meta property="og:description" content="Site card text.">'
             '<meta name="author" content="Raphael Reck">')

# Pages carrying these markers make the generator raise (patched below), so the
# skip logic is tested the same way whatever markup the pinned version chokes on:
# FAIL_ME in the <title> fails build_page, PARSE_FAIL in the HTML fails parse_page.
FAIL_ME = "FAIL-ME"
PARSE_FAIL = "PARSE-FAIL"


def failing(real, mark, text_of):
    """Wrap build_page or parse_page so it raises when `mark` is in text_of(first argument)."""
    def wrapped(arg, *rest, **kw):
        if mark in text_of(arg):
            raise AttributeError("simulated generator failure")
        return real(arg, *rest, **kw)
    return wrapped


# Runs the copied script with build_page failing for FAIL_ME pages.
FAILING_SCRIPT = (
    "import sys; sys.path.insert(0, 'scripts'); import generate_llmr\n"
    "real = generate_llmr.build_page\n"
    "def build_page(parsed, *rest, **kw):\n"
    "    if {!r} in (parsed.title or ''):\n"
    "        raise AttributeError('simulated generator failure')\n"
    "    return real(parsed, *rest, **kw)\n"
    "generate_llmr.build_page = build_page\n"
    "generate_llmr.main()\n"
).format(FAIL_ME)


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

    def setUp(self):
        super().setUp()
        patches = (("build_page", FAIL_ME, lambda parsed: parsed.title or ""),
                   ("parse_page", PARSE_FAIL, lambda html: html))
        for name, mark, text_of in patches:
            patcher = mock.patch.object(generate_llmr, name, failing(getattr(generate_llmr, name), mark, text_of))
            patcher.start()
            self.addCleanup(patcher.stop)

    def test_page_failing_in_build_page_is_skipped_with_its_reason(self):
        self.write("blog/bad.html", page("Bad " + FAIL_ME))
        self.write("blog/good.html", page("Good"))
        pages, skipped, _ = self.build()
        self.assertNotIn("/blog/bad.html", pages)
        self.assertIn("/blog/good.html", pages)
        self.assertIn("AttributeError", skipped["blog/bad.html"])

    def test_page_failing_in_the_parser_is_skipped_with_its_reason(self):
        self.write("bad.html", page("Bad") + "<!-- {} -->".format(PARSE_FAIL))
        pages, skipped, _ = self.build()
        self.assertNotIn("/bad.html", pages)
        self.assertIn("generator failed", skipped["bad.html"])

    def test_skipped_page_does_not_take_an_id(self):
        self.write("a/post.html", page("Broken " + FAIL_ME))
        self.write("b/post.html", page("Fine"))
        pages, _, _ = self.build()
        self.assertEqual(pages["/b/post.html"]["id"], "post")

    def test_broken_index_html_stops_the_build(self):
        self.write("index.html", page("Home " + FAIL_ME, HOME_HEAD))
        with self.assertRaises(SystemExit) as cm:
            self.build()
        self.assertIn("index.html", str(cm.exception.code))

    def run_script(self):
        (self.root / "scripts").mkdir()
        shutil.copy(SCRIPTS / "generate_llmr.py", self.root / "scripts")
        return subprocess.run([sys.executable, "-c", FAILING_SCRIPT], cwd=self.root,
                              capture_output=True, text=True, encoding="utf-8")

    def test_script_exits_0_and_writes_the_files_without_the_broken_page(self):
        self.write("bad.html", page("Bad " + FAIL_ME))
        self.write("good.html", page("Good"))
        run = self.run_script()
        self.assertEqual(run.returncode, 0, run.stderr)
        self.assertIn("skipped bad.html: generator failed", run.stdout)
        urls = [p["u"] for p in json.loads((self.root / "llmr.json").read_text(encoding="utf-8"))["p"]]
        self.assertEqual(sorted(urls), ["/", "/good.html"])
        self.assertTrue((self.root / "llms.txt").is_file())

    def test_script_exits_non_zero_when_index_html_fails(self):
        self.write("index.html", page("Home " + FAIL_ME, HOME_HEAD))
        run = self.run_script()
        self.assertNotEqual(run.returncode, 0)
        self.assertIn("index.html", run.stderr)
        self.assertFalse((self.root / "llmr.json").exists())


class WorkflowTest(unittest.TestCase):
    def setUp(self):
        self.yml = WORKFLOW.read_text(encoding="utf-8")

    def test_generator_is_built_with_pinned_build_tools(self):
        """F3: no build isolation, so no latest setuptools/wheel from PyPI in the deploy job."""
        tools = re.search(r'pip install "setuptools==[\d.]+" "wheel==[\d.]+"\n', self.yml)
        generator = re.search(r'pip install --no-build-isolation '
                              r'"git\+https://gitlab\.com/open-llmr/python\.git@[0-9a-f]{40}"', self.yml)
        self.assertIsNotNone(tools)
        self.assertIsNotNone(generator)
        self.assertLess(tools.start(), generator.start())
        for line in self.yml.splitlines():
            if "pip install" in line and "git+" in line:
                self.assertIn("--no-build-isolation", line)

    def test_claude_folders_are_not_uploaded(self):
        """F5: public/.claude/settings.json is tracked but must not reach the server."""
        exclude = self.yml.split("exclude: |", 1)[1].split("\n\n", 1)[0]
        self.assertIn("**/.claude/**", [line.strip() for line in exclude.splitlines()])


if __name__ == "__main__":
    unittest.main()
