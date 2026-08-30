/**
 * Shola Core — video-guide admin repeater (Settings → راهنمای ویدیویی
 * only; the /video-guide front-end route has no form, so it doesn't
 * enqueue this file). Adds/removes {title, url} row pairs — each row's
 * inputs share an explicit numeric index (shcore_video_guide_entries[N][title]/
 * [url]) rather than empty-bracket names (entries[][title]/entries[][url]):
 * PHP's query-string parser does NOT reliably pair separately-bracketed
 * empty-index fields back into the same row on submit, so an explicit
 * shared index per row is required, not just convenient. Indexes don't
 * need to stay contiguous after a row is removed — sanitize_entries()
 * (class-video-guide.php) iterates whatever keys are present.
 */
( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {
		var container = document.getElementById( 'shcore-vg-repeater' );
		var addButton = document.getElementById( 'shcore-vg-add-row' );
		var template  = document.getElementById( 'shcore-vg-row-template' );

		if ( ! container || ! addButton || ! template ) {
			return;
		}

		var nextIndex = container.querySelectorAll( '.shcore-vg-row' ).length;

		addButton.addEventListener( 'click', function () {
			var html = template.innerHTML.replace( /__INDEX__/g, String( nextIndex ) );
			var wrapper = document.createElement( 'div' );
			wrapper.innerHTML = html.trim();
			container.appendChild( wrapper.firstElementChild );
			nextIndex++;
		} );

		container.addEventListener( 'click', function ( event ) {
			var removeBtn = event.target.closest( '.shcore-vg-remove-row' );
			if ( ! removeBtn ) {
				return;
			}
			var row = removeBtn.closest( '.shcore-vg-row' );
			if ( row ) {
				row.remove();
			}
		} );
	} );
} )();
