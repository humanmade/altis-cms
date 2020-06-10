<?php
/**
 * Altis CMS.
 *
 * @package altis/cms
 */

namespace Altis\CMS;

use Altis;
use WP_CLI;
use WP_DB_Table_Signupmeta;
use WP_DB_Table_Signups;

/**
 * Main bootstrap / entry point for the CMS module.
 */
function bootstrap() {
	$config = Altis\get_config()['modules']['cms'];

	// Prevent web access to wp-admin/install.php.
	add_action( 'wp_loaded', __NAMESPACE__ . '\\prevent_web_install' );

	CLI\bootstrap();
	Remove_Updates\bootstrap();
	Permalinks\bootstrap();
	Add_Site_UI\bootstrap();

	if ( $config['branding'] ) {
		Branding\bootstrap();
	}

	if ( is_bool( $config['large-network'] ) ) {
		add_filter( 'wp_is_large_network', function () use ( $config ) {
			return $config['large-network'];
		} );
	}

	if ( $config['login-logo'] ) {
		add_action( 'login_header', __NAMESPACE__ . '\\add_login_logo' );
	}

	if ( $config['shared-blocks'] ) {
		Block_Editor\bootstrap();
	}

	if ( $config['xmlrpc'] === false ) {
		add_filter( 'xmlrpc_enabled', '__return_false' );
		add_filter( 'xmlrpc_methods', '__return_empty_array' );
		add_filter( 'xmlrpc_element_limit', __NAMESPACE__ . '\\filter_xmlrpc_element_limit_handler', 999 );
	}

	if ( $config['feeds'] === false ) {
		// Prevent feed links from being inserted in the <head> of the page.
		add_action( 'feed_links_show_posts_feed', '__return_false', -1 );
		add_action( 'feed_links_show_comments_feed', '__return_false', -1 );
		add_action( 'wp_head', function () {
			remove_action( 'wp_head', 'feed_links', 2 );
			remove_action( 'wp_head', 'feed_links_extra', 3 );
		}, 1 );

		// Show the 404 page on feed URLs.
		add_action( 'template_redirect', __NAMESPACE__ . '\\disable_feed_redirect' );
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

	// Hide Healthcheck UI.
	add_action( 'admin_menu', __NAMESPACE__ . '\\remove_site_healthcheck_admin_menu' );
	add_action( 'admin_init', __NAMESPACE__ . '\\disable_site_healthcheck_access' );
	add_action( 'wp_dashboard_setup', __NAMESPACE__ . '\\remove_site_healthcheck_dashboard_widget' );

	add_filter( 'login_headerurl', __NAMESPACE__ . '\\login_header_url' );

	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		WP_CLI::add_hook( 'after_invoke:core multisite-install', __NAMESPACE__ . '\\setup_user_signups_on_install' );
	}

	// Don't show the welcome panel.
	add_filter( 'get_user_metadata', __NAMESPACE__ . '\\hide_welcome_panel', 10, 3 );

	if ( $config['remove-emoji'] ) {
		add_action( 'plugins_loaded', __NAMESPACE__ . '\\remove_emoji' );
	}

	// Disable the admin_email verification interval.
	add_filter( 'admin_email_check_interval', '__return_zero' );

	/*
	 * Performance enhancements for comments as best practice.
	 * This is to prevent the CMS generating large HTML pages
	 * in cases where there may be 1000s of comments,
	 * as commenters will not see a cached version of the page.
	 */
	// Comment pagination is always enabled.
	add_filter( 'pre_option_page_comments', '__return_true' );

	// Force limit comments per page to 50 max.
	add_filter( 'pre_update_option_comments_per_page', __NAMESPACE__ . '\\set_comments_per_page' );

	/**
	 * Handle relative URLs in script & style tags.
	 */
	add_filter( 'script_loader_src', __NAMESPACE__ . '\\real_url_path', -10, 2 );
	add_filter( 'style_loader_src', __NAMESPACE__ . '\\real_url_path', -10, 2 );
}

/**
 * Show 404 template on feeds.
 */
function disable_feed_redirect() {
	global $wp_query;
	if ( ! is_feed() ) {
		return;
	}

	$wp_query->set_404();
	$wp_query->is_feed = false;
	status_header( 404 );

	// Ensure feed content type header is overridden.
	header( 'Content-type: text/html; charset=UTF-8' );
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

	// Strip out any URLs referencing the WordPress.org emoji location.
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
	$logo = Altis\get_config()['modules']['cms']['login-logo'];
	?>
	<style>
		.login h1 a {
			background-image: url('<?php echo esc_url( site_url( $logo ) ); ?>');
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
	require_once Altis\ROOT_DIR . '/vendor/stuttter/wp-user-signups/wp-user-signups.php';
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
 * Filters the number of elements to parse in an XML-RPC response.
 *
 * @param int $element_limit Default elements limit.
 *
 * @return int
 */
function filter_xmlrpc_element_limit_handler( int $element_limit ) : int {
	return 1;
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
 * Remove the healthcheck dashboard widget.
 *
 * @return void
 */
function remove_site_healthcheck_dashboard_widget() {
	remove_meta_box( 'dashboard_site_health', 'dashboard', 'normal' );
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

	return [ 0 ];
}

/**
 * Prevent direct web access to wp-admin/install.php.
 */
function prevent_web_install() {
	if ( $_SERVER['REQUEST_URI'] !== '/wp-admin/install.php' ) {
		return;
	}

	// Return 200 status for healthcheck.
	status_header( 200 );
	echo 'This site is currently unavailable';
	exit;
}

/**
 * Set comments per page to be 50 max as best practice.
 *
 * This is to prevent the CMS generating large HTML pages
 * in cases where there may be 1000s of comments,
 * as commenters will not see a cached version of the page.
 *
 * @param mixed $value Option value for the 'comments_per_page' option,
 *                     it's set in WP Admin under Settings -> Discussion -> Other comment settings.
 *
 * @return int Number of comments per page.
 */
function set_comments_per_page( $value ) : int {
	$value = intval( $value );
	return $value <= 50 ? $value : 50;
}

/**
 * Ensure URLs do not contain any relative paths.
 *
 * @param string $url The dependency URL.
 * @param string $handle The dependency handle.
 * @return string
 */
function real_url_path( string $url, string $handle ) : string {
	global $wp_scripts, $wp_styles;

	// Skip if there are no /./ or /../ patterns.
	if ( strpos( $url, '/.' ) === false ) {
		return $url;
	}

	// Show a warning about using bad asset URL practices when in debug mode.
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		trigger_error( sprintf( 'Asset URLs should not contain relative paths. Handle: %s, URL: %s', $handle, $url ), E_USER_WARNING );
	}

	$path = wp_parse_url( $url, PHP_URL_PATH );
	$path_parts = explode( '/', $path );

	foreach ( $path_parts as $index => $part ) {
		// Remove if current directory indicator.
		if ( $part === '.' ) {
			unset( $path_parts[ $index ] );
		}
		// Remove parent directory placeholder if present and the item before it.
		if ( $part === '..' ) {
			unset( $path_parts[ $index ] );
			if ( isset( $path_parts[ $index - 1 ] ) ) {
				unset( $path_parts[ $index - 1 ] );
			}
		}
	}

	$real_path = implode( '/', $path_parts );
	$url = str_replace( $path, $real_path, $url );

	// Get & update the dependency object if available.
	if ( pathinfo( $path, PATHINFO_EXTENSION ) === 'js' ) {
		$asset = $wp_scripts->query( $handle );
	} else {
		$asset = $wp_styles->query( $handle );
	}
	if ( $asset ) {
		$asset->src = $url;
	}

	return $url;
}
