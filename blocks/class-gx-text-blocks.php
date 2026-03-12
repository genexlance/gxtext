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
                'label'     => array( 'type' => 'string', 'default' => 'Text Us!' ),
                'color'     => array( 'type' => 'string', 'default' => '#25D366' ),
                'textColor' => array( 'type' => 'string', 'default' => '#ffffff' ),
                'size'      => array( 'type' => 'string', 'default' => 'medium' ),
                'icon'      => array( 'type' => 'string', 'default' => 'chat' ),
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
        wp_enqueue_script(
            'gx-text-blocks-editor',
            GX_TEXT_PLUGIN_URL . 'blocks/editor.js',
            array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n' ),
            GX_TEXT_VERSION,
            true
        );

        wp_enqueue_style(
            'gx-text-blocks-editor',
            GX_TEXT_PLUGIN_URL . 'blocks/editor.css',
            array(),
            GX_TEXT_VERSION
        );
    }

    public function render_button_block( $attributes ) {
        $shortcode = sprintf(
            '[gx_text_button label="%s" color="%s" text_color="%s" size="%s" icon="%s"]',
            esc_attr( $attributes['label'] ),
            esc_attr( $attributes['color'] ),
            esc_attr( $attributes['textColor'] ),
            esc_attr( $attributes['size'] ),
            esc_attr( $attributes['icon'] )
        );
        return do_shortcode( $shortcode );
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
