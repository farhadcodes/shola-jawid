<?php
/**
 * Template part: template-parts/cards/selected-row.php — one row of the
 * گزیده‌ها (Selected) section, shared by front-page.php's homepage tile and
 * page-selected.php's paginated archive. Added 2026-09-16, inspired by a
 * client-supplied reference (a square-thumbnail article-list card), mirrored
 * for RTL: the image is the first flex child, so it lands on the reading-
 * start side (visual right in this RTL site) with no hardcoded left/right —
 * same DOM-order-not-physical-property technique already used by the
 * `hero_section` rail layouts.
 *
 * @param array $args {
 *     @type WP_Post $post Post object. Defaults to the global $post.
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$row_post = isset( $args['post'] ) ? $args['post'] : get_post();

if ( ! $row_post ) {
	return;
}

$permalink = get_permalink( $row_post );
$term      = shola_get_primary_topic( $row_post );
?>
<article class="selected-row reveal">
	<a href="<?php echo esc_url( $permalink ); ?>" class="selected-row-media" aria-hidden="true" tabindex="-1">
		<?php echo shola_get_featured_image( $row_post, 'shola_selected_square', array( 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- shola_get_featured_image() escapes internally. ?>
	</a>
	<div class="selected-row-body">
		<?php if ( $term ) : ?>
			<p class="type-label"><a href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( $term->name ); ?></a></p>
		<?php endif; ?>
		<h3 class="h-card"><a href="<?php echo esc_url( $permalink ); ?>" class="link-quiet"><?php echo esc_html( get_the_title( $row_post ) ); ?></a></h3>
		<?php
		/*
		 * 32 -> 20 words, 2026-09-16, when the grid went from 2 to 3
		 * columns and .card-dek's clamp here tightened 3 -> 2 lines
		 * (main.css) — a narrower column fits less per line, and the
		 * shorter word count keeps the excerpt reliably filling (not
		 * overrunning) a 2-line clamp instead of always hard-cutting
		 * mid-sentence, same reasoning already applied to .card-dek
		 * elsewhere on the site (card.php, main.css §09).
		 */
		?>
		<p class="card-dek"><?php echo esc_html( wp_trim_words( get_the_excerpt( $row_post ), 20 ) ); ?></p>
		<p class="card-byline">
			<?php echo shola_date_icon(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static, trusted inline SVG, not user input. ?>
			<time datetime="<?php echo esc_attr( shola_get_iso_datetime( $row_post ) ); ?>"><?php echo esc_html( get_the_date( '', $row_post ) ); ?></time>
		</p>
	</div>
</article>
