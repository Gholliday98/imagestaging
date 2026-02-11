# Plastic-Craft Image Staging

WooCommerce product image consolidation scripts for plastic-craft.com.

## Problem
After migrating from BigCommerce to WooCommerce, ~8,000 products had thousands of duplicate images (visually identical files with different filenames). This bloated storage, slowed page loads, and complicated catalog management.

## Solution
Three-phase approach:
1. **Consolidate** — Assign one master image per visual group
2. **Cleanup** — Delete orphaned duplicate images  
3. **Audit** — Verify everything worked correctly

## Current Status
- CSV mapping: 138 groups, 1,857 SKUs (corrected from 129 groups after splitting problem groups)
- Consolidator: All stages tested successfully (0-4) on original CSV
- Cleanup: Needs verification — some duplicates may remain
- Coverage: 91% of variations with images (1,867 of 2,047)

## Directory Structure
```
imagestaging/
├── scripts/
│   ├── consolidator.php          # Phase 1: Assign master images
│   ├── cleanup_unused_images.php # Phase 2: Delete unused duplicates
│   └── audit_images.php          # Phase 3: Verify & diagnose issues
├── data/
│   ├── visual_duplicates.csv              # Original mapping (archived)
│   └── visual_duplicates_CORRECTED.csv    # Active mapping (scripts use this)
├── logs/                         # Auto-generated log files
└── docs/
    └── KNOWN_ISSUES.md           # Current bugs & things to fix
```

## Usage
All scripts must be run from the server via SSH (they require WordPress/WooCommerce):

```bash
cd /path/to/imagestaging/scripts
php consolidator.php        # Follow prompts (stages 0-4)
php audit_images.php        # Run after consolidation to check results
php cleanup_unused_images.php  # Run after audit confirms consolidation worked
```

## Safety
- Always run Stage 0 (validation) first
- Always have Jordan create a Cloudways backup before Stage 2+
- Audit before cleanup — never delete images without verifying consolidation worked
- All actions are logged to the `logs/` directory

## Known Issues
See [docs/KNOWN_ISSUES.md](docs/KNOWN_ISSUES.md)
