# Plastic-Craft Industry Pages — Project Guide

## Project Overview

This repo manages industry page templates for Plastic-Craft's WordPress/Elementor site. Each industry has an HTML source file and a corresponding Elementor-importable JSON template.

### Directory Structure

```
industry-pages/
├── <slug>/index.html          # HTML source for each industry
├── json/<slug>.json           # Generated Elementor JSON templates
├── assets/css/industry.css    # Shared CSS (embedded in JSON)
├── assets/js/industry.js      # Shared JS (embedded in JSON)
├── build-industry-json.py     # Build script for generating JSON
```

### Available Industries

aerospace, agriculture, automotive-transportation, boats-docks-marinas, booths-exhibitions-activations, construction-heavy-equipment, defense, education, food-beverage-manufacturing, lighting, mining-mineral-extraction, pharmaceuticals-research, power-supply, retail-environments, security, signage, stage-film-tv, water-waste-management

---

## Skill: Generate Industry Page JSON

### When to trigger

When the user asks for an industry page JSON (e.g., "I need the aerospace page", "give me the defense JSON", "next is lighting"), do the following:

### Required inputs from user

1. **Industry name** — map to the slug (e.g., "food and beverage" → `food-beverage-manufacturing`)
2. **Applications section image URL** — the user will provide this

### How to generate

Run the build script:

```bash
python3 /home/user/imagestaging/industry-pages/build-industry-json.py <slug> <applications-image-url>
```

This script automatically:
- Reads the HTML source from `industry-pages/<slug>/index.html`
- Reads the full CSS from `industry-pages/assets/css/industry.css`
- Reads the full JS from `industry-pages/assets/js/industry.js`
- Extracts hero (title + background image), intro text, applications, materials, advantages, and CTA sections
- Replaces the applications image `src` with the user-provided URL
- Embeds the **full CSS** inside a `<style>` tag prepended to the applications HTML widget
- Embeds the **full JS** inside a `<script>` tag appended to the advantages HTML widget
- Writes the JSON to `industry-pages/json/<slug>.json`
- Prints the full JSON to stdout

### After generating

1. **Output the full JSON** in chat as a ```json code block so the user can copy it
2. **Commit** the updated JSON file to git
3. **Push** to the current working branch

### Critical rules

- The JSON must ALWAYS have the full CSS embedded in the applications HTML widget and the full JS embedded in the advantages HTML widget. Never output a JSON without these.
- The applications image URL must be the one provided by the user, NOT the placeholder from the HTML source.
- The hero background image URL comes from the HTML source file automatically.
- Always output the complete JSON in chat — the user copies it to a notepad and uploads to Elementor.
