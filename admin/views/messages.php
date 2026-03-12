<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="wrap gx-text-wrap">
    <div class="gx-text-header">
        <h1><span class="dashicons dashicons-email-alt"></span> <?php esc_html_e( 'Message Log', 'gx-text' ); ?></h1>
        <p class="gx-text-brand"><?php esc_html_e( 'by Genex Marketing Agency Ltd', 'gx-text' ); ?></p>
    </div>

    <?php if ( ! empty( $messages ) ) : ?>
    <table class="widefat striped gx-text-table">
        <thead>
            <tr>
                <th><?php esc_html_e( 'ID', 'gx-text' ); ?></th>
                <th><?php esc_html_e( 'Direction', 'gx-text' ); ?></th>
                <th><?php esc_html_e( 'Type', 'gx-text' ); ?></th>
                <th><?php esc_html_e( 'From', 'gx-text' ); ?></th>
                <th><?php esc_html_e( 'To', 'gx-text' ); ?></th>
                <th><?php esc_html_e( 'Message', 'gx-text' ); ?></th>
                <th><?php esc_html_e( 'Status', 'gx-text' ); ?></th>
                <th><?php esc_html_e( 'Date', 'gx-text' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ( $messages as $msg ) : ?>
            <tr>
                <td><?php echo esc_html( $msg->id ); ?></td>
                <td>
                    <span class="gx-text-badge badge-<?php echo esc_attr( $msg->direction ); ?>">
                        <?php echo esc_html( ucfirst( $msg->direction ) ); ?>
                    </span>
                </td>
                <td><?php echo esc_html( ucfirst( str_replace( '_', ' ', $msg->message_type ) ) ); ?></td>
                <td><?php echo esc_html( $msg->phone_from ); ?></td>
                <td><?php echo esc_html( $msg->phone_to ); ?></td>
                <td class="gx-text-msg-body"><?php echo esc_html( wp_trim_words( $msg->message_body, 20 ) ); ?></td>
                <td>
                    <span class="gx-text-badge badge-<?php echo esc_attr( $msg->status ); ?>">
                        <?php echo esc_html( ucfirst( $msg->status ) ); ?>
                    </span>
                </td>
                <td><?php echo esc_html( $msg->created_at ); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <?php if ( $total_pages > 1 ) : ?>
    <div class="gx-text-pagination">
        <?php for ( $i = 1; $i <= $total_pages; $i++ ) : ?>
            <?php if ( $i === $page ) : ?>
                <span class="current-page"><?php echo esc_html( $i ); ?></span>
            <?php else : ?>
                <a href="<?php echo esc_url( add_query_arg( 'paged', $i ) ); ?>"><?php echo esc_html( $i ); ?></a>
            <?php endif; ?>
        <?php endfor; ?>
    </div>
    <?php endif; ?>

    <?php else : ?>
        <div class="gx-text-empty-state">
            <span class="dashicons dashicons-email-alt"></span>
            <h3><?php esc_html_e( 'No messages yet', 'gx-text' ); ?></h3>
            <p><?php esc_html_e( 'Messages will appear here once visitors start texting through the widget.', 'gx-text' ); ?></p>
        </div>
    <?php endif; ?>
</div>
