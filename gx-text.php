<?php
/**
 * Plugin Name: GX Text by Genex Marketing Agency Ltd
 * Plugin URI:  https://genexmarketing.com/gx-text
 * Description: A powerful "Text Us Now" button plugin with Twilio SMS integration, customizable animated UI, and text subscription/newsletter system.
 * Version:     1.1.0
 * Author:      Genex Marketing Agency Ltd
 * Author URI:  https://genexmarketing.com
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: gx-text
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* ─── Constants ─────────────────────────────────────────────── */
define( 'GX_TEXT_VERSION', '1.1.0' );
define( 'GX_TEXT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'GX_TEXT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'GX_TEXT_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/* ─── Autoload includes ────────────────────────────────────── */
require_once GX_TEXT_PLUGIN_DIR . 'includes/class-gx-text-activator.php';
require_once GX_TEXT_PLUGIN_DIR . 'includes/class-gx-text-deactivator.php';
require_once GX_TEXT_PLUGIN_DIR . 'includes/class-gx-text-encryption.php';
require_once GX_TEXT_PLUGIN_DIR . 'includes/class-gx-text-options.php';
require_once GX_TEXT_PLUGIN_DIR . 'includes/class-gx-text-assets.php';
require_once GX_TEXT_PLUGIN_DIR . 'includes/class-gx-text-twilio.php';
require_once GX_TEXT_PLUGIN_DIR . 'includes/class-gx-text-subscribers.php';
require_once GX_TEXT_PLUGIN_DIR . 'admin/class-gx-text-admin.php';
require_once GX_TEXT_PLUGIN_DIR . 'includes/class-gx-text-frontend.php';
require_once GX_TEXT_PLUGIN_DIR . 'includes/class-gx-text-shortcodes.php';
require_once GX_TEXT_PLUGIN_DIR . 'includes/class-gx-text-rest-api.php';
require_once GX_TEXT_PLUGIN_DIR . 'blocks/class-gx-text-blocks.php';

/* ─── Activation / Deactivation ─────────────────────────────── */
register_activation_hook( __FILE__, array( 'GX_Text_Activator', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'GX_Text_Deactivator', 'deactivate' ) );

/* ─── Main Plugin Class ─────────────────────────────────────── */
final class GX_Text {

    private static $instance = null;

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'init', array( $this, 'load_textdomain' ) );
        add_action( 'init', array( 'GX_Text_Encryption', 'maybe_upgrade_options' ), 5 );
        $this->init_components();
    }

    public function load_textdomain() {
        load_plugin_textdomain( 'gx-text', false, dirname( GX_TEXT_PLUGIN_BASENAME ) . '/languages' );
    }

    private function init_components() {
        // Admin
        if ( is_admin() ) {
            new GX_Text_Admin();
        }

        // Frontend
        new GX_Text_Frontend();

        // Shortcodes
        new GX_Text_Shortcodes();

        // REST API
        new GX_Text_REST_API();

        // Blocks
        new GX_Text_Blocks();
    }

    /**
     * Helper: get a plugin option with default.
     */
    public static function get_option( $key, $default = '' ) {
        return GX_Text_Options::get( $key, $default );
    }

    /**
     * Helper: update a single option key.
     */
    public static function update_option( $key, $value ) {
        $options = GX_Text_Options::all();
        $options[ $key ] = $value;
        update_option( 'gx_text_options', $options );
        GX_Text_Options::prime( $options );
    }

    /**
     * Return default options.
     */
    public static function defaults() {
        return array(
            // Twilio
            'twilio_account_sid'   => '',
            'twilio_auth_token'    => '',
            'twilio_phone_number'  => '',
            'business_phone'       => '',

            // Appearance
            'button_position'      => 'bottom-right',
            'button_color'         => '#25D366',
            'button_text_color'    => '#ffffff',
            'button_icon'          => 'chat',
            'button_label'         => 'Text Us!',
            'button_size'          => '60',
            'button_border_radius' => '50',
            'button_graphic_url'   => '',
            'button_graphic_size'  => '28',
            'animation_type'       => 'pulse',
            'show_on_mobile'       => '1',
            'show_on_desktop'      => '1',
            'z_index'              => '9999',
            'offset_x'             => '20',
            'offset_y'             => '20',

            // Widget / Chat window
            'widget_title'         => 'Text Us Now',
            'widget_subtitle'      => 'Send us a message and we\'ll reply via text!',
            'widget_bg_color'      => '#ffffff',
            'widget_header_color'  => '#25D366',
            'widget_header_text'   => '#ffffff',
            'widget_font_family'   => 'inherit',
            'success_message'      => 'Thanks! We\'ll text you back shortly.',
            'placeholder_name'     => 'Your Name',
            'placeholder_phone'    => 'Your Phone Number',
            'placeholder_message'  => 'Type your message…',

            // Subscription
            'enable_subscribe'     => '1',
            'subscribe_heading'    => 'Get Deals via Text',
            'subscribe_description'=> 'Subscribe to receive exclusive deals, updates, and news straight to your phone!',
            'subscribe_consent'    => 'I agree to receive text messages. Msg & data rates may apply. Reply STOP to unsubscribe.',
            'subscribe_success'    => 'You\'re subscribed! Watch for our texts.',
            'subscribe_btn_text'   => 'Subscribe',
            'subscribe_btn_color'  => '#FF6B35',

            // Display rules
            'display_pages'        => 'all',
            'exclude_pages'        => '',
            'twilio_validate_webhook' => '1',

            // Custom CSS
            'custom_css'           => '',
        );
    }
}

/* ─── Boot ──────────────────────────────────────────────────── */
function gx_text() {
    return GX_Text::instance();
}
gx_text();
