<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="wrap gx-text-wrap">
    <div class="gx-text-header">
        <h1><span class="dashicons dashicons-groups"></span> <?php esc_html_e( 'Text Subscribers', 'gx-text' ); ?></h1>
        <p class="gx-text-brand"><?php esc_html_e( 'by Genex Marketing Agency Ltd', 'gx-text' ); ?></p>
    </div>

    <!-- Stats Bar -->
    <div class="gx-text-stats-bar">
        <span class="stat">
            <strong><?php echo esc_html( GX_Text_Subscribers::count() ); ?></strong> <?php esc_html_e( 'Total', 'gx-text' ); ?>
        </span>
        <span class="stat stat-active">
            <strong><?php echo esc_html( GX_Text_Subscribers::count( 'active' ) ); ?></strong> <?php esc_html_e( 'Active', 'gx-text' ); ?>
        </span>
        <span class="stat stat-unsub">
            <strong><?php echo esc_html( GX_Text_Subscribers::count( 'unsubscribed' ) ); ?></strong> <?php esc_html_e( 'Unsubscribed', 'gx-text' ); ?>
        </span>
    </div>

    <!-- Filters & Actions -->
    <div class="gx-text-toolbar">
        <div class="gx-text-filters">
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=gx-text-subscribers' ) ); ?>" class="button <?php echo empty( $status ) ? 'button-primary' : ''; ?>"><?php esc_html_e( 'All', 'gx-text' ); ?></a>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=gx-text-subscribers&status=active' ) ); ?>" class="button <?php echo 'active' === $status ? 'button-primary' : ''; ?>"><?php esc_html_e( 'Active', 'gx-text' ); ?></a>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=gx-text-subscribers&status=unsubscribed' ) ); ?>" class="button <?php echo 'unsubscribed' === $status ? 'button-primary' : ''; ?>"><?php esc_html_e( 'Unsubscribed', 'gx-text' ); ?></a>
        </div>
        <div class="gx-text-actions">
            <button type="button" id="gx-text-export-csv" class="button">
                <span class="dashicons dashicons-download"></span> <?php esc_html_e( 'Export CSV', 'gx-text' ); ?>
            </button>
        </div>
    </div>

    <!-- Subscribers Table -->
    <?php if ( ! empty( $subscribers ) ) : ?>
    <table class="widefat striped gx-text-table">
        <thead>
            <tr>
                <th><?php esc_html_e( 'Phone', 'gx-text' ); ?></th>
                <th><?php esc_html_e( 'Name', 'gx-text' ); ?></th>
                <th><?php esc_html_e( 'Email', 'gx-text' ); ?></th>
                <th><?php esc_html_e( 'Status', 'gx-text' ); ?></th>
                <th><?php esc_html_e( 'Tags', 'gx-text' ); ?></th>
                <th><?php esc_html_e( 'Subscribed', 'gx-text' ); ?></th>
                <th><?php esc_html_e( 'Actions', 'gx-text' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ( $subscribers as $sub ) : ?>
            <tr id="subscriber-row-<?php echo esc_attr( $sub->id ); ?>">
                <td><strong><?php echo esc_html( $sub->phone ); ?></strong></td>
                <td><?php echo esc_html( $sub->name ); ?></td>
                <td><?php echo esc_html( $sub->email ); ?></td>
                <td>
                    <span class="gx-text-badge badge-<?php echo esc_attr( $sub->status ); ?>">
                        <?php echo esc_html( ucfirst( $sub->status ) ); ?>
                    </span>
                </td>
                <td><?php echo esc_html( $sub->tags ); ?></td>
                <td><?php echo esc_html( $sub->subscribed_at ); ?></td>
                <td>
                    <button type="button" class="button button-small gx-text-delete-sub" data-id="<?php echo esc_attr( $sub->id ); ?>">
                        <span class="dashicons dashicons-trash"></span>
                    </button>
                </td>
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
            <span class="dashicons dashicons-groups"></span>
            <h3><?php esc_html_e( 'No subscribers yet', 'gx-text' ); ?></h3>
            <p><?php esc_html_e( 'Subscribers will appear here once visitors sign up via the text subscribe widget.', 'gx-text' ); ?></p>
        </div>
    <?php endif; ?>
</div>
