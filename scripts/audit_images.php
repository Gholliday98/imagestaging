<?php
/**
 * Image Audit Script
 * 
 * Runs AFTER consolidator.php to verify results and find remaining issues.
 * This helps diagnose why some images "didn't get cleaned up."
 *
 * USAGE: php audit_images.php (run from the scripts/ directory)
 * REQUIRES: ../data/visual_duplicates.csv
 *
 * @package PlasticCraft
 * @version 1.0.0
 */

define('WP_USE_THEMES', false);
require_once('/home/master/applications/embhtsmzsy/public_html/wp-load.php');

echo "\n=== IMAGE AUDIT REPORT ===\n\n";

$csv_file = __DIR__ . '/../data/visual_duplicates_CORRECTED.csv';
$log_dir = __DIR__ . '/../logs';
if (!is_dir($log_dir)) mkdir($log_dir, 0755, true);

if (!file_exists($csv_file)) {
    die("ERROR: CSV not found at $csv_file\n");
}

$log = '';
function logit(&$log, $msg) {
    $entry = "[" . date('H:i:s') . "] $msg\n";
    $log .= $entry;
    echo $entry;
}

// Load CSV
$handle = fopen($csv_file, 'r');
$headers = fgetcsv($handle);
$headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', $headers[0]);
$data = [];
while (($row = fgetcsv($handle)) !== false) {
    if (count($row) === count($headers)) {
        $data[] = array_combine($headers, $row);
    }
}
fclose($handle);

logit($log, "Loaded " . count($data) . " groups from CSV\n");

// --- AUDIT 1: Check which products still DON'T have the master image ---
logit($log, "=== AUDIT 1: PRODUCTS NOT MATCHING MASTER IMAGE ===\n");

$correct = 0;
$wrong_image = 0;
$no_image = 0;
$not_found = 0;
$wrong_details = [];

foreach ($data as $i => $row) {
    $skus = array_unique(array_map('trim', explode(',', $row['skus'])));
    $master_url = trim($row['master_image_to_keep']);
    
    foreach ($skus as $sku) {
        $id = wc_get_product_id_by_sku($sku);
        if (!$id) {
            $not_found++;
            continue;
        }
        
        $p = wc_get_product($id);
        $curr_id = $p->get_image_id();
        $curr_url = $curr_id ? wp_get_attachment_url($curr_id) : '';
        
        if (!$curr_url) {
            $no_image++;
            $wrong_details[] = [
                'sku' => $sku,
                'issue' => 'NO IMAGE',
                'expected' => basename($master_url),
                'actual' => '(none)',
                'group' => $i + 1
            ];
        } elseif ($curr_url !== $master_url) {
            $wrong_image++;
            $wrong_details[] = [
                'sku' => $sku,
                'issue' => 'WRONG IMAGE',
                'expected' => basename($master_url),
                'actual' => basename($curr_url),
                'group' => $i + 1
            ];
        } else {
            $correct++;
        }
    }
}

logit($log, "Correct (has master image): $correct");
logit($log, "Wrong image (has different image): $wrong_image");
logit($log, "No image at all: $no_image");
logit($log, "SKU not found in WooCommerce: $not_found");

if (count($wrong_details) > 0) {
    logit($log, "\n--- First 20 mismatches ---");
    foreach (array_slice($wrong_details, 0, 20) as $d) {
        logit($log, "  [{$d['issue']}] SKU: {$d['sku']} (Group {$d['group']})");
        logit($log, "    Expected: {$d['expected']}");
        logit($log, "    Actual:   {$d['actual']}");
    }
}

// --- AUDIT 2: Check for duplicate images still in media library ---
logit($log, "\n\n=== AUDIT 2: DUPLICATE IMAGES STILL IN MEDIA LIBRARY ===\n");

$master_images = [];
$still_exists = 0;
$already_gone = 0;

foreach ($data as $row) {
    $master_url = trim($row['master_image_to_keep']);
    $master_images[$master_url] = true;
    
    // Check images_to_delete column
    $to_delete = trim($row['images_to_delete']);
    if (empty($to_delete)) continue;
    
    $delete_urls = array_map('trim', explode("\n", $to_delete));
    foreach ($delete_urls as $del_url) {
        if (empty($del_url)) continue;
        $del_id = attachment_url_to_postid($del_url);
        if ($del_id) {
            $still_exists++;
        } else {
            $already_gone++;
        }
    }
}

logit($log, "Duplicate images still in media library: $still_exists");
logit($log, "Duplicate images already removed: $already_gone");

// --- AUDIT 3: Master image health check ---
logit($log, "\n\n=== AUDIT 3: MASTER IMAGE HEALTH CHECK ===\n");

$master_ok = 0;
$master_missing = 0;
$missing_masters = [];

foreach ($data as $i => $row) {
    $master_url = trim($row['master_image_to_keep']);
    $master_id = attachment_url_to_postid($master_url);
    
    if ($master_id) {
        $master_ok++;
    } else {
        $master_missing++;
        $missing_masters[] = [
            'group' => $i + 1,
            'url' => $master_url,
            'sku_count' => count(array_unique(array_map('trim', explode(',', $row['skus']))))
        ];
    }
}

logit($log, "Master images found: $master_ok / " . count($data));
logit($log, "Master images MISSING: $master_missing");

if ($master_missing > 0) {
    logit($log, "\n--- Missing master images ---");
    foreach ($missing_masters as $m) {
        logit($log, "  Group {$m['group']}: {$m['sku_count']} SKUs affected");
        logit($log, "    URL: " . basename($m['url']));
    }
}

// --- AUDIT 4: Products outside the CSV (not covered) ---
logit($log, "\n\n=== AUDIT 4: CATALOG COVERAGE ===\n");

global $wpdb;
$total_products = $wpdb->get_var(
    "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'product' AND post_status = 'publish'"
);
$total_with_image = $wpdb->get_var(
    "SELECT COUNT(DISTINCT p.ID) FROM {$wpdb->posts} p 
     INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id 
     WHERE p.post_type = 'product' AND p.post_status = 'publish' 
     AND pm.meta_key = '_thumbnail_id' AND pm.meta_value > 0"
);
$total_no_image = $total_products - $total_with_image;

// Count SKUs in CSV
$csv_skus = [];
foreach ($data as $row) {
    $skus = array_unique(array_map('trim', explode(',', $row['skus'])));
    foreach ($skus as $s) $csv_skus[$s] = true;
}

logit($log, "Total published products: $total_products");
logit($log, "Products with images: $total_with_image");
logit($log, "Products WITHOUT images: $total_no_image");
logit($log, "Products covered by CSV: " . count($csv_skus));
logit($log, "Products NOT in CSV: " . ($total_products - count($csv_skus)));

// --- Save report ---
logit($log, "\n\n=== AUDIT COMPLETE ===");

$log_file = $log_dir . "/audit_" . date('Ymd_His') . ".log";
file_put_contents($log_file, $log);
logit($log, "Log: $log_file");

// Also save wrong_details to CSV for easy review
if (count($wrong_details) > 0) {
    $report_file = $log_dir . "/mismatches_" . date('Ymd_His') . ".csv";
    $fp = fopen($report_file, 'w');
    fputcsv($fp, ['sku', 'issue', 'expected_image', 'actual_image', 'group']);
    foreach ($wrong_details as $d) {
        fputcsv($fp, [$d['sku'], $d['issue'], $d['expected'], $d['actual'], $d['group']]);
    }
    fclose($fp);
    logit($log, "Mismatch report: $report_file");
}

echo "\nDone!\n";
