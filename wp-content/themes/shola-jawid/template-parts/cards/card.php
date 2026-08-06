<?php
/**
 * Template part: template-parts/cards/card.php — the shared borderless
 * card (main.css §09, .card). Converted from v6's repeated <article
 * class="card"> markup.
 *
 * Defaults to article display; pass $args['type'] = 'document' for the
 * two confirmed mixed-content-stream contexts (front-page.php's Latest
 * grid, search.php results) where v6 also renders a document through this
 * same markup — see docs/CHANGELOG.md 2026-08-06. Every other document
 * context uses .doc-row (template-parts/rows/document-row.php) instead;
 * do not pass type=document anywhere else.
 *
 * @param array $args {
 *     @type WP_Post $post Post object. Defaults to the global $post.
 *     @type string  $type 'article' (default) or 'document'.
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$card_post = isset( $args['post'] ) ? $args['post'] : get_post();
$card_type = isset( $args['type'] ) && 'document' === $args['type'] ? 'document' : 'article';

if ( ! $card_post ) {
	return;
}

if ( 'document' === $card_type ) {
	$permalink   = get_permalink( $card_post );
	$type_label  = __( 'سند', 'shola-jawid' );
	$terms       = get_the_terms( $card_post, 'collection' );
	$term        = ( $terms && ! is_wp_error( $terms ) ) ? array_shift( $terms ) : false;
	$term_link   = $term ? get_term_link( $term ) : '';
	$term_name   = $term ? $term->name : '';
	$byline      = __( 'کتابخانه', 'shola-jawid' );
	$type_icon   = '<svg class="glyph" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><rect x="2.5" y="2" width="11" height="12" rx="0.5" fill="none" stroke="currentColor" stroke-width="1.5"/><path d="M6 2v12M10 2v12" stroke="currentColor" stroke-width="1"/></svg>';
} else {
	$permalink   = get_permalink( $card_post );
	$type_label  = has_post_format( 'aside', $card_post ) ? __( 'یادداشت', 'shola-jawid' ) : __( 'مقاله', 'shola-jawid' );
	$terms       = get_the_terms( $card_post, 'topic' );
	$term        = ( $terms && ! is_wp_error( $terms ) ) ? array_shift( $terms ) : false;
	$term_link   = $term ? get_term_link( $term ) : '';
	$term_name   = $term ? $term->name : '';
	$byline_meta = get_post_meta( $card_post->ID, 'shcore_byline', true );
	$byline      = $byline_meta ? $byline_meta : get_the_author_meta( 'display_name', $card_post->post_author );
	$type_icon   = '<svg class="glyph" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M2 2h9a3 3 0 0 1 3 3v9H5a3 3 0 0 1-3-3V2Zm1 1v8a2 2 0 0 0 2 2h8V5a2 2 0 0 0-2-2H3Z"/></svg>';
}
?>
<article class="card reveal">
	<a href="<?php echo esc_url( $permalink ); ?>" class="card-media">
		<?php if ( has_post_thumbnail( $card_post ) ) : ?>
			<?php echo get_the_post_thumbnail( $card_post, 'shola_card', array( 'loading' => 'lazy' ) ); ?>
		<?php endif; ?>
	</a>
	<div class="card-body">
		<p class="type-label">
			<?php echo $type_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static, trusted inline SVG defined above, not user input. ?>
			<span><?php echo esc_html( $type_label ); ?></span>
			<?php if ( $term_name ) : ?>
				<span class="divider">/</span>
				<a href="<?php echo esc_url( $term_link ); ?>"><?php echo esc_html( $term_name ); ?></a>
			<?php endif; ?>
		</p>
		<h3 class="h-card"><a href="<?php echo esc_url( $permalink ); ?>" class="link-quiet"><?php echo esc_html( get_the_title( $card_post ) ); ?></a></h3>
		<p class="card-dek"><?php echo esc_html( wp_trim_words( get_the_excerpt( $card_post ), 24 ) ); ?></p>
		<p class="card-byline"><?php echo esc_html( $byline ); ?> · <time datetime="<?php echo esc_attr( get_the_date( 'c', $card_post ) ); ?>"><?php echo esc_html( get_the_date( '', $card_post ) ); ?></time></p>
	</div>
</article>
