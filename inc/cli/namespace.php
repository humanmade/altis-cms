<?php
/**
 * Altis CMS CLI.
 *
 * @package altis/cms
 */

namespace Altis\CMS\CLI;

use WP_CLI;

/**
 * Define initial install constant.
 *
 * @return void
 */
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
	// Support for PHPUnit & direct calls to install.php.
	if ( php_sapi_name() === 'cli' && basename( $_SERVER['PHP_SELF'] ) === 'install.php' ) {
		return true;
	}

	if ( ! defined( 'WP_CLI' ) ) {
		return false;
	}

	$runner = WP_CLI::get_runner();

	// Check it's the core command.
	if ( $runner->arguments[0] !== 'core' ) {
		return false;
	}

	// If it's the is-installed command and --network is set then
	// allow MULTISITE to be defined.
	if ( $runner->arguments[1] === 'is-installed' && isset( $runner->assoc_args['network'] ) ) {
		return false;
	}

	// Check it's an install related command.
	$commands = [ 'is-installed', 'install', 'multisite-install', 'multisite-convert' ];
	if ( ! in_array( $runner->arguments[1], $commands, true ) ) {
		return false;
	}

	return true;
}
