<?php
/**
 * Plugin Name: Auto Quote Reply
 * Description: Automatically sends customers a quote email with WooCommerce prices when they submit a YITH Request a Quote form.
 * Version: 1.0.0
 * Author: Loai Amr
 * Author URI: https://github.com/Loai-amr
 * Plugin URI: https://github.com/Loai-amr/woo-auto-quote-reply
 * Text Domain: auto-quote-reply
 *
 * Developer: Loai Amr
 * GitHub: https://github.com/Loai-amr
 * LinkedIn: https://www.linkedin.com/in/loai-amrr/
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}


/**
 * Register Admin Menu Page
 */
add_action( 'admin_menu', 'aqr_register_admin_page' );

function aqr_register_admin_page() {
    add_menu_page(
        __( 'Auto Quote Reply', 'auto-quote-reply' ), // Page title
        __( 'Quote Reply', 'auto-quote-reply' ),      // Menu title
        'manage_options',                             // Capability
        'auto-quote-reply',                           // Menu slug
        'aqr_admin_page_content',                     // Callback function
        'dashicons-email-alt',                        // Icon (WP Dashicons)
        56                                            // Position (below WooCommerce ~55)
    );
}

function aqr_admin_page_content() {
    ?>
    <div class="wrap">
        <h1><?php _e( 'Auto Quote Reply', 'auto-quote-reply' ); ?></h1>
        <p>
            This plugin automatically responds to quote requests with product details and prices.  
            No configuration is needed right now.  
        </p>
        <p>
            <strong>Developer:</strong> <a href="https://github.com/Loai-amr" target="_blank">Loai Amr</a><br>
            <strong>LinkedIn:</strong> <a href="https://www.linkedin.com/in/loai-amrr/" target="_blank">Connect</a>
        </p>
    </div>
    <?php
}

/**
 * Hook into YITH Request a Quote email action
 *
 * @author  Loai Amr
 * @link    https://github.com/Loai-amr
 * @link    https://www.linkedin.com/in/loai-amrr/
 */
add_action( 'yith_ywraq_mail_quote_request_admin', 'aqr_send_customer_quote_reply', 10, 2 );

function aqr_send_customer_quote_reply( $raq_content, $raq_data ) {
    if ( empty( $raq_data['user_email'] ) ) {
        return;
    }

    $to      = $raq_data['user_email'];
    $subject = sprintf( __( 'Your Quote Request from %s', 'auto-quote-reply' ), get_bloginfo('name') );

    // Build items table
    $items_html = '<table cellspacing="0" cellpadding="6" style="width:100%; border:1px solid #eee;" border="1">';
    $items_html .= '<thead><tr>';
    $items_html .= '<th scope="col" style="text-align:left;">' . __( 'Product', 'auto-quote-reply' ) . '</th>';
    $items_html .= '<th scope="col" style="text-align:left;">' . __( 'Quantity', 'auto-quote-reply' ) . '</th>';
    $items_html .= '<th scope="col" style="text-align:left;">' . __( 'Price', 'auto-quote-reply' ) . '</th>';
    $items_html .= '</tr></thead><tbody>';

    $total = 0;

    foreach ( $raq_content as $item ) {
        $product_id = $item['product_id'];
        $product    = wc_get_product( $product_id );
        if ( $product ) {
            $price       = $product->get_price();
            $qty         = intval( $item['quantity'] );
            $line_total  = $price * $qty;
            $total      += $line_total;

            $items_html .= '<tr>';
            $items_html .= '<td>' . esc_html( $product->get_name() ) . '</td>';
            $items_html .= '<td>' . $qty . '</td>';
            $items_html .= '<td>' . wc_price( $line_total ) . '</td>';
            $items_html .= '</tr>';
        }
    }

    $items_html .= '<tr>';
    $items_html .= '<td colspan="2" style="text-align:right;"><strong>' . __( 'Total', 'auto-quote-reply' ) . ':</strong></td>';
    $items_html .= '<td><strong>' . wc_price( $total ) . '</strong></td>';
    $items_html .= '</tr>';
    $items_html .= '</tbody></table>';

    // Wrap in WooCommerce email template
    ob_start();
    wc_get_template( 'emails/email-header.php', array( 'email_heading' => __( 'Your Quote Request', 'auto-quote-reply' ) ) );

    echo '<p>' . __( 'Thank you for your request. Here are the details of your quote:', 'auto-quote-reply' ) . '</p>';
    echo $items_html;
    echo '<p>' . __( 'We will get back to you soon with further details.', 'auto-quote-reply' ) . '</p>';

    wc_get_template( 'emails/email-footer.php' );
    $message = ob_get_clean();

    // Send the email
    wc_mail( $to, $subject, $message );
}

