<?php
/**
 * Plugin Name: Quick Edit Author Fix for large multisite
 *
 * Description: Restores the Author dropdown in Quick Edit (edit.php) of subsites in large multisite installations
 * by adjusting the wp_is_large_user_count heuristic per site.
 *
 * Addresses the UX limitation introduced to improve performance on large user networks
 * (see WP Trac #56129), where the Author dropdown is hidden when user counts exceed 10,000.
 */

/**
 * Filter wp_is_large_user_count to restore Quick Edit author dropdown on edit.php.
 *
 * Only affects edit.php screen of subsites in multisite, leaving core behavior intact elsewhere.
 *
 * @param bool $is_large_user_count Original filter value.
 * @return bool Modified value based on current site and screen.
 */
function lsmu_filter_large_user_count_on_edit_screen( $is_large_user_count ) {

    // Only affect admin + multisite
    if ( ! is_admin() || ! is_multisite() ) {
        return $is_large_user_count;
    }

    $screen = get_current_screen();
    if ( ! $screen || $screen->base !== 'edit' ) {
        return $is_large_user_count;
    }

    // Count users for the current subsite
    $user_count = count_users( 'time', get_current_blog_id() );

    return ( $user_count['total_users'] > 10000 );
}

/**
 * Hook the filter on admin_init to ensure current_screen is available.
 */
function lsmu_add_large_user_count_filter() {
    add_filter( 'wp_is_large_user_count', 'lsmu_filter_large_user_count_on_edit_screen' );
}
add_action( 'admin_init', 'lsmu_add_large_user_count_filter' );
