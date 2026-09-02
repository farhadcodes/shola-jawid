<?php
/**
 * Template: page-party-publications.php — انتشارات حزب (Party
 * Publications listing). Applies to the Page with slug
 * `party-publications`. New 2026-09-02 alongside the `party_publication`
 * CPT (shola-core\Post_Types) — see that class's docblock for why this
 * is a separate content type/page from both نشریه (page-publications.php)
 * and کتابخانه (page-library.php).
 *
 * `party_publication` has no taxonomy (client didn't ask for sub-
 * categorization here), so unlike page-library.php's four-collection
 * layout this is a single, paginated grid — same issue-card.php anatomy
 * already used for this content on the homepage, real pagination since
 * this list has no natural "latest N" cutoff the way the homepage's
 * curated section does.
 *
 * @package shola-jawid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$paged = max( 1, get_query_var( 'paged' ) );

$party_publications_query = new WP_Query(
	array(
		'post_type'      => 'party_publication',
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
				<h1 class="h-page"><?php esc_html_e( 'انتشارات حزب', 'shola-jawid' ); ?></h1>
			</div>
			<p class="dek"><?php esc_html_e( 'کتاب‌ها و جزوه‌هایی که خود حزب منتشر کرده است — با پیش‌نمایش درون‌مرورگری و دریافت آزاد PDF.', 'shola-jawid' ); ?></p>
		</header>

		<?php if ( $party_publications_query->have_posts() ) : ?>
			<div class="issue-grid">
				<?php
				while ( $party_publications_query->have_posts() ) :
					$party_publications_query->the_post();
					get_template_part( 'template-parts/cards/issue-card', null, array( 'post' => get_post() ) );
				endwhile;
				wp_reset_postdata();
				?>
			</div>

			<?php if ( $party_publications_query->max_num_pages > 1 ) : ?>
				<div class="pagination">
					<?php
					$links = paginate_links(
						array(
							'base'      => add_query_arg( 'paged', '%#%' ),
							'total'     => $party_publications_query->max_num_pages,
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
			<p class="dek"><?php esc_html_e( 'هنوز اثری منتشر نشده است.', 'shola-jawid' ); ?></p>
		<?php endif; ?>

	</section>
<?php
get_footer();
