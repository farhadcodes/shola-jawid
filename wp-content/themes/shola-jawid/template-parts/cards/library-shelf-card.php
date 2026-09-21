<?php
/**
 * Template part: template-parts/cards/library-shelf-card.php — cover-only
 * shelf card for کتابخانه's homepage section (added 2026-09-21, per a
 * client-provided reference design — see docs/CHANGELOG.md and
 * main.css §33). Deliberately a separate partial from issue-card.php,
 * not a modifier of it: this section's whole point is to look distinct
 * from انتشارات حزب/اسناد حزب, which keep issue-card.php's cover+title+
 * date anatomy completely unchanged.
 *
 * Cover only, no visible title/date under it — the title is still the
 * link's real text content (.screen-reader-text, main.css §33), so it
 * isn't lost for screen readers/SEO, just not shown visually, per the
 * client's explicit "only the cover" reference.
 *
 * @param array $args {
 *     @type WP_Post $post Document post object. Defaults to the global $post.
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$card_post = isset( $args['post'] ) ? $args['post'] : get_post();
if ( ! $card_post ) {
	return;
}
?>
<a href="<?php echo esc_url( get_permalink( $card_post ) ); ?>" class="library-shelf-card">
	<span class="library-shelf-cover"><?php echo shola_get_featured_image( $card_post, 'shola_issue_card', array( 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- shola_get_featured_image() escapes internally. ?></span>
	<span class="screen-reader-text"><?php echo esc_html( get_the_title( $card_post ) ); ?></span>
</a>
