<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class GX_Text_Shortcodes {

    public function __construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'maybe_enqueue_assets' ), 20 );
        add_shortcode( 'gx_text_button', array( $this, 'render_button' ) );
        add_shortcode( 'gx_text_form', array( $this, 'render_form' ) );
        add_shortcode( 'gx_text_subscribe', array( $this, 'render_subscribe' ) );
    }

    public function maybe_enqueue_assets() {
        if ( is_admin() || ! is_singular() ) {
            return;
        }

        $post = get_post();
        if ( ! $post || empty( $post->post_content ) ) {
            return;
        }

        $content = (string) $post->post_content;
        $needs_assets = has_shortcode( $content, 'gx_text_button' )
            || has_shortcode( $content, 'gx_text_form' )
            || has_shortcode( $content, 'gx_text_subscribe' )
            || has_block( 'gx-text/button', $post )
            || has_block( 'gx-text/form', $post )
            || has_block( 'gx-text/subscribe', $post );

        if ( $needs_assets ) {
            GX_Text_Assets::enqueue_frontend_assets();
        }
    }

    public function render_button( $atts ) {
        GX_Text_Assets::enqueue_frontend_assets();

        $options = GX_Text_Options::all();
        $atts    = shortcode_atts(
            array(
                'color'      => $options['button_color'],
                'text_color' => $options['button_text_color'],
                'label'      => $options['button_label'],
                'size'       => 'medium',
                'icon'       => $options['button_graphic_url'] ? 'graphic' : $options['button_icon'],
                'class'      => '',
                'graphic'    => $options['button_graphic_url'],
            ),
            $atts,
            'gx_text_button'
        );

        $sizes = array(
            'small'  => 'padding:8px 18px;font-size:13px;',
            'medium' => 'padding:12px 24px;font-size:15px;',
            'large'  => 'padding:16px 32px;font-size:17px;',
        );

        $size_style   = isset( $sizes[ $atts['size'] ] ) ? $sizes[ $atts['size'] ] : $sizes['medium'];
        $style        = sprintf( 'background:%s;color:%s;%s', esc_attr( $atts['color'] ), esc_attr( $atts['text_color'] ), $size_style );
        $button_class = 'gx-text-inline-btn';
        $graphic_url  = esc_url( $atts['graphic'] );

        if ( ! empty( $atts['class'] ) ) {
            $button_class .= ' ' . esc_attr( $atts['class'] );
        }

        ob_start();
        ?>
        <button type="button" class="<?php echo esc_attr( $button_class ); ?>" style="<?php echo esc_attr( $style ); ?>" onclick="document.getElementById('gx-text-toggle') && document.getElementById('gx-text-toggle').click();">
            <?php if ( $graphic_url ) : ?>
                <span class="gx-text-inline-brand" aria-hidden="true"><img src="<?php echo $graphic_url; ?>" alt="" loading="lazy" decoding="async" /></span>
            <?php else : ?>
                <?php $icon_svg = $this->get_icon( $atts['icon'], $atts['text_color'] ); ?>
                <?php if ( $icon_svg ) : ?>
                    <span class="gx-text-inline-icon" aria-hidden="true"><?php echo $icon_svg; ?></span>
                <?php endif; ?>
            <?php endif; ?>
            <?php if ( '' !== trim( $atts['label'] ) ) : ?>
                <span><?php echo esc_html( $atts['label'] ); ?></span>
            <?php endif; ?>
        </button>
        <?php
        return ob_get_clean();
    }

    public function render_form( $atts ) {
        GX_Text_Assets::enqueue_frontend_assets();

        $options = GX_Text_Options::all();
        $atts    = shortcode_atts(
            array(
                'title'    => $options['widget_title'],
                'subtitle' => $options['widget_subtitle'],
                'class'    => '',
            ),
            $atts,
            'gx_text_form'
        );
        $widget_brand = $this->get_brand_badge_html( $options['button_graphic_url'], 'gx-text-widget-brand' );

        ob_start();
        ?>
        <div class="gx-text-inline <?php echo esc_attr( $atts['class'] ); ?>">
            <div class="gx-text-widget is-visible">
                <div class="gx-text-widget-header">
                    <div class="gx-text-widget-header-content">
                        <div class="gx-text-widget-heading">
                            <?php echo $widget_brand; ?>
                            <div class="gx-text-widget-copy">
                                <h3><?php echo esc_html( $atts['title'] ); ?></h3>
                                <p><?php echo esc_html( $atts['subtitle'] ); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="gx-text-tab-content active">
                    <form class="gx-text-form gx-text-inline-message-form" novalidate>
                        <div class="gx-text-field">
                            <input type="text" name="gx_name" placeholder="<?php echo esc_attr( $options['placeholder_name'] ); ?>" required autocomplete="name" maxlength="100" />
                            <span class="gx-text-field-icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" opacity="0.4"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                            </span>
                        </div>
                        <div class="gx-text-field">
                            <input type="tel" name="gx_phone" placeholder="<?php echo esc_attr( $options['placeholder_phone'] ); ?>" required autocomplete="tel" inputmode="tel" />
                            <span class="gx-text-field-icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" opacity="0.4"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>
                            </span>
                        </div>
                        <div class="gx-text-field">
                            <textarea name="gx_message" placeholder="<?php echo esc_attr( $options['placeholder_message'] ); ?>" required rows="3" maxlength="1000"></textarea>
                        </div>
                        <div class="gx-text-field gx-text-honeypot" aria-hidden="true">
                            <input type="text" name="gx_website" tabindex="-1" autocomplete="off" />
                        </div>
                        <button type="submit" class="gx-text-submit">
                            <span class="gx-text-submit-text"><?php esc_html_e( 'Send Message', 'gx-text' ); ?></span>
                            <span class="gx-text-submit-loading">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" class="gx-spinner"><path d="M12 4V2C6.48 2 2 6.48 2 12h2c0-4.42 3.58-8 8-8z"/></svg>
                            </span>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" class="gx-send-icon"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                        </button>
                    </form>
                    <div class="gx-text-success gx-inline-msg-success" style="display:none;">
                        <div class="gx-text-success-icon">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="#25D366"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                        </div>
                        <p><?php echo esc_html( $options['success_message'] ); ?></p>
                    </div>
                </div>
                <div class="gx-text-widget-footer">
                    <span><?php esc_html_e( 'Powered by', 'gx-text' ); ?> <strong>GX Text</strong></span>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function render_subscribe( $atts ) {
        GX_Text_Assets::enqueue_frontend_assets();

        $options = GX_Text_Options::all();
        $atts    = shortcode_atts(
            array(
                'heading'     => $options['subscribe_heading'],
                'description' => $options['subscribe_description'],
                'class'       => '',
            ),
            $atts,
            'gx_text_subscribe'
        );
        $widget_brand = $this->get_brand_badge_html( $options['button_graphic_url'], 'gx-text-widget-brand' );

        ob_start();
        ?>
        <div class="gx-text-inline <?php echo esc_attr( $atts['class'] ); ?>">
            <div class="gx-text-widget is-visible">
                <div class="gx-text-widget-header" style="background:var(--gx-subscribe-btn, #FF6B35);">
                    <div class="gx-text-widget-header-content">
                        <div class="gx-text-widget-heading">
                            <?php echo $widget_brand; ?>
                            <div class="gx-text-widget-copy">
                                <h3><?php echo esc_html( $atts['heading'] ); ?></h3>
                                <p><?php echo esc_html( $atts['description'] ); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="gx-text-tab-content active">
                    <form class="gx-text-form gx-text-inline-subscribe-form" novalidate>
                        <div class="gx-text-field">
                            <input type="text" name="gx_sub_name" placeholder="<?php echo esc_attr( $options['placeholder_name'] ); ?>" required autocomplete="name" maxlength="100" />
                            <span class="gx-text-field-icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" opacity="0.4"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                            </span>
                        </div>
                        <div class="gx-text-field">
                            <input type="tel" name="gx_sub_phone" placeholder="<?php echo esc_attr( $options['placeholder_phone'] ); ?>" required autocomplete="tel" inputmode="tel" />
                            <span class="gx-text-field-icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" opacity="0.4"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>
                            </span>
                        </div>
                        <div class="gx-text-field">
                            <input type="email" name="gx_sub_email" placeholder="<?php esc_attr_e( 'Email (optional)', 'gx-text' ); ?>" autocomplete="email" maxlength="100" />
                            <span class="gx-text-field-icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" opacity="0.4"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                            </span>
                        </div>
                        <div class="gx-text-field gx-text-honeypot" aria-hidden="true">
                            <input type="text" name="gx_sub_website" tabindex="-1" autocomplete="off" />
                        </div>
                        <div class="gx-text-consent">
                            <label>
                                <input type="checkbox" name="gx_consent" required />
                                <span><?php echo esc_html( $options['subscribe_consent'] ); ?></span>
                            </label>
                        </div>
                        <button type="submit" class="gx-text-submit gx-text-subscribe-btn">
                            <span class="gx-text-submit-text"><?php echo esc_html( $options['subscribe_btn_text'] ); ?></span>
                            <span class="gx-text-submit-loading">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" class="gx-spinner"><path d="M12 4V2C6.48 2 2 6.48 2 12h2c0-4.42 3.58-8 8-8z"/></svg>
                            </span>
                        </button>
                    </form>
                    <div class="gx-text-success gx-inline-sub-success" style="display:none;">
                        <div class="gx-text-success-icon">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="#25D366"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                        </div>
                        <p><?php echo esc_html( $options['subscribe_success'] ); ?></p>
                    </div>
                </div>
                <div class="gx-text-widget-footer">
                    <span><?php esc_html_e( 'Powered by', 'gx-text' ); ?> <strong>GX Text</strong></span>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    private function get_icon( $icon, $color ) {
        switch ( $icon ) {
            case 'sms':
                return '<svg width="20" height="20" viewBox="0 0 24 24" fill="' . esc_attr( $color ) . '"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>';
            case 'message':
                return '<svg width="20" height="20" viewBox="0 0 24 24" fill="' . esc_attr( $color ) . '"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>';
            case 'none':
            case 'graphic':
                return '';
            default:
                return '<svg width="20" height="20" viewBox="0 0 24 24" fill="' . esc_attr( $color ) . '"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>';
        }
    }

    private function get_brand_badge_html( $graphic_url, $class_name ) {
        if ( empty( $graphic_url ) ) {
            return '';
        }

        return sprintf(
            '<span class="%1$s" aria-hidden="true"><img src="%2$s" alt="" loading="lazy" decoding="async" /></span>',
            esc_attr( $class_name ),
            esc_url( $graphic_url )
        );
    }
}
