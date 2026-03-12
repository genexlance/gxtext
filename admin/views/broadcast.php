<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="wrap gx-text-wrap">
    <div class="gx-text-header">
        <h1><span class="dashicons dashicons-megaphone"></span> <?php esc_html_e( 'Send Broadcast', 'gx-text' ); ?></h1>
        <p class="gx-text-brand"><?php esc_html_e( 'by Genex Marketing Agency Ltd', 'gx-text' ); ?></p>
    </div>

    <div class="gx-text-section">
        <div class="gx-text-broadcast-info">
            <p>
                <span class="dashicons dashicons-info"></span>
                <?php printf(
                    esc_html__( 'This will send a text message to all %d active subscriber(s).', 'gx-text' ),
                    $active_count
                ); ?>
            </p>
        </div>

        <?php if ( $active_count > 0 ) : ?>
        <div class="gx-text-broadcast-form">
            <div class="gx-text-field">
                <label for="broadcast-message"><?php esc_html_e( 'Message', 'gx-text' ); ?></label>
                <textarea id="broadcast-message" rows="5" class="large-text" maxlength="1600" placeholder="<?php esc_attr_e( 'Type your broadcast message here…', 'gx-text' ); ?>"></textarea>
                <div class="gx-text-char-count">
                    <span id="broadcast-char-count">0</span> / 1600 <?php esc_html_e( 'characters', 'gx-text' ); ?>
                    (<span id="broadcast-segment-count">0</span> <?php esc_html_e( 'SMS segments', 'gx-text' ); ?>)
                </div>
            </div>
            <p>
                <button type="button" id="gx-text-send-broadcast" class="button button-primary button-large">
                    <span class="dashicons dashicons-megaphone"></span> <?php esc_html_e( 'Send Broadcast', 'gx-text' ); ?>
                </button>
            </p>
            <div id="gx-text-broadcast-result" class="gx-text-broadcast-result" style="display:none;"></div>
        </div>
        <?php else : ?>
        <div class="gx-text-empty-state">
            <span class="dashicons dashicons-groups"></span>
            <h3><?php esc_html_e( 'No active subscribers', 'gx-text' ); ?></h3>
            <p><?php esc_html_e( 'You need at least one active subscriber to send a broadcast.', 'gx-text' ); ?></p>
        </div>
        <?php endif; ?>
    </div>
</div>
