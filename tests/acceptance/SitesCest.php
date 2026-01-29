<?php
/**
 * Tests for core module's sites features.
 *
 * phpcs:disable WordPress.Files, WordPress.NamingConventions, PSR1.Classes.ClassDeclaration.MissingNamespace, HM.Functions.NamespacedFunctions
 */

/**
 * Test core module admin features.
 */
class SiteCest {

	/**
	 * Test sites management actions.
	 *
	 * @param AcceptanceTester $I Tester
	 *
	 * @return void
	 */
	public function testSitesManagementActions( AcceptanceTester $I ) {
		$I->wantToTest( 'I want to manage my network sites.' );
		$I->loginAsAdmin();

		// Add a new site as subdomain.
		$I->amOnAdminPage( 'network/site-new.php' );
		$I->fillField( '#site-address', 'testsubdom' );
		$I->fillField( '#site-title', 'Test Subdomain Site' );
		$I->click('#add-site');  // instead of $I->click('Add Site'); which clashes wih the hidden menu item of the same name.
		$I->waitForText( 'Site added.' );

		// Add a new site as subdirectory.
		$I->amOnAdminPage( 'network/site-new.php' );
		$I->fillField( '#site-address', 'testsubdir' );
		$I->fillField( '#site-title', 'Test Subdirectory Site' );
		$I->click( '#site-subdirectory' );
		$I->click('#add-site');  // instead of $I->click('Add Site');
		$I->waitForText( 'Site added.' );

		// Test both sites are accessible, as well as their dashboards.
		// TODO fix the subdomain site test, since Altis local-server does not yet support it.
		$I->amOnPage( '/testsubdir/' );
		$I->see( 'Test Subdirectory Site' );

		// Grab site blog_id.
		$subdir_site_id = $I->grabFromDatabase( $I->grabPrefixedTableNameFor( 'blogs' ), 'blog_id', [ 'path' => '/testsubdir/' ] );

		// Archive a site.
		$I->amOnAdminPage( 'network/site-info.php?id=' . $subdir_site_id );
		$I->checkOption( 'blog[archived]' );
		$I->click( 'Save Changes' );
		// Check if it worked!
		$this->logOut( $I );
		$I->amOnPage( '/testsubdir/' );
		$I->see( 'This site has been archived or suspended.' );

		// Mark a site as spam.
		$I->loginAsAdmin();
		$I->amOnAdminPage( 'network/site-info.php?id=' . $subdir_site_id );
		$I->uncheckOption( 'blog[archived]' );
		$I->checkOption( 'blog[spam]' );
		$I->click( 'Save Changes' );
		// Check if it worked!
		$this->logOut( $I );
		$I->amOnPage( '/testsubdir/' );
		$I->see( 'This site has been archived or suspended.' );
	}

	/**
	 * Shorthand function to logout without checking the redirection URL, unlike native tester method.
	 *
	 * @param AcceptanceTester $I Tester.
	 *
	 * @return void
	 */
	protected function logOut( AcceptanceTester $I ) {
		$I->amOnPage( '/wp-login.php?action=logout' );
		$I->click( '//a[contains(@href,"action=logout")]' );
	}
}
