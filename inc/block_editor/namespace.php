<?php
/**
 * Altis CMS Block Editor Additions.
 *
 * @package altis/cms
 */

namespace Altis\CMS\Block_Editor;

use Altis;

/**
 * Set up block editor modifications.
 */
function bootstrap() {
	add_action( 'plugins_loaded', __NAMESPACE__ . '\\load_plugins', 1 );
	add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\set_default_editor_preferences' );
}

/**
 * Load block editor related plugins.
 *
 * @return void
 */
function load_plugins() {
	require_once Altis\ROOT_DIR . '/vendor/humanmade/altis-reusable-blocks/plugin.php';
}

/**
 * Queue scripts for setting default block editor preferences in WP 5.4.
 *
 * Disables default fullscreen mode and welcome guide.
 */
function set_default_editor_preferences() {
	global $wp_scripts;

	wp_register_script(
		'altis-default-editor-settings',
		plugin_dir_url( dirname( __FILE__, 2 ) ) . 'assets/editor-settings.js',
		[],
		'2020-06-04-1',
		false
	);
	wp_localize_script(
		'altis-default-editor-settings',
		'altisDefaultEditorSettings',
		[
			'uid' => get_current_user_id(),
		]
	);

	// Add default settings as a dependency of wp-data.
	$wp_scripts->registered['wp-data']->deps[] = 'altis-default-editor-settings';
}
