<?php
/**
 * Test that random content redirects are disabled.
 *
 * phpcs:disable WordPress.Files, HM.Files, HM.Functions.NamespacedFunctions, WordPress.NamingConventions
 */

namespace Integration;

/**
 * Test that random content redirects are disabled.
 */
class DisableRandomContentRedirectTest extends \Codeception\TestCase\WPTestCase {
	/**
	 * Test that the random content redirect filter is registered.
	 *
	 * @return void
	 */
	public function testRandomContentRedirectIsDisabled() {
		$this->assertNotFalse( has_filter( 'wp_enable_random_content_redirect', '__return_false' ) );
	}
}
