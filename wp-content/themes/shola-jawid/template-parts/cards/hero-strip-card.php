<?php
/**
 * Template part: template-parts/cards/hero-strip-card.php — the compact
 * landscape thumbnail used by the `filmstrip` hero layout's horizontal
 * strip (front-page.php, via shola_render_hero_filmstrip()). Distinct,
 * deliberately minimal anatomy — image only, no title/date/topic label
 * — not a variant of card.php or issue-card.php: this card is ~190px
 * wide in a horizontally scrolling row, too narrow for either of those
 * components' full anatomy.
 *
 * No visible caption text (2026-09-13, third pass — Farhad's explicit
 * "clean, without any extra busy information" after seeing the second
 * pass's overlaid title/date live): the title is still the link's
 * accessible name via `aria-label`, so a screen-reader user isn't
 * left with an unlabeled link — only the visible caption was removed,
 * not the information itself.
 *
 * @param array $args {
 *     @type WP_Post $post Article post object. Defaults to the global $post.
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$strip_post = isset( $args['post'] ) ? $args['post'] : get_post();
if ( ! $strip_post ) {
	return;
}
?>
<a href="<?php echo esc_url( get_permalink( $strip_post ) ); ?>" class="hero-strip-card" aria-label="<?php echo esc_attr( get_the_title( $strip_post ) ); ?>">
	<div class="hero-strip-card-media">
		<?php echo shola_get_featured_image( $strip_post, 'shola_card', array( 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- shola_get_featured_image() escapes internally. ?>
	</div>
</a>
