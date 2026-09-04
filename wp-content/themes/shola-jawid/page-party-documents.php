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
 * `party_document_category` is an optional, self-managed taxonomy (the
 * client wants staff to be able to add categories only if/when they need
 * them, not a fixed IA-doc vocabulary), so this deliberately doesn't
 * build a per-category layout up front the way page-library.php's four
 * fixed collections do; add one later if the category list actually
 * grows into something worth splitting by, rather than guessing now.
 *
 * @package shola-jawid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$paged = max( 1, get_query_var( 'paged' ) );

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
							$link = shola_to_persian_digits( $link );
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
