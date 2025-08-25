# Woo Auto Quote Reply

A custom WordPress plugin that extends **WooCommerce** and **YITH Request a Quote** functionality.  
When a customer submits a quote request, instead of only emailing the admin, this plugin automatically sends the customer a professional response email including:

- Product names
- Quantities
- Individual pricing
- Line totals and overall total
- Styled with WooCommerce’s native email template

---

## ✨ Features
- Auto-response to customer quote requests  
- Uses WooCommerce’s stored product prices (even if hidden on the frontend)  
- Branded WooCommerce email template for consistency  
- Lightweight and easy to maintain  

---

## 📂 Installation
1. Download or clone this repository into your WordPress plugins directory:

   ```bash
   cd wp-content/plugins
   git clone https://github.com/yourusername/woo-auto-quote-reply.git
2. Activate Woo Auto Quote Reply in your WordPress admin under Plugins.
3. Make sure WooCommerce and YITH Request a Quote are installed and active.

---

## ⚙️ Requirements
-WordPress 6.0+
-WooCommerce 7.0+
-YITH Request a Quote (Free or Pro)

---

## 🛠️ Development
This plugin hooks into:

add_action( 'yith_ywraq_mail_quote_request_admin', 'aqr_send_customer_quote_reply', 10, 2 );

to trigger the email after the admin notification.
The email is generated using WooCommerce’s email-header and email-footer templates for consistent branding.
