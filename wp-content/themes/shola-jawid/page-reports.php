<?php
/**
 * Template: page-reports.php — گزارش (Reports) full archive. Applies to
 * the Page with slug `reports`. Added 2026-09-04 — گزارش has existed
 * since Phase B (2026-08-25, docs/CHANGELOG.md), but had no page of its
 * own to see the full history, only the newest 6 on the homepage. Its
 * source moved 2026-09-05 from a `post_tag` to a dedicated `report`
 * taxonomy (see class-taxonomies.php) — this template's query below was
 * updated to match, everything else about this page is unchanged.
 *
 * A static Page rather than WordPress's native tag-archive URL
 * (`/tag/reports/`), matching the same pattern already used for
 * نشریات/کتابخانه/انتشارات حزب/اسناد حزب — gives گزارش a clean
 * `/reports/` address consistent with the rest of the site instead of
 * the more technical-looking default tag URL, with no rewrite-rule
 * plumbing needed.
 *
 * Same grid/pagination shape as taxonomy-topic.php (Reports are regular
 * `post`-type articles, so they use the same card.php partial as every
 * other article listing — not issue-card.php, which is for PDF-based
 * content), minus that template's topic-switcher nav and most-read sort
 * tab, neither of which apply here.
 *
 * @package shola-jawid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$paged = max( 1, get_query_var( 'paged' ) );

$reports_query = new WP_Query(
	array(
		'post_type'      => 'post',
		'posts_per_page' => 6,
		'paged'          => $paged,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- small, single-term taxonomy, not a scale concern.
			array(
				'taxonomy' => 'report',
				'field'    => 'slug',
				'terms'    => 'reports',
			),
		),
	)
);
?>
	<section class="wrap section-top">

		<header class="page-header page-header--narrow">
			<div class="kicker-row">
				<p class="section-marker"></p>
				<h1 class="h-page"><?php esc_html_e( 'گزارش', 'shola-jawid' ); ?></h1>
			</div>
			<p class="dek"><?php esc_html_e( 'آرشیو کامل گزارش‌های منتشرشده در سایت.', 'shola-jawid' ); ?></p>
		</header>

		<?php if ( $reports_query->have_posts() ) : ?>
			<div class="grid-cards">
				<?php
				while ( $reports_query->have_posts() ) :
					$reports_query->the_post();
					get_template_part( 'template-parts/cards/card', null, array( 'post' => get_post() ) );
				endwhile;
				wp_reset_postdata();
				?>
			</div>

			<?php if ( $reports_query->max_num_pages > 1 ) : ?>
				<div class="pagination mt-lg" aria-label="<?php esc_attr_e( 'صفحه‌بندی', 'shola-jawid' ); ?>">
					<?php
					$links = paginate_links(
						array(
							'base'      => add_query_arg( 'paged', '%#%' ),
							'total'     => $reports_query->max_num_pages,
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
			<p class="dek"><?php esc_html_e( 'هنوز گزارشی منتشر نشده است.', 'shola-jawid' ); ?></p>
		<?php endif; ?>

	</section>
<?php
get_footer();
