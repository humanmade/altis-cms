<?php

namespace HM\Platform\CMS\Composer;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;

class Plugin implements PluginInterface, EventSubscriberInterface {

	/**
	 * Activate is not used, but is part of the abstract class.
	 *
	 * @param Composer $composer
	 * @param IOInterface $io
	 */
	public function activate( Composer $composer, IOInterface $io ) {
	}

	/**
	 * Register the composer events we want to run on.
	 *
	 * @return array
	 */
	public static function getSubscribedEvents() : array {
		return [
			'post-update-cmd' => [ 'install_files' ],
			'pre-install-cmd' => [ 'install_files' ],
		];
	}

	/**
	 * Install additional files to the project on update / install
	 */
	public function install_files() {
		$source = dirname( __DIR__, 2 );
		$dest   = dirname( $source, 3 );

		copy( $source . '/index.php', $dest . '/index.php' );
		copy( $source . '/wp-config.php', $dest . '/wp-config.php' );

		// Update the .gitignore to include the wp-config.php, WordPress, the index.php
		// as these files should not be included in VCS.
		if ( ! file_exists( $dest . '/.gitignore' ) ) {
			file_put_contents( $dest . '/.gitignore', "wordpress/\nindex.php\nwp-config.php" );
		}

		if ( ! is_dir( $dest . '/content' ) ) {
			mkdir( $dest . '/content' );
		}
		if ( ! is_dir( $dest . '/content/plugins' ) ) {
			mkdir( $dest . '/content/plugins' );
		}
		if ( ! is_dir( $dest . '/content/themes' ) ) {
			mkdir( $dest . '/content/themes' );
		}
	}
}
