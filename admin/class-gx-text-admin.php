<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class GX_Text_Admin {

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_menu' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_action( 'wp_ajax_gx_text_test_twilio', array( $this, 'ajax_test_twilio' ) );
        add_action( 'wp_ajax_gx_text_send_broadcast', array( $this, 'ajax_send_broadcast' ) );
        add_action( 'wp_ajax_gx_text_delete_subscriber', array( $this, 'ajax_delete_subscriber' ) );
        add_action( 'wp_ajax_gx_text_export_subscribers', array( $this, 'ajax_export_subscribers' ) );
        add_filter( 'plugin_action_links_' . GX_TEXT_PLUGIN_BASENAME, array( $this, 'plugin_action_links' ) );
    }

    public function plugin_action_links( $links ) {
        $settings_link = '<a href="' . admin_url( 'admin.php?page=gx-text' ) . '">' . __( 'Settings', 'gx-text' ) . '</a>';
        array_unshift( $links, $settings_link );
        return $links;
    }

    public function add_menu() {
        add_menu_page(
            __( 'GX Text', 'gx-text' ),
            __( 'GX Text', 'gx-text' ),
            'manage_options',
            'gx-text',
            array( $this, 'render_dashboard' ),
            'dashicons-format-chat',
            30
        );

        add_submenu_page(
            'gx-text',
            __( 'Dashboard', 'gx-text' ),
            __( 'Dashboard', 'gx-text' ),
            'manage_options',
            'gx-text',
            array( $this, 'render_dashboard' )
        );

        add_submenu_page(
            'gx-text',
            __( 'Settings', 'gx-text' ),
            __( 'Settings', 'gx-text' ),
            'manage_options',
            'gx-text-settings',
            array( $this, 'render_settings' )
        );

        add_submenu_page(
            'gx-text',
            __( 'Appearance', 'gx-text' ),
            __( 'Appearance', 'gx-text' ),
            'manage_options',
            'gx-text-appearance',
            array( $this, 'render_appearance' )
        );

        add_submenu_page(
            'gx-text',
            __( 'Subscribers', 'gx-text' ),
            __( 'Subscribers', 'gx-text' ),
            'manage_options',
            'gx-text-subscribers',
            array( $this, 'render_subscribers' )
        );

        add_submenu_page(
            'gx-text',
            __( 'Broadcast', 'gx-text' ),
            __( 'Broadcast', 'gx-text' ),
            'manage_options',
            'gx-text-broadcast',
            array( $this, 'render_broadcast' )
        );

        add_submenu_page(
            'gx-text',
            __( 'Message Log', 'gx-text' ),
            __( 'Message Log', 'gx-text' ),
            'manage_options',
            'gx-text-messages',
            array( $this, 'render_messages' )
        );
    }

    public function enqueue_assets( $hook ) {
        if ( strpos( $hook, 'gx-text' ) === false ) {
            return;
        }
        wp_enqueue_media();
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );
        wp_enqueue_style( 'gx-text-admin', GX_TEXT_PLUGIN_URL . 'assets/css/admin.css', array(), GX_TEXT_VERSION );
        wp_enqueue_script( 'gx-text-admin', GX_TEXT_PLUGIN_URL . 'assets/js/admin.js', array( 'jquery', 'wp-color-picker' ), GX_TEXT_VERSION, true );
        wp_localize_script( 'gx-text-admin', 'gxTextAdmin', array(
            'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce'   => wp_create_nonce( 'gx_text_admin_nonce' ),
            'strings' => array(
                'testing'       => __( 'Testing connection…', 'gx-text' ),
                'success'       => __( 'Connection successful!', 'gx-text' ),
                'failed'        => __( 'Connection failed: ', 'gx-text' ),
                'sending'       => __( 'Sending broadcast…', 'gx-text' ),
                'sent'          => __( 'Broadcast sent!', 'gx-text' ),
                'confirmDelete' => __( 'Are you sure you want to delete this subscriber?', 'gx-text' ),
                'confirmBroadcast' => __( 'Send this message to all active subscribers?', 'gx-text' ),
                'chooseGraphic' => __( 'Choose Button Graphic', 'gx-text' ),
                'useGraphic'    => __( 'Use Graphic', 'gx-text' ),
                'chooseBrandLogo' => __( 'Choose Brand Logo', 'gx-text' ),
                'useBrandLogo'    => __( 'Use Brand Logo', 'gx-text' ),
                'chooseHoverGraphic' => __( 'Choose Hover Graphic', 'gx-text' ),
                'useHoverGraphic'    => __( 'Use Hover Graphic', 'gx-text' ),
            ),
        ) );
    }

    public function register_settings() {
        register_setting( 'gx_text_options_group', 'gx_text_options', array( $this, 'sanitize_options' ) );
    }

    public function sanitize_options( $input ) {
        return GX_Text_Options::sanitize( $input, get_option( 'gx_text_options', array() ) );
    }

    /* ─── AJAX Handlers ─────────────────────────────────────── */

    public function ajax_test_twilio() {
        check_ajax_referer( 'gx_text_admin_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'Unauthorized' );
        }
        $twilio = new GX_Text_Twilio();
        $result = $twilio->test_connection();
        if ( true === $result ) {
            wp_send_json_success( array( 'message' => __( 'Twilio connection successful!', 'gx-text' ) ) );
        } else {
            wp_send_json_error( array( 'message' => $result->get_error_message() ) );
        }
    }

    public function ajax_send_broadcast() {
        check_ajax_referer( 'gx_text_admin_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'Unauthorized' );
        }
        $message = isset( $_POST['message'] ) ? sanitize_textarea_field( $_POST['message'] ) : '';
        if ( empty( $message ) ) {
            wp_send_json_error( array( 'message' => __( 'Message cannot be empty.', 'gx-text' ) ) );
        }
        $phones = GX_Text_Subscribers::get_active_phones();
        if ( empty( $phones ) ) {
            wp_send_json_error( array( 'message' => __( 'No active subscribers found.', 'gx-text' ) ) );
        }
        $twilio  = new GX_Text_Twilio();
        $results = $twilio->send_broadcast( $phones, $message );
        $sent    = count( array_filter( $results, function( $r ) { return 'sent' === $r['status']; } ) );
        $failed  = count( $results ) - $sent;
        wp_send_json_success( array(
            'message' => sprintf( __( 'Broadcast complete: %d sent, %d failed.', 'gx-text' ), $sent, $failed ),
            'details' => $results,
        ) );
    }

    public function ajax_delete_subscriber() {
        check_ajax_referer( 'gx_text_admin_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'Unauthorized' );
        }
        $id = isset( $_POST['subscriber_id'] ) ? absint( $_POST['subscriber_id'] ) : 0;
        if ( $id > 0 ) {
            GX_Text_Subscribers::delete( $id );
            wp_send_json_success();
        }
        wp_send_json_error();
    }

    public function ajax_export_subscribers() {
        check_ajax_referer( 'gx_text_admin_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Unauthorized' );
        }
        $csv = GX_Text_Subscribers::export_csv();
        header( 'Content-Type: text/csv' );
        header( 'Content-Disposition: attachment; filename="gx-text-subscribers-' . date( 'Y-m-d' ) . '.csv"' );
        echo $csv;
        wp_die();
    }

    /* ─── Page Renderers ────────────────────────────────────── */

    public function render_dashboard() {
        $total_subs   = GX_Text_Subscribers::count();
        $active_subs  = GX_Text_Subscribers::count( 'active' );
        $unsub_count  = GX_Text_Subscribers::count( 'unsubscribed' );
        $twilio       = new GX_Text_Twilio();
        $is_configured = $twilio->is_configured();

        global $wpdb;
        $msg_table   = $wpdb->prefix . 'gx_text_messages';
        $total_msgs  = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$msg_table}" );
        $recent_msgs = $wpdb->get_results( "SELECT * FROM {$msg_table} ORDER BY created_at DESC LIMIT 10" );

        include GX_TEXT_PLUGIN_DIR . 'admin/views/dashboard.php';
    }

    public function render_settings() {
        $options = GX_Text_Options::all();
        include GX_TEXT_PLUGIN_DIR . 'admin/views/settings.php';
    }

    public function render_appearance() {
        $options = GX_Text_Options::all();
        include GX_TEXT_PLUGIN_DIR . 'admin/views/appearance.php';
    }

    public function render_subscribers() {
        $page    = isset( $_GET['paged'] ) ? max( 1, absint( $_GET['paged'] ) ) : 1;
        $per_page = 25;
        $status  = isset( $_GET['status'] ) ? sanitize_text_field( $_GET['status'] ) : '';
        $subscribers = GX_Text_Subscribers::get_all( $per_page, ( $page - 1 ) * $per_page, $status );
        $total       = GX_Text_Subscribers::count( $status );
        $total_pages = ceil( $total / $per_page );
        include GX_TEXT_PLUGIN_DIR . 'admin/views/subscribers.php';
    }

    public function render_broadcast() {
        $active_count = GX_Text_Subscribers::count( 'active' );
        include GX_TEXT_PLUGIN_DIR . 'admin/views/broadcast.php';
    }

    public function render_messages() {
        global $wpdb;
        $table     = $wpdb->prefix . 'gx_text_messages';
        $page      = isset( $_GET['paged'] ) ? max( 1, absint( $_GET['paged'] ) ) : 1;
        $per_page  = 30;
        $offset    = ( $page - 1 ) * $per_page;
        $total     = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" );
        $messages  = $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM {$table} ORDER BY created_at DESC LIMIT %d OFFSET %d",
            $per_page, $offset
        ) );
        $total_pages = ceil( $total / $per_page );
        include GX_TEXT_PLUGIN_DIR . 'admin/views/messages.php';
    }
}
