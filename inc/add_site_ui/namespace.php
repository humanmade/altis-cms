<?php

namespace Altis\CMS\Add_Site_UI;

/**
 * Setup the Add Site UI.
 */
function bootstrap() {
	add_action( 'network_admin_menu', __NAMESPACE__ . '\\register_menu_page' );
	add_action( 'admin_init', __NAMESPACE__ . '\\add_site_form_handler' );
}

/**
 * Register the menu page for the overrided Add New Site page.
 *
 * @return void
 */
function register_menu_page() {
	if ( ! is_network_admin() ) {
		return;
	}
	remove_submenu_page( 'sites.php', 'site-new.php' );
	add_submenu_page( 'sites.php', __( 'Add New', 'altis' ), __( 'Add New', 'altis' ), 'create_sites', 'altis-add-site', __NAMESPACE__ . '\\output_add_site_page' );
}

/**
 * Display the New Site page in the admin.
 */
function output_add_site_page() {
	?>
	<div class="wrap">
		<h1 id="add-new-site"><?php _e( 'Add New Site' ); ?></h1>
		<p>
			<?php
			printf(
				/* translators: %s: asterisk to mark required form fields. */
				__( 'Required fields are marked %s' ),
				'<span class="required">*</span>'
			);
			?>
		</p>
		<form method="post" action="<?php echo network_admin_url( 'sites.php?page=altis-add-site' ); ?>" novalidate="novalidate">
			<?php wp_nonce_field( 'altis-add-site' ); ?>
			<table class="form-table">
				<tr class="form-field form-required">
					<th scope="row"><label for="site-address"><?php _e( 'Site Address (URL)' ); ?> <span class="required">*</span></label></th>
					<td>
						<input name="url" type="text" class="regular-text" id="site-address" aria-describedby="site-address-desc" autocapitalize="none" autocorrect="off" required />
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
}

/**
 * Handler function for the Add Site form submission.
 */
function add_site_form_handler() {
	if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'altis-add-site' ) ) {
		return;
	}

	$url = sanitize_text_field( $_POST['url'] );
	if ( strpos( $url, 'http' ) !== 0 ) {
		$url = 'https://' . $url;
	}
	$url = wp_parse_url( $url );
	$title = sanitize_text_field( $_POST['title'] );
	$language = $_POST['language'] ?? null;

	$result = wp_insert_site( [
		'domain'  => $url['host'],
		'path'    => $url['path'],
		'lang_id' => $language, // Todo: Language not updating currently.
		'title'   => $title,
	] );

	if ( is_wp_error( $result ) ) {
		print_r( $result ); // Todo: redirect with error
		exit;
	}

	wp_redirect( add_query_arg( 'message', 'created', network_admin_url( 'sites.php?page=altis-add-site' ) ) ); // Todo: Show created message
	exit;
}
