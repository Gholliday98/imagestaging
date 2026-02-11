<?php
/**
 * Unused Image Cleanup
 * 
 * After consolidator.php assigns master images, this script finds and 
 * deletes orphaned duplicate images that are no longer used by any product.
 *
 * USAGE: php cleanup_unused_images.php (run from the scripts/ directory)
 * REQUIRES: ../data/visual_duplicates.csv
 * PREREQUISITE: Run consolidator.php first (all stages)
 * 
 * Steps:
 *   1 - ANALYZE: Shows unused images and space savings (no changes)
 *   2 - DELETE: Permanently removes unused images
 *
 * @package PlasticCraft
 * @version 1.1.0
 */

define('WP_USE_THEMES', false);
require_once('/home/master/applications/embhtsmzsy/public_html/wp-load.php');

echo "\n=== UNUSED IMAGE CLEANUP ===\n\n";
echo "This will find images that are no longer used by any product.\n\n";
echo "Steps:\n";
echo " 1: ANALYZE - Show unused images (no changes)\n";
echo " 2: DELETE - Remove unused images (PERMANENT)\n\n";
echo "Enter step (1 or 2): ";

$step = trim(fgets(STDIN));
if (!in_array($step, ['1', '2'])) die("Invalid\n");

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

logit($log, "Loading CSV...");
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
logit($log, "Loaded " . count($data) . " groups\n");

// Build list of all images that SHOULD be kept (master images)
$master_images = [];
$duplicate_images = [];

foreach ($data as $row) {
    $master = trim($row['master_image_to_keep']);
    $master_images[] = $master;
    
    // Parse the images to delete from the group
    $skus = array_unique(array_map('trim', explode(',', $row['skus'])));
    
    // Get all images for products in this group
    foreach ($skus as $sku) {
        $product_id = wc_get_product_id_by_sku($sku);
        if (!$product_id) continue;
        
        $product = wc_get_product($product_id);
        if (!$product) continue;
        
        // Check old image (before consolidation)
        $image_id = $product->get_image_id();
        if ($image_id) {
            $image_url = wp_get_attachment_url($image_id);
            if ($image_url && $image_url !== $master) {
                $duplicate_images[$image_url] = $image_id;
            }
        }
    }
}

logit($log, "Master images to keep: " . count(array_unique($master_images)));
logit($log, "Potential duplicates found: " . count($duplicate_images) . "\n");

// Find images that are truly unused (not referenced by ANY product)
$unused_images = [];
foreach ($duplicate_images as $url => $attachment_id) {
    global $wpdb;
    
    // Check _thumbnail_id (featured image)
    $used_as_featured = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->postmeta} 
        WHERE meta_key = '_thumbnail_id' AND meta_value = %d",
        $attachment_id
    ));
    
    // Also check _product_image_gallery (gallery images)
    $used_in_gallery = $wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM {$wpdb->postmeta} 
        WHERE meta_key = '_product_image_gallery' AND meta_value LIKE %s",
        '%' . $wpdb->esc_like($attachment_id) . '%'
    ));
    
    if ($used_as_featured == 0 && $used_in_gallery == 0) {
        $unused_images[$url] = $attachment_id;
    }
}

logit($log, "\n=== UNUSED IMAGES ===");
logit($log, "Found " . count($unused_images) . " images not used by any product\n");

if ($step == '1') {
    // ANALYZE ONLY
    $total_size = 0;
    foreach ($unused_images as $url => $attachment_id) {
        $file_path = get_attached_file($attachment_id);
        $size = file_exists($file_path) ? filesize($file_path) : 0;
        $total_size += $size;
        logit($log, "  ID: $attachment_id | " . basename($url) . " | " . size_format($size));
    }
    
    logit($log, "\n=== SUMMARY ===");
    logit($log, "Total unused images: " . count($unused_images));
    logit($log, "Total size: " . size_format($total_size));
    logit($log, "\nNO CHANGES MADE");
    logit($log, "Run step 2 to delete these images");
    
} elseif ($step == '2') {
    // DELETE
    logit($log, "\nDELETING UNUSED IMAGES...\n");
    
    $deleted = 0;
    $failed = 0;
    $total_size = 0;
    
    foreach ($unused_images as $url => $attachment_id) {
        $file_path = get_attached_file($attachment_id);
        $size = file_exists($file_path) ? filesize($file_path) : 0;
        
        logit($log, "Deleting: " . basename($url));
        
        $result = wp_delete_attachment($attachment_id, true);
        
        if ($result) {
            logit($log, "  ✓ Deleted");
            $deleted++;
            $total_size += $size;
        } else {
            logit($log, "  ✗ Failed");
            $failed++;
        }
    }
    
    logit($log, "\n=== SUMMARY ===");
    logit($log, "Deleted: $deleted images");
    logit($log, "Failed: $failed images");
    logit($log, "Space freed: " . size_format($total_size));
    logit($log, "\nCLEANUP COMPLETE");
}

$log_file = $log_dir . "/cleanup_step_{$step}_" . date('Ymd_His') . ".log";
file_put_contents($log_file, $log);
logit($log, "\nLog: $log_file");
echo "\nDone!\n";
