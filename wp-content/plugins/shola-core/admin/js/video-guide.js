/**
 * Shola Core — video-guide thumbnail grid (Settings → راهنمای ویدیویی).
 * Click a thumbnail: swap it for a live YouTube iframe embed in place,
 * autoplaying (the click itself is the required user-gesture browsers
 * need to allow autoplay-with-sound). Plain vanilla JS, event
 * delegation on the grid container — no dependency on jQuery, this is
 * a single click handler, not worth the library.
 */
( function () {
	'use strict';

	document.addEventListener( 'click', function ( event ) {
		var thumb = event.target.closest( '.shcore-video-thumb' );
		if ( ! thumb || thumb.classList.contains( 'is-playing' ) ) {
			return;
		}

		var videoId = thumb.getAttribute( 'data-video-id' );
		if ( ! videoId ) {
			return;
		}

		var iframe = document.createElement( 'iframe' );
		iframe.src = 'https://www.youtube-nocookie.com/embed/' + encodeURIComponent( videoId ) + '?autoplay=1&rel=0';
		iframe.title = thumb.getAttribute( 'data-video-title' ) || '';
		iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
		iframe.allowFullscreen = true;

		var img = thumb.querySelector( 'img' );
		if ( img ) {
			img.remove();
		}

		thumb.classList.add( 'is-playing' );
		thumb.appendChild( iframe );
	} );
} )();
