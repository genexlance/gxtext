<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Twilio API integration – sends SMS via Twilio REST API.
 * Does NOT require the Twilio PHP SDK; uses wp_remote_post().
 */
class GX_Text_Twilio {

    private $account_sid;
    private $auth_token;
    private $from_number;

    public function __construct() {
        $options           = GX_Text_Options::all();
        $this->account_sid = GX_Text_Encryption::decrypt( isset( $options['twilio_account_sid'] ) ? $options['twilio_account_sid'] : '' );
        $this->auth_token  = GX_Text_Encryption::decrypt( isset( $options['twilio_auth_token'] ) ? $options['twilio_auth_token'] : '' );
        $this->from_number = isset( $options['twilio_phone_number'] ) ? $options['twilio_phone_number'] : '';
    }

    /**
     * Check if Twilio is configured.
     */
    public function is_configured() {
        return ! empty( $this->account_sid ) && ! empty( $this->auth_token ) && ! empty( $this->from_number );
    }

    public function get_auth_token() {
        return $this->auth_token;
    }

    /**
     * Send a single SMS.
     *
     * @param string $to   E.164 phone number.
     * @param string $body Message body.
     * @return array|WP_Error
     */
    public function send_sms( $to, $body, $type = 'conversation', $sender_name = '' ) {
        if ( ! $this->is_configured() ) {
            return new WP_Error( 'gx_text_twilio', __( 'Twilio is not configured.', 'gx-text' ) );
        }

        $to   = GX_Text_Options::normalize_phone( $to );
        $body = GX_Text_Options::sanitize_textarea( $body );
        if ( ! $to || '' === $body ) {
            return new WP_Error( 'gx_text_twilio', __( 'A valid destination number and message are required.', 'gx-text' ) );
        }

        $url = sprintf(
            'https://api.twilio.com/2010-04-01/Accounts/%s/Messages.json',
            $this->account_sid
        );

        $response = wp_remote_post( $url, array(
            'headers' => array(
                'Authorization' => 'Basic ' . base64_encode( $this->account_sid . ':' . $this->auth_token ),
            ),
            'body' => array(
                'From' => $this->from_number,
                'To'   => $to,
                'Body' => $body,
            ),
            'timeout' => 30,
        ) );

        if ( is_wp_error( $response ) ) {
            self::log_message( 'outbound', $this->from_number, $to, $body, '', 'failed', $type, $sender_name );
            return $response;
        }

        $code = wp_remote_retrieve_response_code( $response );
        $data = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( $code >= 200 && $code < 300 ) {
            self::log_message( 'outbound', $this->from_number, $to, $body, isset( $data['sid'] ) ? $data['sid'] : '', 'sent', $type, $sender_name );
            return $data;
        }

        $error_msg = isset( $data['message'] ) ? $data['message'] : __( 'Unknown Twilio error.', 'gx-text' );
        self::log_message( 'outbound', $this->from_number, $to, $body, isset( $data['sid'] ) ? $data['sid'] : '', 'failed', $type, $sender_name );
        return new WP_Error( 'gx_text_twilio', $error_msg, $data );
    }

    /**
     * Send a broadcast to multiple numbers.
     *
     * @param array  $numbers Array of E.164 phone numbers.
     * @param string $body    Message body.
     * @return array Results keyed by phone number.
     */
    public function send_broadcast( $numbers, $body ) {
        $results = array();
        foreach ( $numbers as $number ) {
            $result = $this->send_sms( $number, $body, 'broadcast' );
            if ( is_wp_error( $result ) ) {
                $results[ $number ] = array( 'status' => 'failed', 'error' => $result->get_error_message() );
            } else {
                $results[ $number ] = array( 'status' => 'sent', 'sid' => isset( $result['sid'] ) ? $result['sid'] : '' );
            }
            // Small delay to respect rate limits
            usleep( 100000 ); // 100ms
        }
        return $results;
    }

    /**
     * Test the Twilio connection.
     */
    public function test_connection() {
        if ( ! $this->is_configured() ) {
            return new WP_Error( 'gx_text_twilio', __( 'Twilio credentials are not configured.', 'gx-text' ) );
        }

        $url = sprintf(
            'https://api.twilio.com/2010-04-01/Accounts/%s.json',
            $this->account_sid
        );

        $response = wp_remote_get( $url, array(
            'headers' => array(
                'Authorization' => 'Basic ' . base64_encode( $this->account_sid . ':' . $this->auth_token ),
            ),
            'timeout' => 15,
        ) );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $code = wp_remote_retrieve_response_code( $response );
        if ( 200 === $code ) {
            return true;
        }

        $data = json_decode( wp_remote_retrieve_body( $response ), true );
        return new WP_Error( 'gx_text_twilio', isset( $data['message'] ) ? $data['message'] : __( 'Connection failed.', 'gx-text' ) );
    }

    /**
     * Log a message to the database.
     */
    public static function log_message( $direction, $from, $to, $body, $sid = '', $status = 'sent', $type = 'conversation', $sender_name = '' ) {
        global $wpdb;
        $table = $wpdb->prefix . 'gx_text_messages';
        $wpdb->insert( $table, array(
            'direction'    => $direction,
            'phone_from'   => $from,
            'phone_to'     => $to,
            'message_body' => $body,
            'twilio_sid'   => $sid,
            'status'       => $status,
            'message_type' => $type,
            'sender_name'  => $sender_name,
        ), array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' ) );
    }
}
