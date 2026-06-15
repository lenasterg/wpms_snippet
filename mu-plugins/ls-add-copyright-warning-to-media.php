<?php
/**
 * Plugin Name: LS Media Upload Copyright Warning
 * Description: Displays a copyright warning notice on the WordPress media upload interface.
 * Version:     1.0.0
 * Author:      lenasterg
 * License:     GPL-2.0-or-later
 * Text Domain: ls-media-upload-copyright-warning
 * Domain Path: /languages
 *
 * @package WordPress
 * @subpackage MU-Plugins
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Loads plugin translations.
 *
 * @return void
 */
function ls_media_upload_copyright_warning_load_textdomain() {
	load_muplugin_textdomain(
		'ls-media-upload-copyright-warning',
		dirname( plugin_basename( __FILE__ ) ) . '/languages'
	);
}
add_action( 'muplugins_loaded', 'ls_media_upload_copyright_warning_load_textdomain' );

/**
 * Outputs a copyright warning notice above the media upload interface.
 *
 * @return void
 */
function ls_add_copyright_warning_to_media() {
	?>
	<div class="notice notice-warning inline">
		<p>
			<span class="dashicons dashicons-shield" aria-hidden="true"></span>
			<strong>
				<?php esc_html_e( 'Copyright Notice:', 'ls-media-upload-copyright-warning' ); ?>
			</strong>
			<?php
			esc_html_e(
				'Before uploading, make sure you have the necessary rights to use the content.',
				'ls-media-upload-copyright-warning'
			);
			?>
		</p>
	</div>
	<?php
}
add_action( 'pre-upload-ui', 'ls_add_copyright_warning_to_media' );
