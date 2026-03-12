<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class GX_Text_Frontend {

    public function __construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
        add_action( 'wp_footer', array( $this, 'render_floating_widget' ) );
    }

    public function enqueue_assets() {
        if ( ! $this->should_display() ) {
            return;
        }

        GX_Text_Assets::enqueue_frontend_assets();
    }

    public function render_floating_widget() {
        if ( ! $this->should_display() ) {
            return;
        }

        $options           = GX_Text_Options::all();
        $animation_class   = 'none' !== $options['animation_type'] ? 'gx-anim-' . esc_attr( $options['animation_type'] ) : '';
        $button_graphic    = $options['button_graphic_url'];
        $has_graphic       = ! empty( $button_graphic );
        $icon              = $has_graphic ? '' : $this->get_icon_svg( $options['button_icon'], $options['button_text_color'] );
        $widget_brand      = $this->get_brand_badge_html( $button_graphic, 'gx-text-widget-brand' );
        $tab_brand         = $this->get_brand_badge_html( $button_graphic, 'gx-text-tab-brand' );
        $enable_subscribe  = '1' === $options['enable_subscribe'];
        $show_text_only    = 'text' === $options['button_icon'] && ! $has_graphic;
        $button_classes    = array( 'gx-text-btn' );
        $button_label      = trim( $options['button_label'] );

        if ( $animation_class ) {
            $button_classes[] = $animation_class;
        }

        if ( $has_graphic ) {
            $button_classes[] = 'has-graphic';
        }

        if ( $show_text_only ) {
            $button_classes[] = 'is-text-only';
        }

        ?>
        <!-- GX Text by Genex Marketing Agency Ltd -->
        <div class="gx-text-floating" id="gx-text-floating" role="complementary" aria-label="<?php esc_attr_e( 'Text Us Widget', 'gx-text' ); ?>">
            <button class="<?php echo esc_attr( implode( ' ', $button_classes ) ); ?>" id="gx-text-toggle" aria-expanded="false" aria-controls="gx-text-widget" title="<?php echo esc_attr( $button_label ? $button_label : __( 'Open text widget', 'gx-text' ) ); ?>">
                <?php if ( $has_graphic ) : ?>
                    <span class="gx-text-btn-brand" aria-hidden="true">
                        <img src="<?php echo esc_url( $button_graphic ); ?>" alt="" loading="lazy" decoding="async" />
                    </span>
                <?php elseif ( $icon ) : ?>
                    <span class="gx-text-btn-icon" aria-hidden="true"><?php echo $icon; ?></span>
                <?php endif; ?>

                <?php if ( '' !== $button_label ) : ?>
                    <span class="gx-text-btn-label<?php echo $show_text_only ? ' gx-text-only' : ''; ?>"><?php echo esc_html( $button_label ); ?></span>
                <?php endif; ?>

                <span class="gx-text-btn-close" aria-hidden="true">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="<?php echo esc_attr( $options['button_text_color'] ); ?>"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                </span>
            </button>

            <div class="gx-text-widget" id="gx-text-widget" aria-hidden="true">
                <div class="gx-text-widget-header">
                    <div class="gx-text-widget-header-content">
                        <div class="gx-text-widget-heading">
                            <?php echo $widget_brand; ?>
                            <div class="gx-text-widget-copy">
                                <h3><?php echo esc_html( $options['widget_title'] ); ?></h3>
                                <p><?php echo esc_html( $options['widget_subtitle'] ); ?></p>
                            </div>
                        </div>
                    </div>
                    <button class="gx-text-widget-close" id="gx-text-close" aria-label="<?php esc_attr_e( 'Close', 'gx-text' ); ?>">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
                    </button>
                </div>

                <?php if ( $enable_subscribe ) : ?>
                    <div class="gx-text-tabs">
                        <button class="gx-text-tab active" data-tab="message" aria-selected="true">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg>
                            <?php echo $tab_brand; ?>
                            <span class="gx-text-tab-label"><?php esc_html_e( 'Text Us', 'gx-text' ); ?></span>
                        </button>
                        <button class="gx-text-tab" data-tab="subscribe" aria-selected="false">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zM12 17c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2z"/></svg>
                            <?php echo $tab_brand; ?>
                            <span class="gx-text-tab-label"><?php esc_html_e( 'Subscribe', 'gx-text' ); ?></span>
                        </button>
                    </div>
                <?php endif; ?>

                <div class="gx-text-tab-content active" id="gx-tab-message">
                    <form class="gx-text-form" id="gx-text-message-form" novalidate>
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
                    <div class="gx-text-success" id="gx-text-msg-success" style="display:none;">
                        <div class="gx-text-success-icon">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="#25D366"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                        </div>
                        <p></p>
                    </div>
                </div>

                <?php if ( $enable_subscribe ) : ?>
                    <div class="gx-text-tab-content" id="gx-tab-subscribe">
                        <div class="gx-text-subscribe-intro">
                            <h4><?php echo esc_html( $options['subscribe_heading'] ); ?></h4>
                            <p><?php echo esc_html( $options['subscribe_description'] ); ?></p>
                        </div>
                        <form class="gx-text-form" id="gx-text-subscribe-form" novalidate>
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
                        <div class="gx-text-success" id="gx-text-sub-success" style="display:none;">
                            <div class="gx-text-success-icon">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="#25D366"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                            </div>
                            <p></p>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="gx-text-widget-footer">
                    <span><?php esc_html_e( 'Powered by', 'gx-text' ); ?> <strong>GX Text</strong></span>
                </div>
            </div>
        </div>
        <!-- /GX Text -->
        <?php
    }

    private function should_display() {
        if ( is_admin() ) {
            return false;
        }

        $options = GX_Text_Options::all();
        $display = $options['display_pages'];
        $exclude = array_filter( array_map( 'trim', explode( ',', $options['exclude_pages'] ) ) );

        if ( ! empty( $exclude ) && ( is_singular() || is_page() ) ) {
            $current_id = get_the_ID();
            if ( in_array( (string) $current_id, $exclude, true ) ) {
                return false;
            }
        }

        switch ( $display ) {
            case 'homepage':
                return is_front_page() || is_home();
            case 'posts':
                return is_single();
            case 'pages':
                return is_page();
            default:
                return true;
        }
    }

    private function get_icon_svg( $icon, $color ) {
        switch ( $icon ) {
            case 'sms':
                return '<svg width="28" height="28" viewBox="0 0 24 24" fill="' . esc_attr( $color ) . '"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>';
            case 'message':
                return '<svg width="28" height="28" viewBox="0 0 24 24" fill="' . esc_attr( $color ) . '"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>';
            case 'text':
                return '';
            default:
                return '<svg width="28" height="28" viewBox="0 0 24 24" fill="' . esc_attr( $color ) . '"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm0 14H5.17L4 17.17V4h16v12z"/><path d="M7 9h2v2H7zm4 0h2v2h-2zm4 0h2v2h-2z"/></svg>';
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
