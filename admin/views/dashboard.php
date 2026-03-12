<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="wrap gx-text-wrap">
    <div class="gx-text-header">
        <h1><span class="dashicons dashicons-format-chat"></span> <?php esc_html_e( 'GX Text Dashboard', 'gx-text' ); ?></h1>
        <p class="gx-text-brand"><?php esc_html_e( 'by Genex Marketing Agency Ltd', 'gx-text' ); ?></p>
    </div>

    <!-- Status Cards -->
    <div class="gx-text-cards">
        <div class="gx-text-card <?php echo $is_configured ? 'card-success' : 'card-warning'; ?>">
            <div class="card-icon">
                <span class="dashicons <?php echo $is_configured ? 'dashicons-yes-alt' : 'dashicons-warning'; ?>"></span>
            </div>
            <div class="card-content">
                <h3><?php echo $is_configured ? esc_html__( 'Twilio Connected', 'gx-text' ) : esc_html__( 'Twilio Not Configured', 'gx-text' ); ?></h3>
                <p><?php echo $is_configured
                    ? esc_html__( 'Your Twilio account is connected and ready.', 'gx-text' )
                    : esc_html__( 'Please configure your Twilio credentials in Settings.', 'gx-text' ); ?></p>
                <?php if ( ! $is_configured ) : ?>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=gx-text-settings' ) ); ?>" class="button button-primary"><?php esc_html_e( 'Configure Now', 'gx-text' ); ?></a>
                <?php endif; ?>
            </div>
        </div>

        <div class="gx-text-card card-info">
            <div class="card-icon"><span class="dashicons dashicons-groups"></span></div>
            <div class="card-content">
                <h3><?php echo esc_html( $active_subs ); ?></h3>
                <p><?php esc_html_e( 'Active Subscribers', 'gx-text' ); ?></p>
            </div>
        </div>

        <div class="gx-text-card card-neutral">
            <div class="card-icon"><span class="dashicons dashicons-email-alt"></span></div>
            <div class="card-content">
                <h3><?php echo esc_html( $total_msgs ); ?></h3>
                <p><?php esc_html_e( 'Total Messages', 'gx-text' ); ?></p>
            </div>
        </div>

        <div class="gx-text-card card-muted">
            <div class="card-icon"><span class="dashicons dashicons-dismiss"></span></div>
            <div class="card-content">
                <h3><?php echo esc_html( $unsub_count ); ?></h3>
                <p><?php esc_html_e( 'Unsubscribed', 'gx-text' ); ?></p>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="gx-text-section">
        <h2><?php esc_html_e( 'Quick Actions', 'gx-text' ); ?></h2>
        <div class="gx-text-quick-actions">
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=gx-text-settings' ) ); ?>" class="button button-large">
                <span class="dashicons dashicons-admin-generic"></span> <?php esc_html_e( 'Settings', 'gx-text' ); ?>
            </a>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=gx-text-appearance' ) ); ?>" class="button button-large">
                <span class="dashicons dashicons-art"></span> <?php esc_html_e( 'Customize Appearance', 'gx-text' ); ?>
            </a>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=gx-text-subscribers' ) ); ?>" class="button button-large">
                <span class="dashicons dashicons-groups"></span> <?php esc_html_e( 'Manage Subscribers', 'gx-text' ); ?>
            </a>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=gx-text-broadcast' ) ); ?>" class="button button-large">
                <span class="dashicons dashicons-megaphone"></span> <?php esc_html_e( 'Send Broadcast', 'gx-text' ); ?>
            </a>
        </div>
    </div>

    <!-- Shortcode Reference -->
    <div class="gx-text-section">
        <h2><?php esc_html_e( 'Shortcode Reference', 'gx-text' ); ?></h2>
        <table class="widefat gx-text-shortcode-table">
            <thead>
                <tr>
                    <th><?php esc_html_e( 'Shortcode', 'gx-text' ); ?></th>
                    <th><?php esc_html_e( 'Description', 'gx-text' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><code>[gx_text_button]</code></td>
                    <td><?php esc_html_e( 'Embed the Text Us button inline. Supports: position, color, label attributes.', 'gx-text' ); ?></td>
                </tr>
                <tr>
                    <td><code>[gx_text_form]</code></td>
                    <td><?php esc_html_e( 'Embed the full text message form inline.', 'gx-text' ); ?></td>
                </tr>
                <tr>
                    <td><code>[gx_text_subscribe]</code></td>
                    <td><?php esc_html_e( 'Embed the text subscription form inline.', 'gx-text' ); ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Recent Messages -->
    <?php if ( ! empty( $recent_msgs ) ) : ?>
    <div class="gx-text-section">
        <h2><?php esc_html_e( 'Recent Messages', 'gx-text' ); ?></h2>
        <table class="widefat striped">
            <thead>
                <tr>
                    <th><?php esc_html_e( 'Direction', 'gx-text' ); ?></th>
                    <th><?php esc_html_e( 'From', 'gx-text' ); ?></th>
                    <th><?php esc_html_e( 'To', 'gx-text' ); ?></th>
                    <th><?php esc_html_e( 'Message', 'gx-text' ); ?></th>
                    <th><?php esc_html_e( 'Date', 'gx-text' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $recent_msgs as $msg ) : ?>
                <tr>
                    <td>
                        <span class="gx-text-badge badge-<?php echo esc_attr( $msg->direction ); ?>">
                            <?php echo esc_html( ucfirst( $msg->direction ) ); ?>
                        </span>
                    </td>
                    <td><?php echo esc_html( $msg->phone_from ); ?></td>
                    <td><?php echo esc_html( $msg->phone_to ); ?></td>
                    <td><?php echo esc_html( wp_trim_words( $msg->message_body, 12 ) ); ?></td>
                    <td><?php echo esc_html( $msg->created_at ); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p><a href="<?php echo esc_url( admin_url( 'admin.php?page=gx-text-messages' ) ); ?>"><?php esc_html_e( 'View all messages &rarr;', 'gx-text' ); ?></a></p>
    </div>
    <?php endif; ?>
</div>
