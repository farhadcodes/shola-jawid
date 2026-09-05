<?php
/**
 * Template part: template-parts/cards/issue-card.php — the portrait-cover
 * grid thumbnail (main.css §14, .issue-card). Distinct anatomy from
 * card.php, not a variant of it — 3:4 portrait, deliberate box-shadow, no
 * dek/byline — per the Phase 1.2 finding logged in docs/CHANGELOG.md.
 *
 * Generalized to `issue` + `document` CPTs, Phase B (2026-08-24, see
 * docs/CHANGELOG.md) — originally issue-only ("issue-archive grid
 * thumbnail"), now also used for اسناد حزب on the homepage. No
 * conditional-by-type logic needed: every field this partial renders
 * (featured image, title, post date) is already post-type-agnostic —
 * shola_get_featured_image() and shola_get_jalali_month_year_label() all
 * operate on any $post generically, none assume `issue`. Fields the
 * calling sections show elsewhere but this partial never has (issue
 * number, publication term, author/source, collection term, PDF link)
 * stay out of it — same "no dek/byline" anatomy for both types, not a
 * redesign.
 *
 * @param array $args {
 *     @type WP_Post $post Issue or document post object. Defaults to the global $post.
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$card_post = isset( $args['post'] ) ? $args['post'] : get_post();
if ( ! $card_post ) {
	return;
}

// v6 shows month+year for current-publication issues ("MAR ۲۰۲۶") and
// year-only for the archived publication's older issues ("۲۰۰۶"). Our
// data model always has a precise post_date either way, so showing
// month+year consistently is a deliberate, minor simplification —
// logged in docs/CHANGELOG.md — rather than fabricating a "precision
// unknown" state that doesn't reflect real data. Same convention now
// applies to documents. Rendered in Jalali/Dari (not Gregorian) since
// 2026-09-05 — see shola_get_jalali_month_year_label() docblock.
$date_label = shola_get_jalali_month_year_label( $card_post );
?>
<a href="<?php echo esc_url( get_permalink( $card_post ) ); ?>" class="issue-card">
	<div class="issue-card-media">
		<?php echo shola_get_featured_image( $card_post, 'shola_issue_card', array( 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- shola_get_featured_image() escapes internally. ?>
	</div>
	<p class="issue-card-title"><?php echo esc_html( get_the_title( $card_post ) ); ?></p>
	<p class="issue-card-date"><?php echo esc_html( $date_label ); ?></p>
</a>
