<?php
/**
 * Enable Real GUIDs for posts.
 *
 * @package altis/cms
 */

namespace Altis\CMS\Real_GUIDs;

/**
 * Add filters for setting real GUIDs.
 */
function bootstrap() {
	add_filter( 'kses_allowed_protocols', __NAMESPACE__ . '\\add_urn_protocol' );
	add_filter( 'wp_insert_post_data', __NAMESPACE__ . '\\add_uuid_to_new' );
}

/**
 * Add urn: to allowed protocols.
 *
 * The GUID gets passed through `esc_url_raw`, so we need to allow urn.
 *
 * @param array $protocols Protocols array to append to.
 * @return array
 */
function add_urn_protocol( array $protocols ) : array {
	$protocols[] = 'urn';
	return $protocols;
}

/**
 * Add our UUID to new posts.
 *
 * @param array $data Post data to save to the database.
 * @return array
 */
function add_uuid_to_new( array $data ) : array {
	// Set a default GUID.
	if ( empty( $data['guid'] ) ) {
		$data['guid'] = wp_slash( sprintf( 'urn:uuid:%s', wp_generate_uuid4() ) );
	}

	return $data;
}
