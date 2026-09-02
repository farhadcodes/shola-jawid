<?php
/**
 * Template part: template-parts/search/result.php — one search-result
 * item (main.css, .stack-lg > li). Converted from body-search.html.
 *
 * A genuinely distinct anatomy from card.php despite sharing some class
 * names (card-dek/card-byline): no image, no type-icon SVG, h-card-lg
 * instead of h-card, plain <li> instead of <article class="card">.
 * EXECUTION_PLAN.md's Phase 4.1 "second correction" claimed search.php
 * reused card.php's markup for its one document result — checked
 * directly against body-search.html while building this template and
 * found that's not accurate: no `class="card"` appears anywhere in that
 * file. Corrected in docs/CHANGELOG.md 2026-08-06.
 *
 * Covers all 5 result types shown mixed together: article, note
 * (یادداشت — the aside post format), issue, document, and (added
 * 2026-09-02) party_publication.
 *
 * @param array $args {
 *     @type WP_Post $post  Result post object. Defaults to the global $post.
 *     @type string  $query Raw search query, for <mark> highlighting.
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$result = isset( $args['post'] ) ? $args['post'] : get_post();
$query  = isset( $args['query'] ) ? $args['query'] : '';

if ( ! $result ) {
	return;
}

$post_type = get_post_type( $result );

if ( 'issue' === $post_type ) {
	$type_label = __( 'شماره', 'shola-jawid' );
	$terms      = get_the_terms( $result, 'publication' );
	$term       = ( $terms && ! is_wp_error( $terms ) ) ? array_shift( $terms ) : false;

	// Same deliberate month+year simplification already logged for
	// issue-card.php, applied consistently here rather than inventing a
	// third date convention for this one context.
	$byline = shola_get_english_month_abbr( $result ) . ' ' . shola_to_persian_digits( shola_get_gregorian_year( $result ) );
} elseif ( 'document' === $post_type ) {
	$type_label = __( 'سند', 'shola-jawid' );
	$terms      = get_the_terms( $result, 'collection' );
	$term       = ( $terms && ! is_wp_error( $terms ) ) ? array_shift( $terms ) : false;

	$doc_author = get_post_meta( $result->ID, 'shcore_author_source', true );
	$doc_pdf_id = (int) get_post_meta( $result->ID, 'shcore_pdf_id', true );
	$doc_pdf_sz = '';
	if ( $doc_pdf_id ) {
		$doc_pdf_file = get_attached_file( $doc_pdf_id );
		$doc_pdf_sz   = $doc_pdf_file && file_exists( $doc_pdf_file ) ? size_format( filesize( $doc_pdf_file ) ) : '';
	}

	$byline_parts = array_filter( array( $doc_author ) );
	$byline       = implode( ' · ', $byline_parts );
} elseif ( 'party_publication' === $post_type ) {
	$type_label = __( 'انتشارات حزب', 'shola-jawid' );
	$term       = false; // No taxonomy for this type — see class-post-types.php.

	$pub_pdf_id = (int) get_post_meta( $result->ID, 'shcore_pdf_id', true );
	$pub_pdf_sz = '';
	if ( $pub_pdf_id ) {
		$pub_pdf_file = get_attached_file( $pub_pdf_id );
		$pub_pdf_sz   = $pub_pdf_file && file_exists( $pub_pdf_file ) ? size_format( filesize( $pub_pdf_file ) ) : '';
	}
} else {
	$type_label = has_post_format( 'aside', $result ) ? __( 'یادداشت', 'shola-jawid' ) : __( 'مقاله', 'shola-jawid' );
	$terms      = get_the_terms( $result, 'topic' );
	$term       = ( $terms && ! is_wp_error( $terms ) ) ? array_shift( $terms ) : false;
}

$term_name = $term ? $term->name : '';
$term_link = $term ? get_term_link( $term ) : '';
$permalink = get_permalink( $result );
$title     = shola_highlight_search_term( esc_html( get_the_title( $result ) ), $query );
$dek       = shola_highlight_search_term( esc_html( wp_trim_words( get_the_excerpt( $result ), 24 ) ), $query );
?>
<li>
	<p class="type-label">
		<span><?php echo esc_html( $type_label ); ?></span>
		<?php if ( $term_name ) : ?>
			<span class="divider">/</span>
			<a href="<?php echo esc_url( $term_link ); ?>"><?php echo esc_html( $term_name ); ?></a>
		<?php endif; ?>
	</p>
	<h3 class="h-card-lg"><a href="<?php echo esc_url( $permalink ); ?>" class="link-quiet"><?php echo wp_kses( $title, array( 'mark' => array() ) ); ?></a></h3>
	<p class="card-dek"><?php echo wp_kses( $dek, array( 'mark' => array() ) ); ?></p>
	<p class="card-byline">
		<?php if ( 'document' === $post_type ) : ?>
			<?php echo esc_html( $byline ); ?>
			<?php if ( $doc_pdf_sz ) : ?>
				<?php echo $byline ? ' · ' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static separator, not user input. ?>
				<span class="meta-mono" lang="en">PDF · <?php echo esc_html( $doc_pdf_sz ); ?></span>
			<?php endif; ?>
		<?php elseif ( 'issue' === $post_type ) : ?>
			<span lang="en"><?php echo esc_html( $byline ); ?></span>
		<?php elseif ( 'party_publication' === $post_type ) : ?>
			<?php if ( $pub_pdf_sz ) : ?>
				<span class="meta-mono" lang="en">PDF · <?php echo esc_html( $pub_pdf_sz ); ?></span>
			<?php endif; ?>
		<?php else : ?>
			<?php
			/*
			 * Byline (author/username) removed site-wide, 2026-09-02, per
			 * the client's explicit instruction (relayed by Farhad) — the
			 * date stays. The document/issue branches above are
			 * untouched: their "$byline" is source-document citation text
			 * and a month/year respectively, never a CMS username.
			 */
			?>
			<time datetime="<?php echo esc_attr( shola_get_iso_datetime( $result ) ); ?>"><?php echo esc_html( get_the_date( '', $result ) ); ?></time>
		<?php endif; ?>
	</p>
</li>
