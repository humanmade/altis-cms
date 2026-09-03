<?php
/**
 * Test the Add Site UI custom domain handler.
 *
 * phpcs:disable WordPress.Files, HM.Files, HM.Functions.NamespacedFunctions, WordPress.NamingConventions
 */

namespace AddSiteUI;

use function Altis\CMS\Add_Site_UI\handle_custom_domain;

/**
 * Test the Add Site UI custom domain handler.
 */
class HandleCustomDomainTest extends \Codeception\TestCase\WPTestCase {
	/**
	 * Tester
	 *
	 * @var \IntegrationTester
	 */
	protected $tester;

	/**
	 * Custom domain inputs and their expected domain and path.
	 *
	 * The function wp_parse_url() omits the `path` key entirely for a bare domain, 
	 * which is the common case when a super admin adds a site by custom domain.
	 *
	 * @return array<string, array{0: string, 1: array}>
	 */
	public function domainProvider() : array {
		return [
			'bare domain, no path' => [
				'example.com',
				[
					'domain' => 'example.com',
					'path' => '/',
				],
			],
			'bare non-ascii domain, no path' => [
				'exämple.com',
				[
					'domain' => 'xn--exmple-cua.com',
					'path' => '/',
				],
			],
			'domain with a path' => [
				'https://example.com/blog',
				[
					'domain' => 'example.com',
					'path' => '/blog',
				],
			],
			'domain with a trailing slash' => [
				'https://example.com/',
				[
					'domain' => 'example.com',
					'path' => '/',
				],
			],
		];
	}

	/**
	 * Test custom domains resolve without warnings.
	 *
	 * @dataProvider domainProvider
	 *
	 * @param string $url      The custom domain input.
	 * @param array  $expected The expected domain and path.
	 *
	 * @return void
	 */
	public function testHandleCustomDomain( string $url, array $expected ) {
		$this->assertSame( $expected, handle_custom_domain( $url ) );
	}
}
