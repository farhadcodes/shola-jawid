<?php
/**
 * Template part: template-parts/cards/hero-strip-card.php — the compact
 * landscape thumbnail used by the `filmstrip` hero layout's horizontal
 * strip (front-page.php, via shola_render_hero_filmstrip()). Distinct,
 * deliberately minimal anatomy — image + title + date only, no dek/topic
 * label — not a variant of card.php or issue-card.php: this card is
 * ~200px wide in a horizontally scrolling row, too narrow for either of
 * those components' full anatomy.
 *
 * Title/date overlaid directly on the image behind a dark gradient
 * scrim (2026-09-13, revised same day per Farhad's direct comparison
 * against his reference screenshot) — a small version of the same
 * text-over-photo treatment `.hero-lead`'s own headline already uses,
 * rather than a separate white caption strip below the image: since
 * this card now floats over the hero photo itself (main.css §10.5's
 * negative margin), a plain white text block below the image would
 * have fought visually with the photo it overlaps, while an all-photo
 * card with overlaid text reads as one small "mini-hero" tile, closer
 * to the reference's own clean, image-forward cards.
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
<a href="<?php echo esc_url( get_permalink( $strip_post ) ); ?>" class="hero-strip-card">
	<div class="hero-strip-card-media">
		<?php echo shola_get_featured_image( $strip_post, 'shola_card', array( 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- shola_get_featured_image() escapes internally. ?>
	</div>
	<div class="hero-strip-card-caption">
		<p class="hero-strip-card-title"><?php echo esc_html( get_the_title( $strip_post ) ); ?></p>
		<p class="hero-strip-card-date"><time datetime="<?php echo esc_attr( shola_get_iso_datetime( $strip_post ) ); ?>"><?php echo esc_html( get_the_date( '', $strip_post ) ); ?></time></p>
	</div>
</a>
