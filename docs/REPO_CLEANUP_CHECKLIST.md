# GitHub Repo Cleanup Checklist — Plastic-Craft

**Owner:** Gholliday98  
**Target:** 28 repos → ~17 repos  
**How to use:** Work top-to-bottom. Each section has the exact steps. Check off as you go.

---

## Phase 1: Delete (2 repos, ~2 minutes)

These are dead repos. Delete them from GitHub.

### 1. `desktop-tutorial`
- **Why:** Auto-generated GitHub Desktop tutorial. No business value.
- **Steps:**
  1. Go to https://github.com/Gholliday98/desktop-tutorial/settings
  2. Scroll to bottom → "Danger Zone" → **Delete this repository**
  3. Type the repo name to confirm
- [ ] Deleted

### 2. `plastic-craft-theme-trial-store`
- **Why:** Description says "no longer maintained." Untouched since Sep 2021.
- **Steps:**
  1. Go to https://github.com/Gholliday98/plastic-craft-theme-trial-store/settings
  2. Scroll to bottom → "Danger Zone" → **Delete this repository**
  3. Type the repo name to confirm
- [ ] Deleted

---

## Phase 2: Archive old image repos (2 repos, ~2 minutes)

These are 4+ years stale. Archive them so they're read-only but still accessible if anyone has links.

### 3. `plastic-craft-images`
- **Why:** Public, stale since Aug 2021. May have external links.
- **Steps:**
  1. Go to https://github.com/Gholliday98/plastic-craft-images/settings
  2. Scroll to bottom → "Danger Zone" → **Archive this repository**
- [ ] Archived

### 4. `Images`
- **Why:** FileRun sync folder. Public, stale since May 2021.
- **Steps:**
  1. Go to https://github.com/Gholliday98/Images/settings
  2. Scroll to bottom → "Danger Zone" → **Archive this repository**
- [ ] Archived

---

## Phase 3: Merge image repos (2 repos → into imagestaging, needs Claude Code)

### 5. `woocommerce-image-consolidation` → merge into `imagestaging`
- **Why:** Likely an earlier version of what's already in imagestaging.
- **Steps:**
  1. Open Claude Code on `woocommerce-image-consolidation`
  2. Say: "Compare this repo to imagestaging. Move anything useful into imagestaging, then tell me if this repo can be deleted."
  3. If everything is merged → delete or archive the repo
- [ ] Reviewed
- [ ] Merged or confirmed duplicate
- [ ] Deleted/archived

---

## Phase 4: Merge shipping repos (3 → 1, needs Claude Code)

**Target repo:** `pc-shipping-tools` (the most active one)

### 6. `shipping-rates` → merge into `pc-shipping-tools`
- **Why:** JavaScript, stale 4+ months (since Nov 2025). Overlaps with shipping tools.
- **Steps:**
  1. Open Claude Code on `pc-shipping-tools`
  2. Say: "Review the shipping-rates repo and merge any useful code into this repo. It's JavaScript, this repo is Python — adapt as needed or add a js/ subdirectory."
  3. After merge → archive `shipping-rates`
- [ ] Reviewed
- [ ] Merged
- [ ] Source repo archived

### 7. `new-shipping` → merge into `pc-shipping-tools`
- **Why:** JavaScript, stale 6+ months (since Sep 2025). Overlaps with shipping tools.
- **Steps:**
  1. Open Claude Code on `pc-shipping-tools`
  2. Say: "Review the new-shipping repo and merge any useful code into this repo."
  3. After merge → archive `new-shipping`
- [ ] Reviewed
- [ ] Merged
- [ ] Source repo archived

---

## Phase 5: Merge listing repos (2 → 1, needs Claude Code)

### 8. `WOOProductSKU` → merge into `Listing-Engine`
- **Why:** SKU management is a subset of listing management.
- **Steps:**
  1. Open Claude Code on `Listing-Engine`
  2. Say: "Review the WOOProductSKU repo and merge the SKU management code into this repo."
  3. After merge → archive `WOOProductSKU`
- [ ] Reviewed
- [ ] Merged
- [ ] Source repo archived

---

## Phase 6: Rename repos to `pc-` convention (do during merges or standalone)

These repos work fine but don't follow your `pc-` naming standard. Rename from GitHub Settings.

| # | Current Name | New Name | URL to rename |
|---|-------------|----------|---------------|
| 9 | `Bin_Packing_Algo` | `pc-bin-packing` | https://github.com/Gholliday98/Bin_Packing_Algo/settings |
| 10 | `Listing-Engine` | `pc-listing-engine` | https://github.com/Gholliday98/Listing-Engine/settings |
| 11 | `PlasticCuttingBoard_BigCom` | `pc-pcb-bigcommerce` | https://github.com/Gholliday98/PlasticCuttingBoard_BigCom/settings |
| 12 | `PlasticCuttingBoard_WooCom` | `pc-pcb-woocommerce` | https://github.com/Gholliday98/PlasticCuttingBoard_WooCom/settings |
| 13 | `cut-to-size` | `pc-cut-to-size` | https://github.com/Gholliday98/cut-to-size/settings |
| 14 | `pick-radio` | `pc-pick-radio` | https://github.com/Gholliday98/pick-radio/settings |
| 15 | `twilio-controller` | `pc-twilio` | https://github.com/Gholliday98/twilio-controller/settings |
| 16 | `imagestaging` | `pc-image-tools` | https://github.com/Gholliday98/imagestaging/settings |

**How to rename:**
1. Go to the settings URL above
2. Change the "Repository name" field at the top
3. Click "Rename"
4. GitHub auto-redirects the old URL for a while, but update any local clones:
   ```
   git remote set-url origin https://github.com/Gholliday98/NEW-NAME.git
   ```

- [ ] All renames done

---

## Phase 7: Evaluate theme merge (decide, then maybe merge)

### 17. `plastic-cutting-boards-theme` — merge into `plastic-craft-theme`?
- **Why:** If the master theme can handle PCB branding as a variant, you don't need a separate theme repo.
- **Decision needed:** Are these genuinely different themes, or is PCB a reskin?
- **Steps if merging:**
  1. Open Claude Code on `plastic-craft-theme`
  2. Say: "Compare plastic-cutting-boards-theme to this repo. Can the PCB theme be merged as a variant/child theme?"
  3. If yes → merge and archive
- [ ] Decision made: merge / keep separate
- [ ] If merging: completed

---

## Phase 8: Branch cleanup across all remaining repos

For each of your ~17 remaining repos, clean up stale branches:

**Quick way (GitHub web UI):**
1. Go to `https://github.com/Gholliday98/REPO-NAME/branches`
2. Delete any branches that have been merged or are clearly stale
3. Keep `main` and any actively-in-progress branches

**Repos to check:**

- [ ] `pc-shipping-tools` — https://github.com/Gholliday98/pc-shipping-tools/branches
- [ ] `plastic-craft-woo` — https://github.com/Gholliday98/plastic-craft-woo/branches
- [ ] `PlasticCuttingBoard_BigCom` — https://github.com/Gholliday98/PlasticCuttingBoard_BigCom/branches
- [ ] `PlasticCuttingBoard_WooCom` — https://github.com/Gholliday98/PlasticCuttingBoard_WooCom/branches
- [ ] `plastic-craft-website` — https://github.com/Gholliday98/plastic-craft-website/branches
- [ ] `plastic-craft-theme` — https://github.com/Gholliday98/plastic-craft-theme/branches
- [ ] `pc-customer-crm` — https://github.com/Gholliday98/pc-customer-crm/branches
- [ ] `pc-data-reports` — https://github.com/Gholliday98/pc-data-reports/branches
- [ ] `pc-amazon-marketplace` — https://github.com/Gholliday98/pc-amazon-marketplace/branches
- [ ] `pc-email-automation` — https://github.com/Gholliday98/pc-email-automation/branches
- [ ] `pc-domain-monitor` — https://github.com/Gholliday98/pc-domain-monitor/branches
- [ ] `pc-website-seo` — https://github.com/Gholliday98/pc-website-seo/branches
- [ ] `pc-core` — https://github.com/Gholliday98/pc-core/branches
- [ ] `cut-to-size` — https://github.com/Gholliday98/cut-to-size/branches
- [ ] `pick-radio` — https://github.com/Gholliday98/pick-radio/branches
- [ ] `twilio-controller` — https://github.com/Gholliday98/twilio-controller/branches
- [ ] `Bin_Packing_Algo` — https://github.com/Gholliday98/Bin_Packing_Algo/branches
- [ ] `pc-cad-generator` — https://github.com/Gholliday98/pc-cad-generator/branches
- [ ] `imagestaging` — DONE (cleaned up in this session)

---

## Scoreboard

| Phase | Action | Repos | Time Est. |
|-------|--------|-------|-----------|
| 1 | Delete dead repos | 2 | 2 min |
| 2 | Archive old image repos | 2 | 2 min |
| 3 | Merge image repos | 1 → imagestaging | 15 min |
| 4 | Merge shipping repos | 2 → pc-shipping-tools | 30 min |
| 5 | Merge listing repos | 1 → Listing-Engine | 15 min |
| 6 | Rename to pc- convention | 8 renames | 10 min |
| 7 | Evaluate theme merge | 1 decision | 15 min |
| 8 | Branch cleanup | 17 repos | 30 min |
| **Total** | | **28 → ~17** | **~2 hours** |

---

## After cleanup — your final repo list (~17 repos)

| Repo | Purpose |
|------|---------|
| `pc-image-tools` | Image consolidation & cleanup (was imagestaging) |
| `pc-shipping-tools` | Shipping calculators, rates, ShipWorks |
| `pc-listing-engine` | Product listings + SKU management |
| `plastic-craft-woo` | WooCommerce store |
| `pc-pcb-bigcommerce` | Plastic Cutting Boards (BigCommerce) |
| `pc-pcb-woocommerce` | PCB WooCommerce migration |
| `plastic-craft-website` | Corporate WordPress site |
| `plastic-craft-theme` | Master theme (+ PCB variant if merged) |
| `pc-customer-crm` | Customer intelligence |
| `pc-data-reports` | Analytics & dashboards |
| `pc-amazon-marketplace` | Amazon operations |
| `pc-email-automation` | Exchange/email scripts |
| `pc-domain-monitor` | Domain monitoring |
| `pc-website-seo` | SEO tools |
| `pc-core` | Shared/central code |
| `pc-cut-to-size` | Product calculator |
| `pc-pick-radio` | Boss API connector |
| `pc-twilio` | SMS/voice automation |
| `pc-bin-packing` | Packing optimization |
| `pc-cad-generator` | CAD file pipeline |
