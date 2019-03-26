<?php

namespace HM\Platform\CMS\Branding;

use function HM\Platform\get_environment_type;
use WP_Http;
use WP_Theme;

function bootstrap() {
	add_action( 'add_admin_bar_menus', __NAMESPACE__ . '\\remove_wordpress_admin_bar_item' );
	add_filter( 'admin_footer_text', '__return_empty_string' );
	add_action( 'wp_network_dashboard_setup', __NAMESPACE__ . '\\remove_dashboard_widgets' );
	add_action( 'wp_user_dashboard_setup', __NAMESPACE__ . '\\remove_dashboard_widgets' );
	add_action( 'wp_dashboard_setup', __NAMESPACE__ . '\\remove_dashboard_widgets' );
	add_action( 'admin_init', __NAMESPACE__ . '\\add_color_scheme' );
	add_filter( 'get_user_option_admin_color', __NAMESPACE__ . '\\override_default_color_scheme' );
	add_action( 'template_redirect', __NAMESPACE__ . '\\detect_missing_default_theme' );
}

/**
 * Remove the WordPress logo admin menu bar item.
 */
function remove_wordpress_admin_bar_item() {
	remove_action( 'admin_bar_menu', 'wp_admin_bar_wp_menu' );
}

/**
 * Remove dashboard widgets that are not useful.
 */
function remove_dashboard_widgets() {
	remove_meta_box( 'dashboard_primary', [ 'dashboard', 'dashboard-network', 'dashboard-user' ], 'side' );
}

function add_color_scheme() {
	wp_admin_css_color(
		'platform',
		__( 'Platform', 'hm-platform' ),
		plugin_dir_url( dirname( __FILE__, 2 ) ) . '/assets/admin-color-scheme.css',
		[
			'#152354',
			'#14568A',
			'#D54E21',
			'#2683AE',
		],
		[
			'base' => '#152354',
			'focus' => '#fff',
			'current' => '#fff',
		]
	);
}

/**
 * Override the default color scheme
 *
 * This is hooked into "get_user_option_admin_color" so we have to
 * make sure to return the value if it's already set.
 *
 * @param string|false $value
 * @return string
 */
function override_default_color_scheme( $value ) : string {
	if ( $value ) {
		return $value;
	}

	return 'platform';
}

/**
 * Detect a missing default theme.
 *
 * If the theme is still the default, and it's missing, we can show them a
 * custom splash page.
 */
function detect_missing_default_theme() {
	$env = get_environment_type();
	if ( ! in_array( $env, [ 'development', 'local' ], true ) ) {
		return;
	}

	// Only activate if the theme is missing.
	$theme = wp_get_theme();
	if ( $theme->exists() ) {
		return;
	}

	// Check that we're using the default theme.
	if ( $theme->get_stylesheet() !== WP_DEFAULT_THEME || WP_Theme::get_core_default_theme() !== false ) {
		return;
	}

	// No theme, load default helper.
	$title = __( 'Welcome to HM Platform', 'hm-platform' );
	$message = sprintf(
		'<h1>%s</h1><p>%s</p><p><small>%s</small></p>',
		$title,
		sprintf(
			__( 'HM Platform is installed and ready to go. <a href="%s">Activate a theme to get started</a>.', 'hm-platform' ),
			admin_url( 'themes.php' )
		),
		__( 'You‘re seeing this page because debug mode is enabled, and the default theme directory is missing.', 'hm-platform' )
	);

	wp_die( $message, $title, [ 'response' => WP_Http::NOT_FOUND ] );
}
