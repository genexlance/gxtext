=== GX Text by Genex Marketing Agency Ltd ===
Contributors: genexmarketing
Tags: sms, text messaging, twilio, text us, subscribe, newsletter, chat widget
Requires at least: 5.8
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A powerful "Text Us Now" button plugin with Twilio SMS integration, customizable animated UI, and text subscription/newsletter system.

== Description ==

**GX Text** by Genex Marketing Agency Ltd adds a beautiful, animated "Text Us Now" floating button to your WordPress website. Visitors can send you text messages directly from your site, and you can build a text subscriber list for sending deals, updates, and newsletters via SMS.

= Key Features =

* **Floating Text Button** - Customizable animated button with pulse, bounce, shake, glow, and optional square brand graphic support
* **Flexible Positioning** - Place the button in any corner: bottom-right, bottom-left, top-right, or top-left
* **Twilio SMS Integration** - Send and receive text messages through the Twilio API
* **Secure API Storage** - Twilio credentials are encrypted using AES-256-CBC with WordPress security salts
* **Text Subscription System** - Build a subscriber list for text-based newsletters and deal alerts
* **Broadcast Messaging** - Send bulk text messages to all active subscribers
* **Shortcodes** - Embed buttons, forms, and subscribe widgets anywhere with shortcodes
* **Gutenberg Blocks** - Three custom blocks for the WordPress block editor
* **Responsive Design** - Looks great on all devices with mobile-first design
* **Customizable UI** - Control colors, sizes, fonts, animations, and more
* **Message Logging** - Track all inbound and outbound messages
* **Subscriber Management** - View, export, and manage your text subscribers
* **STOP/START Support** - Automatic opt-out and opt-in handling via text keywords
* **Webhook Verification** - Optional Twilio signature validation for inbound webhooks
* **Rate Limiting** - Built-in protection against spam and abuse, including honeypot fields and per-phone throttling
* **Accessibility** - ARIA labels, keyboard navigation, and reduced motion support
* **Custom CSS** - Add your own styles for complete control

= Shortcodes =

* `[gx_text_button]` - Inline "Text Us" button
* `[gx_text_form]` - Full message form
* `[gx_text_subscribe]` - Subscription form

= Branding Graphic =

Go to **GX Text > Appearance > Branding Graphic** to upload a small square logo.

* GX Text crops the square image into a circle automatically
* The logo appears beside the floating button label
* You can control the logo size with the **Logo Size** slider
* Inline buttons also support a `graphic` attribute, for example:
  `[gx_text_button label="Text Us" graphic="https://example.com/logo-square.png"]`

= Gutenberg Blocks =

* **GX Text Button** - Inline button block with color and size options
* **GX Text Form** - Full message form block
* **GX Text Subscribe** - Subscription form block

= Twilio Webhook =

Set your Twilio webhook URL to: `https://yoursite.com/wp-json/gx-text/v1/webhook`

This enables inbound message handling, STOP/START keyword processing, and message forwarding. Signature validation is enabled by default and can be adjusted under **GX Text > Settings** if your hosting stack rewrites webhook URLs.

== Installation ==

1. Upload the `gx-text` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to **GX Text > Settings** to configure your Twilio credentials
4. Customize the appearance under **GX Text > Appearance**
5. Add your small logo under **Appearance > Branding Graphic** if you want a circular brand mark beside the button text
6. The floating button will automatically appear on your site

== Frequently Asked Questions ==

= Do I need a Twilio account? =

Yes, you need a Twilio account to send and receive SMS messages. Sign up at [twilio.com](https://www.twilio.com). The plugin will still log messages even without Twilio configured.

= Are my API keys stored securely? =

Yes! Your Twilio Account SID and Auth Token are encrypted using AES-256-CBC encryption with your WordPress security salts before being stored in the database.

= Can visitors unsubscribe? =

Yes. Subscribers can reply STOP, UNSUBSCRIBE, CANCEL, QUIT, or END to any text message to automatically unsubscribe. They can reply START or SUBSCRIBE to re-subscribe.

= Can I customize the button appearance? =

Absolutely! You can customize colors, size, border radius, position, animation type, icon style, label text, optional square branding graphic, and much more from the Appearance settings page.

= Where do I add the small logo / circle graphic? =

Open **GX Text > Appearance > Branding Graphic**. Upload a square image there, and the plugin will render it as a small circle next to the “Text Us” label.

== Changelog ==

= 1.1.0 =
* Added optional square branding graphic support for the floating and inline buttons
* Hardened public REST endpoints with stronger validation, honeypot fields, and per-phone throttling
* Added optional Twilio webhook signature validation in plugin settings
* Switched encrypted credential storage to an authenticated format with backward compatibility
* Centralized option sanitization and shared frontend asset loading for shortcode/block reliability

= 1.0.0 =
* Initial release
* Floating text button with animations
* Twilio SMS integration
* Text subscription system
* Broadcast messaging
* Shortcodes and Gutenberg blocks
* Subscriber management and export
* Message logging
* Encrypted credential storage

== Upgrade Notice ==

= 1.1.0 =
Security and architecture update with webhook validation, stronger credential protection, and branded button graphics.

= 1.0.0 =
Initial release of GX Text by Genex Marketing Agency Ltd.
