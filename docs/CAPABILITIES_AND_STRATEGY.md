# Claude Code Capabilities & Strategy Report

**Date:** 2026-02-27
**Context:** Plastic-Craft GitHub Organization — Master Plan Execution
**Reference:** [REPO_AUDIT.md](./REPO_AUDIT.md)

---

## Table of Contents

1. [Available Capabilities Overview](#1-available-capabilities-overview)
2. [Capability-to-Plan Mapping](#2-capability-to-plan-mapping)
3. [Phase 1 — Quick Wins (Deletes & Archives)](#3-phase-1--quick-wins-deletes--archives)
4. [Phase 2 — Shipping Merge (3 → 1)](#4-phase-2--shipping-merge-3--1)
5. [Phase 3 — Images Merge (4 → 1)](#5-phase-3--images-merge-4--1)
6. [Phase 4 — Listings Merge (2 → 1)](#6-phase-4--listings-merge-2--1)
7. [Phase 5 — Theme Evaluation](#7-phase-5--theme-evaluation)
8. [Phase 6 — Rename & Standardize](#8-phase-6--rename--standardize)
9. [Cross-Cutting Automation Ideas](#9-cross-cutting-automation-ideas)
10. [Suggested Enhancements for Existing Repos](#10-suggested-enhancements-for-existing-repos)
11. [Recommended Execution Order](#11-recommended-execution-order)

---

## 1. Available Capabilities Overview

### GitHub CLI (`gh`)
Full programmatic access to the Plastic-Craft GitHub organization:

| Capability | Command Examples |
|-----------|-----------------|
| Create repositories | `gh repo create Gholliday98/pc-images --private` |
| Delete repositories | `gh repo delete Gholliday98/desktop-tutorial --yes` |
| Archive repositories | `gh repo archive Gholliday98/shipping-rates` |
| Rename repositories | `gh repo rename pc-bin-packing -R Gholliday98/Bin_Packing_Algo` |
| Create issues | `gh issue create -R Gholliday98/repo --title "..." --body "..."` |
| Create pull requests | `gh pr create --title "..." --body "..."` |
| View repo metadata | `gh repo view`, `gh api repos/Gholliday98/{repo}` |
| Manage labels/milestones | `gh label create`, `gh api` for milestones |
| Transfer issues | `gh issue transfer` between repos during merges |

### Git Operations
Full version control for code migration and merging:

| Capability | Use Case |
|-----------|----------|
| Clone & fetch repos | Pull down source repos for merge analysis |
| Subtree merges | Merge one repo into a subdirectory of another |
| Filter-repo / filter-branch | Rewrite history to move files into new paths |
| Branch management | Create feature branches for staged merges |
| Commit history preservation | Keep attribution and history during migrations |

### Code Analysis & Refactoring
Deep codebase understanding and modification:

| Capability | Use Case |
|-----------|----------|
| Full-text search (Grep) | Find shared functions, duplicate code, cross-repo references |
| Pattern matching (Glob) | Locate files by type, name, or structure across repos |
| File reading & editing | Understand and modify any file in any language |
| Multi-file refactoring | Update imports, paths, namespaces after merges |
| Dependency analysis | Map which repos/scripts depend on each other |

### Web Research
Access to current documentation and best practices:

| Capability | Use Case |
|-----------|----------|
| Web search | Look up WooCommerce APIs, WordPress best practices, migration guides |
| URL fetching | Pull documentation pages, API references, plugin details |
| Technology research | Evaluate tools, plugins, and approaches before implementation |

### Automation & Script Writing
Create tooling to support operations:

| Capability | Use Case |
|-----------|----------|
| PHP scripting | Extend existing image consolidation scripts |
| Python scripting | Match the stack used in shipping, CRM, Amazon tools |
| Shell/Bash scripting | Build batch operations, deployment helpers |
| GitHub Actions | CI/CD pipelines, scheduled tasks, automated checks |
| PowerShell | Match the stack used in email automation |

### Project Planning & Documentation
Organize and track the consolidation effort:

| Capability | Use Case |
|-----------|----------|
| Task management | Break down each phase into trackable steps |
| Report generation | Audit reports, merge compatibility reports, progress summaries |
| Documentation writing | READMEs, migration guides, architecture docs |
| Architecture planning | Design directory structures, evaluate monorepo vs multi-repo |

---

## 2. Capability-to-Plan Mapping

| Master Plan Action | Primary Capabilities Used | Secondary Capabilities |
|-------------------|--------------------------|----------------------|
| Delete 2 repos | GitHub CLI | — |
| Merge shipping (3→1) | Git operations, Code analysis | GitHub CLI, Script writing |
| Merge images (4→1) | Git operations, Code analysis | GitHub CLI, PHP scripting |
| Merge listings (2→1) | Git operations, Code analysis | GitHub CLI, Python scripting |
| Evaluate theme merge | Code analysis, Web research | Documentation |
| Rename repos | GitHub CLI | Documentation |
| Add CI/CD pipelines | GitHub Actions, Script writing | Web research |
| Cross-repo dependency map | Code analysis (Grep/Glob) | Documentation |

---

## 3. Phase 1 — Quick Wins (Deletes & Archives)

**Repos:** `desktop-tutorial`, `plastic-craft-theme-trial-store`
**Effort:** Minimal
**Risk:** Very low

### What I Can Do

1. **Verify repos are truly unused**
   - Check last commit dates, open issues, forks, stars
   - Search other repos for references to these repos
   - Confirm no GitHub Pages or webhooks are active

2. **Execute deletion or archival**
   - Archive first (reversible), delete later (irreversible)
   - `gh repo archive` preserves history while marking as inactive
   - `gh repo delete` permanently removes (requires confirmation)

3. **Generate confirmation report** before any destructive action

### Recommendation
Archive both repos first. Wait 30 days, then delete if no issues surface. This is the safest path.

---

## 4. Phase 2 — Shipping Merge (3 → 1)

**Source repos:** `shipping-rates` (JS), `new-shipping` (JS)
**Target repo:** `pc-shipping-tools` (Python)
**Effort:** Medium
**Risk:** Low-Medium (stale sources, active target)

### What I Can Do

1. **Pre-merge audit of all 3 repos**
   - Clone and analyze each repo's structure, dependencies, and purpose
   - Identify overlapping functionality between JS and Python codebases
   - Map any external references (API keys, server paths, config files)
   - Generate a compatibility report

2. **Design the merged directory structure**
   ```
   pc-shipping-tools/
   ├── python/           ← existing Python calculators, ShipWorks tools
   ├── javascript/       ← migrated from shipping-rates, new-shipping
   │   ├── rates/        ← from shipping-rates
   │   └── calculator/   ← from new-shipping
   ├── docs/
   ├── tests/
   └── README.md         ← updated to cover all tools
   ```

3. **Execute the merge**
   - Create a feature branch on `pc-shipping-tools`
   - Migrate relevant code from both JS repos with history
   - Update any internal references/imports
   - Create PR for review

4. **Post-merge cleanup**
   - Archive `shipping-rates` and `new-shipping`
   - Update any cross-repo references in other Plastic-Craft repos

### Ideas & Suggestions
- **Language consolidation:** Since the target is Python and the sources are JS, evaluate whether the JS functionality should be rewritten in Python for consistency, or kept as-is in a `javascript/` subdirectory
- **Shared config:** Create a unified shipping configuration format that both Python and JS tools can read
- **GitHub Action:** Add a CI workflow that validates shipping rate calculations on every push

---

## 5. Phase 3 — Images Merge (4 → 1)

**Source repos:** `woocommerce-image-consolidation`, `plastic-craft-images`, `Images`
**Target repo:** `imagestaging` (rename to `pc-images`)
**Effort:** Medium-High
**Risk:** Medium (active workspace, public repos with potential external links)

### What I Can Do

1. **Audit all 4 repos**
   - Deep-dive into `woocommerce-image-consolidation` to find useful code not already in `imagestaging`
   - Check `plastic-craft-images` and `Images` for any assets still referenced by the live site
   - Verify no external links point to the public repos' raw file URLs

2. **Merge strategy**
   - `imagestaging` becomes the target (already has the active tooling)
   - Migrate any unique scripts/assets from `woocommerce-image-consolidation`
   - Extract any still-needed assets from the two stale public repos
   - Rename to `pc-images` via `gh repo rename`

3. **Handle the public repo risk**
   - Search for any URLs referencing `plastic-craft-images` or `Images` repos across all other repos
   - Check if GitHub Pages is enabled on either
   - If external links exist, archive (don't delete) and add a README redirect notice

4. **Reorganize `imagestaging` for broader scope**
   ```
   pc-images/
   ├── consolidation/
   │   ├── scripts/       ← existing consolidator, cleanup, audit
   │   ├── data/          ← existing CSVs
   │   └── logs/
   ├── tools/             ← migrated from woocommerce-image-consolidation
   ├── assets/            ← any migrated assets from stale repos
   ├── docs/
   │   ├── REPO_AUDIT.md
   │   ├── KNOWN_ISSUES.md
   │   └── CAPABILITIES_AND_STRATEGY.md
   └── README.md
   ```

### Ideas & Suggestions
- **Image Pipeline V2:** Build a more automated workflow — CSV changes trigger a GitHub Action that runs validation (Stage 0), generates a report, and waits for manual approval before proceeding
- **WP-CLI integration:** Research whether WP-CLI could replace the `wp-load.php` bootstrapping for cleaner remote execution
- **Image health monitoring:** A scheduled GitHub Action that periodically checks if master images are still accessible on the live site (essentially automated Audit 3)

---

## 6. Phase 4 — Listings Merge (2 → 1)

**Source repo:** `WOOProductSKU` (Python)
**Target repo:** `Listing-Engine` (rename to `pc-listing-engine`)
**Effort:** Low-Medium
**Risk:** Low (both recently active, likely complementary)

### What I Can Do

1. **Compatibility analysis**
   - Compare the two repos for overlapping SKU management logic
   - Identify shared dependencies and potential conflicts
   - Map how `WOOProductSKU` functions relate to `Listing-Engine` features

2. **Execute the merge**
   - Migrate `WOOProductSKU` code into a subdirectory or integrate directly
   - Resolve any naming conflicts
   - Update imports and references
   - Rename to `pc-listing-engine`

3. **Create unified documentation**
   - Combined README covering both SKU management and listing engine features
   - Usage guide for the merged toolset

### Ideas & Suggestions
- **SKU validation pipeline:** Add a GitHub Action that validates SKU format consistency on every commit
- **WooCommerce sync:** Build a script that can diff local SKU data against live WooCommerce data via REST API
- **Listing templates:** If `Listing-Engine` handles product descriptions, consider adding template support for consistent formatting across platforms (WooCommerce, Amazon, BigCommerce)

---

## 7. Phase 5 — Theme Evaluation

**Repos:** `plastic-craft-theme`, `plastic-cutting-boards-theme`
**Effort:** High (evaluation) + Variable (merge if applicable)
**Risk:** High (themes are live on production sites)

### What I Can Do

1. **Side-by-side comparison**
   - Diff the two theme codebases structurally
   - Identify shared templates, styles, and functions
   - Calculate percentage of code overlap
   - Map theme-specific customizations

2. **Evaluate merge feasibility**
   - Determine if a parent/child theme approach works
   - Check for BigCommerce vs WooCommerce platform conflicts
   - Research multi-brand theme patterns in WordPress/WooCommerce

3. **Generate a recommendation report**
   - Merge feasibility score
   - Estimated effort
   - Risk assessment
   - Recommended approach (merge, keep separate, or parent/child)

### Ideas & Suggestions
- **Parent/child theme pattern:** If the themes share 60%+ code, a parent theme with brand-specific child themes could work well
- **CSS custom properties:** Use CSS variables for brand colors/fonts so one codebase can serve both brands
- **Don't rush this one:** Themes directly affect live customer-facing sites. A broken theme = lost sales. Recommend thorough testing in a staging environment before any merge

---

## 8. Phase 6 — Rename & Standardize

**Repos to rename:** 7 repos (per audit recommendations)
**Effort:** Low per repo
**Risk:** Low-Medium (may break existing bookmarks, CI references, local clones)

### What I Can Do

1. **Execute renames via GitHub CLI**
   - `gh repo rename` handles the rename and sets up redirects automatically
   - GitHub auto-redirects old URLs to new names (for a period)

2. **Update cross-references**
   - Search all repos for references to old repo names
   - Update any hardcoded URLs, git remote URLs, or documentation links
   - Update any CI/CD configs that reference repos by name

3. **Batch rename script**
   - Create a script that handles all 7 renames in sequence
   - Generates a before/after report
   - Updates local git remotes if repos are cloned locally

### Full Rename Plan

| Current Name | New Name | Type |
|-------------|----------|------|
| `Bin_Packing_Algo` | `pc-bin-packing` | Rename |
| `Listing-Engine` | `pc-listing-engine` | Rename (after merge) |
| `PlasticCuttingBoard_BigCom` | `pc-pcb-bigcommerce` | Rename |
| `PlasticCuttingBoard_WooCom` | `pc-pcb-woocommerce` | Rename |
| `cut-to-size` | `pc-cut-to-size` | Rename |
| `pick-radio` | `pc-pick-radio` | Rename |
| `twilio-controller` | `pc-twilio` | Rename |
| `imagestaging` | `pc-images` | Rename (after merge) |

### Suggestion
Do renames **after** merges are complete. Renaming mid-merge adds unnecessary complexity.

---

## 9. Cross-Cutting Automation Ideas

These ideas apply across the entire organization, not just individual merges:

### 9.1 Automated Repo Health Dashboard
**Capabilities used:** GitHub CLI, Script writing

A script that runs `gh api` across all repos and generates a report:
- Last commit date per repo
- Open issues and PRs
- Repo size and language breakdown
- Active vs stale classification
- Could run as a scheduled GitHub Action in a `pc-org-tools` repo

### 9.2 Cross-Repo Dependency Map
**Capabilities used:** Code analysis (Grep/Glob), Documentation

Before merging anything, scan all 28 repos to find:
- Shared function names or modules
- Hardcoded references to other repos
- Shared API keys or config patterns
- Common dependencies (package.json, requirements.txt, composer.json)

Outputs a dependency graph showing which repos are connected.

### 9.3 Organization-Wide GitHub Actions
**Capabilities used:** GitHub Actions, Script writing

Standardized CI/CD templates for the org:
- **PHP repos:** Lint + WordPress coding standards
- **Python repos:** Lint + type checking + tests
- **JavaScript repos:** Lint + build + tests
- **All repos:** Auto-label stale issues, dependabot for security updates

### 9.4 Centralized Documentation Hub
**Capabilities used:** Documentation, GitHub CLI

A `pc-docs` or `pc-org-tools` repo containing:
- Organization-wide conventions (naming, branching, commit messages)
- Repo inventory (auto-updated by the health dashboard)
- Onboarding guide for new team members or AI assistants
- Links to all active repos with descriptions

### 9.5 Backup & Recovery Strategy
**Capabilities used:** Script writing, GitHub Actions

Automated backup workflow:
- Nightly clone of all repos to a secondary location
- Webhook notifications if a repo is deleted or archived
- Export of all issues/PRs before any repo deletion

---

## 10. Suggested Enhancements for Existing "Keep" Repos

Beyond the consolidation plan, here are improvements I can make to repos that are staying as-is:

| Repo | Enhancement | Capability Used |
|------|-------------|----------------|
| `pc-customer-crm` | Add data validation scripts, export templates | Python scripting |
| `pc-data-reports` | Build automated report generation pipeline | Script writing, GitHub Actions |
| `pc-amazon-marketplace` | Add SAFE-T claim tracking automation | Python scripting, Web research |
| `pc-email-automation` | Convert PowerShell to cross-platform Python | Python scripting |
| `pc-domain-monitor` | Add Slack/email alerts when domains become available | Python scripting, Web research |
| `pc-website-seo` | Build SEO audit automation, sitemap validation | Script writing, Web research |
| `pc-core` | Add shared utilities, org-wide constants | JavaScript, Documentation |
| `cut-to-size` | Add unit tests, input validation | JavaScript, GitHub Actions |
| `pick-radio` | Add health check endpoint, error logging | PHP scripting |
| `twilio-controller` | Add message templates, delivery tracking | Python scripting |
| `Bin_Packing_Algo` | Add visualization output, batch processing | Python scripting |
| `pc-cad-generator` | Add template library, output format options | Python scripting |

---

## 11. Recommended Execution Order

```
WEEK 1-2: Foundation
├── Phase 1: Archive/delete 2 unused repos (30 min)
├── Cross-repo dependency scan (2-3 hours)
└── Generate repo health dashboard (1-2 hours)

WEEK 3-4: First Merge
├── Phase 2: Shipping merge — audit (1 hour)
├── Phase 2: Shipping merge — execute (2-3 hours)
└── Phase 2: Shipping merge — verify & archive sources (1 hour)

WEEK 5-6: Images Merge
├── Phase 3: Images merge — audit all 4 repos (2 hours)
├── Phase 3: Images merge — execute (3-4 hours)
├── Phase 3: Images merge — handle public repo risk (1 hour)
└── Phase 3: Images merge — rename to pc-images (30 min)

WEEK 7-8: Listings & Cleanup
├── Phase 4: Listings merge — audit & execute (2-3 hours)
├── Phase 6: Batch rename remaining repos (1-2 hours)
└── Update all cross-references (1-2 hours)

WEEK 9+: Theme Evaluation
├── Phase 5: Theme comparison analysis (3-4 hours)
├── Phase 5: Recommendation report (1 hour)
└── Phase 5: Execute if recommended (variable)

ONGOING
├── Add GitHub Actions to merged repos
├── Enhance "keep" repos per suggestions
└── Maintain repo health dashboard
```

---

## Quick Reference — What to Ask Me

| When you want to... | Say something like... |
|---------------------|----------------------|
| Start a merge | "Let's start the shipping merge" |
| Audit a repo | "Audit the woocommerce-image-consolidation repo" |
| Delete/archive a repo | "Archive desktop-tutorial" |
| Rename a repo | "Rename Bin_Packing_Algo to pc-bin-packing" |
| Scan for dependencies | "Scan all repos for cross-references" |
| Build a GitHub Action | "Add CI to pc-shipping-tools" |
| Research a tool/approach | "Research WP-CLI for image management" |
| Generate a report | "Generate a health report for all repos" |
| Write a script | "Write a script to validate shipping rates" |

---

*This report was generated as part of the Plastic-Craft GitHub organization consolidation initiative. It maps Claude Code's available capabilities to each phase of the master plan outlined in [REPO_AUDIT.md](./REPO_AUDIT.md).*
