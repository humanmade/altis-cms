<?php
/**
 * Altis CMS Permalink Settings.
 *
 * @package altis/cms
 */

namespace Altis\CMS\Permalinks;

/**
 * Set up permalink related hooks.
 */
function bootstrap() {
	add_filter( 'sanitize_option_permalink_structure', __NAMESPACE__ . '\\remove_blog_prefix' );
	add_filter( 'option_permalink_structure', __NAMESPACE__ . '\\remove_blog_prefix' );
}

/**
 * Strip /blog prefix from the base permalink structure.
 *
 * @param string $value The permalink structure for posts.
 * @return string
 */
function remove_blog_prefix( string $value ) : string {
	if ( strpos( $value, '/blog' ) === 0 ) {
		$value = preg_replace( '#^/blog#', '', $value );
	}

	return $value;
}
