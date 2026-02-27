# Plastic-Craft GitHub Organization
# Strategy & Automation Report

**Prepared for:** Management
**Prepared by:** Development / AI Operations
**Date:** February 27, 2026
**Version:** 1.0

---

## Executive Summary

The Plastic-Craft GitHub organization currently contains **28 repositories**. Through a structured consolidation plan, we can reduce this to approximately **17 repositories** — eliminating redundancy, reducing maintenance overhead, and establishing a clean foundation for future development.

This report outlines:
- The consolidation plan and its phases
- Refactoring strategy for merged and existing code
- Automation opportunities that save recurring time and reduce human error
- Available AI-assisted tooling and what it enables

**Key outcomes:**
- 11 fewer repositories to maintain
- Standardized naming conventions across the organization
- Automated quality checks replacing manual review
- Reduced onboarding time for new tools and team members
- Lower risk of stale or conflicting code causing production issues

---

## Table of Contents

1. [Current State](#1-current-state)
2. [Consolidation Plan Overview](#2-consolidation-plan-overview)
3. [Refactoring Strategy](#3-refactoring-strategy)
4. [Automation Opportunities](#4-automation-opportunities)
5. [AI-Assisted Development Capabilities](#5-ai-assisted-development-capabilities)
6. [Risk Management](#6-risk-management)
7. [Execution Timeline](#7-execution-timeline)
8. [Appendix — Detailed Repo Inventory](#appendix--detailed-repo-inventory)

---

## 1. Current State

### Organization Snapshot

| Metric | Value |
|--------|-------|
| Total repositories | 28 |
| Active (commit within 30 days) | ~12 |
| Stale (no commit in 6+ months) | ~8 |
| Deprecated / no business value | 2 |
| Languages used | PHP, Python, JavaScript, PowerShell, HTML |
| Naming convention compliance | ~50% (14 of 28 use `pc-` prefix) |

### Problems with the Current State

1. **Repository sprawl** — Related tools are scattered across multiple repos (e.g., 3 shipping repos, 4 image repos), making it difficult to find the right code
2. **Duplicate functionality** — Multiple repos solve the same problem in different ways, leading to inconsistent behavior
3. **Stale code risk** — 8+ repos haven't been touched in months. Stale code becomes a liability when someone accidentally uses outdated logic
4. **No automation** — No CI/CD pipelines, no automated testing, no health monitoring. All quality assurance is manual
5. **Inconsistent naming** — Mix of naming styles (`Bin_Packing_Algo`, `WOOProductSKU`, `pc-shipping-tools`) makes the organization harder to navigate

---

## 2. Consolidation Plan Overview

### Summary Table

| Phase | Action | Repos Affected | Net Reduction | Priority |
|-------|--------|---------------|---------------|----------|
| 1 | Delete/archive unused repos | 2 | -2 | Immediate |
| 2 | Merge shipping tools | 3 → 1 | -2 | High |
| 3 | Merge image tools | 4 → 1 | -3 | High |
| 4 | Merge listing tools | 2 → 1 | -1 | Medium |
| 5 | Evaluate theme merge | 2 → 1 | -1 (pending) | Low |
| 6 | Standardize names | 7 repos | 0 (cleanup) | After merges |
| **Total** | | **28 → ~17** | **-9 to -11** | |

### Phase 1 — Remove Unused Repositories

| Repository | Action | Reason |
|-----------|--------|--------|
| `desktop-tutorial` | Delete | Auto-generated GitHub tutorial. No business value. |
| `plastic-craft-theme-trial-store` | Archive | Marked "no longer maintained" since Sep 2021. |

**Approach:** Archive first, delete after 30-day verification period.

### Phase 2 — Shipping Tools Consolidation (3 → 1)

| Source | Target | Status |
|--------|--------|--------|
| `shipping-rates` (JS, stale 4+ months) | `pc-shipping-tools` (Python, active) | Merge |
| `new-shipping` (JS, stale 6+ months) | `pc-shipping-tools` (Python, active) | Merge |

All shipping logic — calculators, rate analysis, ShipWorks integration — consolidated into one repository.

### Phase 3 — Image Tools Consolidation (4 → 1)

| Source | Target | Status |
|--------|--------|--------|
| `woocommerce-image-consolidation` (active 2 weeks ago) | `imagestaging` → rename `pc-images` | Merge |
| `plastic-craft-images` (public, stale 4+ years) | `imagestaging` → rename `pc-images` | Archive |
| `Images` (public, stale 4+ years) | `imagestaging` → rename `pc-images` | Archive |

**Note:** The two public repos may have external links. They will be archived with redirect notices rather than deleted.

### Phase 4 — Listing Tools Consolidation (2 → 1)

| Source | Target | Status |
|--------|--------|--------|
| `WOOProductSKU` (Python, active) | `Listing-Engine` → rename `pc-listing-engine` | Merge |

SKU management is a natural subset of listing management. Combining these eliminates duplication and creates a single source of truth for product data operations.

### Phase 5 — Theme Evaluation

| Repository | Platform | Decision |
|-----------|----------|----------|
| `plastic-craft-theme` | BigCommerce/HTML | Keep (master theme) |
| `plastic-cutting-boards-theme` | BigCommerce | Evaluate for merge |

**This requires careful evaluation.** Themes are live on customer-facing sites. A broken theme directly impacts sales. Recommendation: generate a detailed comparison report before making any merge decision.

### Phase 6 — Naming Standardization

| Current Name | New Name |
|-------------|----------|
| `Bin_Packing_Algo` | `pc-bin-packing` |
| `Listing-Engine` | `pc-listing-engine` |
| `PlasticCuttingBoard_BigCom` | `pc-pcb-bigcommerce` |
| `PlasticCuttingBoard_WooCom` | `pc-pcb-woocommerce` |
| `cut-to-size` | `pc-cut-to-size` |
| `pick-radio` | `pc-pick-radio` |
| `twilio-controller` | `pc-twilio` |
| `imagestaging` | `pc-images` |

GitHub automatically redirects old URLs to new names, minimizing breakage.

---

## 3. Refactoring Strategy

Consolidation alone is not enough. When code from multiple repositories is merged, it needs to be refactored to work as a cohesive unit. Below is the refactoring strategy for each merge and for the organization as a whole.

### 3.1 What Refactoring Means in This Context

Refactoring is restructuring existing code **without changing what it does**. The goals are:

- **Eliminate duplication** — When two repos have similar functions, keep one canonical version
- **Standardize patterns** — Consistent naming, file structure, error handling, and logging
- **Improve maintainability** — Code that's easier to read, test, and extend
- **Reduce technical debt** — Fix shortcuts and workarounds accumulated over time

### 3.2 Refactoring During Shipping Merge

| Area | Current State | Refactored State |
|------|--------------|-----------------|
| **Language fragmentation** | Python + JavaScript solving the same problems | Evaluate which implementations to keep; consolidate to one language where possible |
| **Rate calculation logic** | Potentially duplicated across 3 repos | Single canonical rate engine with clear inputs/outputs |
| **Configuration** | Hardcoded values scattered across files | Centralized config file (`.env` or `config.json`) |
| **ShipWorks integration** | In `pc-shipping-tools` only | Ensure merged code doesn't conflict with existing integration |

### 3.3 Refactoring During Images Merge

| Area | Current State | Refactored State |
|------|--------------|-----------------|
| **Script bootstrapping** | Hardcoded `wp-load.php` path | Configurable WordPress path via environment variable or config file |
| **CSV handling** | BOM-stripping, manual parsing | Standardized CSV reader utility used by all scripts |
| **Logging** | Each script rolls its own logging | Shared logging utility with consistent format |
| **WordPress interaction** | Direct function calls after wp-load | Abstracted into a helper layer for easier testing and potential WP-CLI migration |
| **Earlier consolidation attempts** | Code in `woocommerce-image-consolidation` may overlap | Merge only unique/improved logic; discard redundant code |

### 3.4 Refactoring During Listings Merge

| Area | Current State | Refactored State |
|------|--------------|-----------------|
| **SKU management** | Standalone scripts in `WOOProductSKU` | Integrated as a module within `pc-listing-engine` |
| **WooCommerce API calls** | Potentially duplicated between both repos | Shared API client with authentication and error handling |
| **Data models** | Different representations of products | Unified product data model used across all listing operations |

### 3.5 Organization-Wide Refactoring Standards

After merges are complete, apply these standards across all repos:

| Standard | Description |
|----------|-------------|
| **README template** | Every repo gets a standardized README: purpose, setup, usage, dependencies |
| **Config management** | No hardcoded server paths, API keys, or credentials in code. Use `.env` files or config objects |
| **Error handling** | Consistent try/catch patterns. Errors logged with context. Scripts exit with meaningful codes |
| **File structure** | Consistent directory layout: `src/`, `scripts/`, `data/`, `docs/`, `tests/`, `logs/` |
| **Git practices** | `.gitignore` covers logs, secrets, IDE files, OS files. Branch protection on main/master |

### 3.6 Refactoring Prioritization

Not all refactoring needs to happen immediately. Prioritize by impact:

| Priority | Refactoring Task | Reason |
|----------|-----------------|--------|
| **Critical** | Remove hardcoded paths and credentials | Security and portability |
| **High** | Eliminate duplicate logic during merges | Prevents confusion and bugs |
| **High** | Add README and setup docs to all repos | Enables anyone to use the tools |
| **Medium** | Standardize error handling and logging | Easier debugging |
| **Medium** | Centralize config management | Reduces deployment friction |
| **Low** | Standardize file/directory structure | Nice-to-have consistency |
| **Low** | Add type hints and docstrings | Improves long-term maintainability |

---

## 4. Automation Opportunities

These automations replace manual, error-prone, or time-consuming tasks with reliable automated processes.

### 4.1 CI/CD Pipelines (GitHub Actions)

**What it is:** Automated checks that run every time code is pushed or a pull request is created.

| Automation | Repos | What It Does | Time Saved |
|-----------|-------|-------------|------------|
| **PHP Linting** | `pc-images`, `plastic-craft-woo`, `pick-radio` | Catches syntax errors before deployment | Manual code review time per commit |
| **Python Linting & Tests** | `pc-shipping-tools`, `pc-customer-crm`, `pc-amazon-marketplace`, `pc-cad-generator` | Validates code quality and logic | Prevents bugs from reaching production |
| **JavaScript Build & Lint** | `pc-core`, `cut-to-size` | Catches build errors early | Eliminates "works on my machine" issues |
| **CSV Validation** | `pc-images` | Validates CSV format, checks for duplicate SKUs, verifies image URLs | Prevents running scripts with bad data |

**Example workflow for `pc-images`:**
```
On every push to main:
  1. Validate CSV format and structure
  2. Check for duplicate SKUs
  3. Verify master image URLs are valid format
  4. Run PHP syntax check on all scripts
  5. Report results as PR check status
```

### 4.2 Scheduled Health Monitoring

| Automation | Frequency | What It Does |
|-----------|-----------|-------------|
| **Repo health dashboard** | Weekly | Scans all org repos, reports: last activity, open issues, size, staleness |
| **Image health check** | Daily | Verifies master images on the live site are still accessible (automated Audit 3) |
| **Dependency security scan** | Daily | Checks all repos for known security vulnerabilities in dependencies |
| **Stale branch cleanup** | Weekly | Identifies and reports branches with no activity in 30+ days |

### 4.3 Process Automation

| Automation | Trigger | What It Does |
|-----------|---------|-------------|
| **Image consolidation pipeline** | CSV file updated | Runs validation (Stage 0), generates report, waits for manual approval, then executes |
| **SKU sync checker** | Scheduled / on-demand | Compares local SKU data against live WooCommerce data via REST API, reports discrepancies |
| **Shipping rate validator** | Rate table updated | Validates new rates against business rules, flags anomalies |
| **Backup verification** | Before any destructive script | Confirms Cloudways backup exists and is recent before allowing cleanup operations |

### 4.4 Notification & Alerting

| Automation | Channel | What It Does |
|-----------|---------|-------------|
| **Failed CI notification** | Email / Slack | Alerts when a push breaks validation checks |
| **Stale repo alert** | Email | Monthly summary of repos with no activity |
| **Domain monitor alerts** | Email / SMS (via Twilio) | Immediate notification when a monitored domain becomes available |
| **Deployment confirmation** | Email | Confirmation after scripts run successfully in production |

### 4.5 Automation Impact Summary

| Category | Manual Process | Automated Process | Estimated Savings |
|----------|---------------|-------------------|------------------|
| Code quality | Manual review of every change | Automated linting + validation on push | Hours per week |
| Data validation | Run scripts, check output manually | Automated pre-checks before execution | Prevents costly errors |
| Repository monitoring | Manually check each repo | Weekly automated dashboard | 1-2 hours per month |
| Image verification | Run audit script via SSH | Scheduled health checks with alerts | Catches issues days faster |
| Deployment safety | Remember to check backups | Automated backup verification | Eliminates human forgetfulness |

---

## 5. AI-Assisted Development Capabilities

The following capabilities are available through Claude Code for ongoing development and maintenance.

### 5.1 Code Operations

| Capability | Description | Example Use |
|-----------|-------------|-------------|
| **Full codebase search** | Search across all files for patterns, functions, references | "Find everywhere we reference the old shipping-rates repo" |
| **Multi-file editing** | Modify multiple files in a single operation | Update all config files to use new repo names after renames |
| **Code generation** | Write new scripts, functions, or modules | Build a new CSV validation utility for the images pipeline |
| **Code review** | Analyze code for bugs, security issues, or improvements | Review the consolidator script for edge cases |
| **Language support** | PHP, Python, JavaScript, PowerShell, HTML/CSS, Bash | Matches all languages in the Plastic-Craft stack |

### 5.2 GitHub Operations

| Capability | Description | Example Use |
|-----------|-------------|-------------|
| **Repository management** | Create, delete, archive, rename repos | Execute the consolidation plan |
| **Pull request workflows** | Create PRs with detailed descriptions | Stage merge operations for review before executing |
| **Issue management** | Create, label, transfer issues | Track consolidation tasks; move issues during repo merges |
| **Branch management** | Create branches, manage merges | Feature branches for each phase of consolidation |
| **GitHub Actions authoring** | Write and configure CI/CD workflows | Build all the automation described in Section 4 |

### 5.3 Research & Analysis

| Capability | Description | Example Use |
|-----------|-------------|-------------|
| **Web research** | Search for documentation, best practices, solutions | Research WP-CLI integration, WooCommerce REST API patterns |
| **Documentation review** | Read and analyze external docs | Understand BigCommerce export formats for migration planning |
| **Architecture evaluation** | Compare approaches, weigh trade-offs | Evaluate monorepo vs multi-repo for operations tools |
| **Competitive analysis** | Research how similar problems are solved elsewhere | Find open-source image consolidation tools to learn from |

### 5.4 Documentation & Reporting

| Capability | Description | Example Use |
|-----------|-------------|-------------|
| **Technical documentation** | READMEs, setup guides, API docs | Create docs for each merged repo |
| **Executive reports** | Business-friendly summaries | This report |
| **Migration guides** | Step-by-step instructions for transitions | Guide for team members after repo renames |
| **Audit reports** | Detailed analysis with findings and recommendations | The repository audit that started this initiative |

---

## 6. Risk Management

### Risk Matrix

| Risk | Likelihood | Impact | Mitigation |
|------|-----------|--------|-----------|
| **Merge breaks existing functionality** | Medium | High | Test in feature branches first; never merge directly to main |
| **Public repos have external links** | Low | Medium | Archive (don't delete); add redirect notices |
| **Theme merge causes site issues** | Medium | Very High | Full staging environment testing; keep backup theme ready |
| **Renamed repos break local clones** | High | Low | GitHub auto-redirects; document new names for team |
| **Data loss during repo deletion** | Low | Very High | Archive before delete; 30-day verification period |
| **Automation introduces false confidence** | Medium | Medium | Start with notification-only automations; add enforcement gradually |

### Safety Protocols

1. **No destructive actions without confirmation** — All deletions, archives, and force operations require explicit approval
2. **Staged execution model** — Already proven in the image consolidation scripts. Apply to all merges: validate → dry run → small test → full run
3. **Backup before every merge** — Create backups of source repos before migrating code
4. **Rollback plan** — Every phase has a documented rollback path. Archived repos can be unarchived. Git history preserves previous states
5. **Documentation trail** — Every action is logged and documented in commit messages and reports

---

## 7. Execution Timeline

### Recommended Schedule

```
PHASE 1 — FOUNDATION (Week 1-2)
│
├─ Archive desktop-tutorial and plastic-craft-theme-trial-store
├─ Generate cross-repo dependency map
├─ Establish organization-wide conventions document
└─ Set up repo health monitoring

PHASE 2 — SHIPPING MERGE (Week 3-4)
│
├─ Audit shipping-rates and new-shipping codebases
├─ Design merged directory structure
├─ Execute merge into pc-shipping-tools via PR
├─ Refactor: eliminate duplicate logic, centralize config
├─ Add CI/CD pipeline to merged repo
└─ Archive source repos

PHASE 3 — IMAGES MERGE (Week 5-6)
│
├─ Audit woocommerce-image-consolidation, plastic-craft-images, Images
├─ Migrate unique code into imagestaging
├─ Refactor: standardize bootstrapping, logging, config
├─ Rename to pc-images
├─ Add CI/CD pipeline (CSV validation, PHP lint)
├─ Set up scheduled image health monitoring
└─ Archive source repos

PHASE 4 — LISTINGS MERGE (Week 7-8)
│
├─ Audit WOOProductSKU codebase
├─ Merge into Listing-Engine
├─ Refactor: unify SKU handling, shared API client
├─ Rename to pc-listing-engine
├─ Add CI/CD pipeline
└─ Archive WOOProductSKU

PHASE 5 — NAMING & CLEANUP (Week 9)
│
├─ Batch rename 7 remaining repos
├─ Update all cross-references across the organization
├─ Verify GitHub redirects are working
└─ Update team documentation

PHASE 6 — THEME EVALUATION (Week 10+)
│
├─ Generate side-by-side theme comparison
├─ Produce recommendation report
├─ If merge recommended: execute in staging, test thoroughly
└─ Decision point: merge or keep separate

ONGOING
│
├─ Monitor repo health dashboard
├─ Maintain CI/CD pipelines
├─ Apply refactoring standards to remaining repos
└─ Iterate on automation based on team feedback
```

### Quick Wins Available Now
These can be started immediately with minimal risk:
1. Archive the 2 unused repos
2. Add `.gitignore` and README updates to repos missing them
3. Create the repo health dashboard script
4. Set up CSV validation for the images pipeline

---

## Appendix — Detailed Repo Inventory

### After Consolidation (Target State: ~17 repos)

**Web Properties (5)**
| Repo | Platform | Purpose |
|------|----------|---------|
| `plastic-craft-woo` | WooCommerce/PHP | Plastic-Craft store |
| `pc-pcb-bigcommerce` | BigCommerce | Plastic Cutting Boards store |
| `pc-pcb-woocommerce` | WooCommerce | PCB migration target |
| `plastic-craft-website` | WordPress | Corporate website |
| `plastic-craft-theme` | HTML/CSS | Master theme (+ possible PCB variant) |

**Operations Tools (4)**
| Repo | Language | Purpose |
|------|----------|---------|
| `pc-shipping-tools` | Python/JS | All shipping: calculators, rates, ShipWorks |
| `pc-images` | PHP | All image tools: consolidation, cleanup, audit |
| `pc-listing-engine` | Python | Product listings + SKU management |
| `pc-cad-generator` | Python | CAD file generation |

**Business Intelligence (3)**
| Repo | Language | Purpose |
|------|----------|---------|
| `pc-customer-crm` | Python | Customer intelligence |
| `pc-data-reports` | HTML | Analytics and dashboards |
| `pc-amazon-marketplace` | Python | Amazon operations |

**Infrastructure (3)**
| Repo | Language | Purpose |
|------|----------|---------|
| `pc-core` | JavaScript | Shared code and utilities |
| `pc-pick-radio` | PHP | Boss API connector |
| `pc-twilio` | Python | SMS/voice automation |

**Specialized (2)**
| Repo | Language | Purpose |
|------|----------|---------|
| `pc-bin-packing` | Python | Packing optimization |
| `pc-website-seo` | HTML | SEO templates and tools |

**Administrative (1)**
| Repo | Language | Purpose |
|------|----------|---------|
| `pc-email-automation` | PowerShell | Email and Exchange scripts |
| `pc-domain-monitor` | Python | Domain acquisition monitoring |

---

## 8. Logging & Reliability Standards

All scripts and automation produced as part of this initiative will adhere to the following logging and reliability requirements. These are **non-negotiable standards** — no script ships without them.

### 8.1 Comprehensive Logging

Every script, action, and automation must produce detailed logs. No silent operations.

**Log Requirements:**

| Requirement | Detail |
|------------|--------|
| **Every action logged** | Every file read, write, API call, database query, and decision point gets a log entry |
| **Timestamped entries** | ISO 8601 format: `2026-02-27T14:30:00-05:00` |
| **Log levels** | `INFO`, `WARN`, `ERROR`, `DEBUG` — filterable by level |
| **Context included** | Each entry includes: script name, function/phase, affected entity (SKU, file, repo) |
| **Outcome recorded** | Every operation logs both success and failure, with details |
| **Summary at completion** | Every script run ends with a summary: total processed, succeeded, failed, skipped |
| **Persistent storage** | Logs written to `/logs/` directory with date-stamped filenames |
| **No overwrites** | Each run creates a new log file; previous logs are never overwritten |

**Log Format Standard:**
```
[2026-02-27T14:30:00] [INFO] [consolidator] [Stage 2] Processing group 15 — SKU: PC-1234
[2026-02-27T14:30:01] [INFO] [consolidator] [Stage 2] Updated featured image for PC-1234 → attachment #4521
[2026-02-27T14:30:02] [WARN] [consolidator] [Stage 2] SKU PC-5678 not found in WooCommerce — skipped
[2026-02-27T14:30:03] [ERROR] [consolidator] [Stage 2] Failed to resolve image URL for group 16: HTTP 404
```

**Log File Naming:**
```
{script}_{phase}_{YYYYMMDD}_{HHmmss}.log        ← operation log
{script}_{phase}_{YYYYMMDD}_{HHmmss}_errors.log  ← errors-only log (filtered subset)
{script}_{phase}_{YYYYMMDD}_{HHmmss}_summary.log ← run summary
```

**What Gets Logged (by script type):**

| Script Type | Logged Events |
|------------|---------------|
| **PHP scripts** (consolidator, cleanup, audit) | Every SKU lookup, image resolution, database update, file deletion, validation check |
| **Python scripts** (shipping, CRM, Amazon) | Every API call, data transformation, file I/O, calculation result |
| **Shell scripts** (migration, batch ops) | Every git command, file move, repo operation, exit code |
| **GitHub Actions** | Workflow start/end, each step result, artifact paths, failure details |

### 8.2 Watcher & Retry System

A watcher system monitors script execution and automatically retries failed commands with intelligent backoff. No more losing progress to transient failures.

**Core Watcher Behavior:**

```
┌─────────────────────────────────────────────────┐
│                 WATCHER FLOW                     │
│                                                  │
│  1. Command executes                             │
│  2. Watcher captures exit code + output          │
│  3. If SUCCESS → log result, continue            │
│  4. If FAILURE →                                 │
│     a. Log the error (full output + exit code)   │
│     b. Classify error type (see table below)     │
│     c. If retryable → wait (backoff) → retry     │
│     d. If not retryable → log, alert, halt       │
│  5. After max retries → log failure, alert       │
└─────────────────────────────────────────────────┘
```

**Retry Configuration:**

| Parameter | Default | Description |
|-----------|---------|-------------|
| `max_retries` | 4 | Maximum number of retry attempts |
| `initial_delay` | 2 seconds | Wait time before first retry |
| `backoff_multiplier` | 2x | Each retry waits 2x longer (2s → 4s → 8s → 16s) |
| `max_delay` | 60 seconds | Cap on wait time between retries |
| `retry_on` | Configurable | Which error types trigger retries |

**Error Classification:**

| Error Type | Retryable? | Examples | Action |
|-----------|-----------|----------|--------|
| **Network/timeout** | Yes | API timeout, connection refused, DNS failure | Retry with backoff |
| **Rate limiting** | Yes | HTTP 429, "too many requests" | Retry with extended backoff |
| **Server error** | Yes | HTTP 500/502/503, database connection lost | Retry with backoff |
| **Authentication** | No | HTTP 401/403, invalid API key | Log error, halt, alert |
| **Not found** | No | HTTP 404, file not found, SKU not found | Log warning, skip item, continue |
| **Validation** | No | Bad data format, missing required field | Log error, skip item, continue |
| **Permission** | No | File permission denied, repo access denied | Log error, halt, alert |
| **Unknown** | No | Unrecognized exit code or error | Log full output, halt, alert |

**Watcher Output:**

After every watched command, the system produces:
```
┌─────────────────────────────────────────────────┐
│ WATCHER REPORT — consolidator.php Stage 4       │
│ Started:  2026-02-27T14:00:00                   │
│ Finished: 2026-02-27T14:25:13                   │
│ Duration: 25m 13s                               │
│                                                  │
│ Total commands:    138                           │
│ Succeeded:         134 (first attempt)           │
│ Succeeded (retry): 3 (after 1-2 retries)        │
│ Failed:            1 (max retries exceeded)      │
│                                                  │
│ Retries triggered: 7                             │
│   - Network timeout: 4                           │
│   - Server error:    3                           │
│                                                  │
│ Failed commands saved to:                        │
│   /logs/failed_commands_20260227_140000.json     │
│                                                  │
│ RERUN FAILED: php watcher.php --rerun-failed     │
│   /logs/failed_commands_20260227_140000.json     │
└─────────────────────────────────────────────────┘
```

**Rerun Capability:**

Failed commands are saved to a structured JSON file that can be re-executed:

```json
{
  "run_id": "20260227_140000",
  "script": "consolidator.php",
  "phase": "Stage 4",
  "failed_commands": [
    {
      "command": "update_featured_image",
      "args": {"sku": "PC-9999", "group": 87, "image_url": "https://..."},
      "error": "HTTP 503 Service Unavailable",
      "attempts": 4,
      "last_attempt": "2026-02-27T14:24:50",
      "retryable": true
    }
  ]
}
```

To rerun all failed commands:
```bash
php watcher.php --rerun-failed /logs/failed_commands_20260227_140000.json
```

To rerun a specific failed command:
```bash
php watcher.php --rerun-failed /logs/failed_commands_20260227_140000.json --index 0
```

**Watcher Integration Points:**

| System | How the Watcher Integrates |
|--------|---------------------------|
| **PHP scripts** (images) | Watcher wraps each WordPress API call and database operation |
| **Python scripts** (shipping, CRM) | Decorator-based retry on API calls; watcher wraps full script execution |
| **GitHub Actions** | Built-in retry with `continue-on-error` + custom retry step |
| **Shell scripts** | Watcher bash function wraps each critical command |
| **Git operations** | `git push`, `git fetch` wrapped with network retry (already specified: 4 retries, exponential backoff) |

### 8.3 Alerting on Failures

When the watcher exhausts retries or encounters a non-retryable error:

| Alert Method | When Used |
|-------------|-----------|
| **Log file** | Always — full error details written to error log |
| **Console output** | Always — colored error output with rerun instructions |
| **GitHub Issue** | Optional — auto-create issue in the relevant repo with error details |
| **Email notification** | Optional — configurable for critical production scripts |

### 8.4 Standards Checklist

Before any script is considered complete, it must pass this checklist:

- [ ] Every operation produces a log entry
- [ ] Log entries include timestamp, level, context, and outcome
- [ ] Errors include full error message and stack trace where available
- [ ] Script produces a summary report at completion
- [ ] Network/API calls are wrapped in retry logic with exponential backoff
- [ ] Failed commands are saved to a rerunnable JSON file
- [ ] Non-retryable errors halt execution and produce clear error messages
- [ ] Log files are date-stamped and never overwrite previous runs
- [ ] Watcher report is generated showing success/retry/failure counts

---

*Report prepared February 27, 2026. For questions or to initiate any phase of this plan, contact the development team.*
