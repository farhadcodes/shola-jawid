<?php
/**
 * Template part: template-parts/cards/announcement-spotlight.php — the
 * accent-colored اطلاعیه tile embedded in front-page.php's تازه‌ترین
 * مقالات grid (Phase 11, 2026-09-07, client-requested — see
 * docs/CHANGELOG.md). Sits in the grid's own visually-leftmost slot,
 * spanning both rows on desktop (see .card-spotlight,
 * assets/css/main.css) so it reads as a distinct, always-noticeable
 * block rather than another article card.
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
 *     @type WP_Post $post The latest announcement.
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$announcement = isset( $args['post'] ) ? $args['post'] : null;

if ( ! $announcement ) {
	return;
}
?>
<article class="card-spotlight reveal">
	<div class="card-spotlight-body">
		<p class="type-label">
			<svg class="glyph" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
			<span><?php esc_html_e( 'اطلاعیه', 'shola-jawid' ); ?></span>
		</p>
		<h3 class="h-card"><a href="<?php echo esc_url( get_permalink( $announcement ) ); ?>"><?php echo esc_html( get_the_title( $announcement ) ); ?></a></h3>
		<p class="card-dek"><?php echo esc_html( wp_trim_words( get_the_excerpt( $announcement ), 24 ) ); ?></p>
		<p class="card-byline"><time datetime="<?php echo esc_attr( shola_get_iso_datetime( $announcement ) ); ?>"><?php echo esc_html( get_the_date( '', $announcement ) ); ?></time></p>
	</div>
	<a class="link-more" href="<?php echo esc_url( home_url( '/announcements/' ) ); ?>"><?php esc_html_e( 'همهٔ اطلاعیه‌ها', 'shola-jawid' ); ?> <span class="arr">←</span></a>
</article>
