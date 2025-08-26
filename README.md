# Woo Auto Quote Reply

A custom WordPress plugin that extends **WooCommerce** and **YITH Request a Quote** functionality.  
When a customer submits a quote request, this plugin automatically sends the customer a professional response email including:

- Product names and details
- Quantities requested
- Individual pricing and line totals
- Overall quote total
- Professional WooCommerce email styling
- Debug and testing functionality

---

## ✨ Features

- **Automatic Customer Response**: Sends confirmation emails immediately after quote submission
- **Multiple Hook Support**: Compatible with different YITH plugin versions using multiple hooks
- **WooCommerce Integration**: Uses stored product prices and native email templates
- **Admin Dashboard**: Built-in admin panel with testing and debugging tools
- **Debug Logging**: Comprehensive logging for troubleshooting
- **Test Functionality**: Built-in test email system for verification
- **Professional Styling**: Uses WooCommerce email templates for brand consistency

---

## 📂 Installation

1. **Download the plugin file** (`auto-quote-reply.php`) to your WordPress plugins directory:

   ```bash
   # Place auto-quote-reply.php in wp-content/plugins/
   ```

2. **Activate the plugin** in WordPress Admin → Plugins → Auto Quote Reply

3. **Verify requirements** are met:
   - WordPress 5.0+
   - WooCommerce 5.0+
   - YITH Request a Quote (Free or Pro)

---

## ⚙️ Configuration

### Basic Setup

The plugin works out of the box with no configuration required. It will automatically:

- Hook into quote request submissions
- Send confirmation emails to customers
- Include product details and pricing

### Admin Panel

Access the plugin settings at: **WordPress Admin → Quote Reply**

**Features available:**

- Test email functionality
- Debug information display
- Plugin status verification
- Error log location

### Testing

1. Go to **Quote Reply** in your admin menu
2. Use the **Test Email Functionality** section
3. Enter an email address and click "Send Test Email"
4. Check the debug information for plugin status

---

## 🛠️ Technical Details

### Hooks Used

The plugin hooks into multiple YITH actions for maximum compatibility:

```php
add_action( 'send_raq_mail', 'aqr_send_customer_quote_reply', 20, 1 );
add_action( 'ywraq_process', 'aqr_send_customer_quote_reply', 20, 1 );
add_action( 'yith_ywraq_request_after_sent', 'aqr_send_customer_quote_reply', 10, 2 );
```

### Email Template

- Uses WooCommerce's native email templates (`email-header.php` and `email-footer.php`)
- Includes product table with pricing
- Professional styling consistent with your store

### Debug Logging

Enable debug logging in `wp-config.php`:

```php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
```

Look for these log entries:

```
Auto Quote Reply Hook Triggered with data: ...
Auto Quote Reply: SUCCESS - Quote email sent to ...
```

---

## 🔧 Troubleshooting

### Plugin Not Working?

1. **Check Debug Information**: Visit the admin panel to verify plugin status
2. **Enable Debug Logging**: Check `/wp-content/debug.log` for error messages
3. **Test Email Function**: Use the built-in test to verify email sending
4. **Verify Requirements**: Ensure WooCommerce and YITH plugins are active

### Common Issues

- **No emails sent**: Check WordPress email configuration
- **Hook not triggered**: Verify YITH plugin is active and up to date
- **Missing product data**: Ensure products have prices set in WooCommerce

---

## 📝 Changelog

### Version 1.0.0

- Initial release
- Multiple hook compatibility
- Admin dashboard with testing
- Debug logging system
- WooCommerce email template integration

---

## 👨‍💻 Developer

**Loai Amr**

- **GitHub**: [https://github.com/Loai-amr](https://github.com/Loai-amr)
- **LinkedIn**: [https://www.linkedin.com/in/loai-amrr/](https://www.linkedin.com/in/loai-amrr/)

---

## 📄 License

This plugin is provided as-is for educational and commercial use.

---

## 🤝 Contributing

Feel free to submit issues and enhancement requests!

For support or questions, please open an issue on GitHub or connect via LinkedIn.
