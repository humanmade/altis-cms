<?php
/**
 * Altis CMS Sign-up Blog Notification.
 *
 * @package altis/cms
 */

namespace Altis\CMS\Signup_Notification;

use WP_Signup_Query;

/**
 * Boostrap setup to remove updates from the admin.
 */
function bootstrap() {
	add_action( 'init', __NAMESPACE__ . '\\remove_wpmu_blog_notifications', 9 );

	add_action( 'after_signup_site', __NAMESPACE__ . '\\altis_signup_blog_notification', 10, 7 );
	add_action( 'wpmu_activate_blog',  __NAMESPACE__ . '\\altis_welcome_notification', 10, 5 );

	add_action( 'after_signup_user', __NAMESPACE__ . '\\altis_signup_user_notification', 10, 4 );
	add_action( 'wpmu_activate_user', __NAMESPACE__ . '\\altis_welcome_user_notification', 10, 3 );
}

/**
 * Remove the core wpmu_*_notification functions.
 */
function remove_wpmu_blog_notifications() {
	remove_action( 'wpmu_activate_blog', 'wpmu_welcome_notification', 10 );
	remove_action( 'after_signup_site', 'wpmu_signup_blog_notification', 10 );
	remove_action( 'wpmu_activate_user', 'wpmu_welcome_user_notification', 10 );
	remove_action( 'after_signup_user', 'wpmu_signup_user_notification', 10 );
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

	// Patch error in wp-user-signups passing through signup key.
	if ( class_exists( 'WP_Signup_Query' ) && empty( $key ) ) {
		$query = new WP_Signup_Query( [
			'domain' => $domain,
			'path' => $path,
			'user_email' => $user_email,
			'user_login' => $user_login,
		] );
		$key = $query->signups[0]->activation_key ?? '';
	}

	// Send email with activation link.
	if ( ! is_subdomain_install() || intval( get_current_network_id() ) !== 1 ) {
		$activate_url = network_site_url( "wp-activate.php?key=$key" );
	} else {
		$activate_url = "http://{$domain}{$path}wp-activate.php?key=$key";
	}

	$activate_url = esc_url( $activate_url );

	$admin_email = get_site_option( 'admin_email' );

	if ( '' === $admin_email ) {
		$admin_email = 'support@' . wp_parse_url( network_home_url(), PHP_URL_HOST );
	}

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

/**
 * Sends a confirmation request email to a user when they sign up for a new user account (without signing up for a site
 * at the same time). The user account will not become active until the confirmation link is clicked.
 *
 * This is the notification function used when no new site has
 * been requested.
 *
 * Filter {@see 'wpmu_signup_user_notification'} to bypass this function or
 * replace it with your own notification behavior.
 *
 * Filter {@see 'wpmu_signup_user_notification_email'} and
 * {@see 'wpmu_signup_user_notification_subject'} to change the content
 * and subject line of the email sent to newly registered users.
 *
 * @since MU (3.0.0)
 *
 * @param string $user_login The user's login name.
 * @param string $user_email The user's email address.
 * @param string $key        The activation key created in wpmu_signup_user()
 * @param array  $meta       Optional. Signup meta data. Default empty array.
 * @return bool
 */
function altis_signup_user_notification( $user_login, $user_email, $key, $meta = [] ) {
	/**
	 * Filters whether to bypass the email notification for new user sign-up.
	 *
	 * @since MU (3.0.0)
	 *
	 * @param string $user_login User login name.
	 * @param string $user_email User email address.
	 * @param string $key        Activation key created in wpmu_signup_user().
	 * @param array  $meta       Signup meta data. Default empty array.
	 */
	if ( ! apply_filters( 'wpmu_signup_user_notification', $user_login, $user_email, $key, $meta ) ) {
		return false;
	}

	// Patch error in wp-user-signups passing through signup key.
	if ( class_exists( 'WP_Signup_Query' ) && empty( $key ) ) {
		$query = new WP_Signup_Query( [
			'user_email' => $user_email,
			'user_login' => $user_login,
		] );
		$key = $query->signups[0]->activation_key ?? '';
	}

	$user            = get_user_by( 'login', $user_login );
	$switched_locale = $user && switch_to_user_locale( $user->ID );

	// Send email with activation link.
	$admin_email = get_site_option( 'admin_email' );

	if ( '' === $admin_email ) {
		$admin_email = 'support@' . wp_parse_url( network_home_url(), PHP_URL_HOST );
	}

	$admin_email = apply_filters( 'wp_mail_from', $admin_email );

	$from_name       = ( '' !== get_site_option( 'site_name' ) ) ? esc_html( get_site_option( 'site_name' ) ) : 'Altis';
	$message_headers = "From: \"{$from_name}\" <{$admin_email}>\n" . 'Content-Type: text/plain; charset="' . get_option( 'blog_charset' ) . "\"\n";
	$message         = sprintf(
		/**
		 * Filters the content of the notification email for new user sign-up.
		 *
		 * Content should be formatted for transmission via wp_mail().
		 *
		 * @since MU (3.0.0)
		 *
		 * @param string $content    Content of the notification email.
		 * @param string $user_login User login name.
		 * @param string $user_email User email address.
		 * @param string $key        Activation key created in wpmu_signup_user().
		 * @param array  $meta       Signup meta data. Default empty array.
		 */
		apply_filters(
			'wpmu_signup_user_notification_email',
			/* translators: New user notification email. %s: Activation URL. */
			__( "To activate your user, please click the following link:\n\n%s\n\nAfter you activate, you will receive *another email* with your login." ),
			$user_login,
			$user_email,
			$key,
			$meta
		),
		site_url( "wp-activate.php?key=$key" )
	);

	$subject = sprintf(
		/**
		 * Filters the subject of the notification email of new user signup.
		 *
		 * @since MU (3.0.0)
		 *
		 * @param string $subject    Subject of the notification email.
		 * @param string $user_login User login name.
		 * @param string $user_email User email address.
		 * @param string $key        Activation key created in wpmu_signup_user().
		 * @param array  $meta       Signup meta data. Default empty array.
		 */
		apply_filters(
			'wpmu_signup_user_notification_subject',
			/* translators: New user notification email subject. 1: Network title, 2: New user login. */
			_x( '[%1$s] Activate %2$s', 'New user notification email subject' ),
			$user_login,
			$user_email,
			$key,
			$meta
		),
		$from_name,
		$user_login
	);

	wp_mail( $user_email, wp_specialchars_decode( $subject ), $message, $message_headers );

	if ( $switched_locale ) {
		restore_previous_locale();
	}

	return true;
}

/**
 * Notifies the site administrator that their site activation was successful.
 *
 * Filter {@see 'wpmu_welcome_notification'} to disable or bypass.
 *
 * Filter {@see 'update_welcome_email'} and {@see 'update_welcome_subject'} to
 * modify the content and subject line of the notification email.
 *
 * @since MU (3.0.0)
 *
 * @param int    $blog_id  Site ID.
 * @param int    $user_id  User ID.
 * @param string $password User password, or "N/A" if the user account is not new.
 * @param string $title    Site title.
 * @param array  $meta     Optional. Signup meta data. By default, contains the requested privacy setting and lang_id.
 * @return bool Whether the email notification was sent.
 */
function altis_welcome_notification( $blog_id, $user_id, $password, $title, $meta = [] ) {
	$current_network = get_network();

	/**
	 * Filters whether to bypass the welcome email sent to the site administrator after site activation.
	 *
	 * Returning false disables the welcome email.
	 *
	 * @since MU (3.0.0)
	 *
	 * @param int|false $blog_id  Site ID, or false to prevent the email from sending.
	 * @param int       $user_id  User ID of the site administrator.
	 * @param string    $password User password, or "N/A" if the user account is not new.
	 * @param string    $title    Site title.
	 * @param array     $meta     Signup meta data. By default, contains the requested privacy setting and lang_id.
	 */
	if ( ! apply_filters( 'wpmu_welcome_notification', $blog_id, $user_id, $password, $title, $meta ) ) {
		return false;
	}

	$user = get_userdata( $user_id );

	$switched_locale = switch_to_user_locale( $user_id );

	$welcome_email = get_site_option( 'welcome_email' );
	if ( empty( $welcome_email ) ) {
		/* translators: Do not translate USERNAME, SITE_NAME, BLOG_URL, PASSWORD: those are placeholders. */
		$welcome_email = __(
			'Howdy USERNAME,

Your new SITE_NAME site has been successfully set up at:
BLOG_URL

You can log in to the administrator account with the following information:

Username: USERNAME
Password: PASSWORD
Log in here: BLOG_URLwp-login.php

We hope you enjoy your new site. Thanks!

--The Team @ SITE_NAME'
		);
	}

	$url = get_blogaddress_by_id( $blog_id );

	$welcome_email = str_replace( 'SITE_NAME', $current_network->site_name, $welcome_email );
	$welcome_email = str_replace( 'BLOG_TITLE', $title, $welcome_email );
	$welcome_email = str_replace( 'BLOG_URL', $url, $welcome_email );
	$welcome_email = str_replace( 'USERNAME', $user->user_login, $welcome_email );
	$welcome_email = str_replace( 'PASSWORD', $password, $welcome_email );

	/**
	 * Filters the content of the welcome email sent to the site administrator after site activation.
	 *
	 * Content should be formatted for transmission via wp_mail().
	 *
	 * @since MU (3.0.0)
	 *
	 * @param string $welcome_email Message body of the email.
	 * @param int    $blog_id       Site ID.
	 * @param int    $user_id       User ID of the site administrator.
	 * @param string $password      User password, or "N/A" if the user account is not new.
	 * @param string $title         Site title.
	 * @param array  $meta          Signup meta data. By default, contains the requested privacy setting and lang_id.
	 */
	$welcome_email = apply_filters( 'update_welcome_email', $welcome_email, $blog_id, $user_id, $password, $title, $meta );

	$admin_email = get_site_option( 'admin_email' );

	if ( '' === $admin_email ) {
		$admin_email = 'support@' . wp_parse_url( network_home_url(), PHP_URL_HOST );
	}

	$admin_email = apply_filters( 'wp_mail_from', $admin_email );

	$from_name       = ( '' !== get_site_option( 'site_name' ) ) ? esc_html( get_site_option( 'site_name' ) ) : 'Altis';
	$message_headers = "From: \"{$from_name}\" <{$admin_email}>\n" . 'Content-Type: text/plain; charset="' . get_option( 'blog_charset' ) . "\"\n";
	$message         = $welcome_email;

	if ( empty( $current_network->site_name ) ) {
		$current_network->site_name = 'WordPress';
	}

	/* translators: New site notification email subject. 1: Network title, 2: New site title. */
	$subject = __( 'New %1$s Site: %2$s' );

	/**
	 * Filters the subject of the welcome email sent to the site administrator after site activation.
	 *
	 * @since MU (3.0.0)
	 *
	 * @param string $subject Subject of the email.
	 */
	$subject = apply_filters( 'update_welcome_subject', sprintf( $subject, $current_network->site_name, wp_unslash( $title ) ) );

	wp_mail( $user->user_email, wp_specialchars_decode( $subject ), $message, $message_headers );

	if ( $switched_locale ) {
		restore_previous_locale();
	}

	return true;
}

/**
 * Notifies a user that their account activation has been successful.
 *
 * Filter {@see 'wpmu_welcome_user_notification'} to disable or bypass.
 *
 * Filter {@see 'update_welcome_user_email'} and {@see 'update_welcome_user_subject'} to
 * modify the content and subject line of the notification email.
 *
 * @since MU (3.0.0)
 *
 * @param int    $user_id  User ID.
 * @param string $password User password.
 * @param array  $meta     Optional. Signup meta data. Default empty array.
 * @return bool
 */
function altis_welcome_user_notification( $user_id, $password, $meta = [] ) {
	$current_network = get_network();

	/**
	 * Filters whether to bypass the welcome email after user activation.
	 *
	 * Returning false disables the welcome email.
	 *
	 * @since MU (3.0.0)
	 *
	 * @param int    $user_id  User ID.
	 * @param string $password User password.
	 * @param array  $meta     Signup meta data. Default empty array.
	 */
	if ( ! apply_filters( 'wpmu_welcome_user_notification', $user_id, $password, $meta ) ) {
		return false;
	}

	$welcome_email = get_site_option( 'welcome_user_email' );

	$user = get_userdata( $user_id );

	$switched_locale = switch_to_user_locale( $user_id );

	/**
	 * Filters the content of the welcome email after user activation.
	 *
	 * Content should be formatted for transmission via wp_mail().
	 *
	 * @since MU (3.0.0)
	 *
	 * @param string $welcome_email The message body of the account activation success email.
	 * @param int    $user_id       User ID.
	 * @param string $password      User password.
	 * @param array  $meta          Signup meta data. Default empty array.
	 */
	$welcome_email = apply_filters( 'update_welcome_user_email', $welcome_email, $user_id, $password, $meta );
	$welcome_email = str_replace( 'SITE_NAME', $current_network->site_name, $welcome_email );
	$welcome_email = str_replace( 'USERNAME', $user->user_login, $welcome_email );
	$welcome_email = str_replace( 'PASSWORD', $password, $welcome_email );
	$welcome_email = str_replace( 'LOGINLINK', wp_login_url(), $welcome_email );

	$admin_email = get_site_option( 'admin_email' );

	if ( '' === $admin_email ) {
		$admin_email = 'support@' . wp_parse_url( network_home_url(), PHP_URL_HOST );
	}

	$admin_email = apply_filters( 'wp_mail_from', $admin_email );

	$from_name       = ( '' !== get_site_option( 'site_name' ) ) ? esc_html( get_site_option( 'site_name' ) ) : 'Altis';
	$message_headers = "From: \"{$from_name}\" <{$admin_email}>\n" . 'Content-Type: text/plain; charset="' . get_option( 'blog_charset' ) . "\"\n";
	$message         = $welcome_email;

	if ( empty( $current_network->site_name ) ) {
		$current_network->site_name = 'WordPress';
	}

	/* translators: New user notification email subject. 1: Network title, 2: New user login. */
	$subject = __( 'New %1$s User: %2$s' );

	/**
	 * Filters the subject of the welcome email after user activation.
	 *
	 * @since MU (3.0.0)
	 *
	 * @param string $subject Subject of the email.
	 */
	$subject = apply_filters( 'update_welcome_user_subject', sprintf( $subject, $current_network->site_name, $user->user_login ) );

	wp_mail( $user->user_email, wp_specialchars_decode( $subject ), $message, $message_headers );

	if ( $switched_locale ) {
		restore_previous_locale();
	}

	return true;
}
