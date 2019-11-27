<?php

namespace Altis\CMS\CLI;

use WP_CLI;

function bootstrap() {
	if ( is_initial_install() ) {
		define( 'WP_INITIAL_INSTALL', true );
	}
}

/**
 * Check if the current process is the initial WordPress installation.
 *
 * @return boolean
 */
function is_initial_install() : bool {
	if ( ! defined( 'WP_CLI' ) ) {
		return false;
	}

	$runner = WP_CLI::get_runner();

	// Check it's the core command.
	if ( $runner->arguments[0] !== 'core' ) {
		return false;
	}

	// Check it's an install command.
	$commands = [ 'install', 'multisite-install', 'multisite-convert' ];
	if ( ! in_array( $runner->arguments[1], $commands, true ) ) {
		return false;
	}

	return true;
}
