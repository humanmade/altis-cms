<?php
/**
 * Tests for core module's signups features.
 *
 * phpcs:disable WordPress.Files, WordPress.NamingConventions, PSR1.Classes.ClassDeclaration.MissingNamespace, HM.Functions.NamespacedFunctions
 */

/**
 * Test core module admin features.
 */
class SignupsCest {

	/**
	 * Test signups management actions.
	 *
	 * @param AcceptanceTester $I Tester
	 *
	 * @return void
	 */
	public function testSitesManagementActions( AcceptanceTester $I ) {
		$I->wantToTest( 'I want to manage my network signups.' );
		$I->loginAsAdmin();

		// Add a new site as subdomain.
		$I->amOnAdminPage( 'network/admin.php?page=signup_edit' );
		$I->fillField( '#user_login', 'signo' );
		$I->fillField( '#user_email', 'signo@know.where' );
		$I->click( 'Add Signup' );
		$I->waitForText( 'Added signo@know.where.' );

		// Activate the signup.
		$I->moveMouseOver( '#the-list tr' );
		$I->seeLink( 'Activate' );
		$I->click( 'Activate' );
		$I->waitForText( 'Activated signo@know.where.' );

		// Update user password to try login.
		$I->amOnAdminPage( '/network/users.php' );
		$I->click( 'signo' );
		$I->click( 'Set New Password' );
		$pass = $I->grabValueFrom( '#pass1' );
		$I->click( 'Update User' );

		// Login with that user.
		$I->amOnPage( '/wp-login.php?action=logout' );
		$I->click( '//a[contains(@href,"action=logout")]' );

		$I->wait(5); // wait for 5 seconds to avoid rate limiting.

		$I->loginAs( 'signo', $pass );
	}
}
