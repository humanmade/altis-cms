<?php

namespace Altis\CMS\Remove_Updates;

/**
 * Boostrap setup to remove updates from the admin.
 */
function bootstrap() {
	add_action( 'admin_init', __NAMESPACE__ . '\\remove_update_nag' );
	add_action( 'plugins_loaded', __NAMESPACE__ . '\\remove_update_check_cron' );
	add_filter( 'map_meta_cap', __NAMESPACE__ . '\\remove_update_core_capability', 10, 2 );
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
 * Remove the update cron checks.
 *
 * We don't want to be wasting energy with checking always for available updates. Those are done out
 * of band, at the VCS level.
 */
function remove_update_check_cron() {
	remove_action( 'init', 'wp_schedule_update_checks' );
}

/**
 * Remove the update_core capability from all users.
 *
 * This hooks via map_meta_cap.
 *
 * @param bool[]   $allcaps Array of key/value pairs where keys represent a capability name and boolean values
 * @return array
 */
function remove_update_core_capability( array $caps, string $requested_cap ) : array {
	if ( $requested_cap !== 'update_core' ) {
		return $caps;
	}

	$caps['do_not_allow'] = true;
	return $caps;
}
