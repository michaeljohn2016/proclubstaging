<?php
/**
 * Simplified template that builds the email from session cart.
 * Intended to be called directly by admins from the cart page.
 */

session_start();


// Load WordPress
require_once('wp-config.php');
require_once('wp-load.php');

if ( ! current_user_can('manage_options') ) {
    wp_die('You do not have permission to access this page.');
}

$items = isset($_SESSION['er_qm_cart']) ? $_SESSION['er_qm_cart'] : array();
if ( empty($items) ) {
    wp_die('Cart is empty.');
}

$show_shaft = false;
$show_iron  = false;
foreach ( $items as $item ) {
    if ( ! empty($item['shaft']) && $item['shaft'] !== '0' && strtolower($item['shaft']) !== 'not available' ) {
        $show_shaft = true;
    }
    if ( ! empty($item['ironQuantity']) && $item['ironQuantity'] > 0 ) {
        $show_iron = true;
    }
}

$order_total = 0;
ob_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Quote Details - Print or Email</title>
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
            background: #fff;
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
            margin-bottom: 40px;
            width: 100%;
        }
        .quote-details-right {
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
            color: #fff;
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
        .table-header th:nth-child(2) {
            text-align: left;
        }
        .products-table tbody tr {
            background: #fff;
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
        .products-table td:nth-child(2) {
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
            margin-top: 40px;
            width: 100%;
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
            margin: 0;
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
            color: #fff;
        }
        .btn-primary:hover {
            background: #1565c0;
        }
        .btn-secondary {
            background: #6c757d;
            color: #fff;
        }
        .btn-secondary:hover {
            background: #5a6268;
        }

        #email-control {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 10px;
            padding-top: 0;
        }
        #email-control input[type="text"] {
            border: 1px solid #ccc;
            display: inline-block;
            padding: 12px 24px;
            margin: 0 10px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            min-width: 320px;
            flex: 0 0 100%;
        }
        #email-control form {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            margin: 0;
            flex-wrap: wrap;
            gap: 15px;
            max-width: 330px;
        }
        .email-notice {
            padding: 10px;
            margin-top: 10px;
            background: #f5f5f5;
            text-align: center;
            font-size: 1.3em;
        }
        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            body {
                background: #fff !important;
                padding: 20px !important;
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
            <img src="https://sell.proclubs.com/wp-content/themes/diztinct/src/images/proclubs-logo.png" class="logo" alt="ProClubs Logo">
        </div>
        <!--
        <div class="quote-info">
            <h1 class="title">In-Store Quote Details</h1>
            <div class="quote-details-right">
                <div class="quote-detail">
                    <div class="quote-detail-label">Quote Date:</div>
                    <div class="quote-detail-value"><?php echo date('M j, Y, g:i A'); ?></div>
                </div>
                <div class="quote-detail">
                    <div class="quote-detail-label">Quote Status:</div>
                    <div class="quote-detail-value">In-Store Quote</div>
                </div>
            </div>
        </div>-->
        <table class="quote-info">
            <tr>
                <td>
                    <h1 class="title">In-Store Quote Details</h1>
                </td>
                <td align="right">
                    <table class="quote-details-right">
                        <tr>
                            <td class="quote-detail">
                                <div class="quote-detail-label">Quote Date:</div>
                                <div class="quote-detail-value">
                                        <?php
                        $date = new DateTime("now", new DateTimeZone("Etc/GMT+7")); // UTC−7
                        echo $date->format('M j, Y, g:i A');
                    ?>
                                        </div>
                            </td>
                            <td class="quote-detail" style="padding-left: 20px;">
                                <div class="quote-detail-label">Quote Status:</div>
                                <div class="quote-detail-value">In-Store Quote</div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
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
                <?php foreach ($items as $item):
                    $manufacturer = get_post_meta($item['id'], 'club_manufacturer', true);
                    $type = get_post_meta($item['id'], 'club_type_field', true);
                    if (is_array($type)) {
                        $type = $type[0];
                    }
                    $model = get_post_meta($item['id'], 'club_model', true);
                    global $wpdb;
                    $order_postid = $item['id'];
                    $img_id = $wpdb->get_var($wpdb->prepare("SELECT meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_thumbnail_id' AND post_id = %d", $order_postid));
                    if ($img_id) {
                        $club_image = wp_get_attachment_url((int)$img_id);
                    } else {
                        $club_image = '/wp-content/plugins/quote-manager/default-club-image.php';
                    }
                    if ($item['headOnly'] == 'true') {
                        $shaft = 'Head Only';
                    } else {
                        $shaft = $item['shaft'];
                        if (!empty($item['premiumShaft']) && $item['premiumShaft'] !== 'false' && $item['premiumShaft'] !== 'none' && function_exists('pc_get_shaft_friendly_name')) {
                            $shaft = pc_get_shaft_friendly_name($item['premiumShaft']);
                        }
                        $shaft = preg_replace('/[^A-Za-z0-9 ]/', '', $shaft);
                    }
                    $iron_qty = (!empty($item['ironQuantity']) && $item['ironQuantity'] > 0) ? $item['ironQuantity'] : '';
                    $condition   = $item['condition'];
                    $quantity    = $item['quantity'];
                    $unit_price  = (float)str_replace(',', '', $item['price']);
                    $line_total  = $unit_price * $quantity;
                    $order_total += $line_total;
                ?>
                <tr>
                    <td><img src="<?php echo esc_url($club_image); ?>" alt="<?php echo esc_attr($manufacturer . ' ' . $model); ?>" class="product-image"></td>
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
                <?php endforeach; ?>
            </tbody>
        </table>
        <table class="bottom-section">
            <tr>
                <td>
                    <div class="comments-section">
                        <h3 class="comments-title">Comment / Other Products</h3>
                        <div class="comments-text">
                            <?php
                            if (!empty($_SESSION['er_qm_comments'])) {
                                echo nl2br(esc_html($_SESSION['er_qm_comments']));
                            } else {
                                echo 'No additional comments or products specified.';
                            }
                            ?>
                        </div>
                    </div>
                </td>
                <td align="right">
                    <div class="total-section">
                        <span class="total-label">Total:</span>
                        <span class="total-amount">$<?php echo number_format($order_total, 2); ?></span>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
<?php
$email_html = ob_get_clean();
?>
<?php
$notice = '';
if ( isset($_POST['send_quote']) && ! empty($_POST['recipient_email']) ) {
    $emails = array_map('trim', explode(',', sanitize_text_field($_POST['recipient_email'])));
    $emails = array_filter($emails, 'is_email');
    if ( ! empty($emails) ) {
        $subject = 'Your In-Store Quote from ProClubs';
        $headers = array('Content-Type: text/html; charset=UTF-8');
        foreach ( $emails as $to ) {
            wp_mail($to, $subject, $email_html, $headers);
        }
        $notice = '<div class="container"><div style="color:green;" class="email-notice">Quote sent!</div></div>';
    } else {
        $notice = '<div class="container"><div style="color:red;" class="email-notice">Invalid email address.</div></div>';
    }
}

echo $email_html;
echo $notice;
?>

<div class="container">
    <div id="email-control" class="print-controls">
        <form method="post" style="margin-top:20px;">
            <input type="text" name="recipient_email" style="width:100%" value="" placeholder="Recipient emails (comma separated)">
            <input type="submit" name="send_quote" value="Send Quote" class="btn btn-primary">
            <button onclick="window.close();" class="btn btn-secondary">Close Window</button>
        </form>
    </div>
</div>