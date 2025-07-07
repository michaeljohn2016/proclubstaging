<?php
session_start();

// Load WordPress
require_once('wp-config.php');
require_once('wp-load.php');

// Check if cart has items

// ..Show and hide shaft and iron if 0
$show_shaft = false;
$show_iron = false;

if (!empty($_SESSION['er_qm_cart'])) {
    foreach ($_SESSION['er_qm_cart'] as $item) {
        if (!empty($item['shaft']) && $item['shaft'] > 0) {
            $show_shaft = true;
        }
        if (!empty($item['ironQuantity']) && $item['ironQuantity'] > 0) {
            $show_iron = true;
        }
    }
}
// ..Show and hide shaft and iron if 0

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Quote Details - Print</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            font-size: 14px;
            line-height: 1.4;
            color: #333;
            background-color: #ffffff;
            padding: 40px;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
        }
        
        .header {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
        }
        
        .logo {
            max-height: 60px;
            max-width: 200px;
        }
        
        .title {
            font-size: 48px;
            font-weight: 400;
            color: #000;
            margin: 0;
        }
        
        .quote-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }
        
        .quote-details-right {
            display: flex;
            gap: 40px;
        }
        
        .quote-detail {
            text-align: right;
        }
        
        .quote-detail-label {
            font-size: 14px;
            font-weight: 600;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        
        .quote-detail-value {
            font-size: 16px;
            font-weight: 400;
            color: #1976d2;
        }
        
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
            border-radius: 6px;
            overflow: hidden;
        }
        
        .table-header {
            background: #1976d2;
            color: white;
        }
        
        .table-header th {
            padding: 16px 12px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            text-align: center;
            border: none;
        }
        
        .table-header th:first-child {
            width: 60px;
        }
        
        .table-header th:nth-child(2) { /* Manufacturer */
            text-align: left;
        }
        
        .products-table tbody tr {
            background: white;
        }
        
        .products-table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        .products-table td {
            padding: 16px 12px;
            font-size: 14px;
            vertical-align: middle;
            border: none;
            text-align: center;
        }
        
        .products-table td:nth-child(2) { /* Manufacturer */
            text-align: left;
            font-weight: 500;
        }
        
        .product-image {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #ddd;
        }
        
        .bottom-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 40px;
            margin-top: 40px;
        }
        
        .comments-section {
            background: #f5f5f5;
            padding: 24px;
            border-radius: 6px;
            flex: 1;
            max-width: 500px;
        }
        
        .comments-title {
            font-size: 18px;
            font-weight: 600;
            color: #000;
            margin-bottom: 12px;
        }
        
        .comments-text {
            font-size: 14px;
            line-height: 1.6;
            color: #333;
        }
        
        .total-section {
            text-align: right;
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .total-label {
            font-size: 24px;
            font-weight: 500;
            color: #000;
        }
        
        .total-amount {
            font-size: 32px;
            font-weight: 700;
            color: #000;
        }
        
        .print-controls {
            text-align: center;
            margin-top: 60px;
            padding-top: 30px;
            border-top: 1px solid #e9ecef;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 24px;
            margin: 0 10px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: #1976d2;
            color: white;
        }
        
        .btn-primary:hover {
            background: #1565c0;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            body {
                background: white !important;
                padding: 20px !important;
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif !important;
                font-size: 14px !important;
                line-height: 1.4 !important;
                color: #333 !important;
            }
            
            .container {
                max-width: none !important;
                width: 100% !important;
                background: white !important;
            }
            
            .header {
                display: flex !important;
                justify-content: flex-start !important;
                align-items: center !important;
                margin-bottom: 40px !important;
                padding-bottom: 20px !important;
            }
            
            .logo {
                max-height: 60px !important;
                max-width: 200px !important;
            }
            
            .title {
                font-size: 48px !important;
                font-weight: 400 !important;
                color: #000 !important;
                margin: 0 !important;
            }
            
            .quote-info {
                display: flex !important;
                justify-content: space-between !important;
                align-items: center !important;
                margin-bottom: 40px !important;
            }
            
            .quote-details-right {
                display: flex !important;
                gap: 40px !important;
            }
            
            .quote-detail {
                text-align: right !important;
            }
            
            .quote-detail-label {
                font-size: 14px !important;
                font-weight: 600 !important;
                color: #666 !important;
                text-transform: uppercase !important;
                letter-spacing: 0.5px !important;
                margin-bottom: 5px !important;
            }
            
            .quote-detail-value {
                font-size: 16px !important;
                font-weight: 400 !important;
                color: #1976d2 !important;
            }
            
            .products-table {
                width: 100% !important;
                border-collapse: collapse !important;
                margin-bottom: 40px !important;
                border-radius: 6px !important;
                overflow: hidden !important;
            }
            
            .table-header {
                background: #1976d2 !important;
                color: white !important;
            }
            
            .table-header th {
                padding: 16px 12px !important;
                font-size: 11px !important;
                font-weight: 700 !important;
                text-transform: uppercase !important;
                letter-spacing: 0.8px !important;
                text-align: center !important;
                border: none !important;
                background: #1976d2 !important;
                color: white !important;
            }
            
            .table-header th:first-child {
                width: 60px !important;
            }
            
            .table-header th:nth-child(2) {
                text-align: left !important;
            }
            
            .products-table tbody tr {
                background: white !important;
                page-break-inside: avoid !important;
            }
            
            .products-table tbody tr:nth-child(even) {
                background: #f8f9fa !important;
            }
            
            .products-table td {
                padding: 16px 12px !important;
                font-size: 14px !important;
                vertical-align: middle !important;
                border: none !important;
                text-align: center !important;
            }
            
            .products-table td:nth-child(2) {
                text-align: left !important;
                font-weight: 500 !important;
            }
            
            .product-image {
                width: 40px !important;
                height: 40px !important;
                object-fit: cover !important;
                border-radius: 4px !important;
                border: 1px solid #ddd !important;
            }
            
            .bottom-section {
                display: flex !important;
                justify-content: space-between !important;
                align-items: flex-start !important;
                gap: 40px !important;
                margin-top: 40px !important;
                page-break-inside: avoid !important;
            }
            
            .comments-section {
                background: #f5f5f5 !important;
                padding: 24px !important;
                border-radius: 6px !important;
                flex: 1 !important;
                max-width: 500px !important;
            }
            
            .comments-title {
                font-size: 18px !important;
                font-weight: 600 !important;
                color: #000 !important;
                margin-bottom: 12px !important;
            }
            
            .comments-text {
                font-size: 14px !important;
                line-height: 1.6 !important;
                color: #333 !important;
            }
            
            .total-section {
                text-align: right !important;
                display: flex !important;
                align-items: center !important;
                gap: 20px !important;
            }
            
            .total-label {
                font-size: 24px !important;
                font-weight: 500 !important;
                color: #000 !important;
            }
            
            .total-amount {
                font-size: 32px !important;
                font-weight: 700 !important;
                color: #000 !important;
            }
            
            .print-controls {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="https://sell.proclubs.com/wp-content/themes/diztinct/src/images/proclubs-logo.png" 
                 class="logo" alt="ProClubs Logo">
        </div>
        
        <div class="quote-info">
            <h1 class="title">In-Store Quote Details</h1>
            <div class="quote-details-right">
                <div class="quote-detail">
                    <div class="quote-detail-label">Quote Date:</div>
                    <div class="quote-detail-value">
                                        <?php
                        $date = new DateTime("now", new DateTimeZone("Etc/GMT+7")); // UTC−7
                        echo $date->format('M j, Y, g:i A');
                    ?>
                                        </div>
                </div>
                <div class="quote-detail">
                    <div class="quote-detail-label">Quote Status:</div>
                    <div class="quote-detail-value">In-Store Quote</div>
                </div>
            </div>
        </div>
        
        <table class="products-table">
            <thead class="table-header">
                <tr>
                    <th></th>
                    <th>Manufacturer</th>
                    <th>Type</th>
                    <th>Model</th>
                    <?php if ($show_shaft): ?><th>Shaft</th><?php endif; ?>
                    <?php if ($show_iron): ?><th>Iron Set<br>Qty</th><?php endif; ?>
                    <th>Condition</th>
                    <th>Qty</th>
                    <th>Unit<br>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $order_total = 0;
                foreach($_SESSION['er_qm_cart'] as $item) {
                    $manufacturer = get_post_meta($item['id'], 'club_manufacturer', true);
                    $type = get_post_meta($item['id'], 'club_type_field', true);
                    if (is_array($type)) {
                        $type = $type[0];
                    }
                    $model = get_post_meta($item['id'], 'club_model', true);
                    
                    // Get club image
                    global $wpdb;
                    $order_postid = $item['id'];
                    
                    $query_data_for_image_id = $wpdb->get_results(
                        "SELECT meta_value AS image 
                         FROM wp_postmeta 
                         WHERE meta_key='_thumbnail_id' 
                         AND post_id=$order_postid;"
                    );
                    
                    if (!empty($query_data_for_image_id) && !empty($query_data_for_image_id[0]->image)) {
                        $imageID = intval($query_data_for_image_id[0]->image);
                        
                        $query_data_for_image = $wpdb->get_results(
                            "SELECT guid AS URL 
                             FROM wp_posts 
                             WHERE ID = $imageID;"
                        );
                        
                        if (!empty($query_data_for_image) && !empty($query_data_for_image[0]->URL)) {
                            $club_image = $query_data_for_image[0]->URL;
                        } else {
                            $club_image = '/wp-content/plugins/quote-manager/default-club-image.php';
                        }
                    } else {
                        $club_image = '/wp-content/plugins/quote-manager/default-club-image.php';
                    }
                    
                    // Handle shaft information
                    if ($item["headOnly"] == "true") {
                        $shaft = "Head Only";
                    } else {
                        $shaft = $item['shaft'];
                        if ($item['premiumShaft'] !== "false" && $item['premiumShaft'] !== "none") {
                            if (function_exists('pc_get_shaft_friendly_name')) {
                                $shaft = pc_get_shaft_friendly_name($item['premiumShaft']);
                            }
                        }
                        $shaft = preg_replace("/[^A-Za-z0-9 ]/", '', $shaft);
                    }
                    
                    $condition = $item['condition'];
                    $quantity = $item['quantity'];
                    $unit_price = str_replace(',', '', $item['price']);
                    $line_total = $unit_price * $quantity;
                    $order_total += $line_total;
                    $iron_qty = (isset($item["ironQuantity"]) && $item["ironQuantity"] > 0) ? $item["ironQuantity"] : '';
                    ?>
                    <tr>
                        <td>
                            <img src="<?php echo esc_url($club_image); ?>" 
                                 alt="<?php echo esc_attr($manufacturer . ' ' . $model); ?>" 
                                 class="product-image">
                        </td>
                        <td><?php echo esc_html($manufacturer); ?></td>
                        <td><?php echo esc_html($type); ?></td>
                        <td><?php echo esc_html($model); ?></td>
                        <?php if ($show_shaft): ?><td><?php echo esc_html($shaft); ?></td><?php endif; ?>
                        <?php if ($show_iron): ?><td><?php echo esc_html($iron_qty); ?></td><?php endif; ?>
                        <td><?php echo esc_html($condition); ?></td>
                        <td><?php echo $quantity; ?></td>
                        <td>$<?php echo number_format($unit_price, 2); ?></td>
                        <td>$<?php echo number_format($line_total, 2); ?></td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
        
        <div class="bottom-section">
            <div class="comments-section">
                <h3 class="comments-title">Comment / Other Products</h3>
                <div class="comments-text">
                    <?php 
                    if (!empty($_SESSION['er_qm_comments'])) {
                        echo nl2br(esc_html($_SESSION['er_qm_comments']));
                    } else {
                        echo "No additional comments or products specified.";
                    }
                    ?>
                </div>
            </div>
            
            <div class="total-section">
                <span class="total-label">Total:</span>
                <span class="total-amount">$<?php echo number_format($order_total, 2); ?></span>
            </div>
        </div>
        
        <div class="print-controls">
            <button onclick="window.print();" class="btn btn-primary">Print This Quote</button>
            <button onclick="window.close();" class="btn btn-secondary">Close Window</button>
        </div>
    </div>
</body>
</html>