<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class GX_Text_REST_API {

    public function __construct() {
        add_action( 'rest_api_init', array( $this, 'register_routes' ) );
    }

    public function register_routes() {
        register_rest_route(
            'gx-text/v1',
            '/send-message',
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( $this, 'handle_send_message' ),
                'permission_callback' => '__return_true',
            )
        );

        register_rest_route(
            'gx-text/v1',
            '/subscribe',
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( $this, 'handle_subscribe' ),
                'permission_callback' => '__return_true',
            )
        );

        register_rest_route(
            'gx-text/v1',
            '/webhook',
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( $this, 'handle_webhook' ),
                'permission_callback' => '__return_true',
            )
        );

        register_rest_route(
            'gx-text/v1',
            '/unsubscribe',
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( $this, 'handle_unsubscribe' ),
                'permission_callback' => array( $this, 'can_manage_plugin' ),
            )
        );
    }

    public function can_manage_plugin() {
        return current_user_can( 'manage_options' );
    }

    public function handle_send_message( $request ) {
        $guard = $this->enforce_public_request_guards( $request, 'message' );
        if ( $guard ) {
            return $guard;
        }

        $name    = GX_Text_Options::sanitize_text( $request->get_param( 'name' ) );
        $phone   = GX_Text_Options::normalize_phone( $request->get_param( 'phone' ) );
        $message = GX_Text_Options::sanitize_textarea( $request->get_param( 'message' ) );

        if ( '' === $name || '' === $message ) {
            return $this->error_response( __( 'Please complete all required fields.', 'gx-text' ), 400 );
        }

        if ( ! $phone ) {
            return $this->error_response( __( 'Please enter a valid phone number.', 'gx-text' ), 400 );
        }

        if ( strlen( $name ) > 100 || strlen( $message ) > 1000 ) {
            return $this->error_response( __( 'Your message is too long.', 'gx-text' ), 400 );
        }

        $options        = GX_Text_Options::all();
        $business_phone = $options['business_phone'];
        $notification   = sprintf(
            "New message from %s (%s):\n\n%s\n\n- via GX Text Widget",
            $name,
            $phone,
            $message
        );
        $twilio         = new GX_Text_Twilio();

        if ( ! $twilio->is_configured() ) {
            GX_Text_Twilio::log_message( 'inbound', $phone, $business_phone, $message, '', 'pending', 'conversation', $name );
            return $this->success_response( $options['success_message'] );
        }

        if ( ! empty( $business_phone ) ) {
            $result = $twilio->send_sms( $business_phone, $notification, 'conversation', $name );

            if ( is_wp_error( $result ) ) {
                GX_Text_Twilio::log_message( 'inbound', $phone, $business_phone, $message, '', 'failed', 'conversation', $name );
                return $this->success_response( $options['success_message'] );
            }
        }

        GX_Text_Twilio::log_message( 'inbound', $phone, $business_phone, $message, '', 'sent', 'conversation', $name );

        $auto_reply = sprintf(
            'Hi %1$s! Thanks for reaching out. We received your message and will text you back shortly. - %2$s',
            $name,
            get_bloginfo( 'name' )
        );
        $twilio->send_sms( $phone, $auto_reply, 'auto_reply' );

        return $this->success_response( $options['success_message'] );
    }

    public function handle_subscribe( $request ) {
        $guard = $this->enforce_public_request_guards( $request, 'subscribe' );
        if ( $guard ) {
            return $guard;
        }

        $name    = GX_Text_Options::sanitize_text( $request->get_param( 'name' ) );
        $phone   = GX_Text_Options::normalize_phone( $request->get_param( 'phone' ) );
        $email   = sanitize_email( $request->get_param( 'email' ) );
        $consent = rest_sanitize_boolean( $request->get_param( 'consent' ) );

        if ( '' === $name || ! $phone ) {
            return $this->error_response( __( 'Please enter a valid name and phone number.', 'gx-text' ), 400 );
        }

        if ( ! $consent ) {
            return $this->error_response( __( 'Consent is required to subscribe.', 'gx-text' ), 400 );
        }

        if ( $request->get_param( 'email' ) && ! $email ) {
            return $this->error_response( __( 'Please enter a valid email address.', 'gx-text' ), 400 );
        }

        $result = GX_Text_Subscribers::add( $phone, $name, $email, '', $this->get_client_ip() );
        if ( is_wp_error( $result ) ) {
            return $this->error_response( __( 'Unable to save your subscription right now.', 'gx-text' ), 500 );
        }

        $options = GX_Text_Options::all();
        $twilio  = new GX_Text_Twilio();

        if ( $twilio->is_configured() && in_array( $result['status'], array( 'subscribed', 'resubscribed' ), true ) ) {
            $welcome = sprintf(
                "Welcome, %s! You're now subscribed to text updates from %s. Reply STOP at any time to unsubscribe. Msg & data rates may apply.",
                $name,
                get_bloginfo( 'name' )
            );
            $twilio->send_sms( $phone, $welcome, 'auto_reply' );
        }

        if ( 'already_subscribed' === $result['status'] ) {
            return $this->success_response( __( 'You\'re already subscribed!', 'gx-text' ) );
        }

        return $this->success_response( $options['subscribe_success'] );
    }

    public function handle_webhook( $request ) {
        if ( ! $this->validate_twilio_request( $request ) ) {
            return $this->error_response( __( 'Invalid webhook signature.', 'gx-text' ), 403 );
        }

        $from = GX_Text_Options::normalize_phone( $request->get_param( 'From' ) );
        $to   = GX_Text_Options::normalize_phone( $request->get_param( 'To' ) );
        $body = GX_Text_Options::sanitize_textarea( $request->get_param( 'Body' ) );

        if ( '' === $from || '' === trim( $body ) ) {
            $this->send_xml_response( '<?xml version="1.0" encoding="UTF-8"?><Response></Response>' );
        }

        $body_lower = strtolower( trim( $body ) );

        if ( in_array( $body_lower, array( 'stop', 'unsubscribe', 'cancel', 'quit', 'end' ), true ) ) {
            GX_Text_Subscribers::unsubscribe( $from );
            GX_Text_Twilio::log_message( 'inbound', $from, $to, $body, '', 'received', 'conversation' );

            $this->send_xml_response( '<?xml version="1.0" encoding="UTF-8"?><Response><Message>You have been unsubscribed. Reply START to re-subscribe.</Message></Response>' );
        }

        if ( in_array( $body_lower, array( 'start', 'subscribe', 'yes' ), true ) ) {
            GX_Text_Subscribers::add( $from );
            GX_Text_Twilio::log_message( 'inbound', $from, $to, $body, '', 'received', 'conversation' );

            $this->send_xml_response( '<?xml version="1.0" encoding="UTF-8"?><Response><Message>Welcome back! You\'re re-subscribed to text updates.</Message></Response>' );
        }

        GX_Text_Twilio::log_message( 'inbound', $from, $to, $body, '', 'received', 'conversation' );

        $business_phone = GX_Text_Options::get( 'business_phone', '' );
        if ( ! empty( $business_phone ) ) {
            $twilio = new GX_Text_Twilio();
            if ( $twilio->is_configured() ) {
                $twilio->send_sms( $business_phone, "Inbound text from {$from}:\n\n{$body}" );
            }
        }

        $this->send_xml_response( '<?xml version="1.0" encoding="UTF-8"?><Response></Response>' );
    }

    public function handle_unsubscribe( $request ) {
        $phone = GX_Text_Options::normalize_phone( $request->get_param( 'phone' ) );

        if ( ! $phone ) {
            return $this->error_response( __( 'Invalid phone number.', 'gx-text' ), 400 );
        }

        GX_Text_Subscribers::unsubscribe( $phone );

        return $this->success_response( __( 'You have been unsubscribed.', 'gx-text' ) );
    }

    private function enforce_public_request_guards( $request, $action ) {
        if ( ! $this->is_allowed_origin() ) {
            return $this->error_response( __( 'Request origin is not allowed.', 'gx-text' ), 403 );
        }

        if ( ! empty( trim( (string) $request->get_param( 'website' ) ) ) ) {
            $message = 'subscribe' === $action ? GX_Text_Options::get( 'subscribe_success', __( "You're subscribed!", 'gx-text' ) ) : GX_Text_Options::get( 'success_message', __( 'Thanks! We\'ll text you back shortly.', 'gx-text' ) );
            return $this->success_response( $message );
        }

        $limits = array(
            'message'   => array( 'limit' => 5, 'window' => 300 ),
            'subscribe' => array( 'limit' => 3, 'window' => 600 ),
        );
        $config = isset( $limits[ $action ] ) ? $limits[ $action ] : array( 'limit' => 5, 'window' => 300 );

        if ( ! $this->check_rate_limit( 'gx_text_rate_' . $action . '_' . md5( $this->get_client_ip() ), $config['limit'], $config['window'] ) ) {
            return $this->error_response( __( 'Too many requests. Please try again later.', 'gx-text' ), 429 );
        }

        $phone = GX_Text_Options::normalize_phone( $request->get_param( 'phone' ) );
        if ( $phone && ! $this->check_rate_limit( 'gx_text_rate_phone_' . $action . '_' . md5( $phone ), $config['limit'], $config['window'] ) ) {
            return $this->error_response( __( 'Too many requests. Please try again later.', 'gx-text' ), 429 );
        }

        return null;
    }

    private function check_rate_limit( $key, $limit, $window ) {
        $count = (int) get_transient( $key );
        if ( $count >= $limit ) {
            return false;
        }

        set_transient( $key, $count + 1, $window );
        return true;
    }

    private function validate_twilio_request( $request ) {
        if ( '1' !== GX_Text_Options::get( 'twilio_validate_webhook', '1' ) ) {
            return true;
        }

        $signature = isset( $_SERVER['HTTP_X_TWILIO_SIGNATURE'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_TWILIO_SIGNATURE'] ) ) : '';
        if ( '' === $signature ) {
            return false;
        }

        $twilio     = new GX_Text_Twilio();
        $auth_token = $twilio->get_auth_token();
        if ( '' === $auth_token ) {
            return false;
        }

        $params      = $request->get_body_params();
        $candidate_urls = array_filter(
            array_unique(
                array(
                    rest_url( 'gx-text/v1/webhook' ),
                    untrailingslashit( rest_url( 'gx-text/v1/webhook' ) ),
                    $this->current_request_url(),
                    untrailingslashit( $this->current_request_url() ),
                )
            )
        );

        foreach ( $candidate_urls as $url ) {
            if ( hash_equals( $signature, $this->build_twilio_signature( $url, $params, $auth_token ) ) ) {
                return true;
            }
        }

        return false;
    }

    private function build_twilio_signature( $url, $params, $auth_token ) {
        ksort( $params );
        $payload = $url;

        foreach ( $params as $key => $value ) {
            if ( is_array( $value ) ) {
                sort( $value, SORT_STRING );
                foreach ( $value as $item ) {
                    $payload .= $key . $item;
                }
            } else {
                $payload .= $key . $value;
            }
        }

        return base64_encode( hash_hmac( 'sha1', $payload, $auth_token, true ) );
    }

    private function current_request_url() {
        $host = isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';
        $uri  = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '';

        if ( '' === $host || '' === $uri ) {
            return '';
        }

        return ( is_ssl() ? 'https://' : 'http://' ) . $host . $uri;
    }

    private function is_allowed_origin() {
        $expected_host = wp_parse_url( home_url(), PHP_URL_HOST );
        foreach ( array( 'HTTP_ORIGIN', 'HTTP_REFERER' ) as $header ) {
            if ( empty( $_SERVER[ $header ] ) ) {
                continue;
            }

            $host = wp_parse_url( esc_url_raw( wp_unslash( $_SERVER[ $header ] ) ), PHP_URL_HOST );
            if ( $host && strtolower( $host ) !== strtolower( (string) $expected_host ) ) {
                return false;
            }
        }

        return true;
    }

    private function get_client_ip() {
        foreach ( array( 'HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR' ) as $header ) {
            if ( empty( $_SERVER[ $header ] ) ) {
                continue;
            }

            $ips = explode( ',', wp_unslash( $_SERVER[ $header ] ) );
            foreach ( $ips as $ip ) {
                $ip = trim( $ip );
                if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
                    return $ip;
                }
            }
        }

        return '127.0.0.1';
    }

    private function success_response( $message ) {
        return new WP_REST_Response(
            array(
                'success' => true,
                'message' => $message,
            ),
            200
        );
    }

    private function error_response( $message, $status ) {
        return new WP_REST_Response(
            array(
                'success' => false,
                'message' => $message,
            ),
            $status
        );
    }

    private function send_xml_response( $body ) {
        status_header( 200 );
        header( 'Content-Type: text/xml; charset=' . get_option( 'blog_charset', 'UTF-8' ) );
        echo $body;
        exit;
    }
}
