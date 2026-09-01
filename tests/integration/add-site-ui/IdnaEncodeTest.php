<?php
/**
 * Test the Add Site UI IDNA encoder.
 *
 * phpcs:disable WordPress.Files, HM.Files, HM.Functions.NamespacedFunctions, WordPress.NamingConventions
 */

namespace AddSiteUI;

use function Altis\CMS\Add_Site_UI\idna_encode;

/**
 * Test the Add Site UI IDNA encoder.
 */
class IdnaEncodeTest extends \Codeception\TestCase\WPTestCase {
	/**
	 * Tester
	 *
	 * @var \IntegrationTester
	 */
	protected $tester;

	/**
	 * Domains and their expected punycode encoding.
	 *
	 * @return array<string, array{0: string, 1: string}>
	 */
	public function domainProvider() : array {
		return [
			'non-ascii domain' => [ 'exämple.com', 'xn--exmple-cua.com' ],
			'multiple non-ascii labels' => [ 'täst.münchen.de', 'xn--tst-qla.xn--mnchen-3ya.de' ],
			'plain ascii passes through' => [ 'example.com', 'example.com' ],
			'already punycode passes through' => [ 'xn--exmple-cua.com', 'xn--exmple-cua.com' ],
			'ascii case is preserved' => [ 'ALLCAPS.com', 'ALLCAPS.com' ],
		];
	}

	/**
	 * Test that domains are encoded to punycode.
	 *
	 * The implementation moved from the PSR-0 `Requests_IDNAEncoder` alias to the
	 * PSR-4 `WpOrg\Requests\IdnaEncoder` class it points at. These expectations
	 * are the values the PSR-0 name produced, so they pin the swap to identical
	 * output.
	 *
	 * @dataProvider domainProvider
	 *
	 * @param string $domain   The domain to encode.
	 * @param string $expected The expected encoded domain.
	 *
	 * @return void
	 */
	public function testIdnaEncode( string $domain, string $expected ) {
		$this->assertSame( $expected, idna_encode( $domain ) );
	}

	/**
	 * Test the module has no PSR-0 Requests class references left.
	 *
	 * The PSR-0 `Requests_*` names are deprecated aliases; referencing one emits
	 * an E_USER_DEPRECATED from the Requests autoloader. That notice is silenced
	 * after the first occurrence in a request, so asserting on the deprecation
	 * itself would be order-dependent -- scan the source instead.
	 *
	 * @return void
	 */
	public function testNoLegacyRequestsClassNames() {
		$inc_dir = dirname( __DIR__, 3 ) . '/inc';
		$files = new \RecursiveIteratorIterator( new \RecursiveDirectoryIterator( $inc_dir ) );
		$offenders = [];

		foreach ( $files as $file ) {
			if ( $file->getExtension() !== 'php' ) {
				continue;
			}

			$contents = file_get_contents( $file->getPathname() );

			// A false return would coerce to an empty string and silently scan
			// as clean, which would make this test pass for the wrong reason.
			$this->assertNotFalse( $contents, sprintf( 'Could not read %s', $file->getPathname() ) );

			if ( preg_match( '/\bRequests_[A-Za-z_]+/', $contents ) ) {
				$offenders[] = str_replace( $inc_dir . '/', '', $file->getPathname() );
			}
		}

		$this->assertSame( [], $offenders, 'No PSR-0 Requests_* class names should remain in inc/.' );
	}
}
