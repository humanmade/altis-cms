<?php
/**
 * Altis CMS Add Site UI.
 *
 * @package altis/cms
 */

namespace Altis\CMS\Add_Site_UI;

const REGEX_DOMAIN_SEGMENT = '(?![0-9]+$)(?!.*-$)(?!-)[a-zA-Z0-9-]{1,63}';

/**
 * Setup the Add Site UI.
 */
function bootstrap() {
	add_action( 'admin_init', __NAMESPACE__ . '\\add_site_form_handler' );
	add_action( 'load-site-new.php', __NAMESPACE__ . '\\output_add_site_page', 1000 );
}

/**
 * Display the New Site page in the admin.
 */
function output_add_site_page() {
	$GLOBALS['title'] = __( 'Add New Site', 'altis' );

	wp_enqueue_script( 'altis-customize-settings', plugin_dir_url( dirname( __FILE__, 2 ) ) . 'assets/customize-settings.js', [], false, true );

	require ABSPATH . 'wp-admin/admin-header.php';
	?>
	<div class="wrap">
		<h1 id="add-new-site"><?php esc_html_e( 'Add New Site', 'altis' ); ?></h1>

		<?php
		// Add message if a new site was just added or had an error.
		if ( isset( $_GET['message'] ) ) {
			$message = $_GET['message'];

			if ( 'created' === $message ) {
				$notice = sprintf(
					/* translators: 1: dashboard url, 2: network admin edit url */
					__( 'Site added. <a href="%1$s">Visit Dashboard</a> or <a href="%2$s">Edit Site</a>', 'altis' ),
					esc_url( get_admin_url( absint( $_GET['id'] ) ) ),
					network_admin_url( 'site-info.php?id=' . absint( $_GET['id'] ) )
				);
				echo '<div id="message" class="updated notice is-dismissible"><p>' . wp_kses( $notice, [ 'a' => [ 'href' => [] ] ] ) . '</p></div>';

			} elseif ( 'error' === $message ) {
				$error = $_GET['error'];
				if ( 'wp_error' === $error ) {
					$notice = sprintf(
						/* translators: network admin all sites url */
						__( 'Sorry, we were unable to create your site. Please <a href="%s">double check that the same url doesn\'t already exist.</a>', 'altis' ),
						network_admin_url( 'sites.php' )
					);
				} elseif ( 'missing_values' === $error ) {
					$notice = __( 'Sorry, we were unable to create your site. Please make sure that all required fields are filled in.', 'altis' );
				} elseif ( 'mismatched_values' === $error ) {
					$notice = __( 'Sorry, we were unable to create your site. Please check the Site Address field to be sure that what you enter matches the Site Type selected.', 'altis' );
				}
				echo '<div id="message" class="error notice is-dismissible"><p>' . wp_kses( $notice, [ 'a' => [ 'href' => [] ] ] ) . '</p></div>';
			}
		}
		?>

		<p>
			<?php
			printf(
				/* translators: %s: asterisk to mark required form fields. */
				esc_html__( 'Required fields are marked %s' ),
				'<span class="required">*</span>'
			);
			?>
		</p>
		<form method="post" action="<?php echo esc_attr( network_admin_url( 'site-new.php' ) ); ?>" novalidate="novalidate">
			<?php wp_nonce_field( 'altis-add-site', '_altis_add_site_nonce' ); ?>
			<table class="form-table">
				<tr class="form-field form-required">
					<th scope="row">
						<?php esc_html_e( 'Site Type', 'altis' ); ?> <span class="required">*</span>
					</th>
					<td>
						<fieldset>
							<legend class="screen-reader-text"><?php esc_html_e( 'Site domain settings', 'altis' ); ?></legend>
							<label>
								<input name="domain-type" type="radio" id="site-subdomain" value="site-subdomain" aria-describedby="site-subdomain-desc" checked />
								<strong><?php esc_html_e( 'Subdomain', 'altis' ); ?>: </strong>
								<span class="input-description" id="site-subdomain-desc"><?php esc_html_e( 'recommended for related sites', 'altis' ) ?></span>
							</label>
							<br />
							<label>
								<input name="domain-type" type="radio" id="site-subdirectory" value="site-subdirectory" aria-describedby="site-subdirectory-desc" />
								<strong><?php esc_html_e( 'Subdirectory', 'altis' ); ?>: </strong>
								<span class="input-description" id="site-subdirectory-desc"><?php esc_html_e( 'recommended for regional or multilingual sites', 'altis' ) ?></span>
							</label>
							<br />
							<label>
								<input name="domain-type" type="radio" id="site-custom-domain" value="site-custom-domain" aria-describedby="site-custom-domain-desc" />
								<strong><?php esc_html_e( 'Custom domain', 'altis' ); ?>: </strong>
								<span class="input-description" id="site-custom-domain-desc"><?php esc_html_e( 'recommended for microsites', 'altis' ) ?></span>
							</label>
						</fieldset>
					</td>
				</tr>
				<tr class="form-field form-required">
					<th scope="row"><label for="site-address"><?php esc_html_e( 'Site Address (URL)', 'altis' ); ?> <span class="required">*</span></label></th>
					<td>
						<div class="site-address-wrapper">
							<input name="url" type="text" class="regular-text" id="site-address" aria-describedby="site-address-desc" autocapitalize="none" autocorrect="off" required />
						</div>
						<p class="description" id="site-address-desc"><?php esc_html_e( 'Only lowercase letters (a-z), numbers, and hyphens are allowed.', 'altis' ) ?></p>
					</td>
				</tr>
				<tr class="form-field form-required">
					<th scope="row"><label for="site-title"><?php esc_html_e( 'Site Title', 'altis' ); ?> <span class="required">*</span></label></th>
					<td><input name="title" type="text" class="regular-text" id="site-title" required /></td>
				</tr>
				<?php
				$languages = get_available_languages();
				if ( ! empty( $languages ) ) :
					?>
					<tr class="form-field form-required">
						<th scope="row"><label for="site-language"><?php esc_html_e( 'Site Language', 'altis' ); ?></label></th>
						<td>
							<?php
							// Network default.
							$lang = get_site_option( 'WPLANG' );

							// Use English if the default isn't available.
							if ( ! in_array( $lang, $languages, true ) ) {
								$lang = '';
							}

							wp_dropdown_languages(
								[
									'name'                        => 'language',
									'id'                          => 'site-language',
									'selected'                    => $lang,
									'languages'                   => $languages,
									'translations'                => [],
									'show_available_translations' => false,
								]
							);
							?>
						</td>
					</tr>
				<?php endif; // Languages. ?>
				<tr class="form-field">
					<th scope="row"><?php esc_html_e( 'Public', 'altis' ); ?></th>
					<td>
						<label>
							<input name="public" type="checkbox" id="public" value="1" aria-describedby="public-desc" checked />
							<strong><?php esc_html_e( 'Should this site be public?', 'altis' ); ?> </strong>
							<span class="input-description" id="public-desc"><?php esc_html_e( 'Uncheck to require login.', 'altis' ) ?></span>
						</label>
					</td>
				</tr>
			</table>

			<?php
			/**
			 * Fires at the end of the new site form in network admin.
			 *
			 * @since 4.5.0
			 */
			do_action( 'network_site_new_form' );

			submit_button( __( 'Add Site' ), 'primary', 'add-site' );
			?>
		</form>
	</div>
	<?php

	require ABSPATH . 'wp-admin/admin-footer.php';

	// Exit before we attempt to render WordPress' built-in page.
	exit;
}

/**
 * Validate a domain segment.
 *
 * Check that a string is allowed as a segment in a custom domain, i.e. as a
 * TLD or subdomain name.
 *
 * @param string $segment Segment to validate.
 * @return bool True if valid, false otherwise.
 */
function validate_domain_segment( string $segment ) : bool {
	return (bool) preg_match( '/^' . REGEX_DOMAIN_SEGMENT . '$/', $segment );
}

/**
 * Validate a domain name.
 *
 * Checks that the domain name has 2+ valid DNS segments.
 *
 * @param string $domain Domain to validate.
 * @return bool True if valid, false otherwise.
 */
function validate_domain( string $domain ) : bool {
	$segments = explode( '.', $domain );

	// Domains must have multiple segments.
	if ( count( $segments ) < 2 ) {
		return false;
	}

	foreach ( $segments as $segment ) {
		// Segments may not be empty.
		if ( empty( $segment ) ) {
			return false;
		}

		// Each segment must be valid.
		if ( ! validate_domain_segment( $segment ) ) {
			return false;
		}
	}

	return true;
}

/**
 * Validate a path name.
 *
 * Checks that the path does not include any invalid segments.
 *
 * @param string $path Path to validate.
 * @return bool True if valid, false otherwise.
 */
function validate_path( $path ) {
	if ( $path === '/' ) {
		return true;
	}

	$illegal_names = get_site_option( 'illegal_names' );
	if ( empty( $illegal_names ) ) {
		$illegal_names = [];
	}
	$illegal_paths = array_merge( $illegal_names, get_subdirectory_reserved_names() );

	$segments = explode( '/', trim( $path, '/' ) );

	foreach ( $segments as $segment ) {
		if ( empty( $segment ) ) {
			return false;
		}

		if ( in_array( $segment, $illegal_paths, true ) ) {
			return false;
		}
	}

	return true;
}

/**
 * Handler function for the Add Site form submission.
 */
function add_site_form_handler() {
	global $wpdb;

	if ( ! isset( $_POST['_altis_add_site_nonce'] ) || ! wp_verify_nonce( $_POST['_altis_add_site_nonce'], 'altis-add-site' ) ) {
		return;
	}

	$site_type_valid_values = [
		'site-subdomain',
		'site-subdirectory',
		'site-custom-domain',
	];

	$site_type_value = sanitize_text_field( $_POST['domain-type'] );

	if ( in_array( $site_type_value, $site_type_valid_values, true ) ) {
		$site_type = $site_type_value;
	} else {
		$site_type = 'site-subdomain';
	}

	$value = sanitize_text_field( $_POST['url'] ?? '' );
	$title = sanitize_text_field( $_POST['title'] ?? '' );

	if ( empty( $value ) || empty( $title ) ) {
		// Add URL arg to use for error message.
		wp_safe_redirect(
			add_query_arg(
				[
					'message' => 'error',
					'error'   => 'missing_values',
				],
				network_admin_url( 'site-new.php' )
			)
		);
		exit;
	}

	switch ( $site_type ) {
		case 'site-subdomain':
			$parts = handle_subdomain( $value );
			break;

		case 'site-subdirectory':
			$parts = handle_subdirectory( $value );
			break;

		case 'site-custom-domain':
			$parts = handle_custom_domain( $value );
			break;
	}

	if ( empty( $parts ) ) {
		// Add URL arg to use for error message.
		wp_safe_redirect(
			add_query_arg(
				[
					'message' => 'error',
					'error'   => 'mismatched_values',
				],
				network_admin_url( 'site-new.php' )
			)
		);
		exit;
	}

	if ( ! validate_domain( $parts['domain'] ) || ! validate_path( $parts['path'] ) ) {
		// Add URL arg to use for error message.
		wp_safe_redirect(
			add_query_arg(
				[
					'message' => 'error',
					'error'   => 'mismatched_values',
				],
				network_admin_url( 'site-new.php' )
			)
		);
		exit;
	}

	$language = sanitize_text_field( $_POST['language'] ?? '' );
	$public = sanitize_text_field( $_POST['public'] ?? '' );
	$options = [
		'WPLANG' => $language,
		'public' => $public,
	];

	$wpdb->hide_errors();
	$blog_id = wpmu_create_blog( $parts['domain'], $parts['path'], $title, '', $options );
	$wpdb->show_errors();

	if ( is_wp_error( $blog_id ) ) {
		// Add URL arg to use for error message.
		wp_safe_redirect(
			add_query_arg(
				[
					'message' => 'error',
					'error'   => 'wp_error',
				],
				network_admin_url( 'site-new.php' )
			)
		);
		exit;
	}

	// Add URL args to use for success message.
	wp_safe_redirect(
		add_query_arg(
			[
				'message' => 'created',
				'id'      => $blog_id,
			],
			network_admin_url( 'site-new.php' )
		)
	);
	exit;
}

/**
 * Handle a subdomain input value.
 *
 * @param string $value Input subdomain value.
 * @return array|null $value Assoc array with domain and path keys, or null if invalid.
 */
function handle_subdomain( string $value ) : ?array {
	$network_url = wp_parse_url( network_site_url() );
	$network_host = $network_url['host'];

	// Check the segment is allowed.
	$illegal_names = get_site_option( 'illegal_names' );
	if ( empty( $illegal_names ) ) {
		$illegal_names = [ 'www', 'web', 'root', 'admin', 'main', 'invite', 'administrator' ];
	}

	if ( in_array( $value, $illegal_names, true ) ) {
		return null;
	}

	return [
		'domain' => $value . '.' . $network_host,
		'path' => '/',
	];
}

/**
 * Handle a subdirectory input value.
 *
 * @param string $value Input subdirectory value.
 * @return array|null $value Assoc array with domain and path keys, or null if invalid.
 */
function handle_subdirectory( string $value ) : ?array {
	$network_url = wp_parse_url( network_site_url() );
	$network_host = $network_url['host'];

	$path = trim( $value, '/' );

	return [
		'domain' => $network_host,
		'path' => $path,
	];
}

/**
 * Handle a custom domain input value.
 *
 * @param string $url Input custom domain value.
 * @return array|null $url Assoc array with domain and path keys, or null if invalid.
 */
function handle_custom_domain( string $url ) : ?array {
	if ( strpos( $url, 'http' ) !== 0 ) {
		$url = 'https://' . $url;
	}

	$url_array = wp_parse_url( $url );

	// Only allow valid schemes.
	if ( $url_array['scheme'] !== 'http' && $url_array['scheme'] !== 'https' ) {
		return null;
	}

	// Don't allow anything extra.
	$invalid_parts = [
		'user',
		'pass',
		'port',
		'fragment',
	];
	foreach ( $invalid_parts as $part ) {
		if ( ! empty( $url_array[ $part ] ) ) {
			return null;
		}
	}

	if ( empty( $url_array['host'] ) ) {
		return null;
	}

	$domain = trim( $url_array['host'], '.' );
	$path = trim( $url_array['path'], '/' );

	return [
		'domain' => $domain,
		'path' => '/' . $path,
	];
}
