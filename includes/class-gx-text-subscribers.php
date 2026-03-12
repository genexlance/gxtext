<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class GX_Text_Subscribers {

    /**
     * Add a new subscriber.
     */
    public static function add( $phone, $name = '', $email = '', $tags = '', $ip = '' ) {
        global $wpdb;
        $table = $wpdb->prefix . 'gx_text_subscribers';
        $phone = GX_Text_Options::normalize_phone( $phone );
        $name  = GX_Text_Options::sanitize_text( $name );
        $email = sanitize_email( $email );
        $tags  = sanitize_text_field( $tags );
        $ip    = sanitize_text_field( $ip );

        if ( ! $phone ) {
            return new WP_Error( 'gx_text_subscriber', __( 'Invalid phone number.', 'gx-text' ) );
        }

        // Check if already exists
        $existing = $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$table} WHERE phone = %s", $phone
        ) );

        if ( $existing ) {
            if ( 'unsubscribed' === $existing->status ) {
                // Re-subscribe
                $updated = $wpdb->update( $table, array(
                    'status'          => 'active',
                    'name'            => $name,
                    'email'           => $email,
                    'consent_given'   => 1,
                    'unsubscribed_at' => null,
                ), array( 'id' => $existing->id ), array( '%s', '%s', '%s', '%d', '%s' ), array( '%d' ) );

                if ( false !== $updated ) {
                    return array( 'status' => 'resubscribed', 'id' => $existing->id );
                }

                return new WP_Error( 'gx_text_subscriber', __( 'Unable to update the subscriber record.', 'gx-text' ) );
            }
            return array( 'status' => 'already_subscribed', 'id' => $existing->id );
        }

        $inserted = $wpdb->insert( $table, array(
            'phone'         => $phone,
            'name'          => $name,
            'email'         => $email,
            'tags'          => $tags,
            'ip_address'    => $ip,
            'status'        => 'active',
            'consent_given' => 1,
        ), array( '%s', '%s', '%s', '%s', '%s', '%s', '%d' ) );

        if ( false === $inserted ) {
            return new WP_Error( 'gx_text_subscriber', __( 'Unable to save the subscriber record.', 'gx-text' ) );
        }

        return array( 'status' => 'subscribed', 'id' => $wpdb->insert_id );
    }

    /**
     * Unsubscribe a phone number.
     */
    public static function unsubscribe( $phone ) {
        global $wpdb;
        $table = $wpdb->prefix . 'gx_text_subscribers';
        $phone = GX_Text_Options::normalize_phone( $phone );
        if ( ! $phone ) {
            return false;
        }
        return $wpdb->update( $table, array(
            'status'          => 'unsubscribed',
            'unsubscribed_at' => current_time( 'mysql' ),
        ), array( 'phone' => $phone ), array( '%s', '%s' ), array( '%s' ) );
    }

    /**
     * Get all active subscribers.
     */
    public static function get_active( $limit = 0, $offset = 0 ) {
        global $wpdb;
        $table = $wpdb->prefix . 'gx_text_subscribers';
        $sql   = "SELECT * FROM {$table} WHERE status = 'active' ORDER BY subscribed_at DESC";
        if ( $limit > 0 ) {
            $sql .= $wpdb->prepare( ' LIMIT %d OFFSET %d', $limit, $offset );
        }
        return $wpdb->get_results( $sql );
    }

    /**
     * Get all subscribers (any status).
     */
    public static function get_all( $limit = 50, $offset = 0, $status = '' ) {
        global $wpdb;
        $table = $wpdb->prefix . 'gx_text_subscribers';
        $where = '';
        if ( ! empty( $status ) ) {
            $where = $wpdb->prepare( ' WHERE status = %s', $status );
        }
        $sql = "SELECT * FROM {$table}{$where} ORDER BY created_at DESC";
        if ( $limit > 0 ) {
            $sql .= $wpdb->prepare( ' LIMIT %d OFFSET %d', $limit, $offset );
        }
        return $wpdb->get_results( $sql );
    }

    /**
     * Count subscribers.
     */
    public static function count( $status = '' ) {
        global $wpdb;
        $table = $wpdb->prefix . 'gx_text_subscribers';
        if ( ! empty( $status ) ) {
            return (int) $wpdb->get_var( $wpdb->prepare(
                "SELECT COUNT(*) FROM {$table} WHERE status = %s", $status
            ) );
        }
        return (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" );
    }

    /**
     * Delete a subscriber by ID.
     */
    public static function delete( $id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'gx_text_subscribers';
        return $wpdb->delete( $table, array( 'id' => $id ), array( '%d' ) );
    }

    /**
     * Get active subscriber phone numbers for broadcast.
     */
    public static function get_active_phones() {
        global $wpdb;
        $table = $wpdb->prefix . 'gx_text_subscribers';
        return $wpdb->get_col( "SELECT phone FROM {$table} WHERE status = 'active'" );
    }

    /**
     * Export subscribers as CSV data.
     */
    public static function export_csv() {
        $subscribers = self::get_all( 0 );
        $csv = "Phone,Name,Email,Status,Tags,Subscribed At,Unsubscribed At\n";
        foreach ( $subscribers as $sub ) {
            $csv .= sprintf(
                '"%s","%s","%s","%s","%s","%s","%s"' . "\n",
                self::escape_csv_value( $sub->phone ),
                self::escape_csv_value( $sub->name ),
                self::escape_csv_value( $sub->email ),
                self::escape_csv_value( $sub->status ),
                self::escape_csv_value( $sub->tags ),
                self::escape_csv_value( $sub->subscribed_at ),
                self::escape_csv_value( $sub->unsubscribed_at )
            );
        }
        return $csv;
    }

    private static function escape_csv_value( $value ) {
        $value = str_replace( '"', '""', (string) $value );

        if ( preg_match( '/^[=\+\-@]/', $value ) ) {
            $value = "'" . $value;
        }

        return $value;
    }
}
