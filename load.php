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
		'authorship' => false,
	];
	Altis\register_module( 'cms', __DIR__, 'CMS', $default_settings, __NAMESPACE__ . '\\bootstrap' );
}, 5 );
