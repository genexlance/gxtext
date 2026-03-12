<?php
/**
 * GX Text Uninstall
 *
 * Fired when the plugin is deleted (not deactivated).
 * Removes all plugin data from the database.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

global $wpdb;

// Remove options
delete_option( 'gx_text_options' );
delete_option( 'gx_text_db_version' );

// Remove database tables
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}gx_text_subscribers" );
$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}gx_text_messages" );

// Clean up transients
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_gx_text_%'" );
$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_gx_text_%'" );
