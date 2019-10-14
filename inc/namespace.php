<?php

namespace Altis\CMS;

use const Altis\ROOT_DIR;
use function Altis\get_config;
use WP_CLI;
use WP_DB_Table_Signupmeta;
use WP_DB_Table_Signups;

/**
 * Main bootstrap / entry point for the CMS module.
 */
function bootstrap() {
	require_once ROOT_DIR . '/vendor/roots/wp-password-bcrypt/wp-password-bcrypt.php';

	$config = get_config()['modules']['cms'];

	Remove_Updates\bootstrap();
	Permalinks\bootstrap();

	if ( $config['branding'] ) {
		Branding\bootstrap();
	}

	if ( $config['login-logo'] ) {
		add_action( 'login_header', __NAMESPACE__ . '\\add_login_logo' );
	}

	if ( $config['shared-blocks'] ) {
		Block_Editor\bootstrap();
	}

	add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_plugins', 1 );

	if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) {
		define( 'DISALLOW_FILE_EDIT', true );
	}
	if ( ! defined( 'WP_DEFAULT_THEME' ) ) {
		define( 'WP_DEFAULT_THEME', $config['default-theme'] ?? 'base' );
	}

	add_filter( 'pre_site_option_fileupload_maxk', __NAMESPACE__ . '\\override_fileupload_maxk_option' );
	add_filter( 'wp_fatal_error_handler_enabled', __NAMESPACE__ . '\\filter_wp_fatal_handler' );
	add_action( 'admin_menu', __NAMESPACE__ . '\\remove_site_healthcheck_admin_menu' );
	add_action( 'admin_init', __NAMESPACE__ . '\\disable_site_healthcheck_access' );

	add_filter( 'login_headerurl', __NAMESPACE__ . '\\login_header_url' );

	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		WP_CLI::add_hook( 'after_invoke:core multisite-install', __NAMESPACE__ . '\\setup_user_signups_on_install' );
	}

	// Don't show the welcome panel.
	add_filter( 'get_user_metadata', __NAMESPACE__ . '\\hide_welcome_panel', 10, 3 );

	if ( $config['remove-emoji'] ) {
		add_action( 'plugins_loaded', __NAMESPACE__ . '\\remove_emoji' );
	}
}

/**
 * Remove all the emoji scripts that are included with WordPress.
 */
function remove_emoji() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	add_filter( 'wp_resource_hints', __NAMESPACE__ . '\\disable_emojis_remove_dns_prefetch', 10, 2 );
}

/**
 * Remove emoji DNS prefetching.
 *
 * @param array $urls URLs for resources.
 * @param string $relation_type Relation type.
 * @return array New array with resources.
 */
function disable_emojis_remove_dns_prefetch( array $urls, string $relation_type ) : array {
	if ( 'dns-prefetch' !== $relation_type ) {
		return $urls;
	}

	// Strip out any URLs referencing the WordPress.org emoji location
	$emoji_svg_url_bit = 'https://s.w.org/images/core/emoji/';
	foreach ( $urls as $key => $url ) {
		if ( strpos( $url, $emoji_svg_url_bit ) !== false ) {
			unset( $urls[ $key ] );
		}
	}
	return $urls;
}

/**
 * Add the custom login logo to the login page.
 */
function add_login_logo() {
	$logo = get_config()['modules']['cms']['login-logo'];
	?>
	<style>
		.login h1 a {
			background-image: url('<?php echo site_url( $logo ) ?>');
			background-size: contain;
			width: auto;
		}
	</style>
	<?php
}

/**
 * Load plugins that are bundled with the CMS module.
 */
function load_plugins() {
	require_once ROOT_DIR . '/vendor/stuttter/wp-user-signups/wp-user-signups.php';
}

/**
 * Increase the max upload size (in kb) to 1GB.
 *
 * @return integer
 */
function override_fileupload_maxk_option() : int {
	return 1024 * 1024;
}

/**
 * Disable the WP fatal error handler.
 *
 * This is intended mostly for non-technical users to receive error information.
 *
 * This is hooked into the `wp_fatal_error_handler_enabled` hook, where we cannot
 * just pass the `__return_false` function as that is not available so early in
 * the bootstrap process.
 *
 * @return boolean
 */
function filter_wp_fatal_handler() : bool {
	return false;
}

/**
 * Remove the Site Health link in the Tools menu
 */
function remove_site_healthcheck_admin_menu() {
	remove_submenu_page( 'tools.php', 'site-health.php' );
}

/**
 * Disable access to the site health check admin page.
 *
 * We have disables the site health check as it exposes a lot of details
 * and potential false positives, and ultimately is not useful for our
 * platform.
 *
 * @return void
 */
function disable_site_healthcheck_access() {
	/**
	 * @var string
	 */
	global $pagenow;
	if ( $pagenow !== 'site-health.php' ) {
		return;
	}

	wp_die( 'Site Health not accessible.' );
}

/**
 * When WordPress is installed via WP-CLI, run the user-signups setup.
 */
function setup_user_signups_on_install() {
	$signups = new WP_DB_Table_Signups();
	$signups->maybe_upgrade();

	$signups_meta = new WP_DB_Table_Signupmeta();
	$signups_meta->maybe_upgrade();
}

/**
 * Set the login header URL to the current site.
 * Defaults to wordpress.org for some reason.
 *
 * @return string
 */
function login_header_url() : string {
	return home_url( '/' );
}

/**
 * Filter the show welcome panel meta data to always be false.
 *
 * @param null $value The value to override.
 * @param int $user_id The user ID to get meta data for.
 * @param string $meta_key The meta key being requested.
 * @return mixed
 */
function hide_welcome_panel( $value, int $user_id, string $meta_key ) {
	if ( $meta_key !== 'show_welcome_panel' ) {
		return $value;
	}

	return 0;
}
