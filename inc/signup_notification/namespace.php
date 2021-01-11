<?php
/**
 * Altis CMS Sign-up Blog Notification.
 *
 * @package altis/cms
 */

namespace Altis\CMS\Signup_Notification;

/**
 * Boostrap setup to remove updates from the admin.
 */
function bootstrap() {
	add_action( 'after_signup_site', __NAMESPACE__ . '\\altis_signup_blog_notification', 10, 7 );
	add_action( 'after_signup_site', __NAMESPACE__ . '\\remove_wpmu_signup_blog_notification', 9 );
}

/**
 * Remove the core wpmu_signup_blog_notification function.
 */
function remove_wpmu_signup_blog_notification() {
	remove_action( 'after_signup_site', 'wpmu_signup_blog_notification', 10 );
}

/**
 * Send a confirmation request email to a user when they sign up for a new site. The new site will not become active
 * until the confirmation link is clicked.
 *
 * NOTE: This is a replacement function for the core wpmu_signup_blog_notification function:
 * https://github.com/WordPress/WordPress/blob/master/wp-includes/ms-functions.php#L900-L1036
 *
 * This is the notification function used when site registration
 * is enabled.
 *
 * Filter {@see 'wpmu_signup_blog_notification'} to bypass this function or
 * replace it with your own notification behavior.
 *
 * Filter {@see 'wpmu_signup_blog_notification_email'} and
 * {@see 'wpmu_signup_blog_notification_subject'} to change the content
 * and subject line of the email sent to newly registered users.
 *
 * @since MU (3.0.0)
 *
 * @param string $domain     The new blog domain.
 * @param string $path       The new blog path.
 * @param string $title      The site title.
 * @param string $user_login The user's login name.
 * @param string $user_email The user's email address.
 * @param string $key        The activation key created in wpmu_signup_blog()
 * @param array  $meta       Optional. Signup meta data. By default, contains the requested privacy setting and lang_id.
 * @return bool
 */
function altis_signup_blog_notification( $domain, $path, $title, $user_login, $user_email, $key, $meta = [] ) {
	/**
	 * Filters whether to bypass the new site email notification.
	 *
	 * @since MU (3.0.0)
	 *
	 * @param string|bool $domain     Site domain.
	 * @param string      $path       Site path.
	 * @param string      $title      Site title.
	 * @param string      $user_login User login name.
	 * @param string      $user_email User email address.
	 * @param string      $key        Activation key created in wpmu_signup_blog().
	 * @param array       $meta       Signup meta data. By default, contains the requested privacy setting and lang_id.
	 */
	if ( ! apply_filters( 'wpmu_signup_blog_notification', $domain, $path, $title, $user_login, $user_email, $key, $meta ) ) {
		return false;
	}

	// Send email with activation link.
	if ( ! is_subdomain_install() || intval( get_current_network_id() ) !== 1 ) {
		$activate_url = network_site_url( "wp-activate.php?key=$key" );
	} else {
		$activate_url = "http://{$domain}{$path}wp-activate.php?key=$key";
	}

	$activate_url = esc_url( $activate_url );

	$admin_email = get_site_option( 'admin_email' );

	$admin_email = apply_filters( 'wp_mail_from', $admin_email );

	$from_name       = ( '' !== get_site_option( 'site_name' ) ) ? esc_html( get_site_option( 'site_name' ) ) : 'Altis';
	$message_headers = "From: \"{$from_name}\" <{$admin_email}>\n" . 'Content-Type: text/plain; charset="' . get_option( 'blog_charset' ) . "\"\n";

	$user            = get_user_by( 'login', $user_login );
	$switched_locale = switch_to_locale( get_user_locale( $user ) );

	$message = sprintf(
		/**
		 * Filters the message content of the new blog notification email.
		 *
		 * Content should be formatted for transmission via wp_mail().
		 *
		 * @since MU (3.0.0)
		 *
		 * @param string $content    Content of the notification email.
		 * @param string $domain     Site domain.
		 * @param string $path       Site path.
		 * @param string $title      Site title.
		 * @param string $user_login User login name.
		 * @param string $user_email User email address.
		 * @param string $key        Activation key created in wpmu_signup_blog().
		 * @param array  $meta       Signup meta data. By default, contains the requested privacy setting and lang_id.
		 */
		apply_filters(
			'wpmu_signup_blog_notification_email',
			/* translators: New site notification email. 1: Activation URL, 2: New site URL. */
			__( "To activate your blog, please click the following link:\n\n%1\$s\n\nAfter you activate, you will receive *another email* with your login.\n\nAfter you activate, you can visit your site here:\n\n%2\$s" ),
			$domain,
			$path,
			$title,
			$user_login,
			$user_email,
			$key,
			$meta
		),
		$activate_url,
		esc_url( "http://{$domain}{$path}" ),
		$key
	);

	$subject = sprintf(
		/**
		 * Filters the subject of the new blog notification email.
		 *
		 * @since MU (3.0.0)
		 *
		 * @param string $subject    Subject of the notification email.
		 * @param string $domain     Site domain.
		 * @param string $path       Site path.
		 * @param string $title      Site title.
		 * @param string $user_login User login name.
		 * @param string $user_email User email address.
		 * @param string $key        Activation key created in wpmu_signup_blog().
		 * @param array  $meta       Signup meta data. By default, contains the requested privacy setting and lang_id.
		 */
		apply_filters(
			'wpmu_signup_blog_notification_subject',
			/* translators: New site notification email subject. 1: Network title, 2: New site URL. */
			_x( '[%1$s] Activate %2$s', 'New site notification email subject' ),
			$domain,
			$path,
			$title,
			$user_login,
			$user_email,
			$key,
			$meta
		),
		$from_name,
		esc_url( 'http://' . $domain . $path )
	);

	wp_mail( $user_email, wp_specialchars_decode( $subject ), $message, $message_headers );

	if ( $switched_locale ) {
		restore_previous_locale();
	}

	return true;
}
