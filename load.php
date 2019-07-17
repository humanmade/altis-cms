<?php

namespace Altis\CMS; // @codingStandardsIgnoreLine

use function Altis\register_module;

require_once __DIR__ . '/inc/namespace.php';
require_once __DIR__ . '/inc/remove_updates/namespace.php';
require_once __DIR__ . '/inc/branding/namespace.php';
require_once __DIR__ . '/inc/block_editor/namespace.php';
require_once __DIR__ . '/inc/permalinks/namespace.php';

// Don't self-initialize if this is not an Altis execution.
if ( ! function_exists( 'add_action' ) ) {
	return;
}

// Register core module.
add_action( 'altis.modules.init', function () {
	$default_settings = [
		'enabled'       => true,
		'branding'      => true,
		'login-logo'    => '/vendor/altis/cms/assets/logo.svg',
		'shared-blocks' => true,
		'default-theme' => 'base',
		'remove-emoji'  => true,
	];
	register_module( 'cms', __DIR__, 'CMS', $default_settings, __NAMESPACE__ . '\\bootstrap' );
} );
