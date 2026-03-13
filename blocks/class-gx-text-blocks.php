<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class GX_Text_Blocks {

    public function __construct() {
        add_action( 'init', array( $this, 'register_blocks' ) );
        add_action( 'enqueue_block_editor_assets', array( $this, 'editor_assets' ) );
    }

    public function register_blocks() {
        if ( ! function_exists( 'register_block_type' ) ) {
            return;
        }

        // GX Text Button Block
        register_block_type( 'gx-text/button', array(
            'editor_script'   => 'gx-text-blocks-editor',
            'render_callback' => array( $this, 'render_button_block' ),
            'attributes'      => array(
                'label'     => array( 'type' => 'string', 'default' => '' ),
                'color'     => array( 'type' => 'string', 'default' => '' ),
                'textColor' => array( 'type' => 'string', 'default' => '' ),
                'size'      => array( 'type' => 'string', 'default' => '' ),
                'icon'      => array( 'type' => 'string', 'default' => '' ),
            ),
        ) );

        // GX Text Form Block
        register_block_type( 'gx-text/form', array(
            'editor_script'   => 'gx-text-blocks-editor',
            'render_callback' => array( $this, 'render_form_block' ),
            'attributes'      => array(
                'title'    => array( 'type' => 'string', 'default' => '' ),
                'subtitle' => array( 'type' => 'string', 'default' => '' ),
            ),
        ) );

        // GX Text Subscribe Block
        register_block_type( 'gx-text/subscribe', array(
            'editor_script'   => 'gx-text-blocks-editor',
            'render_callback' => array( $this, 'render_subscribe_block' ),
            'attributes'      => array(
                'heading'     => array( 'type' => 'string', 'default' => '' ),
                'description' => array( 'type' => 'string', 'default' => '' ),
            ),
        ) );
    }

    public function editor_assets() {
        $options = class_exists( 'GX_Text_Options' ) ? GX_Text_Options::all() : GX_Text::defaults();

        wp_enqueue_script(
            'gx-text-blocks-editor',
            GX_TEXT_PLUGIN_URL . 'blocks/editor.js',
            array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n' ),
            GX_TEXT_VERSION,
            true
        );

        wp_localize_script(
            'gx-text-blocks-editor',
            'gxTextEditor',
            array(
                'options' => array(
                    'buttonColor'       => $options['button_color'] ?? '#25D366',
                    'buttonTextColor'   => $options['button_text_color'] ?? '#ffffff',
                    'buttonLabel'       => $options['button_label'] ?? 'Text Us!',
                    'buttonIcon'        => $options['button_icon'] ?? 'chat',
                    'buttonSize'        => $options['button_size'] ?? '60',
                    'buttonRadius'      => $options['button_border_radius'] ?? '50',
                    'buttonGraphicMode' => $options['button_graphic_mode'] ?? 'badge',
                    'buttonGraphicUrl'  => $options['button_graphic_url'] ?? '',
                    'buttonGraphicSize' => $options['button_graphic_size'] ?? '28',
                    'brandLogoUrl'      => $options['brand_logo_url'] ?? '',
                    'widgetTitle'       => $options['widget_title'] ?? 'Text Us Now',
                    'widgetSubtitle'    => $options['widget_subtitle'] ?? 'Send us a message',
                    'widgetBgColor'     => $options['widget_bg_color'] ?? '#ffffff',
                    'widgetHeaderColor' => $options['widget_header_color'] ?? '#25D366',
                    'widgetHeaderText'  => $options['widget_header_text'] ?? '#ffffff',
                    'widgetFontFamily'  => $options['widget_font_family'] ?? 'inherit',
                    'placeholderName'   => $options['placeholder_name'] ?? 'Your Name',
                    'placeholderPhone'  => $options['placeholder_phone'] ?? 'Your Phone Number',
                    'placeholderMessage'=> $options['placeholder_message'] ?? 'Type your message...',
                    'subscribeHeading'  => $options['subscribe_heading'] ?? 'Get Deals via Text',
                    'subscribeDescription' => $options['subscribe_description'] ?? 'Subscribe for exclusive deals!',
                    'subscribeButtonText'  => $options['subscribe_btn_text'] ?? 'Subscribe',
                    'subscribeButtonColor' => $options['subscribe_btn_color'] ?? '#FF6B35',
                ),
            )
        );

        wp_enqueue_style(
            'gx-text-blocks-editor',
            GX_TEXT_PLUGIN_URL . 'blocks/editor.css',
            array(),
            GX_TEXT_VERSION
        );
    }

    public function render_button_block( $attributes ) {
        $parts = array();

        if ( ! empty( $attributes['label'] ) ) {
            $parts[] = 'label="' . esc_attr( $attributes['label'] ) . '"';
        }
        if ( ! empty( $attributes['color'] ) ) {
            $parts[] = 'color="' . esc_attr( $attributes['color'] ) . '"';
        }
        if ( ! empty( $attributes['textColor'] ) ) {
            $parts[] = 'text_color="' . esc_attr( $attributes['textColor'] ) . '"';
        }
        if ( ! empty( $attributes['size'] ) ) {
            $parts[] = 'size="' . esc_attr( $attributes['size'] ) . '"';
        }
        if ( ! empty( $attributes['icon'] ) ) {
            $parts[] = 'icon="' . esc_attr( $attributes['icon'] ) . '"';
        }

        return do_shortcode( '[gx_text_button ' . implode( ' ', $parts ) . ']' );
    }

    public function render_form_block( $attributes ) {
        $parts = array();
        if ( ! empty( $attributes['title'] ) ) {
            $parts[] = 'title="' . esc_attr( $attributes['title'] ) . '"';
        }
        if ( ! empty( $attributes['subtitle'] ) ) {
            $parts[] = 'subtitle="' . esc_attr( $attributes['subtitle'] ) . '"';
        }
        return do_shortcode( '[gx_text_form ' . implode( ' ', $parts ) . ']' );
    }

    public function render_subscribe_block( $attributes ) {
        $parts = array();
        if ( ! empty( $attributes['heading'] ) ) {
            $parts[] = 'heading="' . esc_attr( $attributes['heading'] ) . '"';
        }
        if ( ! empty( $attributes['description'] ) ) {
            $parts[] = 'description="' . esc_attr( $attributes['description'] ) . '"';
        }
        return do_shortcode( '[gx_text_subscribe ' . implode( ' ', $parts ) . ']' );
    }
}
