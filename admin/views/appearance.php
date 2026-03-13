<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="wrap gx-text-wrap">
    <div class="gx-text-header">
        <h1><span class="dashicons dashicons-art"></span> <?php esc_html_e( 'Appearance Settings', 'gx-text' ); ?></h1>
        <p class="gx-text-brand"><?php esc_html_e( 'by Genex Marketing Agency Ltd', 'gx-text' ); ?></p>
    </div>

    <div class="gx-text-appearance-layout">
        <div class="gx-text-appearance-form">
            <form method="post" action="options.php" class="gx-text-form">
                <?php settings_fields( 'gx_text_options_group' ); ?>

                <!-- Hidden fields for non-appearance options to preserve them -->
                <?php
                $preserve_fields = array(
                    'twilio_account_sid', 'twilio_auth_token', 'twilio_phone_number', 'business_phone',
                    'widget_title', 'widget_subtitle', 'success_message', 'placeholder_name',
                    'placeholder_phone', 'placeholder_message', 'enable_subscribe', 'subscribe_heading',
                    'subscribe_description', 'subscribe_consent', 'subscribe_success', 'subscribe_btn_text',
                    'subscribe_btn_color', 'display_pages', 'exclude_pages', 'custom_css',
                    'show_on_mobile', 'show_on_desktop', 'twilio_validate_webhook',
                );
                foreach ( $preserve_fields as $field ) :
                    if ( in_array( $field, array( 'show_on_mobile', 'show_on_desktop', 'enable_subscribe', 'twilio_validate_webhook' ), true ) ) :
                ?>
                    <input type="hidden" name="gx_text_options[<?php echo esc_attr( $field ); ?>]" value="<?php echo esc_attr( $options[ $field ] ?? '1' ); ?>" />
                <?php else : ?>
                    <input type="hidden" name="gx_text_options[<?php echo esc_attr( $field ); ?>]" value="<?php echo esc_attr( $options[ $field ] ?? '' ); ?>" />
                <?php endif; endforeach; ?>

                <!-- Branding Graphic -->
                <div class="gx-text-section">
                    <h2><?php esc_html_e( 'Branding Graphic', 'gx-text' ); ?></h2>
                    <p class="description"><?php esc_html_e( 'Use badge mode for a small brand mark beside the button text, or graphic replacement mode to use an image as the entire launcher button.', 'gx-text' ); ?></p>
                    <table class="form-table">
                        <tr>
                            <th><label for="brand_logo_url"><?php esc_html_e( 'Widget / Tab Logo', 'gx-text' ); ?></label></th>
                            <td>
                                <div class="gx-text-media-field">
                                    <input type="url" id="brand_logo_url" name="gx_text_options[brand_logo_url]" value="<?php echo esc_attr( $options['brand_logo_url'] ?? '' ); ?>" class="regular-text" placeholder="https://example.com/brand-square.png" />
                                    <button type="button" class="button" id="gx-text-select-brand-logo"><?php esc_html_e( 'Choose Logo', 'gx-text' ); ?></button>
                                    <button type="button" class="button button-link-delete" id="gx-text-remove-brand-logo"><?php esc_html_e( 'Remove', 'gx-text' ); ?></button>
                                </div>
                                <p class="description"><?php esc_html_e( 'Square logo used inside the open widget header and tabs. This stays separate from full-button replacement graphics.', 'gx-text' ); ?></p>
                                <div class="gx-text-graphic-preview<?php echo empty( $options['brand_logo_url'] ) ? ' is-empty' : ''; ?>" id="gx-text-brand-logo-preview">
                                    <?php if ( ! empty( $options['brand_logo_url'] ) ) : ?>
                                        <img src="<?php echo esc_url( $options['brand_logo_url'] ); ?>" alt="" />
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="button_graphic_mode"><?php esc_html_e( 'Graphic Mode', 'gx-text' ); ?></label></th>
                            <td>
                                <select id="button_graphic_mode" name="gx_text_options[button_graphic_mode]" class="gx-preview-trigger">
                                    <option value="badge" <?php selected( $options['button_graphic_mode'] ?? 'badge', 'badge' ); ?>><?php esc_html_e( 'Badge Beside Text', 'gx-text' ); ?></option>
                                    <option value="replace" <?php selected( $options['button_graphic_mode'] ?? '', 'replace' ); ?>><?php esc_html_e( 'Replace Entire Button', 'gx-text' ); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="button_graphic_url"><?php esc_html_e( 'Primary Graphic', 'gx-text' ); ?></label></th>
                            <td>
                                <div class="gx-text-media-field">
                                    <input type="url" id="button_graphic_url" name="gx_text_options[button_graphic_url]" value="<?php echo esc_attr( $options['button_graphic_url'] ?? '' ); ?>" class="regular-text gx-preview-trigger" placeholder="https://example.com/brand-square.png" />
                                    <button type="button" class="button" id="gx-text-select-graphic"><?php esc_html_e( 'Choose Image', 'gx-text' ); ?></button>
                                    <button type="button" class="button button-link-delete" id="gx-text-remove-graphic"><?php esc_html_e( 'Remove', 'gx-text' ); ?></button>
                                </div>
                                <p class="description"><?php esc_html_e( 'In badge mode this becomes the small logo beside the label. In replace mode this becomes the full launcher graphic.', 'gx-text' ); ?></p>
                                <div class="gx-text-graphic-preview<?php echo empty( $options['button_graphic_url'] ) ? ' is-empty' : ''; ?>" id="gx-text-graphic-preview">
                                    <?php if ( ! empty( $options['button_graphic_url'] ) ) : ?>
                                        <img src="<?php echo esc_url( $options['button_graphic_url'] ); ?>" alt="" />
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="button_hover_graphic_url"><?php esc_html_e( 'Hover Graphic', 'gx-text' ); ?></label></th>
                            <td>
                                <div class="gx-text-media-field">
                                    <input type="url" id="button_hover_graphic_url" name="gx_text_options[button_hover_graphic_url]" value="<?php echo esc_attr( $options['button_hover_graphic_url'] ?? '' ); ?>" class="regular-text gx-preview-trigger" placeholder="https://example.com/button-hover.png" />
                                    <button type="button" class="button" id="gx-text-select-hover-graphic"><?php esc_html_e( 'Choose Hover Image', 'gx-text' ); ?></button>
                                    <button type="button" class="button button-link-delete" id="gx-text-remove-hover-graphic"><?php esc_html_e( 'Remove', 'gx-text' ); ?></button>
                                </div>
                                <p class="description"><?php esc_html_e( 'Optional. In replace mode, this image is shown when the launcher is hovered.', 'gx-text' ); ?></p>
                                <div class="gx-text-graphic-preview<?php echo empty( $options['button_hover_graphic_url'] ) ? ' is-empty' : ''; ?>" id="gx-text-hover-graphic-preview">
                                    <?php if ( ! empty( $options['button_hover_graphic_url'] ) ) : ?>
                                        <img src="<?php echo esc_url( $options['button_hover_graphic_url'] ); ?>" alt="" />
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="button_graphic_size"><?php esc_html_e( 'Logo Size (px)', 'gx-text' ); ?></label></th>
                            <td>
                                <input type="range" id="button_graphic_size" name="gx_text_options[button_graphic_size]" value="<?php echo esc_attr( $options['button_graphic_size'] ?? '28' ); ?>" min="20" max="44" class="gx-preview-trigger" />
                                <span class="gx-range-value"><?php echo esc_html( $options['button_graphic_size'] ?? '28' ); ?>px</span>
                                <p class="description"><?php esc_html_e( 'Used for badge mode. In replace mode, the launcher uses the image’s natural dimensions.', 'gx-text' ); ?></p>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Button Appearance -->
                <div class="gx-text-section">
                    <h2><?php esc_html_e( 'Button Appearance', 'gx-text' ); ?></h2>
                    <table class="form-table">
                        <tr>
                            <th><label for="button_position"><?php esc_html_e( 'Position', 'gx-text' ); ?></label></th>
                            <td>
                                <select id="button_position" name="gx_text_options[button_position]" class="gx-preview-trigger">
                                    <option value="bottom-right" <?php selected( $options['button_position'] ?? '', 'bottom-right' ); ?>><?php esc_html_e( 'Bottom Right', 'gx-text' ); ?></option>
                                    <option value="bottom-left" <?php selected( $options['button_position'] ?? '', 'bottom-left' ); ?>><?php esc_html_e( 'Bottom Left', 'gx-text' ); ?></option>
                                    <option value="top-right" <?php selected( $options['button_position'] ?? '', 'top-right' ); ?>><?php esc_html_e( 'Top Right', 'gx-text' ); ?></option>
                                    <option value="top-left" <?php selected( $options['button_position'] ?? '', 'top-left' ); ?>><?php esc_html_e( 'Top Left', 'gx-text' ); ?></option>
                                    <option value="manual" <?php selected( $options['button_position'] ?? '', 'manual' ); ?>><?php esc_html_e( 'Manual Placement (Shortcode / Block)', 'gx-text' ); ?></option>
                                </select>
                                <p class="description"><?php esc_html_e( 'Manual placement hides the floating launcher. Use the GX Text Button block or [gx_text_button] shortcode where you want the launcher to appear.', 'gx-text' ); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="button_color"><?php esc_html_e( 'Button Color', 'gx-text' ); ?></label></th>
                            <td><input type="text" id="button_color" name="gx_text_options[button_color]" value="<?php echo esc_attr( $options['button_color'] ?? '#25D366' ); ?>" class="gx-color-picker gx-preview-trigger" /></td>
                        </tr>
                        <tr>
                            <th><label for="button_text_color"><?php esc_html_e( 'Button Text/Icon Color', 'gx-text' ); ?></label></th>
                            <td><input type="text" id="button_text_color" name="gx_text_options[button_text_color]" value="<?php echo esc_attr( $options['button_text_color'] ?? '#ffffff' ); ?>" class="gx-color-picker gx-preview-trigger" /></td>
                        </tr>
                        <tr>
                            <th><label for="button_icon"><?php esc_html_e( 'Button Icon', 'gx-text' ); ?></label></th>
                            <td>
                                <select id="button_icon" name="gx_text_options[button_icon]" class="gx-preview-trigger">
                                    <option value="chat" <?php selected( $options['button_icon'] ?? '', 'chat' ); ?>><?php esc_html_e( 'Chat Bubble', 'gx-text' ); ?></option>
                                    <option value="sms" <?php selected( $options['button_icon'] ?? '', 'sms' ); ?>><?php esc_html_e( 'SMS / Phone', 'gx-text' ); ?></option>
                                    <option value="message" <?php selected( $options['button_icon'] ?? '', 'message' ); ?>><?php esc_html_e( 'Message Envelope', 'gx-text' ); ?></option>
                                    <option value="text" <?php selected( $options['button_icon'] ?? '', 'text' ); ?>><?php esc_html_e( 'Text Only (No Icon)', 'gx-text' ); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="button_label"><?php esc_html_e( 'Button Label', 'gx-text' ); ?></label></th>
                            <td><input type="text" id="button_label" name="gx_text_options[button_label]" value="<?php echo esc_attr( $options['button_label'] ?? 'Text Us!' ); ?>" class="regular-text gx-preview-trigger" /></td>
                        </tr>
                        <tr>
                            <th><label for="button_size"><?php esc_html_e( 'Button Size (px)', 'gx-text' ); ?></label></th>
                            <td>
                                <input type="range" id="button_size" name="gx_text_options[button_size]" value="<?php echo esc_attr( $options['button_size'] ?? '60' ); ?>" min="40" max="100" class="gx-preview-trigger" />
                                <span class="gx-range-value"><?php echo esc_html( $options['button_size'] ?? '60' ); ?>px</span>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="button_border_radius"><?php esc_html_e( 'Border Radius (%)', 'gx-text' ); ?></label></th>
                            <td>
                                <input type="range" id="button_border_radius" name="gx_text_options[button_border_radius]" value="<?php echo esc_attr( $options['button_border_radius'] ?? '50' ); ?>" min="0" max="50" class="gx-preview-trigger" />
                                <span class="gx-range-value"><?php echo esc_html( $options['button_border_radius'] ?? '50' ); ?>%</span>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="animation_type"><?php esc_html_e( 'Animation', 'gx-text' ); ?></label></th>
                            <td>
                                <select id="animation_type" name="gx_text_options[animation_type]" class="gx-preview-trigger">
                                    <option value="pulse" <?php selected( $options['animation_type'] ?? '', 'pulse' ); ?>><?php esc_html_e( 'Pulse', 'gx-text' ); ?></option>
                                    <option value="bounce" <?php selected( $options['animation_type'] ?? '', 'bounce' ); ?>><?php esc_html_e( 'Bounce', 'gx-text' ); ?></option>
                                    <option value="shake" <?php selected( $options['animation_type'] ?? '', 'shake' ); ?>><?php esc_html_e( 'Shake', 'gx-text' ); ?></option>
                                    <option value="glow" <?php selected( $options['animation_type'] ?? '', 'glow' ); ?>><?php esc_html_e( 'Glow', 'gx-text' ); ?></option>
                                    <option value="none" <?php selected( $options['animation_type'] ?? '', 'none' ); ?>><?php esc_html_e( 'None', 'gx-text' ); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="offset_x"><?php esc_html_e( 'Horizontal Offset (px)', 'gx-text' ); ?></label></th>
                            <td>
                                <input type="range" id="offset_x" name="gx_text_options[offset_x]" value="<?php echo esc_attr( $options['offset_x'] ?? '20' ); ?>" min="0" max="100" class="gx-preview-trigger" />
                                <span class="gx-range-value"><?php echo esc_html( $options['offset_x'] ?? '20' ); ?>px</span>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="offset_y"><?php esc_html_e( 'Vertical Offset (px)', 'gx-text' ); ?></label></th>
                            <td>
                                <input type="range" id="offset_y" name="gx_text_options[offset_y]" value="<?php echo esc_attr( $options['offset_y'] ?? '20' ); ?>" min="0" max="100" class="gx-preview-trigger" />
                                <span class="gx-range-value"><?php echo esc_html( $options['offset_y'] ?? '20' ); ?>px</span>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="z_index"><?php esc_html_e( 'Z-Index', 'gx-text' ); ?></label></th>
                            <td><input type="number" id="z_index" name="gx_text_options[z_index]" value="<?php echo esc_attr( $options['z_index'] ?? '9999' ); ?>" class="small-text" /></td>
                        </tr>
                    </table>
                </div>

                <!-- Widget Window Appearance -->
                <div class="gx-text-section">
                    <h2><?php esc_html_e( 'Widget Window Appearance', 'gx-text' ); ?></h2>
                    <table class="form-table">
                        <tr>
                            <th><label for="widget_bg_color"><?php esc_html_e( 'Widget Background', 'gx-text' ); ?></label></th>
                            <td><input type="text" id="widget_bg_color" name="gx_text_options[widget_bg_color]" value="<?php echo esc_attr( $options['widget_bg_color'] ?? '#ffffff' ); ?>" class="gx-color-picker" /></td>
                        </tr>
                        <tr>
                            <th><label for="widget_header_color"><?php esc_html_e( 'Header Background', 'gx-text' ); ?></label></th>
                            <td><input type="text" id="widget_header_color" name="gx_text_options[widget_header_color]" value="<?php echo esc_attr( $options['widget_header_color'] ?? '#25D366' ); ?>" class="gx-color-picker" /></td>
                        </tr>
                        <tr>
                            <th><label for="widget_header_text"><?php esc_html_e( 'Header Text Color', 'gx-text' ); ?></label></th>
                            <td><input type="text" id="widget_header_text" name="gx_text_options[widget_header_text]" value="<?php echo esc_attr( $options['widget_header_text'] ?? '#ffffff' ); ?>" class="gx-color-picker" /></td>
                        </tr>
                        <tr>
                            <th><label for="widget_font_family"><?php esc_html_e( 'Font Family', 'gx-text' ); ?></label></th>
                            <td>
                                <select id="widget_font_family" name="gx_text_options[widget_font_family]">
                                    <option value="inherit" <?php selected( $options['widget_font_family'] ?? '', 'inherit' ); ?>><?php esc_html_e( 'Inherit from Theme', 'gx-text' ); ?></option>
                                    <option value="'Inter', sans-serif" <?php selected( $options['widget_font_family'] ?? '', "'Inter', sans-serif" ); ?>>Inter</option>
                                    <option value="'Poppins', sans-serif" <?php selected( $options['widget_font_family'] ?? '', "'Poppins', sans-serif" ); ?>>Poppins</option>
                                    <option value="'Roboto', sans-serif" <?php selected( $options['widget_font_family'] ?? '', "'Roboto', sans-serif" ); ?>>Roboto</option>
                                    <option value="'Open Sans', sans-serif" <?php selected( $options['widget_font_family'] ?? '', "'Open Sans', sans-serif" ); ?>>Open Sans</option>
                                    <option value="system-ui, sans-serif" <?php selected( $options['widget_font_family'] ?? '', 'system-ui, sans-serif' ); ?>>System UI</option>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>

                <?php submit_button( __( 'Save Appearance', 'gx-text' ) ); ?>
            </form>
        </div>

        <!-- Live Preview -->
        <div class="gx-text-preview-panel">
            <h3><?php esc_html_e( 'Live Preview', 'gx-text' ); ?></h3>
            <div class="gx-text-preview-container" id="gx-text-preview">
                <div class="gx-preview-phone">
                    <div class="gx-preview-screen">
                        <div class="gx-preview-button" id="gx-preview-btn">
                            <span class="gx-preview-icon"></span>
                            <span class="gx-preview-graphic" hidden><img src="" alt="" /></span>
                            <span class="gx-preview-graphic-stack" hidden>
                                <span class="gx-preview-graphic-layer gx-preview-graphic-layer-default"><img src="" alt="" /></span>
                                <span class="gx-preview-graphic-layer gx-preview-graphic-layer-hover"><img src="" alt="" /></span>
                            </span>
                            <span class="gx-preview-label"><?php echo esc_html( $options['button_label'] ?? 'Text Us!' ); ?></span>
                        </div>
                        <p class="gx-preview-manual-note" id="gx-preview-manual-note" hidden><?php esc_html_e( 'Manual placement mode: place the launcher with a shortcode or block.', 'gx-text' ); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
