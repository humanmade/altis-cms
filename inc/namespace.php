<?php
/**
 * Altis CMS.
 *
 * @package altis/cms
 */

namespace Altis\CMS;

use Altis;
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
	Network_UI\bootstrap();
	Real_GUIDs\bootstrap();
	Signup_Notification\bootstrap();

	if ( $config['branding'] ) {
		Branding\bootstrap();
	}

	if ( is_bool( $config['large-network'] ) ) {
		add_filter( 'wp_is_large_network', function () use ( $config ) {
			return $config['large-network'];
		} );

		if ( $config['large-network'] === true ) {
			// Display user pagination in network/users.php even if the network is considered as large.
			add_filter( 'users_list_table_query_args', function( $args ) {
				$args['count_total'] = true;
				return $args;
			} );
		}
	}

	if ( $config['login-logo'] ) {
		add_action( 'login_header', __NAMESPACE__ . '\\add_login_logo' );
	}

	if ( ! empty( $config['favicon'] ) ) {
		add_filter( 'get_site_icon_url', __NAMESPACE__ . '\\filter_favicon' );
	}

	// Backwards compat for `shared-blocks` option.
	if ( isset( $config['shared-blocks'] ) ) {
		$config['reusable-blocks'] = $config['shared-blocks'];
	}
	if ( $config['reusable-blocks'] ) {
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

	// Ensure new themes are network enabled by default.
	add_filter( 'site_option_allowedthemes', __NAMESPACE__ . '\\network_enable_themes_by_default' );
	add_filter( 'pre_update_site_option_allowedthemes', __NAMESPACE__ . '\\network_enable_themes_by_default_on_update', 10, 2 );
	// Filter out themes set to false when checking what's allowed.
	add_filter( 'network_allowed_themes', 'array_filter' );

	add_action( 'muplugins_loaded', __NAMESPACE__ . '\\load_muplugins', 1 );

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

	// Setup signups db tables on migrate.
	add_action( 'altis.migrate', __NAMESPACE__ . '\\setup_user_signups_on_migrate' );

	// Fix network admin site actions.
	add_filter( 'network_admin_url', __NAMESPACE__ . '\\fix_network_action_confirmation' );

	// Fix user signups admin links.
	add_filter( 'wp_signups_admin_url', __NAMESPACE__ . '\\fix_user_signups_action_links', 10, 3 );

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

	// Delete signups object cache before we load the signups page.
	add_action( 'after_signup_user', __NAMESPACE__ . '\\clear_signups_cache' );

	// Fix redirect canonical redirecting on equivalent query strings.
	add_filter( 'redirect_canonical', __NAMESPACE__ . '\\maybe_redirect', 11, 2 );

	// Handle incorrect asset loader URLs.
	add_filter( 'content_url', __NAMESPACE__ . '\\handle_asset_loader_urls', 10, 2 );
}

/**
 * Adds `_wp_http_referer` to confirm action links in the network admin.
 *
 * @see https://core.trac.wordpress.org/ticket/52378
 *
 * @param string $url The complete network admin URL including scheme and path.
 * @return string The complete network admin URL including scheme and path.
 */
function fix_network_action_confirmation( string $url ) : string {
	parse_str( (string) wp_parse_url( $url, PHP_URL_QUERY ), $params );
	if ( isset( $params['action'] ) && $params['action'] === 'confirm' ) {
		$url = add_query_arg( [
			'_wp_http_referer' => urlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) ),
		], $url );
	}
	return $url;
}

/**
 * Filter the WP Signups admin URLs to prevent endless redirects.
 *
 * The action handling logic in WP Signups relies on the value of `wp_get_referer()` and so
 * does not work in cloud environments.
 *
 * @param string $url The full URL.
 * @param string $admin_url The base admin URL.
 * @param array $args The page query arguments.
 * @return string
 */
function fix_user_signups_action_links( string $url, string $admin_url, array $args ) : string {
	if ( isset( $args['action'] ) || isset( $args['bulk_action'] ) || isset( $args['bulk_action2'] ) ) {
		$url = add_query_arg( [
			'_wp_http_referer' => urlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) ),
		], $url );
	}
	return $url;
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
		if ( is_array( $url ) ) {
			if ( ! isset( $url['href'] ) ) {
				continue;
			}

			$url = $url['href'];
		}
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
			background-image: url('<?php echo esc_url( get_site_url( get_main_site_id( get_main_network_id() ), $logo ) ); ?>');
			background-size: contain;
			width: auto;
		}
	</style>
	<?php
}

/**
 * Filter to set the favicon url.
 *
 * @param string $url Filters the site icon URL.
 * @return string $url The URL for the favicon.
 */
function filter_favicon( string $url ) {
	if ( empty( $url ) ) {
		$favicon = Altis\get_config()['modules']['cms']['favicon'];
		$url = Altis\Cloud\get_main_site_url( $favicon );
	}

	return $url;
}

/**
 * Load required plugins.
 */
function load_muplugins() {
	require_once Altis\ROOT_DIR . '/vendor/humanmade/asset-loader/asset-loader.php';
}

/**
 * Load plugins that are bundled with the CMS module.
 */
function load_plugins() {
	if ( defined( 'WP_INITIAL_INSTALL' ) && WP_INITIAL_INSTALL ) {
		return;
	}

	require_once Altis\ROOT_DIR . '/vendor/stuttter/wp-user-signups/wp-user-signups.php';

	$config = Altis\get_config()['modules']['cms'];

	if ( $config['local-avatars'] ) {
		require_once Altis\ROOT_DIR . '/vendor/10up/simple-local-avatars/simple-local-avatars.php';

		// Hide the User Profile Picture field if local avatars is active. Replaced by the Avatar field on the same page.
		add_action( 'admin_head', function() {
			echo '<style>
				.wp-admin tr.user-profile-picture { display: none; }
			</style>';
		} );
	}
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
	 * The current admin page script name.
	 *
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
function setup_user_signups_on_migrate() {
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
 * Get the absolute path of a file without resolving symlinks.
 *
 * Source: https://www.php.net/manual/en/function.realpath.php#84012
 *
 * @param string $path The file path to resolve.
 * @return string
 */
function get_absolute_path( string $path ) : string {
	$path = str_replace( [ '/', '\\' ], DIRECTORY_SEPARATOR, $path );
	$parts = array_filter( explode( DIRECTORY_SEPARATOR, $path ), 'strlen' );
	$absolutes = [];
	foreach ( $parts as $part ) {
		if ( $part === '.' ) {
			continue;
		}
		if ( $part === '..' ) {
			array_pop( $absolutes );
		} else {
			$absolutes[] = $part;
		}
	}
	return DIRECTORY_SEPARATOR . implode( DIRECTORY_SEPARATOR, $absolutes );
}

/**
 * Ensure URLs do not contain any relative paths.
 *
 * @param string|null $url The dependency URL.
 * @param string $handle The dependency handle.
 * @return string|null
 */
function real_url_path( ?string $url, string $handle ) : ?string {
	global $wp_scripts, $wp_styles;

	// Avoid odd behaviour if null or empty value is passed.
	if ( empty( $url ) ) {
		return $url;
	}

	// Skip if there are no /./ or /../ patterns.
	if ( strpos( $url, '/.' ) === false ) {
		return $url;
	}

	// Show a warning about using bad asset URL practices when in debug mode.
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		trigger_error( sprintf( 'Asset URLs should not contain relative paths. Handle: %s, URL: %s', $handle, $url ), E_USER_WARNING );
	}

	// Note we don't use realpath here to avoid symlink resolution as it breaks
	// local dev and any other use of symlink composer repositories.
	$path = wp_parse_url( $url, PHP_URL_PATH );
	$real_path = get_absolute_path( $path );
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

/**
 * Check content_url filtered paths for the Altis Root directory path and strip it if found.
 *
 * @param string|null $url The asset URL.
 * @param string $path The absolute path to the asset.
 * @return string|null
 */
function handle_asset_loader_urls( ?string $url, string $path ) : ?string {
	if ( strpos( $url, Altis\ROOT_DIR ) === false ) {
		return $url;
	}
	return str_replace( Altis\ROOT_DIR, dirname( WP_CONTENT_URL ), $path );
}

/**
 * Clear the signups cache.
 *
 * This function updates the last changed value on the signups cache group
 * to ensure new signups are shown in the admin.
 */
function clear_signups_cache() {
	wp_cache_set( 'last_changed', microtime(), 'signups' );
}

/**
 * Determines whether a redirect should actucally occur.
 *
 * In some instances redirect_canonical() will rebuild the query string which can change
 * its formatting by removing trailing = signs and URL encoding keys. This can result in
 * a redirect that will generate the same Batcache key. Once the redirect is cached then
 * Batcache will start to redirect endlessly. This filter prevents that behaviour.
 *
 * @param string $redirect_url The URL being redirected to.
 * @param string $requested_url The original URL requested.
 * @return string The filtered redirect target URL.
 */
function maybe_redirect( $redirect_url, $requested_url ) {
	$redirect_query_string = parse_url( $redirect_url, PHP_URL_QUERY );
	$requested_query_string = parse_url( $requested_url, PHP_URL_QUERY );

	// Check we have query strings on both request and redirect.
	if ( empty( $redirect_query_string ) || empty( $requested_query_string ) ) {
		return $redirect_url;
	}

	// If the the base URLs are different then perform the redirect.
	if ( substr( $redirect_url, 0, strpos( $redirect_url, '?' ) ) !== substr( $requested_url, 0, strpos( $requested_url, '?' ) ) ) {
		return $redirect_url;
	}

	// Get the query strings being redirected to as an array.
	parse_str( $redirect_query_string, $redirect_query );
	parse_str( $requested_query_string, $requested_query );

	// Ensure query keys are sorted the same way.
	ksort( $redirect_query );
	ksort( $requested_query );

	// If the parsed query strings do not match then perform the redirect.
	if ( serialize( $redirect_query ) !== serialize( $requested_query ) ) {
		return $redirect_url;
	}

	// Prevent unecessary redirect from occuring and getting cached by returning the original URL.
	return $requested_url;
}

/**
 * Ensure all themes have a value in the allowedthemes option value.
 *
 * @param array $allowed_themes Currently allowed themes.
 * @return array
 */
function network_enable_themes_by_default( $allowed_themes = [] ) {
	$themes = array_keys( wp_get_themes() );
	foreach ( $themes as $stylesheet ) {
		// Check if the theme is present in the network option value, then add them and set to true.
		if ( ! isset( $allowed_themes[ $stylesheet ] ) ) {
			$allowed_themes[ $stylesheet ] = true;
		}
	}

	return $allowed_themes;
}

/**
 * Enable themes by default for the network, and respect manual
 * settings there after.
 *
 * @param array $allowed_themes Currently allowed themes.
 * @param array $old_allowed_themes Previous value.
 * @return array
 */
function network_enable_themes_by_default_on_update( $allowed_themes, $old_allowed_themes = [] ) {
	// Something isn't right here.
	if ( ! is_array( $allowed_themes ) || ! is_array( $old_allowed_themes ) ) {
		return $allowed_themes;
	}

	// Get all available stylesheet names.
	$themes = array_keys( wp_get_themes() );

	foreach ( $themes as $stylesheet ) {
		// Check the old value for missing themes, then add them and set to true.
		if ( ! isset( $old_allowed_themes[ $stylesheet ] ) && ! isset( $allowed_themes[ $stylesheet ] ) ) {
			$allowed_themes[ $stylesheet ] = true;
		}
		// Values are removed if a theme is disabled, and added if enabled.
		$allowed_themes[ $stylesheet ] = isset( $allowed_themes[ $stylesheet ] ) ? $allowed_themes[ $stylesheet ] : false;
	}

	return $allowed_themes;
}
