#!/usr/bin/env python3
"""Convert industry page HTML files into Elementor-importable JSON templates.

Uses the container-based format (not section/column) matching the
Industries landing page template that imports successfully into Elementor.
"""

import json
import re
import os
import secrets
from html.parser import HTMLParser

PAGES_DIR = os.path.dirname(os.path.abspath(__file__))
JSON_DIR = os.path.join(PAGES_DIR, "json")

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


def uid():
    """Generate a 7-character hex ID like Elementor uses."""
    return secrets.token_hex(4)[:7]


def extract_title(html):
    match = re.search(r"<title>(.*?)</title>", html, re.DOTALL)
    if match:
        title = match.group(1).strip()
        title = re.sub(r"\s*\|\s*Plastic-Craft Products$", "", title)
        title = title.replace("&amp;", "&").replace("&mdash;", "\u2014").replace("&rsquo;", "\u2019")
        return title
    return ""


def extract_meta_description(html):
    match = re.search(r'<meta\s+name="description"\s+content="([^"]*)"', html)
    return match.group(1) if match else ""


def extract_hero_image(html):
    match = re.search(r"background-image:\s*url\('([^']+)'\)", html)
    return match.group(1) if match else ""


def extract_h1(html):
    match = re.search(r"<h1[^>]*>(.*?)</h1>", html, re.DOTALL)
    if match:
        return match.group(1).strip().replace("&amp;", "&").replace("&mdash;", "\u2014")
    return ""


def extract_intro_paragraphs(html):
    """Extract paragraphs from the intro section."""
    match = re.search(r'<section class="intro[^"]*">(.*?)</section>', html, re.DOTALL)
    if not match:
        return []
    paragraphs = re.findall(r"<p>(.*?)</p>", match.group(1), re.DOTALL)
    return [p.strip() for p in paragraphs]


def extract_section_html(html, section_class):
    """Extract the full inner HTML of a section by its class."""
    pattern = rf'<section class="{section_class}"[^>]*>(.*?)</section>'
    match = re.search(pattern, html, re.DOTALL)
    if match:
        return match.group(0).strip()
    return ""


def extract_body_content(html):
    """Extract content between <body> and </body>, excluding scripts."""
    match = re.search(r"<body>(.*?)</body>", html, re.DOTALL)
    if not match:
        return ""
    body = match.group(1).strip()
    body = re.sub(r"\s*<!--\s*=+\s*JAVASCRIPT\s*=+\s*-->\s*", "\n", body, flags=re.DOTALL)
    body = re.sub(r"\s*<script[^>]*>.*?</script>\s*", "", body, flags=re.DOTALL)
    return body.strip()


def extract_cta_text(html):
    """Extract CTA heading and paragraph."""
    match = re.search(r'<section class="cta-section">(.*?)</section>', html, re.DOTALL)
    if not match:
        return "Ready to Get Started?", "Contact our team for expert guidance."
    section = match.group(1)
    h2 = re.search(r"<h2>(.*?)</h2>", section, re.DOTALL)
    p = re.search(r"<p>(.*?)</p>", section, re.DOTALL)
    heading = h2.group(1).strip() if h2 else "Ready to Get Started?"
    para = p.group(1).strip() if p else ""
    return heading, para


def make_hero_container(title, image_url):
    """Hero: full-width container with background image + heading."""
    return {
        "id": uid(),
        "elType": "container",
        "settings": {
            "content_width": "full",
            "min_height": {"unit": "vw", "size": 27.5},
            "flex_direction": "column",
            "flex_justify_content": "center",
            "flex_align_items": "flex-start",
            "background_background": "classic",
            "background_image": {
                "url": image_url,
                "id": "",
                "size": ""
            },
            "background_position": "center center",
            "background_size": "cover",
            "background_overlay_background": "gradient",
            "background_overlay_color": "rgba(0,0,0,0.55)",
            "background_overlay_color_b": "transparent",
            "background_overlay_gradient_type": "linear",
            "background_overlay_gradient_angle": {"unit": "deg", "size": 90},
            "padding": {
                "unit": "px",
                "top": "80",
                "right": "40",
                "bottom": "80",
                "left": "40",
                "isLinked": False
            }
        },
        "elements": [
            {
                "id": uid(),
                "elType": "widget",
                "widgetType": "heading",
                "settings": {
                    "title": title,
                    "header_size": "h1",
                    "align": "left",
                    "title_color": "#FFFFFF",
                    "typography_typography": "custom",
                    "typography_font_family": "Montserrat",
                    "typography_font_size": {"unit": "rem", "size": 3},
                    "typography_font_weight": "700",
                    "typography_text_transform": "uppercase",
                    "typography_letter_spacing": {"unit": "px", "size": 3},
                    "text_shadow_text_shadow": {
                        "horizontal": 0,
                        "vertical": 2,
                        "blur": 16,
                        "color": "rgba(0,0,0,0.6)"
                    }
                },
                "elements": []
            }
        ],
        "isInner": False
    }


def make_intro_container(paragraphs):
    """Intro: boxed container with text-editor widget."""
    html_content = ""
    for p in paragraphs:
        html_content += f'<p style="font-family: \'Lora\', serif; font-size: 1.1rem; line-height: 1.85; color: #4A5568; text-align: center;">{p}</p>'

    return {
        "id": uid(),
        "elType": "container",
        "settings": {
            "content_width": "boxed",
            "boxed_width": {"unit": "px", "size": 760},
            "flex_direction": "column",
            "flex_align_items": "center",
            "padding": {
                "unit": "px",
                "top": "56",
                "right": "40",
                "bottom": "56",
                "left": "40",
                "isLinked": False
            }
        },
        "elements": [
            {
                "id": uid(),
                "elType": "widget",
                "widgetType": "text-editor",
                "settings": {
                    "editor": html_content,
                    "align": "center"
                },
                "elements": []
            }
        ],
        "isInner": False
    }


def make_html_section_container(section_html):
    """Wrap a raw HTML section in a full-width container with an HTML widget."""
    return {
        "id": uid(),
        "elType": "container",
        "settings": {
            "content_width": "full",
            "padding": {
                "unit": "px",
                "top": "0",
                "right": "0",
                "bottom": "0",
                "left": "0",
                "isLinked": True
            }
        },
        "elements": [
            {
                "id": uid(),
                "elType": "widget",
                "widgetType": "html",
                "settings": {
                    "html": section_html
                },
                "elements": []
            }
        ],
        "isInner": False
    }


def make_cta_container(heading, paragraph):
    """CTA: boxed container with heading, text, and button — matching landing page style."""
    return {
        "id": uid(),
        "elType": "container",
        "settings": {
            "content_width": "boxed",
            "boxed_width": {"unit": "px", "size": 600},
            "flex_direction": "column",
            "flex_align_items": "center",
            "background_background": "classic",
            "background_color": "#F5F6F8",
            "padding": {
                "unit": "px",
                "top": "64",
                "right": "40",
                "bottom": "64",
                "left": "40",
                "isLinked": False
            },
            "border_border": "solid",
            "border_width": {
                "unit": "px",
                "top": "1",
                "right": "0",
                "bottom": "1",
                "left": "0",
                "isLinked": False
            },
            "border_color": "#E2E8F0"
        },
        "elements": [
            {
                "id": uid(),
                "elType": "widget",
                "widgetType": "heading",
                "settings": {
                    "title": heading,
                    "header_size": "h2",
                    "align": "center",
                    "title_color": "#1B365D",
                    "typography_typography": "custom",
                    "typography_font_family": "Montserrat",
                    "typography_font_size": {"unit": "rem", "size": 1.6},
                    "typography_font_weight": "700",
                    "typography_text_transform": "uppercase",
                    "typography_letter_spacing": {"unit": "px", "size": 2}
                },
                "elements": []
            },
            {
                "id": uid(),
                "elType": "widget",
                "widgetType": "text-editor",
                "settings": {
                    "editor": f'<p style="font-family: \'Lora\', serif; font-size: 1.05rem; line-height: 1.8; color: #4A5568; text-align: center;">{paragraph}</p>',
                    "align": "center"
                },
                "elements": []
            },
            {
                "id": uid(),
                "elType": "widget",
                "widgetType": "button",
                "settings": {
                    "text": "GET A QUOTE",
                    "link": {
                        "url": "/request-a-quote/",
                        "is_external": False,
                        "nofollow": False
                    },
                    "align": "center",
                    "typography_typography": "custom",
                    "typography_font_family": "Montserrat",
                    "typography_font_size": {"unit": "rem", "size": 0.8},
                    "typography_font_weight": "700",
                    "typography_letter_spacing": {"unit": "px", "size": 2},
                    "typography_text_transform": "uppercase",
                    "background_color": "#1B365D",
                    "button_text_color": "#FFFFFF",
                    "border_radius": {
                        "unit": "px",
                        "top": "3",
                        "right": "3",
                        "bottom": "3",
                        "left": "3",
                        "isLinked": True
                    },
                    "button_padding": {
                        "unit": "px",
                        "top": "16",
                        "right": "36",
                        "bottom": "16",
                        "left": "36",
                        "isLinked": False
                    },
                    "hover_color": "#FFFFFF",
                    "button_background_hover_color": "#2A4A7A"
                },
                "elements": []
            }
        ],
        "isInner": False
    }


def build_elementor_template(folder_name):
    """Build a full Elementor page template from an industry page HTML file."""
    html_path = os.path.join(PAGES_DIR, folder_name, "index.html")
    if not os.path.exists(html_path):
        print(f"  SKIP: {html_path} not found")
        return

    with open(html_path, "r", encoding="utf-8") as f:
        html = f.read()

    title = extract_title(html)
    h1 = extract_h1(html)
    hero_image = extract_hero_image(html)
    intro_paragraphs = extract_intro_paragraphs(html)
    cta_heading, cta_para = extract_cta_text(html)

    # Extract the middle sections as raw HTML (applications, materials, advantages)
    applications_html = extract_section_html(html, "applications")
    materials_html = extract_section_html(html, "materials")
    advantages_html = extract_section_html(html, "advantages")

    # Build Elementor content array
    content = []

    # 1. Hero
    content.append(make_hero_container(h1 or title, hero_image))

    # 2. Intro
    if intro_paragraphs:
        content.append(make_intro_container(intro_paragraphs))

    # 3. Applications (as HTML widget with inline CSS included)
    if applications_html:
        content.append(make_html_section_container(applications_html))

    # 4. Materials
    if materials_html:
        content.append(make_html_section_container(materials_html))

    # 5. Advantages
    if advantages_html:
        content.append(make_html_section_container(advantages_html))

    # 6. CTA
    content.append(make_cta_container(cta_heading, cta_para))

    # Assemble template
    template = {
        "content": content,
        "page_settings": {
            "template": "elementor_header_footer"
        },
        "version": "0.4",
        "title": title,
        "type": "page"
    }

    json_path = os.path.join(JSON_DIR, f"{folder_name}.json")
    with open(json_path, "w", encoding="utf-8") as f:
        json.dump(template, f, indent=2, ensure_ascii=False)

    print(f"  OK: {folder_name}.json ({len(content)} containers)")


if __name__ == "__main__":
    os.makedirs(JSON_DIR, exist_ok=True)
    print(f"Generating Elementor templates in {JSON_DIR}\n")
    for page in PAGES:
        build_elementor_template(page)
    print(f"\nDone — {len(PAGES)} Elementor templates written to {JSON_DIR}")
