<?php
/**
 * Template part: template-parts/cards/announcement-spotlight.php — the
 * accent-colored اطلاعیه tile embedded in front-page.php's تازه‌ترین
 * مقالات grid (Phase 11, 2026-09-07, client-requested — see
 * docs/CHANGELOG.md). Sits in the grid's own visually-leftmost slot, one
 * grid row tall (matching a single article card) on desktop — see
 * .card-spotlight, assets/css/main.css.
 *
 * Shows up to 5 اطلاعیه‌ها (raised from 3, 2026-09-21 — client feedback,
 * relayed by Farhad with an annotated screenshot, that the tile looked
 * "a little empty" below the 3rd item), not just the latest one: the
 * newest rendered prominently (title + excerpt + date), the other 4 as
 * a compact, smaller list (title + date only) below it.
 *
 * Each item carries a numbered badge (۱–۵, `.card-spotlight-index`) —
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
 *     @type WP_Post[] $posts 1-5 اطلاعیه posts, newest first.
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
$rest     = array_slice( $announcements, 0, 4 );
?>
<article class="card-spotlight reveal">
	<div class="card-spotlight-body">
		<p class="type-label">
			<?php
			/*
			 * Filled bell, not the outlined/stroke version this label used
			 * until 2026-09-21 — Farhad's live follow-up: now that this tag
			 * sits on its own white background (not the tile's solid red),
			 * a thin outline glyph read as weak; a solid filled icon
			 * matches the tag's bold, letterspaced text weight better.
			 * fill="currentColor" so it still inherits --winston-red from
			 * .type-label's own color, same as the outlined version did via
			 * stroke="currentColor".
			 */
			?>
			<svg class="glyph" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/></svg>
			<span><?php esc_html_e( 'اطلاعیه‌ها و بیانیه‌ها', 'shola-jawid' ); ?></span>
		</p>
		<div class="card-spotlight-item card-spotlight-item--featured">
			<span class="card-spotlight-index" aria-hidden="true"><?php echo esc_html( shola_to_persian_digits( 1 ) ); ?></span>
			<div>
				<h3 class="h-card"><a href="<?php echo esc_url( get_permalink( $featured ) ); ?>"><?php echo esc_html( get_the_title( $featured ) ); ?></a></h3>
				<p class="card-dek"><?php echo esc_html( wp_trim_words( get_the_excerpt( $featured ), 16 ) ); ?></p>
				<p class="card-byline">
					<?php echo shola_date_icon(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static, trusted inline SVG, not user input. ?>
					<time datetime="<?php echo esc_attr( shola_get_iso_datetime( $featured ) ); ?>"><?php echo esc_html( get_the_date( '', $featured ) ); ?></time>
				</p>
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
