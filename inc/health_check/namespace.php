<?php

namespace Altis\CMS\Health_Check;

/**
 *
 */
function bootstrap() {
	add_filter( 'site_status_tests', __NAMESPACE__ . '\\site_status_tests_handler' );
}

/**
 * Object Cache Health Check Handler.
 *
 * @param array $tests
 *
 * @return array
 */
function site_status_tests_handler( array $tests ): array {
	$tests['direct']['object_cache'] = [
		'label' => 'Object Cache',
		'test'  => __NAMESPACE__ . '\\get_test_object_cache'
	];

	return $tests;
}

/**
 * Test that object cache is working.
 *
 * @return array
 */
function get_test_object_cache(): array {
	$result = [
		'label'       => 'Object Cache',
		'status'      => '',
		'badge'       => [
			'label' => 'Performance',
			'color' => 'blue',
		],
		'description' => '', // Long explanation
		'actions'     => '', // contact support?
		'test'        => 'object_cache',
	];

	global $wp_object_cache, $wpdb;

	if ( method_exists( $wp_object_cache, 'getStatus' ) && ! $wp_object_cache->getStats() ) {
		$result['status']      = 'critical';
		$result['description'] = 'Unable to get memcached stats.';
	}

	if ( method_exists( $wp_object_cache, 'getStatus' ) && ! $wp_object_cache->stats() ) {
		$result['status']      = 'critical';
		$result['description'] .= 'Unable to get memcached stats.';
	}

	$set = wp_cache_set( 'test', 1 );
	if ( ! $set ) {
		$result['status']      = 'critical';
		$result['description'] .= 'Unable to set object cache value.';
	}

	$value = wp_cache_get( 'test' );
	if ( $value !== 1 ) {
		$result['status']      = 'critical';
		$result['description'] .= 'Unable to get object cache value.';
	}

	// Check alloptions are not out of sync.
	$alloptions_db = $wpdb->get_results( "SELECT option_name, option_value FROM $wpdb->options WHERE autoload = 'yes'" );
	$alloptions    = [];
	foreach ( $alloptions_db as $o ) {
		$alloptions[ $o->option_name ] = $o->option_value;
	}

	$alloptions_cache = wp_cache_get( 'alloptions', 'options' );

	foreach ( $alloptions as $option => $value ) {
		if ( ! array_key_exists( $option, $alloptions_cache ) ) {
			$result['status']      = 'critical';
			$result['description'] .= sprintf( '%s option not found in cache', $option );
		}
		// Values that are stored in the cache can be any scalar type, but scalar values retrieved from the database will always be string.
		// When a cache value is populated via update / add option, it will be stored in the cache as a scalar type, but then a string in the
		// database. We convert all non-string scalars to strings to be able to do the appropriate comparison.
		$cache_value = $alloptions_cache[ $option ];
		if ( is_scalar( $cache_value ) && ! is_string( $cache_value ) ) {
			$cache_value = (string) $cache_value;
		}
		if ( $cache_value !== $value ) {
			$result['status']      = 'critical';
			$result['description'] .= sprintf( '%s option not the same in the cache and DB', $option );
		}
	}

	if (  $result['description'] === '' ) {
		$result['status']      = 'good';
		$result['description'] = 'All good.';
	}
	return $result;
}
