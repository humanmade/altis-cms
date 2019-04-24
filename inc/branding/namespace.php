<?php

namespace HM\Platform\CMS\Branding;

use function HM\Platform\get_environment_type;
use WP_Admin_Bar;
use WP_Http;
use WP_Theme;

const COLOR_BLUE = '#4667de';
const COLOR_DARKBLUE = '#152a4e';
const COLOR_GREEN = '#3fcf8e';
const COLOR_OFFWHITE = '#f3f5f9';
const LOGO = <<<END
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 547.56 320.92001" height="14.06" width="24">
	<defs>
		<clipPath id="a"><path d="M0 2406.94V0h4106.71v2406.94z"/></clipPath>
	</defs>
	<g clip-path="url(#a)" transform="matrix(.13333 0 0 -.13333 0 320.92)">
		<path className="hm-logo-path" d="M399.133 272.191c-77.379 0-133.321 68.457-133.321 208.329 0 236.82 193.567 438.269 399.286 553.95-13.536-146.482-33.903-304.072-48.305-400.892-26.637-179.039-89.684-361.387-217.66-361.387zM4043.11 673.539c-45.31-25.68-93.5-41.5-143.94-41.5-114.67 0-180.79 57.723-208.23 151.09-55.66 189.371-3.75 532.581 15.39 757.111 25.88 303.7 38.23 422.92 43.17 486.45 11.58 148.25-46.35 313.24-214.98 312.08-156.74-1.09-361.16-189.03-451.47-378.17 22.01 242.25-47.85 447.62-232.36 446.33-184.51-1.26-373.5-275.42-431.17-393.38 11.68 110.21 18.04 156.43 23.08 215.99 5.02 59.56 3.01 102.64-59.26 108.73-37.75 3.7-111.42-9.32-153.98-16.55-54.51-9.27-72.19-48.17-82.25-132.52-7.24-60.9-26.28-302.26-45.11-534.81-42.76-3.43-112.77-16.97-154.67-22.81-78.71-10.97-129-19.66-205.93-33.34 25.97 271.86 64.04 562.01 77.18 668.41 7.95 64.36 1.8 104.76-64.88 112.68-37.89 4.49-152.66-11.97-188.36-17.83-58.73-9.63-81.1-59.68-88.54-98.7-30.08-157.71-58.72-442.92-77.96-731.99-125.97-30.83-250.6-66.36-374.78-107.33 12.36 224.59 20.49 475.12 12.76 598.7-11.14 178.23-113.093 295.23-269.84 295.23-166.992 0-359.718-89.76-480.652-191.95-79.078-66.81-123.558-144.16-123.558-205.37 0-62.49 31.296-119.08 113.953-119.08 63.957 87.44 231.336 240.85 324.804 240.85 84.102 0 105.563-73 105.563-147.41 0-61.2-12.348-343.84-24.32-582.72C362.012 1168.4 0 895.09 0 467.52 0 190.73 168.969 9.07 411.109 9.07c316.782 0 482.852 286.942 534.715 600.59 26.563 160.711 46.969 369.231 60.886 549.67 1.43.48 2.88.98 4.33 1.46 123.1 40.88 247.46 78.78 372.92 112.55-18.97-362.481-28.78-690.981 68.3-929.121 82.01-201.18 256-346.25 528.7-344.2 187.5 1.403 362.25 76.7 440.51 147.559 26.09 23.621 36.89 56.293 36.03 78.594-1.77 46.09-30.99 83.668-69.7 109.019-77.52-44.089-163.44-81.25-285.35-85.921-121.93-4.668-287.58 61.832-355.64 284.582-58.73 192.128-41.64 570.618-25.61 813.798 118.2 23.4 237.29 46.97 357.14 59.93l3.75 5.72c-1.5-17.65-2.96-34.72-4.39-51.04-23.57-270.02-54.71-688.94-53.42-772.26 1-63.488 33.15-89.078 80.76-88.738 47.62.316 132.68 16.859 162.29 26.988 24.28 8.32 43.67 19.379 56.91 40.082 11.94 18.707 18.26 46.727 20.51 83.477 6.08 99.281 22.42 343.441 37.85 498.311 15.45 154.88 30.76 289.72 58.69 409.19 56.25 240.57 171.74 501.21 276.9 501.94 67.46.46 69.65-126.57 50.56-301.32-19.1-174.76-70.62-814.071-69.43-891.442 1.06-67.457 20.98-103.047 78.99-102.636 47.61.32 137.55 7.089 173.06 21.238 22.35 8.89 38.93 17.148 50.37 37.129 10.06 17.582 16.14 44.222 18.62 88.34 5.68 101.471 42.56 511.011 74.8 640.461 61.51 246.86 190.2 411.94 261.62 412.43 71.43.49 49.2-175.27 30.51-375.85-36.11-387.21-72.97-791.209 23.96-988.791 57.33-116.879 160.53-226.571 405.28-226.571 91.7 0 197.22 36.883 253.65 92.391 25.53 25.121 37.54 69.051 36.47 92.152-2.17 47.731-24.22 88.481-63.54 114.758" fill="#fff"/>
	</g>
</svg>
END;
/**
 * Bootstrap the branding.
 */
function bootstrap() {
	add_action( 'add_admin_bar_menus', __NAMESPACE__ . '\\remove_wordpress_admin_bar_item' );
	add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\enqueue_admin_scripts' );
	add_action( 'admin_bar_menu', __NAMESPACE__ . '\\admin_bar_menu' );
	add_filter( 'admin_footer_text', '__return_empty_string' );
	add_action( 'wp_network_dashboard_setup', __NAMESPACE__ . '\\remove_dashboard_widgets' );
	add_action( 'wp_user_dashboard_setup', __NAMESPACE__ . '\\remove_dashboard_widgets' );
	add_action( 'wp_dashboard_setup', __NAMESPACE__ . '\\remove_dashboard_widgets' );
	add_action( 'admin_init', __NAMESPACE__ . '\\add_color_scheme' );
	add_filter( 'get_user_option_admin_color', __NAMESPACE__ . '\\override_default_color_scheme' );
	add_action( 'template_redirect', __NAMESPACE__ . '\\detect_missing_default_theme' );
	add_filter( 'admin_title', __NAMESPACE__ . '\\override_admin_title' );
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

/**
 * Add the Platform color scheme to the user options.
 */
function add_color_scheme() {
	wp_admin_css_color(
		'platform',
		__( 'Platform', 'hm-platform' ),
		plugin_dir_url( dirname( __FILE__, 2 ) ) . '/assets/admin-color-scheme.css',
		[
			COLOR_BLUE,
			COLOR_DARKBLUE,
			COLOR_GREEN,
			COLOR_OFFWHITE,
		],
		[
			'base' => '#e5f8ff',
			'focus' => 'white',
			'current' => 'white',
		]
	);
}

/**
 * Enqueue the branding scripts and styles
 */
function enqueue_admin_scripts() {
	wp_enqueue_style( 'hm-platform-branding', plugin_dir_url( dirname( __FILE__, 2 ) ) . '/assets/branding.css', [], '2019-04-24-1' );
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


/**
 * Add the Platform logo menu.
 *
 *
 * @param WP_Admin_Bar $wp_admin_bar
 */
function admin_bar_menu( WP_Admin_Bar $wp_admin_bar ) {
	$logo_menu_args = [
		'id'    => 'hm-platform',
		'title' => '<span class="icon">' . LOGO . '</span>',
	];

	// Set tabindex="0" to make sub menus accessible when no URL is available.
	$logo_menu_args['meta'] = [
		'tabindex' => 0,
	];

	$wp_admin_bar->add_menu( $logo_menu_args );
}

/**
 * Override the admin title.
 *
 * WordPress puts a '> WordPress' after all the <title>.
 *
 * @param string $admin_title
 * @return string
 */
function override_admin_title( string $admin_title ) : string {
	return str_replace( ' &#8212; WordPress', ' &#8212; HM Platform', $admin_title );
}
