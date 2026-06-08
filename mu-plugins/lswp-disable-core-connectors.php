<?php
/**
 * Plugin Name:       LSWP Disable AI Connectors from multisite
 * Plugin URI:        https://github.com/lenasterg/wpms_snippet/
 * Description:       Disables the Connectors menu, restricts access to the connectors screen, and blocks AI/Connectors REST API endpoints across a multisite. 
 * Version:           1.0.0
 * Author:            lenasterg and AI
 * Author URI:        https://lenasterg.wordpress.com
 * License:           GPL-2.0+
 * Text Domain:       lswp-mu
 * Domain Path:       /languages
 * Network:           true
 * Requires at least: 7.0
 * Requires PHP:      7.4
 *
 * @package LSWP_MU
 */

// Enable strict types for modern PHP environments.
declare(strict_types=1);

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Initialize hooks only if the WordPress version is 7.0 or higher.
 * Uses 'plugins_loaded' to ensure the global $wp_version is fully initialized.
 *
 * @return void
 */
function lswp_init_core_connectors_disabler(): void {
	global $wp_version;

	// Fail-safe: If the version variable is not set or is less than 7.0, abort execution.
	if ( ! isset( $wp_version ) || version_compare( $wp_version, '7.0', '<' ) ) {
		return;
	}

	add_action( 'admin_menu', 'lswp_remove_core_connectors_menu', 999 );
	add_action( 'admin_init', 'lswp_disable_connectors_screen' );
	add_filter( 'rest_pre_dispatch', 'lswp_block_ai_rest_endpoints', 10, 3 );
}
add_action( 'plugins_loaded', 'lswp_init_core_connectors_disabler' );

/**
 * Removes the core Connectors submenu added in WordPress 7.0.
 *
 * @return void
 */
function lswp_remove_core_connectors_menu(): void {
	remove_submenu_page( 'options-general.php', 'options-connectors.php' );
}

/**
 * Block direct URL access to the core Connectors screen.
 *
 * @return void
 */
function lswp_disable_connectors_screen(): void {
	global $pagenow;

	if ( 'options-connectors.php' === $pagenow ) {
		wp_die(
			esc_html__( 'The Connectors screen has been disabled on this site.', 'lswp-mu' ),
			'',
			array( 'response' => 403 )
		);
	}
}

/**
 * Block AI and Connectors REST API endpoints introduced in WP 7.0.
 *
 * @param mixed           $result  Response to return, or WP_Error to block.
 * @param WP_REST_Server  $server  Server instance.
 * @param WP_REST_Request $request Request used to generate the response.
 * @return mixed|WP_Error
 */
function lswp_block_ai_rest_endpoints( $result, WP_REST_Server $server, WP_REST_Request $request ) {
	$route = (string) $request->get_route();

	if ( false !== strpos( $route, '/ai/' ) || false !== strpos( $route, '/connectors/' ) ) {
		return new WP_Error(
			'rest_forbidden',
			__( 'AI features are disabled.', 'lswp-mu' ),
			array( 'status' => 403 )
		);
	}

	return $result;
}
