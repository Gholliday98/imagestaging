# Known Issues & Bugs

## Issue 1: Not All Duplicate Images Being Removed
**Status:** Investigating  
**Reported:** Feb 2026

The cleanup script isn't catching all duplicates. Possible causes:

1. **Gallery images not checked** — The original cleanup script only checked `_thumbnail_id` (featured image). If duplicates are also stored as gallery images (`_product_image_gallery`), the cleanup misses them. **FIXED in v1.1.0** — cleanup now checks both.

2. **Images used outside WooCommerce** — If duplicate images are embedded in page content (Elementor pages, blog posts, etc.), the cleanup script won't detect that usage and would incorrectly flag them as "unused."

3. **Attachment URL resolution** — `attachment_url_to_postid()` can fail if URLs have been changed (HTTP vs HTTPS, domain changes, CDN URLs). This would cause the script to think a master image doesn't exist.

## Issue 2: Accuracy of Matching
**Status:** Needs audit  

Concerns about whether the image hash matching in the CSV is accurate. The `image_hash` column uses perceptual hashing to group visually similar images. Potential problems:

1. **False positives** — Different products that happen to look similar could be grouped together incorrectly
2. **Color variants grouped together** — If hash is too loose, a black sheet and a blue sheet might be in the same group when they should have different images

**Next step:** Run `audit_images.php` to generate a mismatch report, then manually spot-check 10-20 groups.

## Issue 3: Problem Groups Mixing Different Materials (RESOLVED)
**Status:** Fixed in visual_duplicates_CORRECTED.csv  
**Resolved:** Feb 2026

The original CSV had 8 groups that mixed products from different materials/colors under one master image. After manual review:

- **Group 2:** Split into 5 groups by material (Acetal, Expanded PVC, HDPE, Phenolic, UHMW)
- **Group 3:** Kept — Cast Type 6 + Extruded Type 6/6 can share image when same color (black)
- **Group 4:** Kept — Same as Group 3 (natural color)
- **Group 18:** Split into 3 groups by color (Black 10 SKUs, Red 9 SKUs, Yellow 9 SKUs)
- **Group 58:** Split into 2 groups (Acrylic Opaque vs Polypropylene)
- **Group 79:** Kept — Mirror sheets can share
- **Group 83:** Split into 3 groups (each Weld-On product gets its own image)
- **Group 88:** Kept — Same product, same color

Result: 129 groups → 138 groups. 4 single-SKU groups created (won't consolidate but won't cause issues).

## Issue 4: CSV Only Covers ~91% of Variations With Images
**Status:** By design (but may need expansion)

The CSV has 1,857 SKUs across 138 groups, covering 1,867 of 2,047 variations that have their own images. The remaining 180 variations have legitimately unique images (different colors, sizes of adhesives/accessories, etc.) confirmed by spot-check. Additionally:
- 1,600 variations inherit their parent's image (no action needed)
- 170 simple products exist (125 with images, 45 without — mostly unpublished SPEC pages)
- 219 parent products all have images

**Next step:** Run audit to confirm everything is aligned after consolidation.

## Issue 4: Group 53 Has Duplicate SKUs
**Status:** Minor bug in CSV

Group 53 has 10 SKU entries but only 5 unique SKUs (AB23, AB95, AB16, AB24, AB25 are each listed twice). **FIXED in scripts** — consolidator now deduplicates SKUs per group. The CSV should also be cleaned.

## Issue 5: Master Image Library Verification
**Status:** Needs verification

Need to confirm all 129 master images still exist in the WordPress media library. If any were accidentally deleted, the consolidator would fail silently for those groups.

**Next step:** Run `audit_images.php` — Audit 3 specifically checks this.
