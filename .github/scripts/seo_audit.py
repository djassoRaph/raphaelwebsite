"""
seo_audit.py — Weekly SEO audit agent
Fetches Google Search Console data for each site,
asks Claude to analyse it, then creates GitHub Issues.

Place this file at: .github/scripts/seo_audit.py
"""

import os
import json
import sys
from datetime import datetime, timedelta

from google.oauth2 import service_account
from googleapiclient.discovery import build
import anthropic
import requests


# ─────────────────────────────────────────────
# CONFIG — edit repo names here OR in the yml
# ─────────────────────────────────────────────
SITES = [
    {
        "search_console_url": "sc-domain:raphaelreck.com",    # domain property
        "display_name":       "raphaelreck.com",
        "github_repo":        os.getenv("REPO_RAPHAELRECK", "djassoRaph/raphaelreck.com"),
    },
    {
        "search_console_url": "https://open-llmr.org/",       # URL prefix property
        "display_name":       "open-llmr.org",
        "github_repo":        os.getenv("REPO_LLMR", "djassoRaph/open-llmr"),
    },
]

LOOKBACK_DAYS = 28   # Search Console data window
MAX_QUERIES   = 20   # Top N queries to include in the prompt
MAX_PAGES     = 15   # Top N pages by impressions


# ─────────────────────────────────────────────
# GOOGLE SEARCH CONSOLE — auth + fetch
# ─────────────────────────────────────────────
def build_search_console_service():
    """Authenticate using the service account JSON stored as a GitHub secret."""
    raw = os.environ.get("GOOGLE_SERVICE_ACCOUNT_JSON")
    if not raw:
        sys.exit("ERROR: GOOGLE_SERVICE_ACCOUNT_JSON secret is missing.")

    info = json.loads(raw)
    creds = service_account.Credentials.from_service_account_info(
        info,
        scopes=["https://www.googleapis.com/auth/webmasters.readonly"],
    )
    return build("searchconsole", "v1", credentials=creds, cache_discovery=False)


def fetch_search_data(service, site_url: str) -> dict:
    """
    Pull queries, pages, and devices for the last LOOKBACK_DAYS days.
    Returns a dict with 'queries' and 'pages' lists.
    """
    end_date   = datetime.utcnow().date()
    start_date = end_date - timedelta(days=LOOKBACK_DAYS)

    base_request = {
        "startDate": str(start_date),
        "endDate":   str(end_date),
        "rowLimit":  MAX_QUERIES,
    }

    def run(dimensions, row_limit=None):
        req = {**base_request, "dimensions": dimensions}
        if row_limit:
            req["rowLimit"] = row_limit
        try:
            resp = service.searchanalytics().query(
                siteUrl=site_url, body=req
            ).execute()
            return resp.get("rows", [])
        except Exception as e:
            print(f"  Warning: Search Console query failed for {site_url}: {e}")
            return []

    queries = run(["query"],     row_limit=MAX_QUERIES)
    pages   = run(["page"],      row_limit=MAX_PAGES)
    devices = run(["device"],    row_limit=5)

    return {
        "period":  f"{start_date} → {end_date}",
        "queries": queries,
        "pages":   pages,
        "devices": devices,
    }


def format_for_prompt(site: dict, data: dict) -> str:
    """Convert raw API data into a readable block for the Claude prompt."""

    def row_to_str(row):
        keys = row.get("keys", [])
        label = " | ".join(keys)
        clicks = row.get("clicks", 0)
        imps   = row.get("impressions", 0)
        ctr    = round(row.get("ctr", 0) * 100, 1)
        pos    = round(row.get("position", 0), 1)
        return f"  {label:<60}  clicks={clicks:<5}  impr={imps:<6}  ctr={ctr}%  pos={pos}"

    lines = [
        f"=== {site['display_name']} ({data['period']}) ===",
        "",
        f"TOP {MAX_QUERIES} QUERIES:",
    ]
    if data["queries"]:
        lines += [row_to_str(r) for r in data["queries"]]
    else:
        lines.append("  (no data)")

    lines += ["", f"TOP {MAX_PAGES} PAGES BY IMPRESSIONS:"]
    if data["pages"]:
        lines += [row_to_str(r) for r in data["pages"]]
    else:
        lines.append("  (no data)")

    lines += ["", "DEVICE BREAKDOWN:"]
    if data["devices"]:
        lines += [row_to_str(r) for r in data["devices"]]
    else:
        lines.append("  (no data)")

    return "\n".join(lines)


# ─────────────────────────────────────────────
# CLAUDE — analyse and generate issues
# ─────────────────────────────────────────────
SYSTEM_PROMPT = """You are an expert SEO analyst and web consultant.
You receive Google Search Console data for a website and must produce
a structured JSON list of actionable GitHub issues to improve SEO.
Each issue must be concrete, specific, and implementable by a developer.
Do NOT produce vague advice like "improve content quality."
Return ONLY a valid JSON array. No markdown, no prose, no code fences.
Each object has these exact keys:
  - "title"     : short issue title (max 80 chars), starts with [SEO]
  - "body"      : issue body in Markdown — be concise, no fluff, data + fix steps only
  - "labels"    : array of strings
  - "priority"  : "high" | "medium" | "low"
  - "page_key"  : a STABLE identifier for the underlying page or problem, used for
                  de-duplication across weekly runs. You MUST reuse the exact same
                  page_key every week for the same underlying issue — do not invent
                  a new one just because the numbers changed. Rules:
                  * For a specific page: use its exact URL path, e.g. "/blog/hook-webform-submission-insert-drupal.html"
                  * For the homepage: use "/"
                  * For a site-wide/technical issue (not tied to one page), use a short
                    fixed slug you commit to reusing, e.g. "sitewide-http-https-redirect",
                    "sitewide-desktop-mobile-ctr-gap", "cv-pdf-indexed"
Be terse. No preamble. No pleasantries. Data evidence + exact fix. Nothing else.
Generate 3 to 5 issues per site. Focus on these patterns:
1. Quick wins (high impressions, low CTR → meta/title fixes)
2. Position 4–10 queries that could reach page 1 with minor improvements
3. Any page with impressions but near-zero clicks
4. Technical issues if patterns suggest them

IMPORTANT: you will also be given a list of page_keys that already have an OPEN
tracking issue in the repo, with their issue numbers. If the underlying problem
you'd otherwise report matches one of those page_keys, do NOT invent a new issue —
report it with that exact same page_key so it gets appended as a progress update to
the existing issue instead of filed as a new one. Only use a new page_key for a
genuinely new page or problem not already tracked.
"""

def ask_claude(site_name: str, data_block: str, existing_keys: dict) -> list[dict]:
    """Send Search Console data to Claude, get back a list of issue dicts."""
    client = anthropic.Anthropic(api_key=os.environ["ANTHROPIC_API_KEY"])

    if existing_keys:
        tracked_block = "\n".join(f"  - {k}  (already tracked in #{v})" for k, v in existing_keys.items())
    else:
        tracked_block = "  (none yet — this is the first run)"

    user_message = f"""Analyse this Google Search Console data for {site_name} and generate SEO issues.

{data_block}

PAGE_KEYS ALREADY TRACKED BY AN OPEN ISSUE (reuse these exactly if the same problem recurs):
{tracked_block}

Remember: return ONLY a JSON array of issue objects. No other text."""

    msg = client.messages.create(
        model="claude-sonnet-4-6",
        max_tokens=10000,
        system=SYSTEM_PROMPT,
        messages=[{"role": "user", "content": user_message}],
    )

    raw = msg.content[0].text.strip()

    # Strip accidental code fences if Claude adds them despite instructions
    if raw.startswith("```"):
        raw = raw.split("```")[1]
        if raw.startswith("json"):
            raw = raw[4:]
    raw = raw.strip()

    try:
        issues = json.loads(raw)
        if not isinstance(issues, list):
            raise ValueError("Response is not a JSON array")
        return issues
    except json.JSONDecodeError as e:
        print(f"  Warning: Claude returned invalid JSON for {site_name}: {e}")
        print(f"  Raw response: {raw[:300]}")
        return []


# ─────────────────────────────────────────────
# GITHUB — dedup, create issues, comment on existing ones
# ─────────────────────────────────────────────
KEY_MARKER = "<!-- seo-agent-key: {key} -->"
KEY_MARKER_RE_PREFIX = "<!-- seo-agent-key: "


def _gh_headers(token: str) -> dict:
    return {
        "Authorization": f"Bearer {token}",
        "Accept": "application/vnd.github+json",
        "X-GitHub-Api-Version": "2022-11-28",
    }


def fetch_open_seo_issues(repo: str) -> dict:
    """
    Return {page_key: issue_number} for every OPEN issue in the repo that
    carries a 'seo-agent' label and an embedded page_key marker in its body.
    This is what lets future runs update an existing issue instead of
    filing a new one for the same underlying problem.
    """
    token = os.environ.get("GH_TOKEN")
    if not token:
        sys.exit("ERROR: GH_TOKEN secret is missing.")

    keys = {}
    url = f"https://api.github.com/repos/{repo}/issues"
    params = {"state": "open", "labels": "seo-agent", "per_page": 100}

    try:
        resp = requests.get(url, headers=_gh_headers(token), params=params, timeout=15)
        resp.raise_for_status()
    except Exception as e:
        print(f"    Warning: could not fetch existing issues for dedup: {e}")
        return keys

    for issue in resp.json():
        body = issue.get("body") or ""
        for line in body.splitlines():
            line = line.strip()
            if line.startswith(KEY_MARKER_RE_PREFIX):
                key = line[len(KEY_MARKER_RE_PREFIX):].split("-->")[0].strip()
                if key:
                    keys[key] = issue["number"]
                break

    return keys


def comment_on_issue(repo: str, issue_number: int, issue: dict, site_name: str):
    """Append a progress-update comment to an already-tracked issue instead of
    filing a duplicate."""
    token = os.environ.get("GH_TOKEN")
    url = f"https://api.github.com/repos/{repo}/issues/{issue_number}/comments"

    comment = (
        f"> **SEO Agent** — weekly update on {datetime.utcnow().strftime('%Y-%m-%d')}"
        f" for `{site_name}` (still open, same underlying issue)\n\n"
        + issue.get("body", "")
    )

    resp = requests.post(url, headers=_gh_headers(token), json={"body": comment}, timeout=15)
    if resp.status_code == 201:
        print(f"    ↻ Updated existing issue #{issue_number}: {issue.get('title', '')}")
    else:
        print(f"    ❌ Failed to comment on #{issue_number} ({resp.status_code}): {resp.text[:200]}")


def create_github_issue(repo: str, issue: dict, site_name: str, existing_keys: dict):
    """POST a single issue to GitHub, unless its page_key is already tracked by
    an open issue — in which case update that issue instead. Adds a 'seo-agent'
    label automatically."""
    token = os.environ.get("GH_TOKEN")
    if not token:
        sys.exit("ERROR: GH_TOKEN secret is missing.")

    page_key = (issue.get("page_key") or "").strip()

    if page_key and page_key in existing_keys:
        comment_on_issue(repo, existing_keys[page_key], issue, site_name)
        return

    url     = f"https://api.github.com/repos/{repo}/issues"
    labels = list(issue.get("labels", [])) + ["seo-agent"]

    key_line = KEY_MARKER.format(key=page_key) if page_key else ""
    body_prefix = (
        f"{key_line}\n"
        f"> **SEO Agent** — auto-generated on {datetime.utcnow().strftime('%Y-%m-%d')}"
        f" for `{site_name}`\n\n"
    )

    payload = {
        "title":  issue.get("title", "[SEO] Untitled issue"),
        "body":   body_prefix + issue.get("body", ""),
        "labels": labels,
    }

    resp = requests.post(url, headers=_gh_headers(token), json=payload, timeout=15)

    if resp.status_code == 201:
        issue_url = resp.json().get("html_url", "")
        print(f"    ✅ Created: {payload['title']}")
        print(f"       {issue_url}")
        if page_key:
            existing_keys[page_key] = resp.json().get("number")
    elif resp.status_code == 422:
        # Label might not exist yet — retry without labels
        print(f"    ⚠  Label error, retrying without labels...")
        payload["labels"] = []
        resp2 = requests.post(url, headers=_gh_headers(token), json=payload, timeout=15)
        if resp2.status_code == 201:
            print(f"    ✅ Created (no labels): {payload['title']}")
            if page_key:
                existing_keys[page_key] = resp2.json().get("number")
        else:
            print(f"    ❌ Failed: {resp2.status_code} — {resp2.text[:200]}")
    else:
        print(f"    ❌ Failed ({resp.status_code}): {resp.text[:200]}")


# ─────────────────────────────────────────────
# MAIN
# ─────────────────────────────────────────────
def main():
    print(f"\n{'='*60}")
    print(f"  SEO Audit Agent — {datetime.utcnow().strftime('%Y-%m-%d %H:%M UTC')}")
    print(f"{'='*60}\n")

    service = build_search_console_service()

    for site in SITES:
        name = site["display_name"]
        print(f"── {name} ──────────────────────────────────")

        print(f"  Fetching Search Console data...")
        data = fetch_search_data(service, site["search_console_url"])

        formatted = format_for_prompt(site, data)
        print(f"  Data block ({len(data['queries'])} queries, {len(data['pages'])} pages)")

        print(f"  Fetching already-tracked open SEO issues for dedup...")
        existing_keys = fetch_open_seo_issues(site["github_repo"])
        print(f"  {len(existing_keys)} page(s) already tracked: {list(existing_keys.keys())}")

        print(f"  Asking Claude for analysis...")
        issues = ask_claude(name, formatted, existing_keys)
        print(f"  Claude generated {len(issues)} issue(s)")

        if not issues:
            print(f"  Skipping issue creation (no valid issues returned)\n")
            continue

        print(f"  Creating/updating GitHub issues in {site['github_repo']}...")
        for issue in issues:
            create_github_issue(site["github_repo"], issue, name, existing_keys)

        print()

    print("Done.\n")


if __name__ == "__main__":
    main()