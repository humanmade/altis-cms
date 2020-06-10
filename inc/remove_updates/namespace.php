<?php
/**
 * Altis CMS Update Checks.
 *
 * @package altis/cms
 */

namespace Altis\CMS\Remove_Updates;

/**
 * Boostrap setup to remove updates from the admin.
 */
function bootstrap() {
	add_action( 'admin_init', __NAMESPACE__ . '\\remove_maybe_update_checks', 1 );
	add_action( 'admin_init', __NAMESPACE__ . '\\remove_update_nag' );
	add_action( 'plugins_loaded', __NAMESPACE__ . '\\remove_update_check_cron' );
	add_filter( 'map_meta_cap', __NAMESPACE__ . '\\remove_update_capabilities', 10, 2 );
	remove_auto_updates();
}

/**
 * Remove the checks that call out to api.wordpress.org in the admin
 * to check for updates.
 *
 * This prevents WordPress from running blocking synchronous ajax requests
 * while loading the admin.
 */
function remove_maybe_update_checks() {
	remove_action( 'admin_init', '_maybe_update_core' );
	remove_action( 'admin_init', '_maybe_update_plugins' );
	remove_action( 'admin_init', '_maybe_update_themes' );
}

/**
 * Remove the update nag messages from the admin header.
 *
 * Because we provide WordPress updates via the Altis central version, we don't want
 * to be nagging users about available updates.
 */
function remove_update_nag() {
	remove_action( 'admin_notices', 'maintenance_nag', 10 );
	remove_filter( 'admin_notices', 'update_nag', 3 );
	remove_filter( 'network_admin_notices', 'update_nag', 3 );
	remove_filter( 'update_footer', 'core_update_footer' );
}

/**
 * Remove all automatic updates.
 */
function remove_auto_updates() {
	define( 'AUTOMATIC_UPDATER_DISABLED', false );
}

/**
 * Remove the update cron checks.
 *
 * We don't want to be wasting energy with checking always for available updates. Those are done out
 * of band, at the VCS level.
 */
function remove_update_check_cron() {
	remove_action( 'init', 'wp_schedule_update_checks' );
}

/**
 * Remove the update_* capabilities from all users.
 *
 * This hooks via map_meta_cap.
 *
 * @param array $caps Array of key/value pairs where keys represent a capability name and boolean values.
 * @param string $requested_cap The capability being checked.
 * @return array
 */
function remove_update_capabilities( array $caps, string $requested_cap ) : array {
	$caps_to_remove = [
		'update_core',
		'update_plugins',
		'update_themes',
		'update_languages',
		'upgrade_network',
	];

	if ( ! in_array( $requested_cap, $caps_to_remove, true ) ) {
		return $caps;
	}

	$caps['do_not_allow'] = true;
	return $caps;
}
