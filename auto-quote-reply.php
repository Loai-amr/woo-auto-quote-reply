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
        
        <!-- Test Email Section -->
        <div style="margin-top: 30px; padding: 20px; background-color: #fff3cd; border-left: 4px solid #ffc107;">
            <h3><?php _e( 'Test Email Functionality', 'auto-quote-reply' ); ?></h3>
            <p><?php _e( 'Use this to test if the email functionality is working correctly:', 'auto-quote-reply' ); ?></p>
            
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="test_email"><?php _e( 'Test Email Address', 'auto-quote-reply' ); ?></label>
                    </th>
                    <td>
                        <input type="email" id="test_email" name="test_email" value="<?php echo esc_attr( get_option( 'admin_email' ) ); ?>" class="regular-text" />
                        <button type="button" id="send_test_email" class="button button-secondary"><?php _e( 'Send Test Email', 'auto-quote-reply' ); ?></button>
                        <p class="description"><?php _e( 'Enter an email address and click the button to send a test email.', 'auto-quote-reply' ); ?></p>
                        <div id="test_email_result" style="margin-top: 10px;"></div>
                    </td>
                </tr>
            </table>
        </div>
        
        <!-- Debug Information -->
        <div style="margin-top: 30px; padding: 20px; background-color: #d1ecf1; border-left: 4px solid #17a2b8;">
            <h3><?php _e( 'Debug Information', 'auto-quote-reply' ); ?></h3>
            <p><strong><?php _e( 'Plugin Status:', 'auto-quote-reply' ); ?></strong> 
            <?php 
            if ( defined( 'YITH_YWRAQ_VERSION' ) ) {
                echo '<span style="color: green;">✓ YITH Request a Quote plugin is active (v' . YITH_YWRAQ_VERSION . ')</span>';
            } else {
                echo '<span style="color: red;">✗ YITH Request a Quote plugin is NOT active</span>';
            }
            ?>
            </p>
            <p><strong><?php _e( 'WordPress Email:', 'auto-quote-reply' ); ?></strong> 
            <?php 
            $admin_email = get_option( 'admin_email' );
            echo '<span>' . esc_html( $admin_email ) . '</span>';
            ?>
            </p>
            <p><strong><?php _e( 'Error Log Location:', 'auto-quote-reply' ); ?></strong> 
            <?php 
            $log_file = ini_get( 'error_log' );
            if ( empty( $log_file ) ) {
                $log_file = WP_CONTENT_DIR . '/debug.log';
            }
            echo '<span>' . esc_html( $log_file ) . '</span>';
            ?>
            </p>
        </div>
        
        <p>
            <strong>Developer:</strong> <a href="https://github.com/Loai-amr" target="_blank">Loai Amr</a><br>
            <strong>LinkedIn:</strong> <a href="https://www.linkedin.com/in/loai-amrr/" target="_blank">Connect</a>
        </p>
        
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('#send_test_email').on('click', function() {
                var testEmail = $('#test_email').val();
                var button = $(this);
                var resultDiv = $('#test_email_result');
                
                if (!testEmail) {
                    resultDiv.html('<div style="color: red;">Please enter a valid email address.</div>');
                    return;
                }
                
                button.prop('disabled', true).text('Sending...');
                resultDiv.html('<div style="color: blue;">Sending test email...</div>');
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'aqr_test_email',
                        test_email: testEmail,
                        nonce: '<?php echo wp_create_nonce( 'aqr_test_email' ); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            resultDiv.html('<div style="color: green;">' + response.data + '</div>');
                        } else {
                            resultDiv.html('<div style="color: red;">Error: ' + response.data + '</div>');
                        }
                    },
                    error: function() {
                        resultDiv.html('<div style="color: red;">Ajax error occurred. Please try again.</div>');
                    },
                    complete: function() {
                        button.prop('disabled', false).text('Send Test Email');
                    }
                });
            });
        });
        </script>
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
 
// Hook into the quote request submission - try multiple hooks for compatibility
add_action( 'send_raq_mail', 'aqr_send_customer_quote_reply', 20, 1 );
add_action( 'ywraq_process', 'aqr_send_customer_quote_reply', 20, 1 );
add_action( 'yith_ywraq_request_after_sent', 'aqr_send_customer_quote_reply', 10, 2 );

// Add test email functionality
add_action( 'wp_ajax_aqr_test_email', 'aqr_test_email' );

function aqr_send_customer_quote_reply( $raq ) {
    error_log('Auto Quote Reply Hook Triggered with data: ' . print_r( $raq, true ) );

    // Handle different hook parameter formats
    if ( is_array( $raq ) && isset( $raq['user_email'] ) ) {
        $customer_email = $raq['user_email'];
        $raq_content = isset( $raq['raq_content'] ) ? $raq['raq_content'] : array();
    } else {
        error_log('Invalid RAQ data format received');
        return;
    }

    if ( empty( $customer_email ) ) {
        error_log('No customer email found in RAQ data');
        return;
    }

    $to      = sanitize_email( $customer_email );
    $subject = sprintf( __( 'Your Quote Request from %s', 'auto-quote-reply' ), get_bloginfo('name') );

    // Build items table
    $items_html = '<table cellspacing="0" cellpadding="6" style="width:100%; border:1px solid #eee;" border="1">';
    $items_html .= '<thead><tr>';
    $items_html .= '<th>' . __( 'Product', 'auto-quote-reply' ) . '</th>';
    $items_html .= '<th>' . __( 'Quantity', 'auto-quote-reply' ) . '</th>';
    $items_html .= '<th>' . __( 'Price', 'auto-quote-reply' ) . '</th>';
    $items_html .= '</tr></thead><tbody>';

    $total = 0;

    if ( ! empty( $raq_content ) && is_array( $raq_content ) ) {
        foreach ( $raq_content as $item ) {
            $product_id = $item['product_id'];
            $product    = wc_get_product( $product_id );

            if ( $product ) {
                $price      = floatval( $product->get_price() );
                $qty        = intval( $item['quantity'] );
                $line_total = $price * $qty;
                $total     += $line_total;

                $items_html .= '<tr>';
                $items_html .= '<td>' . esc_html( $product->get_name() ) . '</td>';
                $items_html .= '<td>' . $qty . '</td>';
                $items_html .= '<td>' . wc_price( $line_total ) . '</td>';
                $items_html .= '</tr>';
            }
        }
    }

    $items_html .= '<tr>';
    $items_html .= '<td colspan="2" style="text-align:right;"><strong>' . __( 'Total', 'auto-quote-reply' ) . ':</strong></td>';
    $items_html .= '<td><strong>' . wc_price( $total ) . '</strong></td>';
    $items_html .= '</tr>';
    $items_html .= '</tbody></table>';

    // Wrap with WooCommerce email template
    ob_start();
    wc_get_template( 'emails/email-header.php', array( 'email_heading' => __( 'Your Quote Request', 'auto-quote-reply' ) ) );

    echo '<p>' . __( 'Thank you for your request. Here are the details of your quote:', 'auto-quote-reply' ) . '</p>';
    echo $items_html;
    echo '<p>' . __( 'We will get back to you soon with further details.', 'auto-quote-reply' ) . '</p>';

    wc_get_template( 'emails/email-footer.php' );
    $message = ob_get_clean();

    // Send with WooCommerce mailer
    $headers = array( 'Content-Type: text/html; charset=UTF-8' );

    if ( wc_mail( $to, $subject, $message, $headers ) ) {
        error_log('Auto Quote Reply: SUCCESS - Quote email sent to ' . $to);
    } else {
        error_log('Auto Quote Reply: FAILED - Quote email not sent to ' . $to);
    }
}

/**
 * Test email functionality
 */
function aqr_test_email() {
    // Check nonce for security
    if ( ! wp_verify_nonce( $_POST['nonce'], 'aqr_test_email' ) ) {
        wp_die( 'Security check failed' );
    }
    
    // Check user permissions
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'Permission denied' );
    }
    
    $test_email = sanitize_email( $_POST['test_email'] );
    
    if ( empty( $test_email ) ) {
        wp_send_json_error( 'Please provide a valid email address' );
    }
    
    // Create test data
    $test_raq = array(
        'user_email' => $test_email,
        'user_name' => 'Test Customer',
        'raq_content' => array(
            array(
                'product_id' => 0,
                'quantity' => 1
            )
        )
    );
    
    // Send test email
    aqr_send_customer_quote_reply( $test_raq );
    
    wp_send_json_success( 'Test email sent to ' . $test_email . '. Check your email and the WordPress error log for details.' );
}
