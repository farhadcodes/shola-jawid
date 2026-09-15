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
	/*
	 * masthead date REST URL (2026-09-15) — see the endpoint's own
	 * docblock (inc/template-tags.php, shola_register_masthead_date_route())
	 * for why this exists: main.js fetches it client-side to replace the
	 * server-rendered masthead date, which a full-page cache (the live
	 * site's Hostinger/LiteSpeed setup) can otherwise serve stale for as
	 * long as a page sits in cache. rest_url(), not a hardcoded
	 * '/wp-json/' path — correct even if the site's permalink structure
	 * or REST API prefix is ever non-default.
	 */
	wp_localize_script(
		'shola-jawid-main',
		'sholaMastheadDate',
		array( 'endpoint' => esc_url_raw( rest_url( 'shola/v1/masthead-date' ) ) )
	);
}
add_action( 'wp_enqueue_scripts', 'shola_enqueue_assets' );

// Contact Form 7 (CLAUDE.md §3 whitelist) must render using the theme's
// own markup/CSS, not the plugin's default styles — disable CF7's own
// stylesheet entirely rather than fighting it with overrides.
add_filter( 'wpcf7_load_css', '__return_false' );
