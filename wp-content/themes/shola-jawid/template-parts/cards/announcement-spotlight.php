<?php
/**
 * Template part: template-parts/cards/announcement-spotlight.php — the
 * accent-colored اطلاعیه tile embedded in front-page.php's تازه‌ترین
 * مقالات grid (Phase 11, 2026-09-07, client-requested — see
 * docs/CHANGELOG.md). Sits in the grid's own visually-leftmost slot, one
 * grid row tall (matching a single article card) on desktop — see
 * .card-spotlight, assets/css/main.css.
 *
 * Shows up to 3 اطلاعیه‌ها, not just the latest one: the newest rendered
 * prominently (title + excerpt + date), the other 2 as a compact,
 * smaller list (title + date only) below it.
 *
 * Each item carries a numbered badge (۱/۲/۳, `.card-spotlight-index`) —
 * added 2026-09-07 (still Phase 11), after Farhad reviewed a live
 * screenshot and asked for the newest-to-oldest order to be
 * unambiguous "without being puzzled." The original version relied on
 * font-size/opacity alone to imply order; hierarchy now comes from an
 * explicit number, not just a size difference a visitor has to infer.
 *
 * Deliberately has its own "همهٔ اطلاعیه‌ها" link rather than sharing
 * تازه‌ترین مقالات's "همهٔ مقالات" link above the grid — this tile isn't
 * مقالات content, so it needs its own way out to its own archive.
 *
 * `announcement` has no featured image (title + body text only, per
 * shola-core\Post_Types) — unlike card.php there's no .card-media here,
 * by design, not an oversight.
 *
 * @param array $args {
 *     @type WP_Post[] $posts 1-3 اطلاعیه posts, newest first.
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$announcements = isset( $args['posts'] ) ? (array) $args['posts'] : array();

if ( ! $announcements ) {
	return;
}

$featured = array_shift( $announcements );
$rest     = array_slice( $announcements, 0, 2 );
?>
<article class="card-spotlight reveal">
	<div class="card-spotlight-body">
		<p class="type-label">
			<svg class="glyph" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
			<span><?php esc_html_e( 'اطلاعیه', 'shola-jawid' ); ?></span>
		</p>
		<div class="card-spotlight-item card-spotlight-item--featured">
			<span class="card-spotlight-index" aria-hidden="true"><?php echo esc_html( shola_to_persian_digits( 1 ) ); ?></span>
			<div>
				<h3 class="h-card"><a href="<?php echo esc_url( get_permalink( $featured ) ); ?>"><?php echo esc_html( get_the_title( $featured ) ); ?></a></h3>
				<p class="card-dek"><?php echo esc_html( wp_trim_words( get_the_excerpt( $featured ), 16 ) ); ?></p>
				<p class="card-byline"><time datetime="<?php echo esc_attr( shola_get_iso_datetime( $featured ) ); ?>"><?php echo esc_html( get_the_date( '', $featured ) ); ?></time></p>
			</div>
		</div>

		<?php if ( $rest ) : ?>
			<ul class="card-spotlight-more">
				<?php foreach ( $rest as $index => $item ) : ?>
					<li>
						<span class="card-spotlight-index card-spotlight-index--sm" aria-hidden="true"><?php echo esc_html( shola_to_persian_digits( $index + 2 ) ); ?></span>
						<a href="<?php echo esc_url( get_permalink( $item ) ); ?>"><?php echo esc_html( get_the_title( $item ) ); ?></a>
						<time datetime="<?php echo esc_attr( shola_get_iso_datetime( $item ) ); ?>"><?php echo esc_html( get_the_date( '', $item ) ); ?></time>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
	<a class="link-more" href="<?php echo esc_url( home_url( '/announcements/' ) ); ?>"><?php esc_html_e( 'همهٔ اطلاعیه‌ها', 'shola-jawid' ); ?> <span class="arr">←</span></a>
</article>
