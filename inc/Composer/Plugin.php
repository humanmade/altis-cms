<?php

namespace HM\Platform\CMS\Composer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\EventDispatcher\EventSubscriberInterface;

class Plugin implements PluginInterface, EventSubscriberInterface {
	public function activate( Composer $composer, IOInterface $io ) {
	}
	public static function getSubscribedEvents() : array {
		return [
			'post-update-cmd' => [ 'on_post_update_cmd' ],
			'pre-install-cmd' => [ 'on_post_update_cmd' ],
		];
	}

	public function on_post_update_cmd() {
		$source = dirname( dirname( __DIR__ ) );
		$dest = dirname( dirname( dirname( $source ) ) );
		copy( $source . '/index.php', $dest . '/index.php' );
		copy( $source . '/wp-config.php', $dest . '/wp-config.php' );

		// Update the .gitignore to include the wp-config.php, WordPress, the index.php
		// as these files should not be included in VCS.
		if ( ! file_exists( $dest . '/.gitignore' ) ) {
			file_put_contents( $dest . '/.gitignore', "wordpress/\nindex.php\nwp-config.php" );
		}

		@mkdir( $dest . '/content' ); // @codingStandardsIgnoreLine
		@mkdir( $dest . '/content/plugins' ); // @codingStandardsIgnoreLine
		@mkdir( $dest . '/content/themes' ); // @codingStandardsIgnoreLine
	}
}
