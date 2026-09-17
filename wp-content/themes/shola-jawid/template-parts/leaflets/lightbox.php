<?php
/**
 * Template part: template-parts/leaflets/lightbox.php — the shared
 * fullscreen تراکت viewer, added 2026-09-17. Included once by both
 * page-leaflets.php (indexed, prev/next through that page's loaded batch)
 * and front-page.php (single-image mode, no prev/next — see main.js's
 * `data-leaflet-trigger` wiring for how the two modes differ).
 *
 * A native <dialog>, not a hand-built overlay <div> — `.showModal()`
 * still gives a real modal top-layer element and an implicit dialog
 * role for free. Focus trap and Escape-to-close are implemented
 * explicitly in main.js rather than relied on as native behavior:
 * verified live during implementation that this dialog's native "close"
 * event, and a real (trusted, OS-level) Escape keypress, did not
 * actually close it in the browser used to test this feature — see
 * main.js's own comment on leafletClose() for the full finding.
 *
 * Markup structure is deliberate for the "click outside the image closes
 * it" requirement: the close/prev/next buttons and the image/caption
 * `.leaflet-lightbox-stage` are direct children of the <dialog>. A click
 * landing on the <dialog> element itself (not one of those children) is
 * "outside the image" — main.js checks `event.target === dialog`, which
 * only works because those children fully account for the visible
 * control surface, leaving the rest of the (viewport-filling) dialog box
 * as genuine "outside" space.
 *
 * The caption container always exists (the date always has content —
 * native post_date is never empty) but the title/caption paragraph
 * inside it is only ever created by JS when that entry actually has one
 * — see main.js's leafletRender(), not this file, which renders no
 * placeholder for it at all.
 *
 * @package shola-jawid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<dialog id="leaflet-lightbox" class="leaflet-lightbox" aria-label="<?php esc_attr_e( 'نمایش تراکت', 'shola-jawid' ); ?>">
	<button type="button" class="leaflet-lightbox-close" aria-label="<?php esc_attr_e( 'بستن', 'shola-jawid' ); ?>">
		<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M5 5l14 14M19 5L5 19"/></svg>
	</button>
	<?php
	/*
	 * Arrow direction confirmed against page-selected.php's own real
	 * pagination ('prev_text' => '→', 'next_text' => '←') rather than
	 * assumed: in this site's RTL layout, "next" (chronologically older)
	 * is the left-pointing arrow at the reading-end side; "previous"
	 * (newer) is right-pointing at the reading-start side. Positioned via
	 * inset-inline-start/-end (main.css), not physical left/right, so
	 * this mirrors correctly under dir="ltr" with no changes.
	 */
	?>
	<button type="button" class="leaflet-lightbox-nav leaflet-lightbox-prev" aria-label="<?php esc_attr_e( 'تراکت قبلی، جدیدتر', 'shola-jawid' ); ?>">
		<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 6l6 6-6 6"/></svg>
	</button>
	<div class="leaflet-lightbox-stage">
		<img class="leaflet-lightbox-image" src="" alt="">
		<div class="leaflet-lightbox-caption"></div>
	</div>
	<button type="button" class="leaflet-lightbox-nav leaflet-lightbox-next" aria-label="<?php esc_attr_e( 'تراکت بعدی، قدیمی‌تر', 'shola-jawid' ); ?>">
		<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 6l-6 6 6 6"/></svg>
	</button>
</dialog>
