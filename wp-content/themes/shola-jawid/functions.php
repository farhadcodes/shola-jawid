<?php
// Theme bootstrap: requires the inc/ modules. Keep this file thin — logic lives in inc/.

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once get_theme_file_path( 'inc/setup.php' );
require_once get_theme_file_path( 'inc/enqueue.php' );
require_once get_theme_file_path( 'inc/template-tags.php' );
require_once get_theme_file_path( 'inc/admin-jalali-months.php' );
