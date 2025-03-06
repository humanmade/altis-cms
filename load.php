<?php
/**
 * Altis CMS Module.
 *
 * @package altis/cms
 */

namespace Altis\CMS; // phpcs:ignore

use Altis;

// Register core module.
add_action( 'altis.modules.init', function () {
	$favicon_path = '/vendor/altis/cms/assets/favicon.ico';
	if ( Altis\get_environment_type() === 'local' ) {
		$favicon_path = '/vendor/altis/cms/assets/favicon-local.ico';
	}

	$default_settings = [
		'enabled' => true,
		'branding' => true,
		'favicon' => $favicon_path,
		'large-network' => true,
		'login-logo' => '/vendor/altis/cms/assets/logo.svg',
		'reusable-blocks' => true,
		'default-theme' => 'base',
		'remove-emoji' => true,
		'xmlrpc' => true,
		'feeds' => true,
		'network-ui' => [
			'disable-spam' => true,
		],
		'local-avatars' => true,
	];
	$options = [
		'defaults' => $default_settings,
	];
	Altis\register_module( 'cms', __DIR__, 'CMS', $options, __NAMESPACE__ . '\\bootstrap' );
}, 5 );
