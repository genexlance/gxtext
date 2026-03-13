<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Handles encryption/decryption of sensitive data (Twilio credentials).
 * Uses WordPress salts to derive encryption and MAC keys, storing
 * new values in an authenticated versioned format while remaining
 * backward-compatible with legacy ciphertext.
 */
class GX_Text_Encryption {

    private static $cipher = 'aes-256-cbc';
    private static $version_prefix = 'v2:';

    /**
     * Derive a 32-byte key from WP salts.
     */
    private static function get_key( $context = 'enc' ) {
        $raw = '';
        if ( defined( 'AUTH_KEY' ) ) {
            $raw .= AUTH_KEY;
        }
        if ( defined( 'SECURE_AUTH_KEY' ) ) {
            $raw .= SECURE_AUTH_KEY;
        }
        // Fallback if salts are not configured
        if ( empty( $raw ) ) {
            $raw = 'gx-text-default-key-change-your-salts';
        }
        return hash( 'sha256', $context . '|' . $raw, true );
    }

    public static function maybe_upgrade_options() {
        $options = get_option( 'gx_text_options', array() );
        $dirty   = false;

        foreach ( array( 'twilio_account_sid', 'twilio_auth_token' ) as $field ) {
            if ( empty( $options[ $field ] ) || self::is_current_format( $options[ $field ] ) ) {
                continue;
            }

            $plaintext = self::decrypt( $options[ $field ] );
            if ( '' === $plaintext || ! self::is_valid_twilio_plaintext( $field, $plaintext ) ) {
                continue;
            }

            $options[ $field ] = self::encrypt( $plaintext );
            $dirty             = true;
        }

        if ( $dirty ) {
            update_option( 'gx_text_options', $options );
            if ( class_exists( 'GX_Text_Options' ) ) {
                GX_Text_Options::prime( $options );
            }
        }
    }

    /**
     * Encrypt a plaintext value.
     */
    public static function encrypt( $plaintext ) {
        if ( empty( $plaintext ) ) {
            return '';
        }
        $enc_key = self::get_key( 'enc' );
        $mac_key = self::get_key( 'mac' );
        $iv_len = openssl_cipher_iv_length( self::$cipher );
        $iv     = function_exists( 'random_bytes' ) ? random_bytes( $iv_len ) : openssl_random_pseudo_bytes( $iv_len );
        $cipher = openssl_encrypt( $plaintext, self::$cipher, $enc_key, OPENSSL_RAW_DATA, $iv );
        if ( false === $cipher ) {
            return '';
        }
        $mac = hash_hmac( 'sha256', $iv . $cipher, $mac_key, true );

        return self::$version_prefix . base64_encode( $iv . $mac . $cipher );
    }

    /**
     * Decrypt an encrypted value.
     */
    public static function decrypt( $encrypted ) {
        if ( empty( $encrypted ) ) {
            return '';
        }
        if ( self::is_current_format( $encrypted ) ) {
            return self::decrypt_current( substr( $encrypted, strlen( self::$version_prefix ) ) );
        }

        return self::decrypt_legacy( $encrypted );
    }

    private static function is_current_format( $encrypted ) {
        return 0 === strpos( (string) $encrypted, self::$version_prefix );
    }

    private static function decrypt_current( $payload ) {
        $data = base64_decode( $payload, true );
        if ( false === $data ) {
            return '';
        }

        $iv_len = openssl_cipher_iv_length( self::$cipher );
        if ( strlen( $data ) <= ( $iv_len + 32 ) ) {
            return '';
        }

        $iv      = substr( $data, 0, $iv_len );
        $mac     = substr( $data, $iv_len, 32 );
        $cipher  = substr( $data, $iv_len + 32 );
        $calc_mac = hash_hmac( 'sha256', $iv . $cipher, self::get_key( 'mac' ), true );

        if ( ! hash_equals( $mac, $calc_mac ) ) {
            return '';
        }

        $result = openssl_decrypt( $cipher, self::$cipher, self::get_key( 'enc' ), OPENSSL_RAW_DATA, $iv );
        return false === $result ? '' : $result;
    }

    private static function decrypt_legacy( $encrypted ) {
        $data = base64_decode( $encrypted, true );
        if ( false === $data ) {
            return '';
        }

        $iv_len = openssl_cipher_iv_length( self::$cipher );
        if ( strlen( $data ) <= $iv_len ) {
            return '';
        }

        $iv     = substr( $data, 0, $iv_len );
        $cipher = substr( $data, $iv_len );
        $result = openssl_decrypt( $cipher, self::$cipher, self::get_key( 'enc' ), OPENSSL_RAW_DATA, $iv );

        return false === $result ? '' : $result;
    }

    private static function is_valid_twilio_plaintext( $field, $plaintext ) {
        $plaintext = trim( (string) $plaintext );

        if ( '' === $plaintext ) {
            return false;
        }

        if ( class_exists( 'GX_Text_Twilio' ) ) {
            if ( 'twilio_account_sid' === $field ) {
                return GX_Text_Twilio::is_valid_account_sid( $plaintext );
            }

            if ( 'twilio_auth_token' === $field ) {
                return GX_Text_Twilio::is_valid_auth_token( $plaintext );
            }
        }

        if ( 'twilio_account_sid' === $field ) {
            return 1 === preg_match( '/^AC[a-f0-9]{32}$/i', $plaintext );
        }

        if ( 'twilio_auth_token' === $field ) {
            return 1 === preg_match( '/^[A-Za-z0-9]{32}$/', $plaintext );
        }

        return false;
    }
}
