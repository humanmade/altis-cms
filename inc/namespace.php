<?php

namespace HM\Platform\CMS;

use function HM\Platform\get_config;

function bootstrap() {
	require_once __DIR__ . '/remove_updates/namespace.php';
	require_once __DIR__ . '/branding/namespace.php';

	$config = get_config()['modules']['cms'];
	Remove_Updates\bootstrap();

	if ( $config['branding'] ) {
		Branding\bootstrap();
	}

	if ( $config['login-logo'] ) {
		add_action( 'login_header', __NAMESPACE__ . '\\add_login_logo' );
	}
}

/**
 * Add the custom login logo to the login page.
 *
 * @return void
 */
function add_login_logo() {
	$logo = get_config()['modules']['cms']['login-logo'];
	?>
	<style>
		.login h1 a {
			background-image: url('<?php echo site_url( $logo ) ?>');
			background-size: contain;
			width: auto;
		}
	</style>
	<?php
}
