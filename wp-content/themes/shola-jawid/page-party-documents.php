<?php
/**
 * Template: page-party-documents.php — اسناد حزب (Party Documents
 * listing). Applies to the Page with slug `party-documents`. New
 * 2026-09-04 alongside the `party_document` CPT (shola-core\Post_Types)
 * — see that class's docblock for why this is a separate content type/
 * page from both نشریه (page-publications.php) and کتابخانه
 * (page-library.php), including the migration of the 2 documents
 * previously filed under کتابخانه's now-removed "اسناد حزب" shelf.
 *
 * A single, paginated grid, same shape as page-party-publications.php —
 * `party_document_category` is a self-managed taxonomy (the client wants
 * staff to be able to add categories freely as اسناد حزب accumulates, not
 * a fixed IA-doc vocabulary).
 *
 * Subsection tile block added 2026-09-24, per the client's explicit
 * request to bring اسناد حزب to parity with کتابخانه's own collection
 * tiles (page-library.php): reuses that same `.topic-list` component,
 * fed by shola_get_party_document_subsections() (inc/template-tags.php)
 * — dynamic via get_terms(), not a hardcoded slug array like کتابخانه's
 * own `$collection_slugs` (see that function's docblock for why that
 * wasn't replicated). Every اسناد حزب entry is guaranteed at least the
 * existing، sitewide «دسته‌بندی‌نشده» fallback term (Category_Manager,
 * shola-core) once assigned a category or not — see
 * Taxonomies::default_to_uncategorized_party_document() and
 * Taxonomies::migrate_unassigned_party_documents() (class-taxonomies.php)
 * — so that term's own tile is a real, clickable subsection like any
 * other, not a separate query-filtered view; this grid below stays the
 * same single flat, unfiltered listing it always was.
 *
 * @package shola-jawid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$paged       = max( 1, get_query_var( 'paged' ) );
$subsections = shola_get_party_document_subsections();

$party_documents_query = new WP_Query(
	array(
		'post_type'      => 'party_document',
		'posts_per_page' => 20,
		'paged'          => $paged,
		'orderby'        => 'date',
		'order'          => 'DESC',
	)
);
?>
	<section class="wrap section-top">

		<header class="page-header">
			<div class="kicker-row">
				<p class="section-marker"></p>
				<h1 class="h-page"><?php esc_html_e( 'اسناد حزب', 'shola-jawid' ); ?></h1>
			</div>
			<p class="dek"><?php esc_html_e( 'اسناد داخلی حزب — با پیش‌نمایش درون‌مرورگری و دریافت آزاد PDF.', 'shola-jawid' ); ?></p>
		</header>

		<ul class="topic-list">
			<?php foreach ( $subsections as $subsection ) : ?>
				<li><a href="<?php echo esc_url( $subsection['link'] ); ?>">
					<span class="name"><?php echo esc_html( $subsection['name'] ); ?></span>
					<span class="count"><?php echo esc_html( sprintf( /* translators: %s: document count. */ _n( '%s سند', '%s سند', $subsection['count'], 'shola-jawid' ), shola_to_persian_digits( $subsection['count'] ) ) ); ?></span></a></li>
			<?php endforeach; ?>
		</ul>

		<?php if ( $party_documents_query->have_posts() ) : ?>
			<div class="issue-grid">
				<?php
				while ( $party_documents_query->have_posts() ) :
					$party_documents_query->the_post();
					get_template_part( 'template-parts/cards/issue-card', null, array( 'post' => get_post() ) );
				endwhile;
				wp_reset_postdata();
				?>
			</div>

			<?php if ( $party_documents_query->max_num_pages > 1 ) : ?>
				<div class="pagination">
					<?php
					$links = paginate_links(
						array(
							'base'      => add_query_arg( 'paged', '%#%' ),
							'total'     => $party_documents_query->max_num_pages,
							'current'   => $paged,
							'type'      => 'array',
							'prev_text' => '→',
							'next_text' => '←',
						)
					);
					if ( $links ) {
						foreach ( $links as $link ) {
							$link = shola_persian_digits_pagination_link( $link );
							$link = str_replace( 'page-numbers', 'page-num', $link );
							echo wp_kses_post( $link );
						}
					}
					?>
				</div>
			<?php endif; ?>
		<?php else : ?>
			<p class="dek"><?php esc_html_e( 'هنوز سندی منتشر نشده است.', 'shola-jawid' ); ?></p>
		<?php endif; ?>

	</section>
<?php
get_footer();
