<?php
/**
 * Template part: template-parts/leaflets/leaflet-item.php — one entry in
 * the تراکت‌ها (Leaflets) stream, shared by page-leaflets.php's paginated
 * archive. Added 2026-09-17. Not under template-parts/cards/ — this isn't
 * a card anatomy (no boxed container, no dek/byline block); it's a plain
 * image + caption pairing, per the Aeon "whitespace and typography, not
 * containers" principle this section's design brief calls for.
 *
 * The image is the content here, not a thumbnail beside text — no fixed
 * aspect-ratio box, no object-fit crop: the leaflet/banner/poster renders
 * at its own real proportions (portrait posters and landscape banners
 * both need to stay uncropped), capped only by the column width via CSS.
 *
 * Alternating caption position (odd entries: reading-start side; even:
 * reading-end side) is handled entirely in CSS via :nth-child on
 * .leaflet-item, not here — this partial has no knowledge of its own
 * position in the stream, matching how every other looped partial on this
 * site stays position-agnostic.
 *
 * Image is wrapped in an <a> to the full-size attachment file — added
 * 2026-09-17 for the fullscreen lightbox feature. This is also the
 * complete no-JS fallback: with JavaScript disabled, this link is never
 * intercepted and behaves exactly as a plain link to the image file
 * (main.js's delegated click-listener is what turns it into a lightbox
 * trigger when JS is available). `data-leaflet-index` matches this
 * item's position in page-leaflets.php's server-rendered JSON dataset
 * (`#leaflet-lightbox-data`), so main.js knows which entry to open
 * without a second lookup.
 *
 * @param array $args {
 *     @type WP_Post $post Post object. Defaults to the global $post.
 *     @type int     $index This item's position in the current page's
 *                          batch (0-based) — must match its position in
 *                          the JSON dataset page-leaflets.php builds.
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$leaflet_post = isset( $args['post'] ) ? $args['post'] : get_post();

if ( ! $leaflet_post ) {
	return;
}

$leaflet_index = isset( $args['index'] ) ? (int) $args['index'] : 0;
$leaflet_title = get_the_title( $leaflet_post );
$leaflet_full  = wp_get_attachment_image_url( get_post_thumbnail_id( $leaflet_post ), 'full' );
?>
<article class="leaflet-item reveal">
	<a href="<?php echo esc_url( $leaflet_full ); ?>" data-leaflet-trigger data-leaflet-index="<?php echo esc_attr( $leaflet_index ); ?>">
		<?php
		/*
		 * shola_get_featured_image()'s generic-fallback path is
		 * unreachable here by construction, not just by convention —
		 * shola_get_leaflets_query() (inc/template-tags.php) already
		 * excludes any leaflet with no featured image, so every
		 * $leaflet_post reaching this partial is guaranteed to have a
		 * real one.
		 */
		echo shola_get_featured_image( $leaflet_post, 'full', array( 'loading' => 'lazy', 'class' => 'leaflet-item-image' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- shola_get_featured_image() escapes internally.
		?>
	</a>
	<div class="leaflet-item-meta">
		<?php if ( $leaflet_title ) : ?>
			<p class="leaflet-item-caption"><?php echo esc_html( $leaflet_title ); ?></p>
		<?php endif; ?>
		<p class="card-byline">
			<?php echo shola_date_icon(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static, trusted inline SVG, not user input. ?>
			<time datetime="<?php echo esc_attr( shola_get_iso_datetime( $leaflet_post ) ); ?>"><?php echo esc_html( get_the_date( '', $leaflet_post ) ); ?></time>
		</p>
	</div>
</article>
