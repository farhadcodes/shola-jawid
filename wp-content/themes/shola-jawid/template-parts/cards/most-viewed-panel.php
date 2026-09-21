<?php
/**
 * Template part: template-parts/cards/most-viewed-panel.php — the
 * پربازدیدترین (Most Viewed) panel embedded in front-page.php's
 * تازه‌ترین مقالات grid (2026-09-15, client-requested via Farhad,
 * inspired by an aawsat.com reference design — see docs/CHANGELOG.md).
 *
 * Ranked by real reader views (`SholaCore\View_Counter`, shcore_view_count
 * postmeta) across articles, reports (both post type `post`, distinguished
 * only by the `report` taxonomy — deliberately NOT excluded here, unlike
 * تازه‌ترین مقالات's own query), and اطلاعیه‌ها. Publications/documents are
 * deliberately excluded — a PDF opened once isn't comparable to an article
 * actually read, and the client confirmed "articles, reports, announcements"
 * is the intended scope, not the full site.
 *
 * Visual structure: item #1 gets a featured image (shola_get_featured_image()
 * — CLAUDE.md §5 — falls back to fallback.png for an اطلاعیه, which has no
 * featured image of its own), items #2-5 are plain number + title rows with
 * dividers, no image, no icon/badge chrome around the number — Farhad's
 * 2026-09-15 revision explicitly asked for large, frameless numbers and
 * "only the title, nothing else" after the first version (a red-background
 * tile with a bordered number badge) read as too busy next to the aawsat.com
 * reference's plain, clean list. No Day/Week toggle — the underlying counter
 * only tracks a lifetime total (see class-view-counter.php), not a
 * timestamped log, so a toggle that didn't actually change the ranking would
 * be worse than no toggle at all; Farhad confirmed dropping it, 2026-09-15.
 *
 * Count raised 5 -> 6 (items #2-6, not #2-5), 2026-09-21, per Farhad's
 * explicit ask: the panel had visible empty space below its 5th item.
 *
 * @param array $args {
 *     @type WP_Post[] $posts Up to 6 posts, most-viewed first.
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$most_viewed = isset( $args['posts'] ) ? (array) $args['posts'] : array();

if ( ! $most_viewed ) {
	return;
}

$featured = array_shift( $most_viewed );
$rest     = array_slice( $most_viewed, 0, 5 );
?>
<div class="most-viewed-panel reveal">
	<p class="mv-title"><?php esc_html_e( 'پربازدیدترین‌ها', 'shola-jawid' ); ?></p>

	<a class="mv-item mv-item--featured" href="<?php echo esc_url( get_permalink( $featured ) ); ?>">
		<span class="mv-thumb"><?php echo shola_get_featured_image( $featured, 'shola_card', array( 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- shola_get_featured_image() escapes internally. ?></span>
		<span class="mv-item-body">
			<span class="mv-num" aria-hidden="true"><?php echo esc_html( shola_to_persian_digits( 1 ) ); ?></span>
			<span class="mv-item-title"><?php echo esc_html( get_the_title( $featured ) ); ?></span>
		</span>
	</a>

	<?php if ( $rest ) : ?>
		<ol class="mv-list" start="2">
			<?php foreach ( $rest as $index => $item ) : ?>
				<li>
					<a href="<?php echo esc_url( get_permalink( $item ) ); ?>">
						<span class="mv-num" aria-hidden="true"><?php echo esc_html( shola_to_persian_digits( $index + 2 ) ); ?></span>
						<span class="mv-item-title"><?php echo esc_html( get_the_title( $item ) ); ?></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ol>
	<?php endif; ?>
</div>
