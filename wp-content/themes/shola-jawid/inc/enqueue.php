<?php
// inc/enqueue.php — front-end CSS/JS registration.
//
// Only style.css (the theme's required header file) is enqueued at this
// scaffolding stage. The ported v6 main.css/main.js/fonts are wired up in
// Phase 4 as templates are converted — enqueuing them here first would
// reference files that don't exist yet.

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue theme styles.
 *
 * @return void
 */
function shola_enqueue_assets() {
	wp_enqueue_style( 'shola-jawid-style', get_stylesheet_uri(), array(), wp_get_theme()->get( 'Version' ) );
}
add_action( 'wp_enqueue_scripts', 'shola_enqueue_assets' );
