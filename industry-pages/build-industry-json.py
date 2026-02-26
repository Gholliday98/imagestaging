#!/usr/bin/env python3
"""
Build a complete Elementor-importable JSON template for an industry page.

Usage:
    python3 build-industry-json.py <industry-slug> <applications-image-url>

Example:
    python3 build-industry-json.py aerospace https://plastic-craft.com/wp-content/uploads/2026/02/aerospace2.jpeg

The script:
  1. Reads the HTML source from industry-pages/<slug>/index.html
  2. Reads the full CSS from industry-pages/assets/css/industry.css
  3. Reads the full JS from industry-pages/assets/js/industry.js
  4. Extracts hero, intro, applications, materials, advantages, and CTA sections
  5. Replaces the applications image src with the provided URL
  6. Builds the Elementor JSON with CSS embedded in applications widget
     and JS embedded in advantages widget
  7. Writes the JSON to industry-pages/json/<slug>.json and prints it to stdout
"""

import json
import os
import re
import sys
import html as html_mod


def extract_between(text, start_tag, end_tag):
    """Extract content between start_tag and end_tag, inclusive."""
    start = text.find(start_tag)
    if start == -1:
        return None
    end = text.find(end_tag, start)
    if end == -1:
        return None
    return text[start:end + len(end_tag)]


def extract_hero_info(html):
    """Extract hero background image URL and title from HTML."""
    # Hero background image
    m = re.search(r"background-image:\s*url\(['\"]?([^'\")\s]+)['\"]?\)", html)
    hero_img = m.group(1) if m else ""

    # Hero title
    m = re.search(r'<h1 class="hero__title">(.+?)</h1>', html)
    hero_title = html_mod.unescape(m.group(1)) if m else "Industry Page"

    return hero_img, hero_title


def extract_intro_paragraphs(html):
    """Extract intro section paragraphs as styled HTML for the text-editor widget."""
    intro_section = extract_between(html, '<section class="intro', '</section>')
    if not intro_section:
        return ""

    paragraphs = re.findall(r'<p>(.*?)</p>', intro_section, re.DOTALL)
    styled = []
    for p in paragraphs:
        text = p.strip()
        styled.append(
            f'<p style="font-family: \'Lora\', serif; font-size: 1.1rem; '
            f'line-height: 1.85; color: #4A5568; text-align: center;">{text}</p>'
        )
    return "".join(styled)


def extract_section_html(html, class_name):
    """Extract a full <section class="...">...</section> block."""
    pattern = f'<section class="{class_name}"'
    start = html.find(pattern)
    if start == -1:
        return None

    # Find the matching </section>
    depth = 0
    i = start
    while i < len(html):
        if html[i:i+8] == '<section':
            depth += 1
        elif html[i:i+10] == '</section>':
            depth -= 1
            if depth == 0:
                return html[start:i + 10]
        i += 1
    return None


def extract_cta_description(html):
    """Extract the CTA section description paragraph."""
    cta = extract_section_html(html, 'cta-section')
    if not cta:
        return "Our specialist team is ready to help with your application."
    m = re.search(r'<p>(.*?)</p>', cta, re.DOTALL)
    return html_mod.unescape(m.group(1).strip()) if m else ""


def replace_applications_image(section_html, new_url):
    """Replace the applications image src with the user-provided URL."""
    return re.sub(
        r'(<img\s+src=")[^"]*(")',
        rf'\g<1>{new_url}\2',
        section_html,
        count=1
    )


def build_json(hero_title, hero_img, intro_html, applications_html_with_css,
               materials_html, advantages_html_with_js, cta_description):
    """Build the complete Elementor JSON structure."""
    return {
        "content": [
            # Container 1: Hero
            {
                "id": "029c722",
                "elType": "container",
                "settings": {
                    "content_width": "full",
                    "min_height": {"unit": "vw", "size": 27.5},
                    "flex_direction": "column",
                    "flex_justify_content": "center",
                    "flex_align_items": "flex-start",
                    "background_background": "classic",
                    "background_image": {"url": hero_img, "id": "", "size": ""},
                    "background_position": "center center",
                    "background_size": "cover",
                    "background_overlay_background": "gradient",
                    "background_overlay_color": "rgba(0,0,0,0.55)",
                    "background_overlay_color_b": "transparent",
                    "background_overlay_gradient_type": "linear",
                    "background_overlay_gradient_angle": {"unit": "deg", "size": 90},
                    "padding": {
                        "unit": "px", "top": "80", "right": "40",
                        "bottom": "80", "left": "40", "isLinked": False
                    }
                },
                "elements": [{
                    "id": "747a0aa",
                    "elType": "widget",
                    "widgetType": "heading",
                    "settings": {
                        "title": hero_title,
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
                            "horizontal": 0, "vertical": 2,
                            "blur": 16, "color": "rgba(0,0,0,0.6)"
                        }
                    },
                    "elements": []
                }],
                "isInner": False
            },
            # Container 2: Intro
            {
                "id": "4d5f302",
                "elType": "container",
                "settings": {
                    "content_width": "boxed",
                    "boxed_width": {"unit": "px", "size": 760},
                    "flex_direction": "column",
                    "flex_align_items": "center",
                    "padding": {
                        "unit": "px", "top": "56", "right": "40",
                        "bottom": "56", "left": "40", "isLinked": False
                    }
                },
                "elements": [{
                    "id": "3c99709",
                    "elType": "widget",
                    "widgetType": "text-editor",
                    "settings": {
                        "editor": intro_html,
                        "align": "center"
                    },
                    "elements": []
                }],
                "isInner": False
            },
            # Container 3: Applications (with embedded CSS)
            {
                "id": "4f09038",
                "elType": "container",
                "settings": {
                    "content_width": "full",
                    "padding": {
                        "unit": "px", "top": "0", "right": "0",
                        "bottom": "0", "left": "0", "isLinked": True
                    }
                },
                "elements": [{
                    "id": "ca77c0d",
                    "elType": "widget",
                    "widgetType": "html",
                    "settings": {
                        "html": applications_html_with_css
                    },
                    "elements": []
                }],
                "isInner": False
            },
            # Container 4: Materials
            {
                "id": "f6b64b4",
                "elType": "container",
                "settings": {
                    "content_width": "full",
                    "padding": {
                        "unit": "px", "top": "0", "right": "0",
                        "bottom": "0", "left": "0", "isLinked": True
                    }
                },
                "elements": [{
                    "id": "bda82b3",
                    "elType": "widget",
                    "widgetType": "html",
                    "settings": {
                        "html": materials_html
                    },
                    "elements": []
                }],
                "isInner": False
            },
            # Container 5: Advantages (with embedded JS)
            {
                "id": "e961e0e",
                "elType": "container",
                "settings": {
                    "content_width": "full",
                    "padding": {
                        "unit": "px", "top": "0", "right": "0",
                        "bottom": "0", "left": "0", "isLinked": True
                    }
                },
                "elements": [{
                    "id": "02ed2a9",
                    "elType": "widget",
                    "widgetType": "html",
                    "settings": {
                        "html": advantages_html_with_js
                    },
                    "elements": []
                }],
                "isInner": False
            },
            # Container 6: CTA
            {
                "id": "4d11d0f",
                "elType": "container",
                "settings": {
                    "content_width": "boxed",
                    "boxed_width": {"unit": "px", "size": 600},
                    "flex_direction": "column",
                    "flex_align_items": "center",
                    "background_background": "classic",
                    "background_color": "#F5F6F8",
                    "padding": {
                        "unit": "px", "top": "64", "right": "40",
                        "bottom": "64", "left": "40", "isLinked": False
                    },
                    "border_border": "solid",
                    "border_width": {
                        "unit": "px", "top": "1", "right": "0",
                        "bottom": "1", "left": "0", "isLinked": False
                    },
                    "border_color": "#E2E8F0"
                },
                "elements": [
                    {
                        "id": "d24182a",
                        "elType": "widget",
                        "widgetType": "heading",
                        "settings": {
                            "title": "Ready to Get Started?",
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
                        "id": "050e8bc",
                        "elType": "widget",
                        "widgetType": "text-editor",
                        "settings": {
                            "editor": (
                                f'<p style="font-family: \'Lora\', serif; font-size: 1.05rem; '
                                f'line-height: 1.8; color: #4A5568; text-align: center;">'
                                f'{cta_description}</p>'
                            ),
                            "align": "center"
                        },
                        "elements": []
                    },
                    {
                        "id": "8995d78",
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
                                "unit": "px", "top": "3", "right": "3",
                                "bottom": "3", "left": "3", "isLinked": True
                            },
                            "button_padding": {
                                "unit": "px", "top": "16", "right": "36",
                                "bottom": "16", "left": "36", "isLinked": False
                            },
                            "hover_color": "#FFFFFF",
                            "button_background_hover_color": "#2A4A7A"
                        },
                        "elements": []
                    }
                ],
                "isInner": False
            }
        ],
        "page_settings": {"template": "elementor_header_footer"},
        "version": "0.4",
        "title": "",
        "type": "page"
    }


def main():
    if len(sys.argv) < 3:
        print("Usage: python3 build-industry-json.py <industry-slug> <applications-image-url>")
        print("Example: python3 build-industry-json.py aerospace https://example.com/image.jpg")
        sys.exit(1)

    slug = sys.argv[1]
    app_image_url = sys.argv[2]

    # Resolve paths relative to this script
    script_dir = os.path.dirname(os.path.abspath(__file__))
    html_path = os.path.join(script_dir, slug, "index.html")
    css_path = os.path.join(script_dir, "assets", "css", "industry.css")
    js_path = os.path.join(script_dir, "assets", "js", "industry.js")
    out_path = os.path.join(script_dir, "json", f"{slug}.json")

    # Validate
    if not os.path.exists(html_path):
        print(f"ERROR: HTML source not found: {html_path}", file=sys.stderr)
        sys.exit(1)

    # Read sources
    with open(html_path, "r") as f:
        html = f.read()
    with open(css_path, "r") as f:
        css = f.read()
    with open(js_path, "r") as f:
        js = f.read()

    # Extract sections
    hero_img, hero_title = extract_hero_info(html)
    intro_html = extract_intro_paragraphs(html)
    applications_html = extract_section_html(html, "applications")
    materials_html = extract_section_html(html, "materials")
    advantages_html = extract_section_html(html, "advantages")
    cta_description = extract_cta_description(html)

    if not applications_html or not materials_html or not advantages_html:
        print("ERROR: Could not extract all required sections from HTML", file=sys.stderr)
        sys.exit(1)

    # Replace applications image URL
    applications_html = replace_applications_image(applications_html, app_image_url)

    # Embed CSS into applications widget, JS into advantages widget
    applications_html_with_css = f"<style>\n{css}\n</style>\n{applications_html}"
    advantages_html_with_js = f"{advantages_html}\n<script>\n{js}\n</script>"

    # Build the JSON
    data = build_json(
        hero_title=hero_title,
        hero_img=hero_img,
        intro_html=intro_html,
        applications_html_with_css=applications_html_with_css,
        materials_html=materials_html,
        advantages_html_with_js=advantages_html_with_js,
        cta_description=cta_description
    )
    data["title"] = hero_title

    # Write to file
    os.makedirs(os.path.dirname(out_path), exist_ok=True)
    with open(out_path, "w") as f:
        json.dump(data, f, indent=2, ensure_ascii=False)

    # Also print to stdout
    print(json.dumps(data, indent=2, ensure_ascii=False))
    print(f"\n--- Written to {out_path} ---", file=sys.stderr)


if __name__ == "__main__":
    main()
