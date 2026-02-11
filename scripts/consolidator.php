<?php
/**
 * WooCommerce Image Consolidator
 * 
 * Assigns master images to products based on visual_duplicates.csv mapping.
 * Products are grouped by visual similarity (image hash), and each group
 * gets assigned a single master image.
 *
 * USAGE: php consolidator.php (run from the scripts/ directory)
 * REQUIRES: ../data/visual_duplicates.csv
 * 
 * Stages:
 *   0 - VALIDATION: Checks first 5 SKUs exist (no changes)
 *   1 - DRY RUN: Shows what would happen (no changes)  
 *   2 - TEST 10: Updates first 10 groups (real changes)
 *   3 - TEST 50: Updates first 50 groups (real changes)
 *   4 - FULL RUN: Updates all groups (real changes)
 *
 * @package PlasticCraft
 * @version 1.1.0
 */

define('WP_USE_THEMES', false);
require_once('/home/master/applications/embhtsmzsy/public_html/wp-load.php');

echo "\n=== WOOCOMMERCE IMAGE CONSOLIDATOR ===\n\n";
echo "Stages:\n";
echo " 0: VALIDATION (no changes)\n";
echo " 1: DRY RUN (no changes)\n";
echo " 2: TEST 10 products\n";
echo " 3: TEST 50 products\n";
echo " 4: FULL RUN\n\n";
echo "Enter stage (0-4): ";

$stage = trim(fgets(STDIN));
if (!in_array($stage, ['0','1','2','3','4'])) die("Invalid\n");

$csv_file = __DIR__ . '/../data/visual_duplicates_CORRECTED.csv';
$log_dir = __DIR__ . '/../logs';
if (!is_dir($log_dir)) mkdir($log_dir, 0755, true);

$log = '';
function logit(&$log, $msg) {
    $entry = "[" . date('H:i:s') . "] $msg\n";
    $log .= $entry;
    echo $entry;
}

// --- Load and validate CSV ---
logit($log, "Loading CSV...");
if (!file_exists($csv_file)) {
    die("ERROR: CSV not found at $csv_file\n");
}

$handle = fopen($csv_file, 'r');
$headers = fgetcsv($handle);

// Strip BOM if present
$headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', $headers[0]);

$data = [];
while (($row = fgetcsv($handle)) !== false) {
    if (count($row) === count($headers)) {
        $data[] = array_combine($headers, $row);
    }
}
fclose($handle);
logit($log, "Loaded " . count($data) . " groups\n");

$stats = ['processed'=>0, 'updated'=>0, 'skipped'=>0, 'failed'=>0, 'not_found'=>0, 'image_missing'=>0];

if ($stage == '0') {
    // --- STAGE 0: VALIDATION ---
    logit($log, "STAGE 0: VALIDATION\n");
    
    // Check first 5 groups
    for ($i = 0; $i < min(5, count($data)); $i++) {
        $skus = array_map('trim', explode(',', $data[$i]['skus']));
        // Deduplicate SKUs within the group
        $skus = array_unique($skus);
        $sku = $skus[0];
        logit($log, "Checking: $sku");
        $id = wc_get_product_id_by_sku($sku);
        if ($id) {
            $p = wc_get_product($id);
            logit($log, "  ✓ Found: " . $p->get_name());
            
            // Also validate the master image URL resolves
            $master_url = trim($data[$i]['master_image_to_keep']);
            $master_id = attachment_url_to_postid($master_url);
            if ($master_id) {
                logit($log, "  ✓ Master image found (ID: $master_id)");
            } else {
                logit($log, "  ✗ Master image NOT found in media library: $master_url");
            }
        } else {
            logit($log, "  ✗ Not found");
        }
    }
    
    // Summary stats
    logit($log, "\n--- Quick Stats ---");
    logit($log, "Total groups: " . count($data));
    $total_skus = 0;
    foreach ($data as $row) {
        $skus = array_unique(array_map('trim', explode(',', $row['skus'])));
        $total_skus += count($skus);
    }
    logit($log, "Total unique SKUs: $total_skus");
    logit($log, "\nVALIDATION COMPLETE");
    
} elseif ($stage == '1') {
    // --- STAGE 1: DRY RUN ---
    logit($log, "STAGE 1: DRY RUN\n");
    foreach ($data as $i => $row) {
        logit($log, "Group " . ($i+1) . "/" . count($data));
        $skus = array_unique(array_map('trim', explode(',', $row['skus'])));
        $img = $row['master_image_to_keep'];
        logit($log, "  Master: " . basename($img));
        logit($log, "  SKUs (" . count($skus) . "): " . implode(', ', array_slice($skus, 0, 5)) . (count($skus) > 5 ? '...' : ''));
        
        // Check if master image exists
        $master_id = attachment_url_to_postid($img);
        if (!$master_id) {
            logit($log, "  ⚠ WARNING: Master image not found in media library!");
            $stats['image_missing']++;
        }
        
        $stats['processed']++;
    }
    logit($log, "\nDRY RUN COMPLETE");
    
} else {
    // --- STAGES 2-4: REAL CHANGES ---
    $limits = ['2'=>10, '3'=>50, '4'=>count($data)];
    $limit = $limits[$stage];
    logit($log, "STAGE $stage: Processing $limit groups - REAL CHANGES\n");
    
    $limited = array_slice($data, 0, $limit);
    foreach ($limited as $i => $row) {
        logit($log, "\n=== Group " . ($i+1) . "/$limit ===");
        
        // Deduplicate SKUs within the group
        $skus = array_unique(array_map('trim', explode(',', $row['skus'])));
        $new_img = trim($row['master_image_to_keep']);
        logit($log, "Master: " . basename($new_img));
        
        // Resolve master image ID once per group (not per SKU)
        $new_id = attachment_url_to_postid($new_img);
        if (!$new_id) {
            logit($log, "  ✗ Master image not found in media library - SKIPPING GROUP");
            $stats['image_missing']++;
            $stats['processed']++;
            continue;
        }
        
        foreach ($skus as $sku) {
            logit($log, "SKU: $sku");
            $id = wc_get_product_id_by_sku($sku);
            if (!$id) {
                logit($log, "  ✗ Product not found");
                $stats['not_found']++;
                continue;
            }
            
            $p = wc_get_product($id);
            $curr_id = $p->get_image_id();
            $curr = $curr_id ? wp_get_attachment_url($curr_id) : '';
            
            if ($curr === $new_img) {
                logit($log, "  ✓ Already correct");
                $stats['skipped']++;
                continue;
            }
            
            $p->set_image_id($new_id);
            $p->save();
            logit($log, "  ✓ UPDATED (was: " . ($curr ? basename($curr) : 'no image') . ")");
            $stats['updated']++;
        }
        $stats['processed']++;
    }
    logit($log, "\nPROCESSING COMPLETE");
}

logit($log, "\n=== SUMMARY ===");
logit($log, "Stage: $stage");
logit($log, "Groups processed: " . $stats['processed']);
logit($log, "Products updated: " . $stats['updated']);
logit($log, "Already correct (skipped): " . $stats['skipped']);
logit($log, "Products not found: " . $stats['not_found']);
logit($log, "Master images missing: " . $stats['image_missing']);

$log_file = $log_dir . "/consolidator_stage_{$stage}_" . date('Ymd_His') . ".log";
file_put_contents($log_file, $log);
logit($log, "\nLog: $log_file");
echo "\nDone!\n";
