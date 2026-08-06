<?php
/**
 * Template part: template-parts/rows/document-row.php — the shared
 * document list row (main.css §14, .doc-row). Extracted here (Phase
 * 4.2, while building page-library.php) rather than duplicated a third
 * time — front-page.php's inline version is being switched to this same
 * partial in the same commit.
 *
 * Distinct anatomy from card.php, not a variant of it — documents never
 * render as cards, per the Phase 1.2 finding logged in
 * docs/CHANGELOG.md.
 *
 * Fixes a real gap found while building this partial: the previous
 * inline version on front-page.php omitted the author/source field
 * entirely, even though v6's own example
 * (`body-library.html`: "آثار کلاسیک · لنین · PDF · 2.8 MB") includes it
 * and shola-core tracks it (`shcore_author_source`, Phase 3.3).
 *
 * Also suppresses the collection name when rendered on that same
 * collection's own archive (`taxonomy-collection.php`) — checked against
 * `body-library-classics.html` before building that template: its rows
 * show "لنین · ترجمه ۱۴۰۵ · ..." with no "آثار کلاسیک" prefix, the same
 * self-reference suppression already proven for card.php's topic link
 * (Phase 4.2, `is_tax()`).
 *
 * @param array $args {
 *     @type WP_Post $post Document post object. Defaults to the global $post.
 * }
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$doc = isset( $args['post'] ) ? $args['post'] : get_post();
if ( ! $doc ) {
	return;
}

$doc_terms  = get_the_terms( $doc, 'collection' );
$doc_term   = ( $doc_terms && ! is_wp_error( $doc_terms ) ) ? array_shift( $doc_terms ) : false;
$doc_author = get_post_meta( $doc->ID, 'shcore_author_source', true );

$doc_pdf_id = (int) get_post_meta( $doc->ID, 'shcore_pdf_id', true );
$doc_pdf_sz = '';
if ( $doc_pdf_id ) {
	$doc_pdf_file = get_attached_file( $doc_pdf_id );
	$doc_pdf_sz   = $doc_pdf_file && file_exists( $doc_pdf_file ) ? size_format( filesize( $doc_pdf_file ) ) : '';
}

// "ص" (page count) and "ترجمه ۱۴۰۵" (translation year, seen on some
// body-library-classics.html rows) both appear in v6's examples but
// neither has a content-model field (page count confirmed out of scope
// by Farhad, 2026-08-06) -- omitted rather than fabricated.
$on_own_collection_archive = $doc_term && is_tax( $doc_term->taxonomy, $doc_term->term_id );
$meta_parts                = array_filter(
	array(
		$on_own_collection_archive ? '' : ( $doc_term ? $doc_term->name : '' ),
		$doc_author,
	)
);
?>
<li class="doc-row reveal">
	<div class="doc-body">
		<a href="<?php echo esc_url( get_permalink( $doc ) ); ?>" class="link-quiet"><?php echo esc_html( get_the_title( $doc ) ); ?></a>
		<p class="doc-meta">
			<?php echo esc_html( implode( ' · ', $meta_parts ) ); ?>
			<?php if ( $doc_pdf_sz ) : ?>
				<?php echo $meta_parts ? ' · ' : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static separator string, not user input. ?>
				<span class="meta-mono" lang="en">PDF · <?php echo esc_html( $doc_pdf_sz ); ?></span>
			<?php endif; ?>
		</p>
	</div>
	<a class="btn btn-ghost btn-sm" href="<?php echo esc_url( get_permalink( $doc ) ); ?>"><?php esc_html_e( 'دریافت', 'shola-jawid' ); ?></a>
</li>
