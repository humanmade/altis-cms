<?php

namespace HM\Platform\CMS; // @codingStandardsIgnoreLine

use function HM\Platform\register_module;

require_once __DIR__ . '/inc/namespace.php';
require_once __DIR__ . '/remove_updates/namespace.php';
require_once __DIR__ . '/branding/namespace.php';

// Register core module.
add_action( 'hm-platform.modules.init', function () {
	$default_settings = [
		'enabled'    => true,
		'branding'   => true,
		'login-logo' => null,
	];
	register_module( 'cms', __DIR__, 'CMS', $default_settings, __NAMESPACE__ . '\\bootstrap' );
} );
