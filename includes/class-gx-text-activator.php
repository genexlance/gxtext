<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class GX_Text_Activator {

    public static function activate() {
        self::create_tables();
        self::set_defaults();
        flush_rewrite_rules();
    }

    private static function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        // Subscribers table
        $table_subscribers = $wpdb->prefix . 'gx_text_subscribers';
        $sql_subscribers = "CREATE TABLE {$table_subscribers} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            phone varchar(20) NOT NULL,
            name varchar(100) DEFAULT '',
            email varchar(100) DEFAULT '',
            status enum('active','unsubscribed','pending') DEFAULT 'active',
            tags text DEFAULT NULL,
            ip_address varchar(45) DEFAULT '',
            consent_given tinyint(1) DEFAULT 1,
            subscribed_at datetime DEFAULT CURRENT_TIMESTAMP,
            unsubscribed_at datetime DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY phone (phone),
            KEY status (status)
        ) {$charset_collate};";

        // Messages log table
        $table_messages = $wpdb->prefix . 'gx_text_messages';
        $sql_messages = "CREATE TABLE {$table_messages} (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            direction enum('inbound','outbound') NOT NULL,
            phone_from varchar(20) NOT NULL,
            phone_to varchar(20) NOT NULL,
            message_body text NOT NULL,
            twilio_sid varchar(64) DEFAULT '',
            status varchar(20) DEFAULT 'sent',
            message_type enum('conversation','broadcast','auto_reply') DEFAULT 'conversation',
            sender_name varchar(100) DEFAULT '',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY direction (direction),
            KEY phone_from (phone_from),
            KEY created_at (created_at)
        ) {$charset_collate};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql_subscribers );
        dbDelta( $sql_messages );

        update_option( 'gx_text_db_version', '1.0.0' );
    }

    private static function set_defaults() {
        if ( false === get_option( 'gx_text_options' ) ) {
            update_option( 'gx_text_options', GX_Text::defaults() );
        }
    }
}
