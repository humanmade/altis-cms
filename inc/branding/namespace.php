<?php

namespace Altis\CMS\Branding;

use function Altis\get_environment_type;
use WP_Admin_Bar;
use WP_Http;
use WP_Theme;

const COLOR_BLUE = '#4667de';
const COLOR_DARKBLUE = '#152a4e';
const COLOR_GREEN = '#3fcf8e';
const COLOR_OFFWHITE = '#f3f5f9';

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
	add_filter( 'insert_user_meta', __NAMESPACE__ . '\\insert_user_meta', 10, 3 );
	add_filter( 'login_title', __NAMESPACE__ . '\\wordpress_to_altis' );
	add_filter( 'login_headertext', __NAMESPACE__ . '\\wordpress_to_altis' );
	add_filter( 'get_the_generator_html', __NAMESPACE__ . '\\override_generator', 10, 2 );
	add_filter( 'get_the_generator_xhtml', __NAMESPACE__ . '\\override_generator', 10, 2 );
	add_filter( 'get_the_generator_atom', __NAMESPACE__ . '\\override_generator', 10, 2 );
	add_filter( 'get_the_generator_rss2', __NAMESPACE__ . '\\override_generator', 10, 2 );
	add_filter( 'get_the_generator_comment', __NAMESPACE__ . '\\override_generator', 10, 2 );
	add_filter( 'get_the_generator_export', __NAMESPACE__ . '\\override_generator', 10, 2 );
	add_filter( 'admin_bar_menu', __NAMESPACE__ . '\\remove_howdy_greeting', 25 );
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
 * Add the Altis color scheme to the user options.
 */
function add_color_scheme() {
	wp_admin_css_color(
		'altis',
		__( 'Altis', 'altis' ),
		add_query_arg( 'version', '2019-04-25-1', plugin_dir_url( dirname( __FILE__, 2 ) ) . 'assets/admin-color-scheme.css' ),
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
 *
 * @param string $hook
 */
function enqueue_admin_scripts( string $hook ) {

	wp_enqueue_style( 'altis-branding', plugin_dir_url( dirname( __FILE__, 2 ) ) . 'assets/branding.css', [], '2019-04-24-1' );

	if ( $hook === 'sites_page_altis-add-site' ) {
		wp_enqueue_script( 'customize-settings', plugin_dir_url( dirname( __FILE__, 2 ) ) . 'assets/customize-settings.js', [], false, true );
	}
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

	return 'altis';
}

/**
 * Filter meta for new users to set admin_color to HM theme.
 *
 * @param array    $meta
 * @param \WP_User $user
 * @param bool     $update
 * @return array
 */
function insert_user_meta( array $meta, $user, $update ) : array {
	if ( $update ) {
		return $meta;
	}

	$meta['admin_color'] = 'altis';

	return $meta;
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
	$title = __( 'Welcome to Altis', 'altis' );
	$message = sprintf(
		'<h1>%s</h1><p>%s</p><p><small>%s</small></p>',
		$title,
		sprintf(
			/* translators: %s: URL for the themes page */
			__( 'Altis is installed and ready to go. <a href="%s">Activate a theme to get started</a>.', 'altis' ),
			admin_url( 'themes.php' )
		),
		__( 'You‘re seeing this page because debug mode is enabled, and the default theme directory is missing.', 'altis' )
	);

	wp_die( $message, $title, [ 'response' => WP_Http::NOT_FOUND ] );
}


/**
 * Add the Altis logo menu.
 *
 * @param WP_Admin_Bar $wp_admin_bar
 */
function admin_bar_menu( WP_Admin_Bar $wp_admin_bar ) {
	$logo_menu_args = [
		'id'    => 'altis',
		'title' => '<span class="icon"><img src="' . get_logo_url( 'white' ) . '" /></span>',
	];

	// Set tabindex="0" to make sub menus accessible when no URL is available.
	$logo_menu_args['meta'] = [
		'tabindex' => 0,
	];

	$wp_admin_bar->add_menu( $logo_menu_args );
}

/**
 * Get URL for the logo.
 *
 * @param string|null $variant Variant of the logo. One of 'white' or null.
 * @return string URL for the Altis logo.
 */
function get_logo_url( $variant = null ) {
	$file = $variant === 'white' ? 'logo-white.svg' : 'logo.svg';
	return sprintf( '%s/assets/%s', untrailingslashit( plugin_dir_url( dirname( __FILE__, 2 ) ) ), $file );
}

/**
 * Render the logo image.
 *
 * @param string|null $variant Variant of the logo. One of 'white' or null.
 * @return void Outputs the logo directly to the page.
 */
function render_logo( $variant = null ) {
	printf( '<img class="altis-logo" alt="Altis" src="%s" />', get_logo_url( $variant ) );
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
	return str_replace( ' &#8212; WordPress', ' &#8212; Altis', $admin_title );
}

/**
 * Replace WordPress with Altis in text.
 *
 * @param string $text The string to modify.
 * @return string
 */
function wordpress_to_altis( string $text ) : string {
	return str_replace( 'WordPress', 'Altis', $text );
}

/**
 * Override the various generator tags.
 *
 * @param string $gen Default HTML for the type.
 * @param string $type The type of generator. One of 'html', 'xhtml', 'atom', 'rss2', 'rdf', 'comment', 'export'.
 * @return string Overridden generator.
 */
function override_generator( string $gen, string $type ) : string {
	$wp_version = get_bloginfo( 'version' );
	$wp_version_rss = convert_chars( wp_strip_all_tags( $wp_version ) );
	switch ( $type ) {
		case 'html':
			return sprintf(
				'<meta name="generator" content="Altis (WordPress %s)">',
				esc_attr( $wp_version )
			);

		case 'xhtml':
			return sprintf(
				'<meta name="generator" content="Altis (WordPress %s)" />',
				esc_attr( $wp_version )
			);

		case 'atom':
			return sprintf(
				'<generator uri="https://www.altis-dxp.com/" version="%s">Altis</generator>',
				esc_attr( $wp_version_rss )
			);

		case 'rss2':
			return sprintf(
				'<generator>%s</generator>',
				esc_url_raw( add_query_arg( 'v', $wp_version_rss, 'https://www.altis-dxp.com/' ) )
			);

		case 'rdf':
			return sprintf(
				'<admin:generatorAgent rdf:resource="%s" />',
				esc_url_raw( add_query_arg( 'v', $wp_version_rss, 'https://www.altis-dxp.com/' ) )
			);

		case 'comment':
			return sprintf(
				'<!-- generator="Altis (WordPress/%s)" -->',
				$wp_version
			);

		case 'export':
			return sprintf(
				'<!-- generator="Altis (WordPress/%s)" created="%s" -->',
				$wp_version_rss,
				date( 'Y-m-d H:i' )
			);
	}
}

/**
 * Remove the 'Howdy <name>' greeting in the admin bar.
 *
 * @param WP_Admin_Bar Admin bar object.
 */
function remove_howdy_greeting( WP_Admin_Bar $wp_admin_bar ) {
	$acct_bar = $wp_admin_bar->get_node( 'my-account' );

	$new_text = str_replace( 'Howdy,', 'Welcome,', $acct_bar->title );

	$wp_admin_bar->add_node( [
		'id' => 'my-account',
		'title' => $new_text,
	] );
}
