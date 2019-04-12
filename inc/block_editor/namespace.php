<?php

namespace HM\Platform\CMS\Block_Editor;

/**
 * Set up block editor modifications.
 */
function bootstrap() {
	add_filter( 'register_post_type_args', __NAMESPACE__ . '\\show_wp_block_in_menu', 10, 2 );
}

/**
 * Update the wp_block post type to display in the admin menu.
 *
 * @param array $args The post type creation args.
 * @param string $post_type The post type name.
 * @return array
 */
function show_wp_block_in_menu( array $args, string $post_type ) {
	if ( $post_type !== 'wp_block' ) {
		return $args;
	}

	$args['show_in_menu'] = true;

	return $args;
}
