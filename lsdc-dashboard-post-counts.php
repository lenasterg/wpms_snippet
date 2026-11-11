<?php
/**
 * Plugin Name: LSDC - Dashboard Post Counts
 * Description: Displays draft, pending, and scheduled post and page counts in the WordPress dashboard.
 * Version: 1.3.0
 * Author: LSDC
 * License: GPL-2.0-or-later
 *
 * @package LSDC
 */

defined( 'ABSPATH' ) || exit;

/**
 * Adds draft, pending, and scheduled post and page counts to the dashboard.
 *
 * Uses native WordPress post type and status labels.
 *
 * @param array $items Existing dashboard glance items.
 * @return array Modified dashboard glance items.
 */
function lsdc_add_post_status_glance_items( $items ) {

	$post_types = array(
		'post' => array(
			'capability' => 'edit_posts',
		),
		'page' => array(
			'capability' => 'edit_pages',
		),
	);

	$statuses = array( 'draft', 'pending', 'future' );

	foreach ( $post_types as $post_type => $settings ) {

		if ( ! current_user_can( $settings['capability'] ) ) {
			continue;
		}

		$post_type_object = get_post_type_object( $post_type );

		if ( ! $post_type_object ) {
			continue;
		}

		$counts = wp_count_posts( $post_type );

		foreach ( $statuses as $status ) {

			$count = isset( $counts->$status ) ? (int) $counts->$status : 0;

			if ( 0 === $count ) {
				continue;
			}

			$status_object = get_post_status_object( $status );

			if ( ! $status_object || empty( $status_object->label_count ) ) {
				continue;
			}

			$status_label = sprintf(
				wp_strip_all_tags(
					translate_nooped_plural(
						$status_object->label_count,
						$count
					)
				),
				number_format_i18n( $count )
			);

			$label = sprintf(
				/* translators: 1: Post type plural label, 2: Post status and count. */
				__( '%1$s – %2$s', 'lsdc' ),
				$post_type_object->labels->name,
				$status_label
			);

			$url = add_query_arg(
				'post_status',
				$status,
				admin_url(
					'edit.php?post_type=' . $post_type
				)
			);

			$items[] = sprintf(
				'<a class="post-count" href="%1$s">%2$s</a>',
				esc_url( $url ),
				esc_html( $label )
			);
		}
	}

	return $items;
}
add_filter( 'dashboard_glance_items', 'lsdc_add_post_status_glance_items' );
