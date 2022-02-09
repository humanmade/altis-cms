<?php
/**
 * Tests for core module's authorship features.
 *
 * phpcs:disable WordPress.Files, WordPress.NamingConventions, PSR1.Classes.ClassDeclaration.MissingNamespace, HM.Functions.NamespacedFunctions
 */

use Codeception\Util\Locator;

/**
 * Test core module admin features.
 */
class AuthorshipCest {

	/**
	 * Rollback callback for the authorship activation bootstrap call.
	 *
	 * @var callable
	 */
	protected $rollback = null;

	/**
	 * Make sure Authorship is activated.
	 *
	 * @param AcceptanceTester $I Actor object.
	 *
	 * @return void
	 */
	public function _before( AcceptanceTester $I ) {
		$this->rollback = $I->bootstrapWith( [ __CLASS__, '_enableAuthorship' ] );
	}

	/**
	 * Deactivate authorship after tests are finished.
	 *
	 * @param AcceptanceTester $I Actor.
	 *
	 * @return void
	 */
	public function _after( AcceptanceTester $I ) {
		call_user_func( $this->rollback );
	}

	/**
	 * Activate authorship.
	 *
	 * @return void
	 */
	public static function _enableAuthorship() {
		add_filter( 'altis.config', function( $config ) {
			$config['modules']['cms']['authorship'] = true;
			return $config;
		} );
	}

	/**
	 * Test creating a new post with both a guest and an author user.
	 *
	 * @param AcceptanceTester $I Tester
	 *
	 * @return void
	 */
	public function testAuthorship( AcceptanceTester $I ) {
		$I->haveUserInDatabase( 'Arthur', 'author' );

		$I->reindexContent();

		$I->wantToTest( 'I can add multiple authors' );
		$I->loginAsAdmin();

		// Go to new post page.
		$I->amOnAdminPage( 'post-new.php' );

		// Add a title.
		$I->click( '.editor-post-title__input' );
		$I->type( 'Test post 1' );

		// Authorship input exists.
		$I->seeElement( '.authorship-select__input' );

		// Add a guest author.
		$I->click( '.authorship-select__input input' );
		$I->type( 'Gusto' );
		$el = Locator::contains( 'div.authorship-select__option', 'Create "Gusto"' );
		$I->waitForElementVisible( $el, 30 );
		$I->click( $el );
		$I->seeElement( '.authorship-select__multi-value__label' );

		// Add an existing author.
		$I->click( '.authorship-select__input input' );
		$I->type( 'Arthur' );
		$el = Locator::contains( 'div.authorship-select__option', 'Arthur' );
		$I->waitForElementVisible( $el, 30 );
		$I->click( $el );
		$I->seeElement( '.authorship-select__multi-value__label:nth-child(1)' );

		// Publish the post.
		$I->click( '.editor-post-publish-button__button' );
		$I->click( '.editor-post-publish-panel__header-publish-button .editor-post-publish-button__button' );
		$el = Locator::contains( '.editor-post-publish-panel__header-publish-button .editor-post-publish-button__button', 'Publishing' );
		$I->waitForElementNotVisible( $el, 20 );

		// Check post is published correctly.
		$I->seePostInDatabase( [
			'post_title' => 'Test post 1',
			'post_status' => 'publish',
		] );

		// Check both guest and author users are added.
		$I->seeUserInDatabase( [ 'user_login' => 'Gusto' ] );
		$post_id = $I->grabFromDatabase( $I->grabPostsTableName(), 'ID', [ 'post_title' => 'Test post 1' ] );
		$guest_user_id = $I->grabUserIdFromDatabase( 'Gusto' );
		$author_user_id = $I->grabUserIdFromDatabase( 'Arthur' );
		$guest_term_id = $I->grabTermIdFromDatabase( [ 'slug' => $guest_user_id ] );
		$author_term_id = $I->grabTermIdFromDatabase( [ 'slug' => $author_user_id ] );
		$I->seePostWithTermInDatabase( $post_id, $guest_term_id, null, 'authorship' );
		$I->seePostWithTermInDatabase( $post_id, $author_term_id, null, 'authorship' );

		// Test post API response has expected authorship values.
		$I->amOnPage( '/wp-json/wp/v2/posts/' . $post_id );
		$source = $I->grabTextFrom( 'pre' );
		$decoded = json_decode( $source );
		$I->assertEquals( [ 3, 2 ], $decoded->authorship );
	}

	/**
	 * Test creating a new guest author user.
	 *
	 * @param AcceptanceTester $I Tester
	 *
	 * @return void
	 */
	public function testAuthorshipCreateGuestUser( AcceptanceTester $I ) {
		$I->wantToTest( 'I can add multiple authors' );
		$I->loginAsAdmin();

		// Go to new user page.
		$I->amOnAdminPage( 'user-new.php' );

		// Fill the form.
		$I->click( '#createuser input[name=user_login]' );
		$I->type( 'gogo' );
		$I->click( '#createuser input[name=email]' );
		$I->type( 'gogo@know.where' );
		$I->click( '#createuser select[name=role]' );
		$I->type( 'Guest Author' );
		$I->click( '#noconfirmation' );
		$I->click( 'Add New User' );

		// Check if user creation succeeded.
		$I->see( 'User has been added to your site.' );
		$I->seeUserInDatabase( [ 'user_login' => 'gogo' ] );
	}
}
