<?php
// inc/enqueue.php — front-end CSS/JS registration.

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue theme styles and scripts. style.css (the WP-required header
 * file) stays enqueued for its metadata; the actual design system is
 * assets/css/main.css, ported verbatim from the v6 prototype (Phase 4.1).
 *
 * @return void
 */
function shola_enqueue_assets() {
	$version = wp_get_theme()->get( 'Version' );

	wp_enqueue_style( 'shola-jawid-style', get_stylesheet_uri(), array(), $version );
	wp_enqueue_style( 'shola-jawid-main', get_theme_file_uri( 'assets/css/main.css' ), array( 'shola-jawid-style' ), $version );

	wp_enqueue_script( 'shola-jawid-main', get_theme_file_uri( 'assets/js/main.js' ), array(), $version, true );
}
add_action( 'wp_enqueue_scripts', 'shola_enqueue_assets' );
