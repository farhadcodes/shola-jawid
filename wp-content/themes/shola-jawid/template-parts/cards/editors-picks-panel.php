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
 * Visual anatomy based on most-viewed-panel.php (same "keep the same
 * structure... only the function should be different" instruction),
 * with two deliberate deviations requested 2026-10-01: the #1 featured
 * item has no thumbnail (title/number only, like items #2-6), and a
 * `.link-more` archive link (the same pattern as the leaflets teaser on
 * this page) is appended at the bottom, linking to /editors-picks/. A
 * new set of CSS classes (.editors-pick-panel, .ep-*) was written rather
 * than reusing most-viewed-panel.php's .mv-* classes or editing that
 * file: the two panels now have independent content sources and
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

$featured = array_shift( $editors_picks );
$rest     = array_slice( $editors_picks, 0, 4 );
?>
<div class="editors-pick-panel reveal">
	<p class="ep-title"><?php esc_html_e( 'گزیده‌ها', 'shola-jawid' ); ?></p>

	<a class="ep-item ep-item--featured" href="<?php echo esc_url( get_permalink( $featured ) ); ?>">
		<span class="ep-item-body">
			<span class="ep-num" aria-hidden="true"><?php echo esc_html( shola_to_persian_digits( 1 ) ); ?></span>
			<span class="ep-item-title"><?php echo esc_html( get_the_title( $featured ) ); ?></span>
		</span>
	</a>

	<?php if ( $rest ) : ?>
		<ol class="ep-list" start="2">
			<?php foreach ( $rest as $index => $item ) : ?>
				<li>
					<a href="<?php echo esc_url( get_permalink( $item ) ); ?>">
						<span class="ep-num" aria-hidden="true"><?php echo esc_html( shola_to_persian_digits( $index + 2 ) ); ?></span>
						<span class="ep-item-title"><?php echo esc_html( get_the_title( $item ) ); ?></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ol>
	<?php endif; ?>

	<a class="link-more" href="<?php echo esc_url( home_url( '/editors-picks/' ) ); ?>"><?php esc_html_e( 'مشاهدهٔ آرشیو گزیده‌ها', 'shola-jawid' ); ?> <span class="arr">←</span></a>
</div>
