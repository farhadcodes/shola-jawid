/**
 * Rewrites the Persian Calendar plugin's hardcoded Iranian Jalali month
 * names (فروردین, اردیبهشت, ...) to the Afghan Dari variant (حمل, ثور,
 * ...) inside wp-admin — the front-end equivalent of this fix is
 * shola_convert_jalali_months_to_dari() in inc/template-tags.php, which
 * filters the plain-text output of date_i18n()/wp_date(); wp-admin's date
 * picker (both the classic-editor "انتشار" metabox and the block editor's
 * schedule panel) renders its own month names client-side in JavaScript
 * instead, so a PHP output filter can't reach it.
 *
 * Persian Calendar exposes its month-name list as a single array,
 * window.PersianDateConverter.PERSIAN_MONTHS (assets/js/persian-calendar.js)
 * — every other part of the plugin's admin JS (the date-picker widget
 * itself, gutenberg.js's schedule-button/hint text) reads month names from
 * that exact array object by reference, not a copy, at the moment they
 * render — never caching it at load time. Splicing new strings into that
 * same array in place therefore corrects every surface at once, without
 * touching a single plugin file, so a plugin update can't silently revert
 * this (see inc/admin-jalali-months.php for why this script only loads on
 * the screens where Persian Calendar's own admin script does).
 */
( function () {
	'use strict';

	if ( ! window.PersianDateConverter || ! Array.isArray( window.PersianDateConverter.PERSIAN_MONTHS ) ) {
		return;
	}

	var dariMonths = [
		'حمل', 'ثور', 'جوزا', 'سرطان', 'اسد', 'سنبله',
		'میزان', 'عقرب', 'قوس', 'جدی', 'دلو', 'حوت'
	];

	window.PersianDateConverter.PERSIAN_MONTHS.splice.apply(
		window.PersianDateConverter.PERSIAN_MONTHS,
		[ 0, dariMonths.length ].concat( dariMonths )
	);
} )();
