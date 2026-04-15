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
  - "body"      : full issue body in Markdown, with context, data evidence, and exact steps to fix
  - "labels"    : array of strings, choose from: ["seo", "content", "technical", "performance", "indexing", "priority-high", "priority-medium", "priority-low"]
  - "priority"  : "high" | "medium" | "low"

Generate between 3 and 6 issues per site. Focus on:
1. Quick wins (high impressions, low CTR → meta/title fixes)
2. Position 4–10 queries that could reach page 1 with minor improvements
3. Any page with impressions but near-zero clicks
4. Technical issues if patterns suggest them
"""

def ask_claude(site_name: str, data_block: str) -> list[dict]:
    """Send Search Console data to Claude, get back a list of issue dicts."""
    client = anthropic.Anthropic(api_key=os.environ["ANTHROPIC_API_KEY"])

    user_message = f"""Analyse this Google Search Console data for {site_name} and generate SEO issues.

{data_block}

Remember: return ONLY a JSON array of issue objects. No other text."""

    msg = client.messages.create(
        model="claude-sonnet-4-6",
        max_tokens=2000,
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
# GITHUB — create issues
# ─────────────────────────────────────────────
def create_github_issue(repo: str, issue: dict, site_name: str):
    """POST a single issue to GitHub. Adds a 'seo-agent' label automatically."""
    token = os.environ.get("GH_TOKEN")
    if not token:
        sys.exit("ERROR: GH_TOKEN secret is missing.")

    url     = f"https://api.github.com/repos/{repo}/issues"
    headers = {
        "Authorization": f"Bearer {token}",
        "Accept": "application/vnd.github+json",
        "X-GitHub-Api-Version": "2022-11-28",
    }

    labels = list(issue.get("labels", [])) + ["seo-agent"]

    body_prefix = (
        f"> **SEO Agent** — auto-generated on {datetime.utcnow().strftime('%Y-%m-%d')}"
        f" for `{site_name}`\n\n"
    )

    payload = {
        "title":  issue.get("title", "[SEO] Untitled issue"),
        "body":   body_prefix + issue.get("body", ""),
        "labels": labels,
    }

    resp = requests.post(url, headers=headers, json=payload, timeout=15)

    if resp.status_code == 201:
        issue_url = resp.json().get("html_url", "")
        print(f"    ✅ Created: {payload['title']}")
        print(f"       {issue_url}")
    elif resp.status_code == 422:
        # Label might not exist yet — retry without labels
        print(f"    ⚠  Label error, retrying without labels...")
        payload["labels"] = []
        resp2 = requests.post(url, headers=headers, json=payload, timeout=15)
        if resp2.status_code == 201:
            print(f"    ✅ Created (no labels): {payload['title']}")
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

        print(f"  Asking Claude for analysis...")
        issues = ask_claude(name, formatted)
        print(f"  Claude generated {len(issues)} issue(s)")

        if not issues:
            print(f"  Skipping issue creation (no valid issues returned)\n")
            continue

        print(f"  Creating GitHub issues in {site['github_repo']}...")
        for issue in issues:
            create_github_issue(site["github_repo"], issue, name)

        print()

    print("Done.\n")


if __name__ == "__main__":
    main()