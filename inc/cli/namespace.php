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
		error_reporting( E_ALL & ~E_STRICT & ~E_DEPRECATED & ~E_USER_WARNING );
	}
}

/**
 * Check if the current process is the initial WordPress installation.
 *
 * @return boolean
 */
function is_initial_install() : bool {
	// Support for PHPUnit & direct calls to install.php.
	// phpcs:ignore -- Ignoring requirement for isset on $_SERVER['PHP_SELF'] and wp_unslash().
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
