<?php
// inc/admin-jalali-months.php — wp-admin counterpart to
// shola_convert_jalali_months_to_dari() (inc/template-tags.php): rewrites
// the Persian Calendar plugin's Iranian Jalali month names to the Afghan
// Dari variant this site needs, but inside wp-admin's own client-rendered
// date pickers, which that PHP filter can't reach.

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueues assets/js/admin-jalali-months.js — which mutates Persian
 * Calendar's in-memory month-name array to the Dari variant — on any
 * wp-admin screen where Persian Calendar has already enqueued its own
 * 'persian-calendar-main' script (classic-editor post/edit/comment
 * screens, per Persian Calendar's own enqueue_admin_timewrap_assets()).
 * Declared as a dependency of 'persian-calendar-main' rather than
 * unconditionally enqueued, so this never pulls Persian Calendar's script
 * onto a screen it wouldn't otherwise load on, and never runs before that
 * script has set up window.PersianDateConverter for it to mutate.
 *
 * Hooked at priority 20 (Persian Calendar registers its own
 * admin_enqueue_scripts callback at the default priority 10), so
 * wp_script_is() below sees an accurate answer for this screen.
 *
 * @return void
 */
function shola_admin_enqueue_jalali_months_classic() {
	if ( ! wp_script_is( 'persian-calendar-main', 'enqueued' ) ) {
		return;
	}

	wp_enqueue_script(
		'shola-admin-jalali-months',
		get_theme_file_uri( 'assets/js/admin-jalali-months.js' ),
		array( 'persian-calendar-main' ),
		wp_get_theme()->get( 'Version' ),
		true
	);
}
add_action( 'admin_enqueue_scripts', 'shola_admin_enqueue_jalali_months_classic', 20 );

/**
 * Same fix as shola_admin_enqueue_jalali_months_classic(), for the block
 * editor: Persian Calendar loads its Gutenberg integration
 * ('persian-calendar-main' + gutenberg.js) via enqueue_block_editor_assets
 * instead of admin_enqueue_scripts, so it needs its own hook.
 *
 * @return void
 */
function shola_admin_enqueue_jalali_months_block_editor() {
	if ( ! wp_script_is( 'persian-calendar-main', 'enqueued' ) ) {
		return;
	}

	wp_enqueue_script(
		'shola-admin-jalali-months',
		get_theme_file_uri( 'assets/js/admin-jalali-months.js' ),
		array( 'persian-calendar-main' ),
		wp_get_theme()->get( 'Version' ),
		true
	);
}
add_action( 'enqueue_block_editor_assets', 'shola_admin_enqueue_jalali_months_block_editor', 20 );
