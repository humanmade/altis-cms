<?php

namespace HM\Platform\CMS\Remove_Updates;

function bootstrap() {
	add_action( 'admin_init', 'HM\\Platform\\CMS\\Remove_Updates\\remove_update_nag' );
	add_action( 'plugins_loaded', 'HM\\Platform\\CMS\\Remove_Updates\\remove_update_check_cron' );
}

/**
 * Remove the update nag messages from the admin header.
 *
 * Because we provide WordPress updates via the Platform central version, we don't want
 * to be nagging users about available updates.
 */
function remove_update_nag() {
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
