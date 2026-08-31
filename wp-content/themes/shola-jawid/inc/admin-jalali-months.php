<?php
// inc/admin-jalali-months.php — wp-admin counterpart to
// shola_convert_jalali_months_to_dari() (inc/template-tags.php): rewrites
// the Persian Calendar plugin's Iranian Jalali month names to the Afghan
// Dari variant this site needs, but inside wp-admin's own client-rendered
// date pickers, which that PHP filter can't reach. Also fixes Persian
// Calendar's Quick Edit date field, which — independent of the month-name
// issue — never switches to Jalali at all on this WordPress version (see
// assets/js/admin-quickedit-jalali.js for why).

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

	shola_admin_enqueue_quickedit_jalali();
}
add_action( 'admin_enqueue_scripts', 'shola_admin_enqueue_jalali_months_classic', 20 );

/**
 * Enqueues assets/js/admin-quickedit-jalali.js, which fixes the posts/
 * pages/CPT list-table Quick Edit date field: Persian Calendar's own
 * `.editinline` click handler (admin-timewrap.js) never renders the
 * Jalali fields at all here, because it looks for the row's date data
 * inside `.closest('td')`, and the title column in this WordPress
 * version is a `<th>`, not a `<td>` — confirmed live (2026-08-31) by
 * opening Quick Edit and finding WordPress core's native Gregorian date
 * fields untouched (with Persian-language Gregorian month names, e.g.
 * "آگوست" for August — the wrong calendar entirely, not just the wrong
 * month-name variant).
 *
 * Only enqueued when Persian Calendar's own admin-timewrap script is
 * present on this screen ('persian-calendar-admin-timewrap', enqueued
 * by its enqueue_admin_timewrap_assets() on post/edit/comment screens)
 * — same reasoning as shola_admin_enqueue_jalali_months_classic(): never
 * pulls in functionality this screen wouldn't otherwise have, and
 * guarantees window.PersianDateConverter and admin-timewrap.js's own
 * (still-working) Jalali→Gregorian write-back handlers are already
 * loaded before this script runs.
 *
 * @return void
 */
function shola_admin_enqueue_quickedit_jalali() {
	if ( ! wp_script_is( 'persian-calendar-admin-timewrap', 'enqueued' ) ) {
		return;
	}

	wp_enqueue_script(
		'shola-admin-quickedit-jalali',
		get_theme_file_uri( 'assets/js/admin-quickedit-jalali.js' ),
		array( 'jquery', 'persian-calendar-main', 'persian-calendar-admin-timewrap' ),
		wp_get_theme()->get( 'Version' ),
		true
	);
}

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
