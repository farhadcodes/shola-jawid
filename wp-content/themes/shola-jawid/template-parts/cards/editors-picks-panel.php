<?php
/**
 * Template part: template-parts/cards/editors-picks-panel.php — the
 * گزیده‌ها (Editor's Picks) panel embedded in front-page.php's
 * تازه‌ترین مقالات grid, added 2026-10-01. Replaces the پربازدیدترین
 * (Most Viewed) panel in this one homepage slot, per Farhad's explicit
 * request — View_Counter and most-viewed-panel.php themselves are
 * untouched and still used elsewhere (single.php's article sidebar,
 * taxonomy-topic.php's پرخواننده‌ترین sort tab).
 *
 * Revision history on this panel's visual anatomy:
 * - 2026-10-01: based on most-viewed-panel.php's structure, #1 item
 *   shown bigger/featured with a thumbnail.
 * - 2026-10-02 (critique round): thumbnail dropped, #1 given a white
 *   mini-card + kicker to stay visually "featured", other items
 *   single-line truncated to hold the column's total height flat.
 * - 2026-10-02 (reverted by Farhad): the featured treatment and
 *   truncation were both explicitly rejected — "all of them are
 *   important at the same time," titles must show in full, not
 *   truncated. All 5 items are now rendered identically: number, full
 *   (wrapped, untruncated) title at 12px, and a publication date below
 *   it reusing the site's existing `.card-byline` date treatment
 *   (shola_date_icon() + <time>) at its established 10px small-meta
 *   size (see `.selected-row-body .card-byline` in main.css for the
 *   same established pattern). This makes the panel taller again by
 *   design — Farhad explicitly chose readability over the previous
 *   round's height-matching constraint.
 *
 * A new set of CSS classes (.editors-pick-panel, .ep-*) was written
 * rather than reusing most-viewed-panel.php's .mv-* classes or editing
 * that file: the two panels have independent content sources and
 * histories, and most-viewed-panel.php must keep working unmodified for
 * its two remaining call sites.
 *
 * @param array $args {
 *     @type WP_Post[] $posts Up to 5 گزیده‌ها posts, latest first.
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$editors_picks = isset( $args['posts'] ) ? (array) $args['posts'] : array();

if ( ! $editors_picks ) {
	return;
}
?>
<div class="editors-pick-panel reveal">
	<p class="ep-title"><?php esc_html_e( 'گزیده‌ها', 'shola-jawid' ); ?></p>

	<ol class="ep-list">
		<?php foreach ( $editors_picks as $index => $item ) : ?>
			<li>
				<a href="<?php echo esc_url( get_permalink( $item ) ); ?>">
					<span class="ep-num" aria-hidden="true"><?php echo esc_html( shola_to_persian_digits( $index + 1 ) ); ?></span>
					<span class="ep-item-text">
						<span class="ep-item-title"><?php echo esc_html( get_the_title( $item ) ); ?></span>
						<p class="card-byline">
							<?php echo shola_date_icon(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static, trusted inline SVG, not user input. ?>
							<time datetime="<?php echo esc_attr( shola_get_iso_datetime( $item ) ); ?>"><?php echo esc_html( get_the_date( '', $item ) ); ?></time>
						</p>
					</span>
				</a>
			</li>
		<?php endforeach; ?>
	</ol>

	<a class="link-more" href="<?php echo esc_url( home_url( '/editors-picks/' ) ); ?>"><?php esc_html_e( 'مشاهدهٔ آرشیو گزیده‌ها', 'shola-jawid' ); ?> <span class="arr">←</span></a>
</div>
