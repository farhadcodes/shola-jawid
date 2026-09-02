<?php
/**
 * Template part: template-parts/cards/card.php — the shared borderless
 * card (main.css §09, .card). Converted from v6's repeated <article
 * class="card"> markup.
 *
 * Defaults to article display; pass $args['type'] = 'document' for the
 * one confirmed mixed-content-stream context (front-page.php's Latest
 * grid) where v6 also renders a document through this same markup — see
 * docs/CHANGELOG.md 2026-08-06. search.php's results were assumed to be a
 * second such context but turned out not to be, on checking
 * body-search.html directly while building search.php (2026-08-06,
 * corrected in docs/CHANGELOG.md) — that page uses its own distinct
 * template-parts/search/result.php instead. Every other document context
 * uses .doc-row (template-parts/rows/document-row.php); do not pass
 * type=document anywhere else.
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
	$permalink           = get_permalink( $card_post );
	$type_label          = __( 'سند', 'shola-jawid' );
	$terms               = get_the_terms( $card_post, 'collection' );
	$term                = ( $terms && ! is_wp_error( $terms ) ) ? array_shift( $terms ) : false;
	$term_link           = $term ? get_term_link( $term ) : '';
	$term_name           = $term ? $term->name : '';
	$byline              = __( 'کتابخانه', 'shola-jawid' );
	$type_icon           = '<svg class="glyph" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><rect x="2.5" y="2" width="11" height="12" rx="0.5" fill="none" stroke="currentColor" stroke-width="1.5"/><path d="M6 2v12M10 2v12" stroke="currentColor" stroke-width="1"/></svg>';
	$on_own_term_archive = $term && is_tax( $term->taxonomy, $term->term_id );
} else {
	$permalink   = get_permalink( $card_post );
	$type_label  = has_post_format( 'aside', $card_post ) ? __( 'یادداشت', 'shola-jawid' ) : __( 'مقاله', 'shola-jawid' );
	$term        = shola_get_primary_topic( $card_post );
	$term_link   = $term ? get_term_link( $term ) : '';
	$term_name   = $term ? $term->name : '';
	$byline_meta = get_post_meta( $card_post->ID, 'shcore_byline', true );
	$byline      = $byline_meta ? $byline_meta : get_the_author_meta( 'display_name', $card_post->post_author );

	/*
	 * v6 uses two different glyph SVGs for article-type cards, and it's a
	 * real, consistent split, not random inconsistency — confirmed by
	 * checking all 6 body-topic-*.html files, not just economy:
	 * body-index.html (homepage/mixed-stream context) uses a two-subpath
	 * path that renders hollow/outlined; every body-topic-*.html (a
	 * topic's own archive) uses a one-subpath path that renders solid
	 * filled. Found by Farhad comparing rendered screenshots — source
	 * diffing alone missed it because both are valid, similar-looking SVG
	 * paths; only checking what they actually render as (nonzero fill
	 * winding: two nested opposite-wound subpaths cut a hole = outline,
	 * one subpath = solid) revealed the real difference. Toggled by the
	 * same is_tax() condition as the term-link suppression below, since
	 * v6 changes both together.
	 */
	$on_own_term_archive = $term && is_tax( $term->taxonomy, $term->term_id );
	$type_icon           = $on_own_term_archive
		? '<svg class="glyph" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M2 2h9a3 3 0 0 1 3 3v9H5a3 3 0 0 1-3-3V2Z"/></svg>'
		: '<svg class="glyph" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M2 2h9a3 3 0 0 1 3 3v9H5a3 3 0 0 1-3-3V2Zm1 1v8a2 2 0 0 0 2 2h8V5a2 2 0 0 0-2-2H3Z"/></svg>';
}
?>
<article class="card reveal">
	<a href="<?php echo esc_url( $permalink ); ?>" class="card-media" aria-hidden="true" tabindex="-1">
		<?php echo shola_get_featured_image( $card_post, 'shola_card', array( 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- shola_get_featured_image() escapes internally. ?>
	</a>
	<div class="card-body">
		<p class="type-label">
			<?php echo $type_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static, trusted inline SVG defined above, not user input. ?>
			<span><?php echo esc_html( $type_label ); ?></span>
			<?php if ( $term_name ) : ?>
				<span class="divider">/</span>
				<?php
				// v6 shows the term as plain text, not a link, when the card
				// appears on that same term's own archive page — avoids a
				// redundant self-link (confirmed against
				// body-topic-economy.html: no <a> around "اقتصاد" there, vs. a
				// real link on the homepage's equivalent card). Every other
				// context keeps the link. $on_own_term_archive is computed
				// above, alongside the matching icon-style decision.
				if ( $on_own_term_archive ) :
					echo esc_html( $term_name );
				else :
					?>
					<a href="<?php echo esc_url( $term_link ); ?>"><?php echo esc_html( $term_name ); ?></a>
					<?php
				endif;
				?>
			<?php endif; ?>
		</p>
		<h3 class="h-card"><a href="<?php echo esc_url( $permalink ); ?>" class="link-quiet"><?php echo esc_html( get_the_title( $card_post ) ); ?></a></h3>
		<p class="card-dek"><?php echo esc_html( wp_trim_words( get_the_excerpt( $card_post ), 24 ) ); ?></p>
		<p class="card-byline"><?php echo esc_html( $byline ); ?> · <time datetime="<?php echo esc_attr( shola_get_iso_datetime( $card_post ) ); ?>"><?php echo esc_html( get_the_date( '', $card_post ) ); ?></time></p>
	</div>
</article>
