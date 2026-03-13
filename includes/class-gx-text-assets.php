<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class GX_Text_Assets {

    private static $localized = false;
    private static $styles_set = false;
    private static $launcher_used = false;

    public static function mark_launcher_used() {
        self::$launcher_used = true;
    }

    public static function launcher_used() {
        return self::$launcher_used;
    }

    public static function enqueue_frontend_assets() {
        $options = GX_Text_Options::all();
        $font    = $options['widget_font_family'];

        if ( $font && 'inherit' !== $font && 'system-ui, sans-serif' !== $font ) {
            $font_name = str_replace( "'", '', explode( ',', $font )[0] );
            $font_name = preg_replace( '/[^a-zA-Z ]/', '', $font_name );
            $font_name = trim( $font_name );

            if ( $font_name ) {
                wp_enqueue_style(
                    'gx-text-google-font',
                    'https://fonts.googleapis.com/css2?family=' . rawurlencode( $font_name ) . ':wght@400;500;600;700&display=swap',
                    array(),
                    null
                );
            }
        }

        wp_enqueue_style( 'gx-text-frontend', GX_TEXT_PLUGIN_URL . 'assets/css/frontend.css', array(), GX_TEXT_VERSION );
        wp_enqueue_script( 'gx-text-frontend', GX_TEXT_PLUGIN_URL . 'assets/js/frontend.js', array(), GX_TEXT_VERSION, true );

        if ( ! self::$localized ) {
            wp_localize_script(
                'gx-text-frontend',
                'gxTextFront',
                array(
                    'restUrl' => esc_url_raw( rest_url( 'gx-text/v1/' ) ),
                    'nonce'   => wp_create_nonce( 'wp_rest' ),
                    'options' => array(
                        'position'         => $options['button_position'],
                        'enableSubscribe'  => $options['enable_subscribe'],
                        'successMessage'   => $options['success_message'],
                        'subscribeSuccess' => $options['subscribe_success'],
                    ),
                )
            );

            self::$localized = true;
        }

        if ( ! self::$styles_set ) {
            if ( ! empty( $options['custom_css'] ) ) {
                wp_add_inline_style( 'gx-text-frontend', $options['custom_css'] );
            }

            wp_add_inline_style( 'gx-text-frontend', self::generate_dynamic_css( $options ) );
            self::$styles_set = true;
        }
    }

    private static function generate_dynamic_css( $options ) {
        $position = isset( $options['button_position'] ) ? $options['button_position'] : 'bottom-right';
        $pos      = explode( '-', $position );
        $vert     = isset( $pos[0] ) ? $pos[0] : 'bottom';
        $horiz    = isset( $pos[1] ) ? $pos[1] : 'right';

        $css = ":root {\n";
        $css .= "    --gx-btn-color: {$options['button_color']};\n";
        $css .= "    --gx-btn-text: {$options['button_text_color']};\n";
        $css .= "    --gx-btn-size: {$options['button_size']}px;\n";
        $css .= "    --gx-btn-radius: {$options['button_border_radius']}%;\n";
        $css .= "    --gx-btn-graphic-size: {$options['button_graphic_size']}px;\n";
        $css .= "    --gx-offset-x: {$options['offset_x']}px;\n";
        $css .= "    --gx-offset-y: {$options['offset_y']}px;\n";
        $css .= "    --gx-z-index: {$options['z_index']};\n";
        $css .= "    --gx-widget-bg: {$options['widget_bg_color']};\n";
        $css .= "    --gx-header-bg: {$options['widget_header_color']};\n";
        $css .= "    --gx-header-text: {$options['widget_header_text']};\n";
        $css .= "    --gx-font: {$options['widget_font_family']};\n";
        $css .= "    --gx-subscribe-btn: {$options['subscribe_btn_color']};\n";
        $css .= "}\n";

        if ( 'manual' === $position ) {
            $css .= ".gx-text-floating.is-manual { inset: 0; pointer-events: none; }\n";
            $css .= ".gx-text-floating.is-manual .gx-text-widget { top: 50%; left: 50%; right: auto; bottom: auto; max-height: min(80vh, 720px); pointer-events: auto; transform: translate(-50%, calc(-50% + 20px)) scale(0.95); }\n";
            $css .= ".gx-text-floating.is-manual .gx-text-widget.is-visible { transform: translate(-50%, -50%) scale(1); }\n";
        } else {
            $css .= ".gx-text-floating { {$vert}: var(--gx-offset-y); {$horiz}: var(--gx-offset-x); }\n";

            if ( 'bottom' === $vert ) {
                $css .= ".gx-text-widget { bottom: calc(var(--gx-btn-size) + var(--gx-offset-y) + 12px); }\n";
            } else {
                $css .= ".gx-text-widget { top: calc(var(--gx-btn-size) + var(--gx-offset-y) + 12px); }\n";
            }

            if ( 'right' === $horiz ) {
                $css .= ".gx-text-widget { right: var(--gx-offset-x); }\n";
            } else {
                $css .= ".gx-text-widget { left: var(--gx-offset-x); }\n";
            }
        }

        if ( 'manual' !== $position && '1' !== $options['show_on_mobile'] ) {
            $css .= "@media (max-width: 768px) { .gx-text-floating { display: none !important; } }\n";
        }

        if ( 'manual' !== $position && '1' !== $options['show_on_desktop'] ) {
            $css .= "@media (min-width: 769px) { .gx-text-floating { display: none !important; } }\n";
        }

        return $css;
    }
}
