<?php

add_action( 'admin_init', 'HM\\Platform\\CMS\\Remove_Updates\\remove_update_nag' );
add_action( 'plugins_loaded', 'HM\\Platform\\CMS\\Remove_Updates\\remove_update_check_cron' );
