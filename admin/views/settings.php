<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="wrap gx-text-wrap">
    <div class="gx-text-header">
        <h1><span class="dashicons dashicons-admin-generic"></span> <?php esc_html_e( 'GX Text Settings', 'gx-text' ); ?></h1>
        <p class="gx-text-brand"><?php esc_html_e( 'by Genex Marketing Agency Ltd', 'gx-text' ); ?></p>
    </div>

    <form method="post" action="options.php" class="gx-text-form">
        <?php settings_fields( 'gx_text_options_group' ); ?>

        <!-- Twilio Configuration -->
        <div class="gx-text-section">
            <h2><span class="dashicons dashicons-lock"></span> <?php esc_html_e( 'Twilio Configuration', 'gx-text' ); ?></h2>
            <p class="description"><?php esc_html_e( 'Your Twilio credentials are encrypted and stored securely in the WordPress database.', 'gx-text' ); ?></p>

            <table class="form-table">
                <tr>
                    <th><label for="twilio_account_sid"><?php esc_html_e( 'Account SID', 'gx-text' ); ?></label></th>
                    <td>
                        <?php
                        $sid_decrypted = GX_Text_Encryption::decrypt( isset( $options['twilio_account_sid'] ) ? $options['twilio_account_sid'] : '' );
                        $sid_display = ! empty( $sid_decrypted ) ? '••••••••' . substr( $sid_decrypted, -4 ) : '';
                        ?>
                        <input type="text" id="twilio_account_sid" name="gx_text_options[twilio_account_sid]"
                               value="<?php echo esc_attr( $sid_display ); ?>"
                               class="regular-text" placeholder="ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
                               autocomplete="off" />
                        <p class="description"><?php esc_html_e( 'Find this in your Twilio Console dashboard.', 'gx-text' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><label for="twilio_auth_token"><?php esc_html_e( 'Auth Token', 'gx-text' ); ?></label></th>
                    <td>
                        <?php
                        $token_decrypted = GX_Text_Encryption::decrypt( isset( $options['twilio_auth_token'] ) ? $options['twilio_auth_token'] : '' );
                        $token_display = ! empty( $token_decrypted ) ? '••••••••' . substr( $token_decrypted, -4 ) : '';
                        ?>
                        <input type="password" id="twilio_auth_token" name="gx_text_options[twilio_auth_token]"
                               value="<?php echo esc_attr( $token_display ); ?>"
                               class="regular-text" placeholder="Your Auth Token"
                               autocomplete="new-password" />
                        <p class="description"><?php esc_html_e( 'Your Twilio Auth Token (stored encrypted).', 'gx-text' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><label for="twilio_phone_number"><?php esc_html_e( 'Twilio Phone Number', 'gx-text' ); ?></label></th>
                    <td>
                        <input type="text" id="twilio_phone_number" name="gx_text_options[twilio_phone_number]"
                               value="<?php echo esc_attr( isset( $options['twilio_phone_number'] ) ? $options['twilio_phone_number'] : '' ); ?>"
                               class="regular-text" placeholder="+1234567890" />
                        <p class="description"><?php esc_html_e( 'Your Twilio phone number in E.164 format (e.g., +1234567890).', 'gx-text' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><label for="business_phone"><?php esc_html_e( 'Business Phone (Receive)', 'gx-text' ); ?></label></th>
                    <td>
                        <input type="text" id="business_phone" name="gx_text_options[business_phone]"
                               value="<?php echo esc_attr( isset( $options['business_phone'] ) ? $options['business_phone'] : '' ); ?>"
                               class="regular-text" placeholder="+1234567890" />
                        <p class="description"><?php esc_html_e( 'The phone number that will receive notification texts when someone messages you.', 'gx-text' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th><label for="twilio_validate_webhook"><?php esc_html_e( 'Webhook Signature Validation', 'gx-text' ); ?></label></th>
                    <td>
                        <label>
                            <input type="checkbox" id="twilio_validate_webhook" name="gx_text_options[twilio_validate_webhook]" value="1" <?php checked( $options['twilio_validate_webhook'] ?? '1', '1' ); ?> />
                            <?php esc_html_e( 'Validate Twilio webhook signatures before processing inbound texts.', 'gx-text' ); ?>
                        </label>
                        <p class="description"><?php esc_html_e( 'Keep this enabled unless a reverse proxy or custom Twilio setup prevents signature verification.', 'gx-text' ); ?></p>
                    </td>
                </tr>
            </table>

            <p>
                <button type="button" id="gx-text-test-twilio" class="button button-secondary">
                    <span class="dashicons dashicons-cloud"></span> <?php esc_html_e( 'Test Twilio Connection', 'gx-text' ); ?>
                </button>
                <span id="gx-text-test-result" class="gx-text-test-result"></span>
            </p>
        </div>

        <!-- Widget Content -->
        <div class="gx-text-section">
            <h2><span class="dashicons dashicons-edit"></span> <?php esc_html_e( 'Widget Content', 'gx-text' ); ?></h2>
            <table class="form-table">
                <tr>
                    <th><label for="widget_title"><?php esc_html_e( 'Widget Title', 'gx-text' ); ?></label></th>
                    <td><input type="text" id="widget_title" name="gx_text_options[widget_title]" value="<?php echo esc_attr( $options['widget_title'] ?? '' ); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th><label for="widget_subtitle"><?php esc_html_e( 'Widget Subtitle', 'gx-text' ); ?></label></th>
                    <td><input type="text" id="widget_subtitle" name="gx_text_options[widget_subtitle]" value="<?php echo esc_attr( $options['widget_subtitle'] ?? '' ); ?>" class="large-text" /></td>
                </tr>
                <tr>
                    <th><label for="success_message"><?php esc_html_e( 'Success Message', 'gx-text' ); ?></label></th>
                    <td><input type="text" id="success_message" name="gx_text_options[success_message]" value="<?php echo esc_attr( $options['success_message'] ?? '' ); ?>" class="large-text" /></td>
                </tr>
                <tr>
                    <th><label for="placeholder_name"><?php esc_html_e( 'Name Placeholder', 'gx-text' ); ?></label></th>
                    <td><input type="text" id="placeholder_name" name="gx_text_options[placeholder_name]" value="<?php echo esc_attr( $options['placeholder_name'] ?? '' ); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th><label for="placeholder_phone"><?php esc_html_e( 'Phone Placeholder', 'gx-text' ); ?></label></th>
                    <td><input type="text" id="placeholder_phone" name="gx_text_options[placeholder_phone]" value="<?php echo esc_attr( $options['placeholder_phone'] ?? '' ); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th><label for="placeholder_message"><?php esc_html_e( 'Message Placeholder', 'gx-text' ); ?></label></th>
                    <td><input type="text" id="placeholder_message" name="gx_text_options[placeholder_message]" value="<?php echo esc_attr( $options['placeholder_message'] ?? '' ); ?>" class="regular-text" /></td>
                </tr>
            </table>
        </div>

        <!-- Subscription Settings -->
        <div class="gx-text-section">
            <h2><span class="dashicons dashicons-megaphone"></span> <?php esc_html_e( 'Text Subscription Settings', 'gx-text' ); ?></h2>
            <table class="form-table">
                <tr>
                    <th><label for="enable_subscribe"><?php esc_html_e( 'Enable Subscribe Tab', 'gx-text' ); ?></label></th>
                    <td>
                        <label>
                            <input type="checkbox" id="enable_subscribe" name="gx_text_options[enable_subscribe]" value="1" <?php checked( $options['enable_subscribe'] ?? '1', '1' ); ?> />
                            <?php esc_html_e( 'Show a "Subscribe" tab in the text widget for text newsletter sign-ups.', 'gx-text' ); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th><label for="subscribe_heading"><?php esc_html_e( 'Subscribe Heading', 'gx-text' ); ?></label></th>
                    <td><input type="text" id="subscribe_heading" name="gx_text_options[subscribe_heading]" value="<?php echo esc_attr( $options['subscribe_heading'] ?? '' ); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th><label for="subscribe_description"><?php esc_html_e( 'Subscribe Description', 'gx-text' ); ?></label></th>
                    <td><textarea id="subscribe_description" name="gx_text_options[subscribe_description]" class="large-text" rows="3"><?php echo esc_textarea( $options['subscribe_description'] ?? '' ); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="subscribe_consent"><?php esc_html_e( 'Consent Text', 'gx-text' ); ?></label></th>
                    <td><textarea id="subscribe_consent" name="gx_text_options[subscribe_consent]" class="large-text" rows="2"><?php echo esc_textarea( $options['subscribe_consent'] ?? '' ); ?></textarea></td>
                </tr>
                <tr>
                    <th><label for="subscribe_success"><?php esc_html_e( 'Subscribe Success Message', 'gx-text' ); ?></label></th>
                    <td><input type="text" id="subscribe_success" name="gx_text_options[subscribe_success]" value="<?php echo esc_attr( $options['subscribe_success'] ?? '' ); ?>" class="large-text" /></td>
                </tr>
                <tr>
                    <th><label for="subscribe_btn_text"><?php esc_html_e( 'Subscribe Button Text', 'gx-text' ); ?></label></th>
                    <td><input type="text" id="subscribe_btn_text" name="gx_text_options[subscribe_btn_text]" value="<?php echo esc_attr( $options['subscribe_btn_text'] ?? '' ); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th><label for="subscribe_btn_color"><?php esc_html_e( 'Subscribe Button Color', 'gx-text' ); ?></label></th>
                    <td><input type="text" id="subscribe_btn_color" name="gx_text_options[subscribe_btn_color]" value="<?php echo esc_attr( $options['subscribe_btn_color'] ?? '#FF6B35' ); ?>" class="gx-color-picker" /></td>
                </tr>
            </table>
        </div>

        <!-- Display Rules -->
        <div class="gx-text-section">
            <h2><span class="dashicons dashicons-visibility"></span> <?php esc_html_e( 'Display Rules', 'gx-text' ); ?></h2>
            <table class="form-table">
                <tr>
                    <th><label for="show_on_desktop"><?php esc_html_e( 'Show on Desktop', 'gx-text' ); ?></label></th>
                    <td><label><input type="checkbox" id="show_on_desktop" name="gx_text_options[show_on_desktop]" value="1" <?php checked( $options['show_on_desktop'] ?? '1', '1' ); ?> /> <?php esc_html_e( 'Display the floating button on desktop devices.', 'gx-text' ); ?></label></td>
                </tr>
                <tr>
                    <th><label for="show_on_mobile"><?php esc_html_e( 'Show on Mobile', 'gx-text' ); ?></label></th>
                    <td><label><input type="checkbox" id="show_on_mobile" name="gx_text_options[show_on_mobile]" value="1" <?php checked( $options['show_on_mobile'] ?? '1', '1' ); ?> /> <?php esc_html_e( 'Display the floating button on mobile devices.', 'gx-text' ); ?></label></td>
                </tr>
                <tr>
                    <th><label for="display_pages"><?php esc_html_e( 'Display On', 'gx-text' ); ?></label></th>
                    <td>
                        <select id="display_pages" name="gx_text_options[display_pages]">
                            <option value="all" <?php selected( $options['display_pages'] ?? 'all', 'all' ); ?>><?php esc_html_e( 'All Pages', 'gx-text' ); ?></option>
                            <option value="homepage" <?php selected( $options['display_pages'] ?? '', 'homepage' ); ?>><?php esc_html_e( 'Homepage Only', 'gx-text' ); ?></option>
                            <option value="posts" <?php selected( $options['display_pages'] ?? '', 'posts' ); ?>><?php esc_html_e( 'Posts Only', 'gx-text' ); ?></option>
                            <option value="pages" <?php selected( $options['display_pages'] ?? '', 'pages' ); ?>><?php esc_html_e( 'Pages Only', 'gx-text' ); ?></option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="exclude_pages"><?php esc_html_e( 'Exclude Page IDs', 'gx-text' ); ?></label></th>
                    <td>
                        <input type="text" id="exclude_pages" name="gx_text_options[exclude_pages]" value="<?php echo esc_attr( $options['exclude_pages'] ?? '' ); ?>" class="regular-text" placeholder="e.g., 12,45,78" />
                        <p class="description"><?php esc_html_e( 'Comma-separated list of page/post IDs to exclude.', 'gx-text' ); ?></p>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Custom CSS -->
        <div class="gx-text-section">
            <h2><span class="dashicons dashicons-editor-code"></span> <?php esc_html_e( 'Custom CSS', 'gx-text' ); ?></h2>
            <table class="form-table">
                <tr>
                    <th><label for="custom_css"><?php esc_html_e( 'Additional CSS', 'gx-text' ); ?></label></th>
                    <td>
                        <textarea id="custom_css" name="gx_text_options[custom_css]" class="large-text code" rows="8" placeholder="/* Your custom CSS here */"><?php echo esc_textarea( $options['custom_css'] ?? '' ); ?></textarea>
                    </td>
                </tr>
            </table>
        </div>

        <?php submit_button( __( 'Save Settings', 'gx-text' ) ); ?>
    </form>
</div>
