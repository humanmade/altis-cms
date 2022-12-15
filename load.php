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
	$default_settings = [
		'enabled' => true,
		'branding' => true,
		'favicon' => '/vendor/altis/cms/assets/favicon.ico',
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
