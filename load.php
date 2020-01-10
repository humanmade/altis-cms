<?php

namespace Altis\CMS; // @codingStandardsIgnoreLine

use function Altis\register_module;

// Register core module.
add_action( 'altis.modules.init', function () {
	$default_settings = [
		'enabled'       => true,
		'branding'      => true,
		'large-network' => true,
		'login-logo'    => '/vendor/altis/cms/assets/logo.svg',
		'shared-blocks' => true,
		'default-theme' => 'base',
		'remove-emoji'  => true,
		'xmlrpc'        => true,
	];
	register_module( 'cms', __DIR__, 'CMS', $default_settings, __NAMESPACE__ . '\\bootstrap' );
}, 5 );
