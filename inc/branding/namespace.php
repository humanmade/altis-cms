<?php
/**
 * Altis CMS Branding.
 *
 * @package altis/cms
 */

namespace Altis\CMS\Branding;

use Altis;
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
	add_action( 'admin_bar_init', __NAMESPACE__ . '\\enqueue_admin_scripts' );
	add_action( 'admin_bar_menu', __NAMESPACE__ . '\\admin_bar_menu' );
	add_filter( 'admin_footer_text', '__return_empty_string' );
	add_action( 'wp_network_dashboard_setup', __NAMESPACE__ . '\\remove_dashboard_widgets' );
	add_action( 'wp_user_dashboard_setup', __NAMESPACE__ . '\\remove_dashboard_widgets' );
	add_action( 'wp_dashboard_setup', __NAMESPACE__ . '\\remove_dashboard_widgets' );
	add_action( 'admin_init', __NAMESPACE__ . '\\add_color_scheme' );
	add_action( 'admin_init', __NAMESPACE__ . '\\remove_wp_admin_color_schemes' );
	add_action( 'personal_options', __NAMESPACE__ . '\\add_default_color_scheme_input' );
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
	add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\\enqueue_block_editor_branding_assets' );
	add_action( 'do_faviconico', __NAMESPACE__ . '\\override_default_favicon' );
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
 * Remove all unused default WP admin color schemes for a user.
 *
 * If a user has previously chosen one of the WP default admin color schemes,
 * leave that scheme as is. Remove all other WP default admin color schemes,
 * so that they can't be selected.
 */
function remove_wp_admin_color_schemes() {
	global $_wp_admin_css_colors;

	// List of WP default admin colour schemes.
	$admin_color_schemes = [
		'fresh',
		'light',
		'blue',
		'midnight',
		'sunrise',
		'ectoplasm',
		'ocean',
		'coffee',
	];

	$user_admin_color = get_user_option( 'admin_color', get_current_user_id() );

	// Remove all WP default admin color schemes, except the one that's selected for a user if any.
	foreach ( $admin_color_schemes as $color_slug ) {
		if ( $color_slug !== $user_admin_color ) {
			unset( $_wp_admin_css_colors[ $color_slug ] );
		}
	}
}

/**
 * Enqueue the branding scripts and styles
 */
function enqueue_admin_scripts() {
	global $wp_styles;

	// Ensure wp-components is always included before the Altis color
	// scheme to maintain cascade specificity.
	$wp_styles->registered['colors']->deps[] = 'wp-components';

	wp_enqueue_style( 'altis-branding', plugin_dir_url( dirname( __FILE__, 2 ) ) . 'assets/branding.css', [], '2020-06-22-1' );
}

/**
 * Override the default color scheme
 *
 * This is hooked into "get_user_option_admin_color" so we have to
 * make sure to return the value if it's already set.
 *
 * @param string|false $value Color scheme name.
 * @return string
 */
function override_default_color_scheme( $value ) : string {
	if ( $value ) {
		return $value;
	}

	return 'altis';
}

/**
 * Add hidden input for default color scheme.
 *
 * This ensures that if the user is on the Altis color scheme, the handler
 * doesn't accidentally wipe out the setting and default it back to "fresh".
 *
 * This is output at the top of the form so that the later radio control
 * overrides it.
 *
 * @return void Renders directly to the browser.
 */
function add_default_color_scheme_input() : void {
	$schemes = $GLOBALS['_wp_admin_css_colors'];

	// Replicate the UI check from wp-admin/user-edit.php
	if ( count( $schemes ) <= 1 || ! has_action( 'admin_color_scheme_picker' ) ) {
		echo '<input type="hidden" name="admin_color" value="altis" />';
	}
}

/**
 * Filter meta for new users to set admin_color to HM theme.
 *
 * @param array $meta The user meta array.
 * @param \WP_User $user The current user object.
 * @param bool $update Whether the meta data is being updated or created.
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
	$env = Altis\get_environment_type();
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

	wp_die( wp_kses_post( $message ), esc_html( $title ), [ 'response' => intval( WP_Http::NOT_FOUND ) ] );
}

/**
 * Add the Altis logo menu.
 *
 * @param WP_Admin_Bar $wp_admin_bar The admin bar manager class.
 */
function admin_bar_menu( WP_Admin_Bar $wp_admin_bar ) {
	$logo_menu_args = [
		'href'  => admin_url(),
		'id'    => 'altis',
		'title' => '<span class="altis-logo-wrapper"><img src="' . get_logo_url( 'white' ) . '" /></span>',
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
	printf( '<img class="altis-logo" alt="Altis" src="%s" />', esc_attr( get_logo_url( $variant ) ) );
}

/**
 * Override the admin title.
 *
 * WordPress puts a '> WordPress' after all the <title>.
 *
 * @param string $admin_title The admin area title tag text.
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
				gmdate( 'Y-m-d H:i' )
			);
	}
}

/**
 * Remove the 'Howdy <name>' greeting in the admin bar.
 *
 * @param WP_Admin_Bar $wp_admin_bar Admin bar object.
 */
function remove_howdy_greeting( WP_Admin_Bar $wp_admin_bar ) {
	$acct_bar = $wp_admin_bar->get_node( 'my-account' );

	$new_text = str_replace( 'Howdy,', 'Welcome,', $acct_bar->title );

	$wp_admin_bar->add_node( [
		'id' => 'my-account',
		'title' => $new_text,
	] );
}

/**
 * Enqueue branding script for the post previews.
 */
function enqueue_block_editor_branding_assets() {
	wp_enqueue_script(
		'altis-branding',
		plugin_dir_url( dirname( __FILE__, 2 ) ) . 'assets/branding.js',
		[
			'wp-element',
			'wp-editor',
			'wp-hooks',
		],
		false,
		true
	);

	$markup = '
		<div class="editor-post-preview-button__interstitial-message">
			<svg version="1.1" id="altis-svg-shape" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 243 226.8" xml:space="preserve">
				<path class="altis-shape" d="M22.05,38.81,220.44,3.86C218.63,8.77,159.21,169.94,155.18,181c-4.65,12.8-12.37,27.8-28.61,25.79A24.35,24.35,0,0,1,108,195.67a25.23,25.23,0,0,1-3.44-9.14c-2.78-15.74-19-97.66-61.12-134.4,0,0-12.11-10.52-21.4-13.32a40.15,40.15,0,0,0-7.84,3.09C-4.91,53.05,3.3,77.11,17.49,86,32,95,90.11,96.73,133.73,102.58s40.34,27.19,40.34,27.19l-2.51,6.8c-3.27,7.54-20.31,15.69-29.16-2.47S132.06,53.44,87.69,27.24"/>
			</svg>
			<div class="loading">' . esc_html__( 'Loading the future...', 'altis' ) . '</div>
		</div>
		<style>
			body {
				margin: 0;
			}

			.editor-post-preview-button__interstitial-message {
				display: flex;
				flex-direction: column;
				align-items: center;
				justify-content: center;
				height: 100vh;
				width: 100vw;
			}

			#altis-svg-shape {
				display: block;
				height: 130px;
				margin: 70px auto 20px;
				fill: transparent;
				stroke: #152A4E;
				stroke-width: 3px;
			}

			@-webkit-keyframes paint {
				0% {
					stroke-dashoffset: 0;
				}
			}
			@-moz-keyframes paint {
				0% {
					stroke-dashoffset: 0;
				}
			}
			@-o-keyframes paint {
				0% {
					stroke-dashoffset: 0;
				}
			}
			@keyframes paint {
				0% {
					stroke-dashoffset: 0;
				}
			}

			#altis-svg-shape .altis-shape {
				stroke-dasharray: 1200;
				stroke-dashoffset: 1200;
				-webkit-animation: paint 1.5s ease-in-out infinite alternate;
				-moz-animation: paint 1.5s ease-in-out infinite alternate;
				-o-animation: paint 1.5s ease-in-out infinite alternate;
				animation: paint 1.5s ease-in-out infinite alternate;
			}

			.loading {
				font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif;
				text-align: center;
				color: #152A4E;
			}
		</style>
	';

	wp_localize_script( 'altis-branding', 'altisPostPreview', [
		'markup' => $markup,
	] );
}

/**
 * Override the default favicon.
 *
 * By default, WordPress will send its own logo for favicon requests.
 * favicon.ico is blocked by our Cloud servers, but local installs will
 * incorrectly show the WordPress logo due to this fallback.
 *
 * This function replaces the fallback with a zero-byte icon file, which was
 * the fallback prior to WordPress 5.4. (Sites can continue to use the site
 * icon functionality to set their own icons instead.)
 */
function override_default_favicon() {
	header( 'Content-Type: image/vnd.microsoft.icon' );
	exit;
}
