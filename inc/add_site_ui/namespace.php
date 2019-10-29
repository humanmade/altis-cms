<?php

namespace Altis\CMS\Add_Site_UI;

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

	require ABSPATH . 'wp-admin/admin-header.php';
	?>
	<div class="wrap">
		<h1 id="add-new-site"><?php _e( 'Add New Site', 'altis' ); ?></h1>

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
				echo '<div id="message" class="updated notice is-dismissible"><p>' . $notice . '</p></div>';

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
				echo '<div id="message" class="error notice is-dismissible"><p>' . $notice . '</p></div>';
			}
		}
		?>

		<p>
			<?php
			printf(
				/* translators: %s: asterisk to mark required form fields. */
				__( 'Required fields are marked %s' ),
				'<span class="required">*</span>'
			);
			?>
		</p>
		<form method="post" action="<?php echo network_admin_url( 'site-new.php' ); ?>" novalidate="novalidate">
			<?php wp_nonce_field( 'altis-add-site' ); ?>
			<table class="form-table">
				<tr class="form-field form-required">
					<th scope="row">
						<?php _e( 'Site Type', 'altis' ); ?> <span class="required">*</span>
					</th>
					<td>
						<fieldset>
							<legend class="screen-reader-text"><?php _e( 'Site domain settings', 'altis' ); ?></legend>
							<label>
								<input name="domain-type" type="radio" id="site-subdomain" value="site-subdomain" aria-describedby="site-subdomain-desc" checked />
								<strong><?php _e( 'Subdomain', 'altis' ); ?>: </strong>
								<span class="input-description" id="site-subdomain-desc"><?php _e( 'recommended for related sites', 'altis' ) ?></span>
							</label>
							<br />
							<label>
								<input name="domain-type" type="radio" id="site-subdirectory" value="site-subdirectory" aria-describedby="site-subdirectory-desc" />
								<strong><?php _e( 'Subdirectory', 'altis' ); ?>: </strong>
								<span class="input-description" id="site-subdirectory-desc"><?php _e( 'recommended for regional or multilingual sites', 'altis' ) ?></span>
							</label>
							<br />
							<label>
								<input name="domain-type" type="radio" id="site-custom-domain" value="site-custom-domain" aria-describedby="site-custom-domain-desc" />
								<strong><?php _e( 'Custom domain', 'altis' ); ?>: </strong>
								<span class="input-description" id="site-custom-domain-desc"><?php _e( 'recommended for microsites', 'altis' ) ?></span>
							</label>
						</fieldset>
					</td>
				</tr>
				<tr class="form-field form-required">
					<th scope="row"><label for="site-address"><?php _e( 'Site Address (URL)', 'altis' ); ?> <span class="required">*</span></label></th>
					<td>
						<div class="site-address-wrapper">
							<input name="url" type="text" class="regular-text" id="site-address" aria-describedby="site-address-desc" autocapitalize="none" autocorrect="off" required />
						</div>
						<p class="description" id="site-address-desc"><?php _e( 'Only lowercase letters (a-z), numbers, and hyphens are allowed.', 'altis' ) ?></p>
					</td>
				</tr>
				<tr class="form-field form-required">
					<th scope="row"><label for="site-title"><?php _e( 'Site Title', 'altis' ); ?> <span class="required">*</span></label></th>
					<td><input name="title" type="text" class="regular-text" id="site-title" required /></td>
				</tr>
				<?php
				$languages = get_available_languages();
				if ( ! empty( $languages ) ) :
					?>
					<tr class="form-field form-required">
						<th scope="row"><label for="site-language"><?php _e( 'Site Language', 'altis' ); ?></label></th>
						<td>
							<?php
							// Network default.
							$lang = get_site_option( 'WPLANG' );

							// Use English if the default isn't available.
							if ( ! in_array( $lang, $languages ) ) {
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
					<th scope="row"><?php _e( 'Public', 'altis' ); ?></th>
					<td>
						<label>
							<input name="public" type="checkbox" id="public" value="1" aria-describedby="public-desc" checked />
							<strong><?php _e( 'Should this site be public?', 'altis' ); ?> </strong>
							<span class="input-description" id="public-desc"><?php _e( 'Uncheck to require login.', 'altis' ) ?></span>
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

	// Exit before we attempt to render WordPress' about page.
	exit;
}

/**
 * Handler function for the Add Site form submission.
 */
function add_site_form_handler() {
	global $wpdb;

	if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'altis-add-site' ) ) {
		return;
	}

	$site_type_valid_values = [
		'site-subdomain',
		'site-subdirectory',
		'site-custom-domain',
	];

	$site_type_value = sanitize_text_field( $_POST['domain-type'] );

	if ( in_array( $site_type_value, $site_type_valid_values ) ) {
		$site_type = $site_type_value;
	} else {
		$site_type = 'site-subdomain';
	}

	$network_url = wp_parse_url( network_site_url() );
	$network_url = $network_url['host'];
	$form_url    = sanitize_text_field( $_POST['url'] );
	$url         = $form_url;
	$title       = sanitize_text_field( $_POST['title'] );
	$language    = sanitize_text_field( $_POST['language'] ) ?? '';
	$public      = sanitize_text_field( $_POST['public'] );
	$domain      = '';
	$path        = '';

	if ( ( strpos( $url, $network_url ) && strpos( $url, 'http' ) !== 0 )
		|| ( 'site-custom-domain' === $site_type && strpos( $url, 'http' ) !== 0 ) ) {
		$url = 'https://' . $url;
	}

	// Check if this is already a valid URL.
	if ( wp_http_validate_url( $url ) ) {
		$url_array = wp_parse_url( $url );
		$domain    = str_replace( $network_url, '', $url_array['host'] );
		$domain    = trim( $domain, '.' );
		$path      = trim( $url_array['path'], '/' );
	} elseif ( 'site-custom-domain' !== $site_type ) {
		$domain = trim( $url, '.' );
		$path   = trim( $url, '/' );
	}

	if ( '' === $form_url || '' === $title ) {
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
	} elseif ( ( ( 'site-custom-domain' === $site_type || 'site-subdomain' === $site_type ) && '' === $domain )
		|| ( 'site-subdirectory' === $site_type && '' === $path ) ) {
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

	switch ( $site_type ) {
		case 'site-subdomain':
			$domain = $domain . '.' . $network_url;
			$path   = '/';
			break;
		case 'site-subdirectory':
			$domain = $network_url;
			$path   = '/' . $path;
			break;
		case 'site-custom-domain':
			$domain = $domain;
			$path   = '/' . $path;
			break;
	}

	$options = [
		'WPLANG' => $language,
		'public' => $public,
	];

	$wpdb->hide_errors();
	$blog_id = wpmu_create_blog( $domain, $path, $title, '', $options );
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
