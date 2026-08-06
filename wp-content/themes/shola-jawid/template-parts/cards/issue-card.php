<?php
/**
 * Template part: template-parts/cards/issue-card.php — the issue-archive
 * grid thumbnail (main.css §14, .issue-card). Distinct anatomy from
 * card.php, not a variant of it — 3:4 portrait, deliberate box-shadow —
 * per the Phase 1.2 finding logged in docs/CHANGELOG.md.
 *
 * @param array $args {
 *     @type WP_Post $post Issue post object. Defaults to the global $post.
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$issue_post = isset( $args['post'] ) ? $args['post'] : get_post();
if ( ! $issue_post ) {
	return;
}

// v6 shows month+year for current-publication issues ("MAR ۲۰۲۶") and
// year-only for the archived publication's older issues ("۲۰۰۶"). Our
// data model always has a precise post_date either way, so showing
// month+year consistently for every issue is a deliberate, minor
// simplification — logged in docs/CHANGELOG.md — rather than fabricating
// a "precision unknown" state that doesn't reflect real data.
$date_label = shola_get_english_month_abbr( $issue_post ) . ' ' . shola_to_persian_digits( get_the_date( 'Y', $issue_post ) );
?>
<a href="<?php echo esc_url( get_permalink( $issue_post ) ); ?>" class="issue-card">
	<div class="issue-card-media">
		<?php echo shola_get_featured_image( $issue_post, 'shola_issue_card', array( 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- shola_get_featured_image() escapes internally. ?>
	</div>
	<p class="issue-card-title"><?php echo esc_html( get_the_title( $issue_post ) ); ?></p>
	<p class="issue-card-date" lang="en"><?php echo esc_html( $date_label ); ?></p>
</a>
