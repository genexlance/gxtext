<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class GX_Text_Options {

    private static $options = null;

    public static function all() {
        if ( null === self::$options ) {
            self::$options = wp_parse_args( get_option( 'gx_text_options', array() ), GX_Text::defaults() );
        }

        return self::$options;
    }

    public static function get( $key, $default = '' ) {
        $options = self::all();

        if ( array_key_exists( $key, $options ) ) {
            return $options[ $key ];
        }

        return $default;
    }

    public static function prime( $options ) {
        self::$options = wp_parse_args( $options, GX_Text::defaults() );
    }

    public static function sanitize( $input, $old = null ) {
        $input    = is_array( $input ) ? $input : array();
        $old      = is_array( $old ) ? $old : get_option( 'gx_text_options', array() );
        $defaults = GX_Text::defaults();
        $output   = array();

        foreach ( array( 'twilio_account_sid', 'twilio_auth_token' ) as $field ) {
            $submitted = isset( $input[ $field ] ) ? trim( wp_unslash( (string) $input[ $field ] ) ) : '';

            if ( '' === $submitted || false !== strpos( $submitted, '••••' ) ) {
                $output[ $field ] = isset( $old[ $field ] ) ? $old[ $field ] : '';
                continue;
            }

            $output[ $field ] = GX_Text_Encryption::encrypt( sanitize_text_field( $submitted ) );
        }

        $output['twilio_phone_number'] = self::sanitize_phone_setting( 'twilio_phone_number', $input, $old, $defaults );
        $output['business_phone']      = self::sanitize_phone_setting( 'business_phone', $input, $old, $defaults );

        $output['button_position']      = self::sanitize_choice( self::input_value( $input, 'button_position', $old, $defaults ), array( 'bottom-right', 'bottom-left', 'top-right', 'top-left' ), $defaults['button_position'] );
        $output['button_color']         = self::sanitize_color( self::input_value( $input, 'button_color', $old, $defaults ), $defaults['button_color'] );
        $output['button_text_color']    = self::sanitize_color( self::input_value( $input, 'button_text_color', $old, $defaults ), $defaults['button_text_color'] );
        $output['button_icon']          = self::sanitize_choice( self::input_value( $input, 'button_icon', $old, $defaults ), array( 'chat', 'sms', 'message', 'text' ), $defaults['button_icon'] );
        $output['button_label']         = self::sanitize_text( self::input_value( $input, 'button_label', $old, $defaults ) );
        $output['button_size']          = (string) self::sanitize_int( self::input_value( $input, 'button_size', $old, $defaults ), 40, 100, (int) $defaults['button_size'] );
        $output['button_border_radius'] = (string) self::sanitize_int( self::input_value( $input, 'button_border_radius', $old, $defaults ), 0, 50, (int) $defaults['button_border_radius'] );
        $output['button_graphic_url']   = self::sanitize_image_url( self::input_value( $input, 'button_graphic_url', $old, $defaults ) );
        $output['button_graphic_size']  = (string) self::sanitize_int( self::input_value( $input, 'button_graphic_size', $old, $defaults ), 20, 44, (int) $defaults['button_graphic_size'] );
        $output['animation_type']       = self::sanitize_choice( self::input_value( $input, 'animation_type', $old, $defaults ), array( 'pulse', 'bounce', 'shake', 'glow', 'none' ), $defaults['animation_type'] );
        $output['show_on_mobile']       = ! empty( $input['show_on_mobile'] ) ? '1' : '0';
        $output['show_on_desktop']      = ! empty( $input['show_on_desktop'] ) ? '1' : '0';
        $output['z_index']              = (string) self::sanitize_int( self::input_value( $input, 'z_index', $old, $defaults ), 1, 999999, (int) $defaults['z_index'] );
        $output['offset_x']             = (string) self::sanitize_int( self::input_value( $input, 'offset_x', $old, $defaults ), 0, 200, (int) $defaults['offset_x'] );
        $output['offset_y']             = (string) self::sanitize_int( self::input_value( $input, 'offset_y', $old, $defaults ), 0, 200, (int) $defaults['offset_y'] );

        $output['widget_title']        = self::sanitize_text( self::input_value( $input, 'widget_title', $old, $defaults ) );
        $output['widget_subtitle']     = self::sanitize_textarea( self::input_value( $input, 'widget_subtitle', $old, $defaults ) );
        $output['widget_bg_color']     = self::sanitize_color( self::input_value( $input, 'widget_bg_color', $old, $defaults ), $defaults['widget_bg_color'] );
        $output['widget_header_color'] = self::sanitize_color( self::input_value( $input, 'widget_header_color', $old, $defaults ), $defaults['widget_header_color'] );
        $output['widget_header_text']  = self::sanitize_color( self::input_value( $input, 'widget_header_text', $old, $defaults ), $defaults['widget_header_text'] );
        $output['widget_font_family']  = self::sanitize_choice(
            self::input_value( $input, 'widget_font_family', $old, $defaults ),
            array( 'inherit', "'Inter', sans-serif", "'Poppins', sans-serif", "'Roboto', sans-serif", "'Open Sans', sans-serif", 'system-ui, sans-serif' ),
            $defaults['widget_font_family']
        );
        $output['success_message']     = self::sanitize_textarea( self::input_value( $input, 'success_message', $old, $defaults ) );
        $output['placeholder_name']    = self::sanitize_text( self::input_value( $input, 'placeholder_name', $old, $defaults ) );
        $output['placeholder_phone']   = self::sanitize_text( self::input_value( $input, 'placeholder_phone', $old, $defaults ) );
        $output['placeholder_message'] = self::sanitize_text( self::input_value( $input, 'placeholder_message', $old, $defaults ) );

        $output['enable_subscribe']      = ! empty( $input['enable_subscribe'] ) ? '1' : '0';
        $output['subscribe_heading']     = self::sanitize_text( self::input_value( $input, 'subscribe_heading', $old, $defaults ) );
        $output['subscribe_description'] = self::sanitize_textarea( self::input_value( $input, 'subscribe_description', $old, $defaults ) );
        $output['subscribe_consent']     = self::sanitize_textarea( self::input_value( $input, 'subscribe_consent', $old, $defaults ) );
        $output['subscribe_success']     = self::sanitize_textarea( self::input_value( $input, 'subscribe_success', $old, $defaults ) );
        $output['subscribe_btn_text']    = self::sanitize_text( self::input_value( $input, 'subscribe_btn_text', $old, $defaults ) );
        $output['subscribe_btn_color']   = self::sanitize_color( self::input_value( $input, 'subscribe_btn_color', $old, $defaults ), $defaults['subscribe_btn_color'] );

        $output['display_pages']            = self::sanitize_choice( self::input_value( $input, 'display_pages', $old, $defaults ), array( 'all', 'homepage', 'posts', 'pages' ), $defaults['display_pages'] );
        $output['exclude_pages']            = self::sanitize_page_ids( self::input_value( $input, 'exclude_pages', $old, $defaults ) );
        $output['twilio_validate_webhook']  = ! empty( $input['twilio_validate_webhook'] ) ? '1' : '0';
        $output['custom_css']               = self::sanitize_custom_css( self::input_value( $input, 'custom_css', $old, $defaults ) );

        self::prime( $output );

        return $output;
    }

    public static function normalize_phone( $phone ) {
        $phone = trim( wp_unslash( (string) $phone ) );

        if ( '' === $phone ) {
            return '';
        }

        $phone = preg_replace( '/(?!^\+)\+/', '', $phone );
        $phone = preg_replace( '/[^\d\+]/', '', $phone );

        if ( '' === $phone ) {
            return '';
        }

        if ( '+' !== substr( $phone, 0, 1 ) ) {
            if ( 10 === strlen( $phone ) ) {
                $phone = '+1' . $phone;
            } elseif ( 11 === strlen( $phone ) && '1' === substr( $phone, 0, 1 ) ) {
                $phone = '+' . $phone;
            } else {
                $phone = '+' . ltrim( $phone, '+' );
            }
        }

        return preg_match( '/^\+[1-9]\d{6,14}$/', $phone ) ? $phone : '';
    }

    public static function sanitize_text( $value ) {
        return sanitize_text_field( wp_unslash( (string) $value ) );
    }

    public static function sanitize_textarea( $value ) {
        return sanitize_textarea_field( wp_unslash( (string) $value ) );
    }

    public static function sanitize_color( $value, $fallback ) {
        $color = sanitize_hex_color( wp_unslash( (string) $value ) );
        return $color ? $color : $fallback;
    }

    private static function input_value( $input, $key, $old, $defaults ) {
        if ( array_key_exists( $key, $input ) ) {
            return $input[ $key ];
        }

        if ( array_key_exists( $key, $old ) ) {
            return $old[ $key ];
        }

        return isset( $defaults[ $key ] ) ? $defaults[ $key ] : '';
    }

    private static function sanitize_choice( $value, $allowed, $fallback ) {
        $value = sanitize_text_field( wp_unslash( (string) $value ) );
        return in_array( $value, $allowed, true ) ? $value : $fallback;
    }

    private static function sanitize_int( $value, $min, $max, $fallback ) {
        $value = is_numeric( $value ) ? (int) $value : $fallback;
        if ( $value < $min || $value > $max ) {
            return $fallback;
        }
        return $value;
    }

    private static function sanitize_phone_setting( $field, $input, $old, $defaults ) {
        $raw = self::input_value( $input, $field, $old, $defaults );

        if ( '' === trim( (string) $raw ) ) {
            return '';
        }

        $phone = self::normalize_phone( $raw );
        if ( $phone ) {
            return $phone;
        }

        add_settings_error(
            'gx_text_options',
            'gx_text_invalid_' . $field,
            sprintf(
                /* translators: %s: field label. */
                __( '%s must be a valid phone number in E.164 format.', 'gx-text' ),
                'twilio_phone_number' === $field ? __( 'Twilio Phone Number', 'gx-text' ) : __( 'Business Phone', 'gx-text' )
            )
        );

        return isset( $old[ $field ] ) ? $old[ $field ] : '';
    }

    private static function sanitize_page_ids( $value ) {
        $parts = preg_split( '/\s*,\s*/', (string) $value );
        $ids   = array();

        foreach ( $parts as $part ) {
            $id = absint( $part );
            if ( $id > 0 ) {
                $ids[] = $id;
            }
        }

        $ids = array_values( array_unique( $ids ) );

        return implode( ',', $ids );
    }

    private static function sanitize_custom_css( $value ) {
        $css = wp_unslash( (string) $value );
        $css = wp_strip_all_tags( $css );
        $css = preg_replace( '/@import\s+[^;]+;?/i', '', $css );
        $css = preg_replace( '/expression\s*\(|javascript\s*:/i', '', $css );

        return trim( $css );
    }

    private static function sanitize_image_url( $value ) {
        $url = trim( esc_url_raw( wp_unslash( (string) $value ), array( 'http', 'https' ) ) );

        if ( '' === $url ) {
            return '';
        }

        $path = wp_parse_url( $url, PHP_URL_PATH );
        if ( ! empty( $path ) && ! preg_match( '/\.(png|jpe?g|gif|webp|svg)$/i', $path ) ) {
            return '';
        }

        return $url;
    }
}
