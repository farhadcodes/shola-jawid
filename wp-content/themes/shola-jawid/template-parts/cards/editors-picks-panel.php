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
 * with deliberate deviations requested 2026-10-01/2026-10-02: the #1
 * featured item has no thumbnail (title/number only, like items #2-5);
 * a `.link-more` archive link (same pattern as the leaflets teaser on
 * this page) sits at the bottom, linking to /editors-picks/; the header
 * now matches the weight of the page's other section headings
 * (`.h-section`-style, not a plain paragraph); and the featured item
 * gets a white mini-card treatment with a red accent border plus its
 * own category kicker, so it reads as genuinely featured now that it
 * has no image to do that job. The kicker is deliberately featured-only,
 * not on items #2-5 — the client flagged the panel as looking too tall
 * next to its row neighbors, so metadata was added only where it does
 * the most hierarchy work, not uniformly. A new set of CSS classes
 * (.editors-pick-panel, .ep-*) was written rather than reusing
 * most-viewed-panel.php's .mv-* classes or editing that file: the two
 * panels now have independent content sources and histories, and
 * most-viewed-panel.php must keep working unmodified for its two
 * remaining call sites.
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

$featured_terms = get_the_terms( $featured, 'editors_pick_category' );
$featured_term  = ( $featured_terms && ! is_wp_error( $featured_terms ) ) ? array_shift( $featured_terms ) : false;
/*
 * Falls back to a static "پیشنهاد ویژه" label when the post has no
 * گزیده دسته‌بندی term yet — the kicker's job is to visually mark #1 as
 * featured, which must not silently disappear just because an editor
 * hasn't categorized a post yet.
 */
$featured_kicker = $featured_term ? $featured_term->name : __( 'پیشنهاد ویژه', 'shola-jawid' );
?>
<div class="editors-pick-panel reveal">
	<p class="ep-title"><?php esc_html_e( 'گزیده‌ها', 'shola-jawid' ); ?></p>

	<a class="ep-item ep-item--featured" href="<?php echo esc_url( get_permalink( $featured ) ); ?>">
		<span class="ep-item-body">
			<span class="ep-num" aria-hidden="true"><?php echo esc_html( shola_to_persian_digits( 1 ) ); ?></span>
			<span class="ep-item-text">
				<span class="ep-kicker"><?php echo esc_html( $featured_kicker ); ?></span>
				<span class="ep-item-title"><?php echo esc_html( get_the_title( $featured ) ); ?></span>
			</span>
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
