#!/usr/bin/env python3
"""Generate WordPress-compatible JSON files from industry page HTML templates."""

import json
import re
import os
from html.parser import HTMLParser

PAGES_DIR = os.path.dirname(os.path.abspath(__file__))
JSON_DIR = os.path.join(PAGES_DIR, "json")

# Map folder names to their data
PAGES = [
    "pharmaceuticals-research",
    "aerospace",
    "agriculture",
    "automotive-transportation",
    "boats-docks-marinas",
    "booths-exhibitions-activations",
    "construction-heavy-equipment",
    "defense",
    "education",
    "food-beverage-manufacturing",
    "lighting",
    "mining-mineral-extraction",
    "power-supply",
    "security",
    "signage",
    "stage-film-tv",
    "water-waste-management",
]


def extract_meta_description(html):
    match = re.search(r'<meta\s+name="description"\s+content="([^"]*)"', html)
    return match.group(1) if match else ""


def extract_title(html):
    match = re.search(r"<title>(.*?)</title>", html, re.DOTALL)
    if match:
        # Clean up the title — take part before the pipe or vertical separator
        title = match.group(1).strip()
        # Remove " | Plastic-Craft Products" suffix
        title = re.sub(r"\s*\|\s*Plastic-Craft Products$", "", title)
        # Decode HTML entities
        title = title.replace("&amp;", "&").replace("&mdash;", "—").replace("&rsquo;", "'")
        return title
    return ""


def extract_body_content(html):
    """Extract content between <body> and </body>, excluding <script> tags."""
    match = re.search(r"<body>(.*?)</body>", html, re.DOTALL)
    if not match:
        return ""
    body = match.group(1).strip()
    # Remove script tags and their preceding comment markers
    body = re.sub(r"\s*<!--\s*=+\s*JAVASCRIPT\s*=+\s*-->\s*", "\n", body, flags=re.DOTALL)
    body = re.sub(r"\s*<script[^>]*>.*?</script>\s*", "", body, flags=re.DOTALL)
    return body.strip()


def process_page(folder_name):
    html_path = os.path.join(PAGES_DIR, folder_name, "index.html")
    if not os.path.exists(html_path):
        print(f"  SKIP: {html_path} not found")
        return

    with open(html_path, "r", encoding="utf-8") as f:
        html = f.read()

    title = extract_title(html)
    meta_desc = extract_meta_description(html)
    body_content = extract_body_content(html)

    page_json = {
        "title": title,
        "slug": folder_name,
        "status": "draft",
        "content": body_content,
        "excerpt": meta_desc,
        "meta": {
            "_yoast_wpseo_metadesc": meta_desc
        }
    }

    json_path = os.path.join(JSON_DIR, f"{folder_name}.json")
    with open(json_path, "w", encoding="utf-8") as f:
        json.dump(page_json, f, indent=2, ensure_ascii=False)

    print(f"  OK: {folder_name}.json ({len(body_content):,} chars)")


if __name__ == "__main__":
    os.makedirs(JSON_DIR, exist_ok=True)
    print(f"Generating JSON files in {JSON_DIR}\n")
    for page in PAGES:
        process_page(page)
    print(f"\nDone — {len(PAGES)} files written to {JSON_DIR}")
