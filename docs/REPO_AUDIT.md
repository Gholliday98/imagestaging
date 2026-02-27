# GitHub Repository Audit — Plastic-Craft Organization

**Date:** 2026-02-27
**Total repositories:** 28
**Recommended target:** ~17 repositories

---

## Web Properties Overview

Plastic-Craft operates 3 distinct web properties:
1. **Plastic-Craft Store** — WooCommerce (migrated from BigCommerce)
2. **Plastic Cutting Boards Store** — BigCommerce (owned by Plastic-Craft, future WooCommerce migration planned)
3. **Plastic-Craft Corporate** — Standalone WordPress website

---

## ACTION: Delete (2 repos)

| Repo | Reason |
|------|--------|
| `desktop-tutorial` | Auto-generated GitHub Desktop tutorial. Zero business value. |
| `plastic-craft-theme-trial-store` | Description says "no longer maintained." Untouched since Sep 2021. |

---

## ACTION: Merge — Shipping (3 repos → 1)

**Target repo:** `pc-shipping-tools`

| Repo | Language | Last Active | Notes |
|------|----------|-------------|-------|
| `pc-shipping-tools` | Python | last week | Calculators, weight formulas, rate analysis, ShipWorks. **Keep as target.** |
| `shipping-rates` | JavaScript | Nov 2025 | Stale 4+ months. Merge useful code into pc-shipping-tools. |
| `new-shipping` | JavaScript | Sep 2025 | Stale 6+ months. Merge useful code into pc-shipping-tools. |

**Saves:** 2 repos

---

## ACTION: Merge — Images (4 repos → 1)

**Target repo:** Create new `pc-images` or use `imagestaging` as the consolidated repo.

| Repo | Visibility | Last Active | Notes |
|------|------------|-------------|-------|
| `imagestaging` | Private | now | Current active workspace. **Candidate as target.** |
| `woocommerce-image-consolidation` | Private | 2 weeks ago | Likely an earlier consolidation attempt. Merge in. |
| `plastic-craft-images` | Public | Aug 2021 | Stale 4+ years. Archive after migrating any needed assets. |
| `Images` | Public | May 2021 | FileRun sync folder. Stale 4+ years. Archive after migrating. |

**Note:** The two public repos (`plastic-craft-images`, `Images`) may have external links pointing to them. Consider redirecting or keeping as read-only archives if URLs are referenced elsewhere.

**Saves:** 3 repos

---

## ACTION: Merge — Product Listings (2 repos → 1)

**Target repo:** `Listing-Engine`

| Repo | Language | Last Active | Notes |
|------|----------|-------------|-------|
| `Listing-Engine` | — | 2 days ago | Broader scope. **Keep as target.** |
| `WOOProductSKU` | Python | yesterday | SKU management is a subset of listing management. Merge in. |

**Saves:** 1 repo

---

## ACTION: Evaluate — Website Theme

| Repo | Last Active | Notes |
|------|-------------|-------|
| `plastic-craft-theme` | Feb 2025 | Master/overall theme. **Keep.** |
| `plastic-cutting-boards-theme` | Jan 2025 | Potentially redundant if master theme covers PCB branding. **Evaluate whether this can be merged into `plastic-craft-theme` as a variant.** |

**Potential saves:** 1 repo (pending evaluation)

---

## KEEP — Website Repos (5 repos)

| Repo | Platform | Purpose |
|------|----------|---------|
| `plastic-craft-woo` | WooCommerce/PHP | Active Plastic-Craft store |
| `PlasticCuttingBoard_BigCom` | BigCommerce/HTML | Active Plastic Cutting Boards store |
| `PlasticCuttingBoard_WooCom` | WooCommerce | Future PCB migration target (in progress) |
| `plastic-craft-website` | WordPress/HTML | Standalone corporate website |
| `plastic-craft-theme` | BigCommerce/HTML | Master theme |

---

## KEEP — Operations & Business Tools (6 repos)

| Repo | Language | Purpose |
|------|----------|---------|
| `pc-customer-crm` | Python | Customer intelligence and profiling |
| `pc-data-reports` | HTML | Database blueprint, analytics, sales dashboards |
| `pc-amazon-marketplace` | Python | SAFE-T claims, listing optimization, inventory, compliance |
| `pc-email-automation` | PowerShell | Exchange scripts, email signature deployment |
| `pc-domain-monitor` | Python | Domain acquisition monitoring |
| `pc-website-seo` | HTML | SEO templates, scripts, meta tags, glossary, AEO |

---

## KEEP — Core & Integrations (5 repos)

| Repo | Language | Purpose |
|------|----------|---------|
| `pc-core` | JavaScript | Central/shared code |
| `cut-to-size` | JavaScript | Product calculator |
| `pick-radio` | PHP | API connector to Boss |
| `twilio-controller` | Python | SMS/voice automation |
| `Bin_Packing_Algo` | Python | Packing optimization |

---

## KEEP — Product Tools (1 repo)

| Repo | Language | Purpose |
|------|----------|---------|
| `pc-cad-generator` | Python | CAD file generation pipeline |

---

## Summary

| Action | Repos Affected | Net Change |
|--------|---------------|------------|
| Delete | 2 | -2 |
| Merge shipping | 3 → 1 | -2 |
| Merge images | 4 → 1 | -3 |
| Merge listings | 2 → 1 | -1 |
| Evaluate theme merge | 2 → 1 | -1 (pending) |
| Keep as-is | 17 | 0 |
| **Total** | **28 → ~17** | **-9 to -11** |

---

## Naming Convention Note

Several repos don't follow the `pc-` prefix convention. For consistency, consider renaming during merges:

| Current Name | Suggested Name |
|-------------|----------------|
| `Bin_Packing_Algo` | `pc-bin-packing` |
| `WOOProductSKU` | (merging into `Listing-Engine`) |
| `Listing-Engine` | `pc-listing-engine` |
| `PlasticCuttingBoard_BigCom` | `pc-pcb-bigcommerce` |
| `PlasticCuttingBoard_WooCom` | `pc-pcb-woocommerce` |
| `cut-to-size` | `pc-cut-to-size` |
| `pick-radio` | `pc-pick-radio` |
| `twilio-controller` | `pc-twilio` |
