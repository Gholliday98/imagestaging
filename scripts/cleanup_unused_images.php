<?php
/**
 * Unused Image Cleanup
 * 
 * Finds and removes orphaned product images in the WordPress media library
 * that are no longer assigned to any product (as featured image or gallery).
 *
 * USAGE: php cleanup_unused_images.php [1|2]
 *   1 = ANALYZE (no changes)
 *   2 = DELETE (permanent)
 * 
 * PREREQUISITE: Run consolidator.php first (all stages)
 * 
 * @package PlasticCraft
 * @version 1.2.0
 */

define('WP_USE_THEMES', false);

// Auto-detect WordPress path (looks 2 levels up from scripts/ directory)
$wp_load = realpath(__DIR__ . '/../../wp-load.php');
if (!$wp_load || !file_exists($wp_load)) {
    die("ERROR: Could not find wp-load.php. Run this script from within the WordPress public_html directory.\n");
}
require_once($wp_load);

echo "\n=== UNUSED IMAGE CLEANUP v1.2 ===\n\n";
echo "This finds product images that are no longer used by any product.\n\n";
echo "Steps:\n";
echo " 1: ANALYZE - Show unused images (no changes)\n";
echo " 2: DELETE - Remove unused images (PERMANENT)\n\n";

// Accept step from command line or interactive input
if (isset($argv[1]) && in_array($argv[1], ['1','2'])) {
    $step = $argv[1];
} else {
    echo "Enter step (1 or 2): ";
    $step = trim(fgets(STDIN));
}
if (!in_array($step, ['1', '2'])) die("Invalid\n");

$csv_file = __DIR__ . '/../data/visual_duplicates_CORRECTED.csv';
$log_dir = __DIR__ . '/../logs';
if (!is_dir($log_dir)) mkdir($log_dir, 0755, true);

$log = '';
function logit(&$log, $msg) {
    $entry = "[" . date('H:i:s') . "] $msg\n";
    $log .= $entry;
    echo $entry;
}

global $wpdb;

// -------------------------------------------------------
// STEP A: Find ALL image attachment IDs used by products
// -------------------------------------------------------
logit($log, "Scanning all product image assignments...\n");

$used_image_ids = [];

// Featured images on products and variations
$featured = $wpdb->get_results("
    SELECT DISTINCT pm.meta_value as image_id
    FROM {$wpdb->postmeta} pm
    INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
    WHERE pm.meta_key = '_thumbnail_id'
    AND pm.meta_value > 0
    AND p.post_type IN ('product', 'product_variation')
    AND p.post_status IN ('publish', 'private', 'draft')
");

foreach ($featured as $row) {
    $used_image_ids[(int)$row->image_id] = true;
}
logit($log, "Featured images in use: " . count($used_image_ids));

// Gallery images
$galleries = $wpdb->get_results("
    SELECT pm.meta_value as gallery_ids
    FROM {$wpdb->postmeta} pm
    INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
    WHERE pm.meta_key = '_product_image_gallery'
    AND pm.meta_value != ''
    AND p.post_type = 'product'
    AND p.post_status IN ('publish', 'private', 'draft')
");

$gallery_count = 0;
foreach ($galleries as $row) {
    $ids = array_filter(array_map('intval', explode(',', $row->gallery_ids)));
    foreach ($ids as $id) {
        if ($id > 0) {
            $used_image_ids[$id] = true;
            $gallery_count++;
        }
    }
}
logit($log, "Gallery image references: $gallery_count");
logit($log, "Total unique images in use by products: " . count($used_image_ids) . "\n");

// -------------------------------------------------------
// STEP B: Build list of master images to protect
// -------------------------------------------------------
$protected_filenames = [];
if (file_exists($csv_file)) {
    $handle = fopen($csv_file, 'r');
    $headers = fgetcsv($handle);
    $headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', $headers[0]);
    while (($row = fgetcsv($handle)) !== false) {
        if (count($row) === count($headers)) {
            $r = array_combine($headers, $row);
            $master = trim($r['master_image_to_keep']);
            $protected_filenames[basename($master)] = true;
        }
    }
    fclose($handle);
    logit($log, "Protected master images from CSV: " . count($protected_filenames));
} else {
    logit($log, "WARNING: CSV not found, no master image protection");
}

// -------------------------------------------------------
// STEP C: Find ALL image attachments in media library
// -------------------------------------------------------
logit($log, "\nScanning media library for product images...");

$all_product_images = $wpdb->get_results("
    SELECT p.ID, p.guid, pm.meta_value as file_path
    FROM {$wpdb->posts} p
    LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_wp_attached_file'
    WHERE p.post_type = 'attachment'
    AND p.post_mime_type LIKE 'image/%'
    ORDER BY p.ID
");

logit($log, "Total image attachments in media library: " . count($all_product_images) . "\n");

// -------------------------------------------------------
// STEP D: Identify unused product images
// -------------------------------------------------------
logit($log, "Identifying unused images...\n");

$unused_images = [];
$protected_count = 0;
$in_use_count = 0;
$non_product_count = 0;

foreach ($all_product_images as $img) {
    $id = (int)$img->ID;
    $filename = basename($img->file_path ?: $img->guid);
    
    // Skip if currently used by a product
    if (isset($used_image_ids[$id])) {
        $in_use_count++;
        continue;
    }
    
    // Skip if it's a protected master image
    if (isset($protected_filenames[$filename])) {
        $protected_count++;
        continue;
    }
    
    // Only target images that match product image patterns (source_ from BigCommerce)
    // This avoids deleting blog, theme, or Elementor images
    if (strpos($filename, 'source_') !== false || strpos($filename, '_source_') !== false) {
        $file_path = get_attached_file($id);
        $size = ($file_path && file_exists($file_path)) ? filesize($file_path) : 0;
        
        $unused_images[] = [
            'id' => $id,
            'filename' => $filename,
            'file_path' => $file_path,
            'size' => $size,
            'url' => $img->guid
        ];
    } else {
        $non_product_count++;
    }
}

logit($log, "Images currently in use by products: $in_use_count");
logit($log, "Protected master images: $protected_count");
logit($log, "Non-product images (skipped): $non_product_count");
logit($log, "Unused product images found: " . count($unused_images) . "\n");

// -------------------------------------------------------
// RESULTS
// -------------------------------------------------------
if ($step == '1') {
    $total_size = 0;
    
    if (count($unused_images) > 0) {
        logit($log, "=== UNUSED IMAGES (first 50) ===\n");
        $shown = 0;
        foreach ($unused_images as $img) {
            $total_size += $img['size'];
            if ($shown < 50) {
                logit($log, "  ID: {$img['id']} | {$img['filename']} | " . size_format($img['size']));
                $shown++;
            }
        }
        if (count($unused_images) > 50) {
            logit($log, "  ... and " . (count($unused_images) - 50) . " more");
        }
        
        logit($log, "\n=== SUMMARY ===");
        logit($log, "Total unused original images: " . count($unused_images));
        logit($log, "Total size (originals only): " . size_format($total_size));
        logit($log, "Estimated with thumbnails: ~" . size_format($total_size * 5));
        logit($log, "\nNO CHANGES MADE");
        logit($log, "Run step 2 to delete these images (including their thumbnails)");
    } else {
        logit($log, "=== SUMMARY ===");
        logit($log, "No unused product images found.");
    }
    
} elseif ($step == '2') {
    if (count($unused_images) == 0) {
        logit($log, "Nothing to delete.");
    } else {
        logit($log, "DELETING " . count($unused_images) . " UNUSED IMAGES...\n");
        
        $deleted = 0;
        $failed = 0;
        $total_size = 0;
        
        foreach ($unused_images as $img) {
            logit($log, "Deleting: {$img['filename']} (ID: {$img['id']})");
            
            // wp_delete_attachment with true = also deletes file + thumbnails from disk
            $result = wp_delete_attachment($img['id'], true);
            
            if ($result) {
                logit($log, "  ✓ Deleted");
                $deleted++;
                $total_size += $img['size'];
            } else {
                logit($log, "  ✗ Failed");
                $failed++;
            }
        }
        
        logit($log, "\n=== SUMMARY ===");
        logit($log, "Deleted: $deleted images (+ their thumbnails)");
        logit($log, "Failed: $failed images");
        logit($log, "Space freed (originals): " . size_format($total_size));
        logit($log, "\nCLEANUP COMPLETE");
    }
}

$log_file = $log_dir . "/cleanup_step_{$step}_" . date('Ymd_His') . ".log";
file_put_contents($log_file, $log);
logit($log, "\nLog: $log_file");
echo "\nDone!\n";
