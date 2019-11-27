<?php

namespace Altis\CMS\CLI;

function bootstrap() {
	if ( ! defined( 'WP_CLI' ) ) {
		return;
	}

	WP_CLI::add_hook( 'before_invoke:core multisite-install', __NAMESPACE__ . '\\before_install' );
}

function before_install() {
	var_dump( func_get_args() );
}
