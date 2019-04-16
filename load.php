<?php

namespace HM\Platform\CMS; // @codingStandardsIgnoreLine

use function HM\Platform\register_module;

require_once __DIR__ . '/inc/namespace.php';
require_once __DIR__ . '/inc/remove_updates/namespace.php';
require_once __DIR__ . '/inc/branding/namespace.php';
require_once __DIR__ . '/inc/custom_meta_boxes/namespace.php';

// Don't self-initialize if this is not a Platform execution.
if ( ! function_exists( 'add_action' ) ) {
	return;
}

// Register core module.
add_action( 'hm-platform.modules.init', function () {
	$default_settings = [
		'enabled' => true,
		'branding' => true,
		'login-logo' => null,
		'custom-meta-boxes' => true,
	];
	register_module( 'cms', __DIR__, 'CMS', $default_settings, __NAMESPACE__ . '\\bootstrap' );
} );
