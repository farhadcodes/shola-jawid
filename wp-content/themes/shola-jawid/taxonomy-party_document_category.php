<?php
/**
 * Template: taxonomy-party_document_category.php — اسناد حزب archive for
 * the `party_document_category` taxonomy (one template for every term).
 * Added 2026-09-24, per the client's explicit request to bring اسناد حزب
 * to parity with کتابخانه's own per-collection archive
 * (taxonomy-collection.php), which this file deliberately mirrors the
 * structure of — same header/nav/pagination shape — while swapping in
 * اسناد حزب's own existing `.issue-grid`/`issue-card` display (the pattern
 * page-party-documents.php already uses), not کتابخانه's
 * document-row list.
 *
 * The cross-subsection nav strip is built from
 * shola_get_party_document_subsections() (inc/template-tags.php) — a live
 * get_terms() call (including the taxonomy's existing «دسته‌بندی‌نشده»
 * fallback term, Category_Manager, always sorted last), not a hardcoded
 * slug array like taxonomy-collection.php's own `$collection_slugs`
 * (found, during this feature's planning, to require a code change for
 * any new `collection` term to ever appear). Any term created, renamed, or
 * deleted for `party_document_category` from wp-admin is reflected here
 * immediately, with no such gap.
 *
 * @package shola-jawid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$term  = get_queried_object();
$paged = max( 1, get_query_var( 'paged' ) );

$subsections = shola_get_party_document_subsections();

$archive_query = new WP_Query(
	array(
		'post_type'      => 'party_document',
		'posts_per_page' => 20,
		'paged'          => $paged,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			array(
				'taxonomy' => 'party_document_category',
				'field'    => 'term_id',
				'terms'    => $term->term_id,
			),
		),
	)
);
?>
	<section class="wrap section-top">

		<header class="page-header page-header--narrow page-header--tight">
			<div class="kicker-row">
				<p class="section-marker"></p>
				<h1 class="h-page"><?php echo esc_html( $term->name ); ?></h1>
			</div>
			<?php if ( $term->description ) : ?>
				<p class="dek"><?php echo esc_html( $term->description ); ?></p>
			<?php endif; ?>
		</header>

		<nav class="topic-nav" aria-label="<?php esc_attr_e( 'پیمایش دسته‌های اسناد حزب', 'shola-jawid' ); ?>">
			<?php foreach ( $subsections as $subsection ) : ?>
				<a<?php echo ( $subsection['term']->term_id === $term->term_id ) ? ' class="active"' : ''; ?> href="<?php echo esc_url( $subsection['link'] ); ?>"><?php echo esc_html( $subsection['name'] ); ?></a>
			<?php endforeach; ?>
		</nav>

		<?php if ( $archive_query->have_posts() ) : ?>
			<div class="issue-grid">
				<?php
				while ( $archive_query->have_posts() ) :
					$archive_query->the_post();
					get_template_part( 'template-parts/cards/issue-card', null, array( 'post' => get_post() ) );
				endwhile;
				wp_reset_postdata();
				?>
			</div>

			<?php if ( $archive_query->max_num_pages > 1 ) : ?>
				<div class="pagination" aria-label="<?php esc_attr_e( 'صفحه‌بندی', 'shola-jawid' ); ?>">
					<?php
					$links = paginate_links(
						array(
							'total'     => $archive_query->max_num_pages,
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
			<p class="dek"><?php esc_html_e( 'هنوز سندی در این دسته منتشر نشده است.', 'shola-jawid' ); ?></p>
		<?php endif; ?>

	</section>
<?php
get_footer();
