<?php

namespace HM\Platform\CMS;

function bootstrap() {
	require_once __DIR__ . '/remove_updates/namespace.php';

	Remove_Updates\bootstrap();
}
