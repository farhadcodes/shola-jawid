/**
 * Fixes Persian Calendar's Quick Edit (posts list "ویرایش سریع") date
 * field, which never actually switches to Jalali at all — verified live
 * (2026-08-31): its own admin-timewrap.js binds a `.editinline` click
 * handler that does `jQuery(this).closest('td')` to find the row's date
 * data, but the posts list's title column is a `<th scope="row">` in
 * this WordPress version, not a `<td>` — so `.closest('td')` always
 * returns an empty set and the handler silently no-ops, leaving WP
 * core's native Gregorian month/day/year fields (with Persian-language
 * Gregorian month names, e.g. "آگوست" for August — not Dari, and not
 * even the right calendar) showing instead.
 *
 * Deliberately NOT a fix to that plugin file (would revert on the next
 * Persian Calendar update, and Farhad's explicit requirement is that
 * this survive one) — this is a fully independent `.editinline` click
 * handler that only reads from the hidden per-row `#inline_<id>` data
 * div's `.jj`/`.mm`/`.aa`/`.hh`/`.mn` children (present in every
 * WordPress version's Quick Edit markup — it's how WP core itself feeds
 * Quick Edit, not a Persian Calendar implementation detail) and the
 * global `window.PersianDateConverter` Persian Calendar already exposes
 * for exactly this purpose (see admin-jalali-months.js).
 *
 * The injected fields intentionally reuse admin-timewrap.js's own
 * field ids (jja/mma/aaa/hha/mna) and `.jalali` class, and are inserted
 * in the same place (after `.inline-edit-date legend`, hiding the
 * native `.timestamp-wrap`) — admin-timewrap.js's generic keyup/blur/
 * change handlers (bound to `#timestampdiv,.inline-edit-date`, not to
 * `.editinline`, so unaffected by the closest('td') bug) are what
 * actually convert an edited Jalali/Dari date back to Gregorian and
 * write it into WordPress core's real `name="mm"/"jj"/"aa"` fields on
 * Update — reusing that means this file only has to fix the *broken*
 * half (getting the Jalali fields to appear with the right date in the
 * first place), not reimplement the working half. If Persian Calendar
 * ever fixes its own `.editinline` handler, this one simply becomes
 * redundant (both would render the same correct fields) rather than
 * conflicting with it.
 */
( function ( $ ) {
	'use strict';

	if ( typeof $ === 'undefined' ) {
		return;
	}

	function pad2( val ) {
		return String( val ).padStart( 2, '0' );
	}

	function jalaliTimestampDiv( year, mon, day, hour, minu ) {
		var formattedDay = pad2( day );
		var formattedHour = pad2( hour );
		var formattedMinu = pad2( minu );
		var months = window.PersianDateConverter.PERSIAN_MONTHS;

		var html = '<div class="timestamp-wrap jalali">' +
			'<label><input type="text" id="jja" name="jja" value="' + formattedDay + '" size="2" maxlength="2" autocomplete="off" /></label>' +
			'<label><select id="mma" name="mma">';
		for ( var i = 1; i < 13; i++ ) {
			html += '<option value="' + i + '"' + ( i === mon ? ' selected="selected"' : '' ) + '>' + months[ i - 1 ] + '</option>';
		}
		html += '</select></label>' +
			'<label><input type="text" id="aaa" name="aaa" value="' + year + '" size="4" maxlength="4" autocomplete="off" /></label> در ' +
			'<input type="text" id="mna" name="mna" value="' + formattedMinu + '" size="2" maxlength="2" autocomplete="off" />:' +
			'<input type="text" id="hha" name="hha" value="' + formattedHour + '" size="2" maxlength="2" autocomplete="off" />' +
			'</div>';
		return html;
	}

	$( '#the-list' ).on( 'click', '.editinline', function () {
		if ( ! window.PersianDateConverter ) {
			return;
		}

		var tr = $( this ).closest( 'tr' );
		var year = tr.find( '.aa' ).text();

		if ( ! ( year > 1700 ) ) {
			return;
		}

		var month = tr.find( '.mm' ).text();
		var day = tr.find( '.jj' ).text();
		var hour = tr.find( '.hh' ).text();
		var minu = tr.find( '.mn' ).text();

		var jalali = window.PersianDateConverter.gregorianToJalali( parseInt( year, 10 ), parseInt( month, 10 ), parseInt( day, 10 ) );

		$( '.inline-edit-date .timestamp-wrap' ).hide();
		$( '.jalali' ).remove();
		$( '.inline-edit-date legend' ).after( jalaliTimestampDiv( jalali[ 0 ], jalali[ 1 ], jalali[ 2 ], hour, minu ) );
	} );
} )( window.jQuery );
