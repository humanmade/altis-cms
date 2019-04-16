<?php
/**
 * Custom Meta Boxes component.
 *
 * @package hm-platform/cms
 */

namespace HM\Platform\CMS\Custom_Meta_Boxes;

use function HM\Platform\get_config;
use const HM\Platform\ROOT_DIR;

/**
 * Setup custom meta box framework related features.
 */
function bootstrap() {
	$config = get_config()['modules']['cms']['custom-meta-boxes'];

	if ( ! empty( $config ) ) {
		load_custom_meta_box_framework();
	}
}

/**
 * Load the custom meta box framework file.
 */
function load_custom_meta_box_framework() {
	require_once ROOT_DIR . '/vendor/cmb2/cmb2/init.php';
}
