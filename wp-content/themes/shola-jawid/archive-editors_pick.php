<?php
/**
 * Template: archive-editors_pick.php — گزیده‌ها (Editor's Picks) archive.
 * Added 2026-10-01, alongside the new `editors_pick` CPT — per Farhad's
 * explicit instruction, this content type needs a real archive page
 * "with all the properties as other archive pages have," same as
 * نشریات/کتابخانه/گزارش/اطلاعیه‌ها already do, not just the homepage
 * column (front-page.php) it was originally requested for.
 *
 * `card.php`, not `issue-card.php` — گزیده‌ها posts are regular,
 * article-shaped content (title/body/excerpt/author), the same anatomy
 * مقالات/گزارش archives already use, not PDF-based content.
 * `posts_per_page` capped at 20 via
 * SholaCore\Post_Types::set_editors_pick_archive_posts_per_page()
 * (pre_get_posts), same pattern as archive-announcement.php's own cap
 * (spec-audit gap B12) — this template runs WordPress's native main
 * query, not its own WP_Query, so there was nowhere else to set it.
 *
 * @package shola-jawid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$paged = max( 1, get_query_var( 'paged' ) );
?>
	<section class="wrap section-top">

		<?php
		get_template_part(
			'template-parts/breadcrumb',
			null,
			array(
				'items' => array(
					array(
						'label' => __( 'گزیده‌ها', 'shola-jawid' ),
						'url'   => home_url( '/editors-picks/' ),
					),
				),
			)
		);
		?>

		<header class="page-header page-header--narrow">
			<div class="kicker-row">
				<p class="section-marker"></p>
				<h1 class="h-page"><?php esc_html_e( 'گزیده‌ها', 'shola-jawid' ); ?></h1>
			</div>
			<p class="dek"><?php esc_html_e( 'نوشته‌هایی که هیئت تحریریه برای معرفی ویژه برگزیده است.', 'shola-jawid' ); ?></p>
		</header>

		<?php if ( have_posts() ) : ?>
			<div class="grid-cards">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/cards/card', null, array( 'post' => get_post() ) );
				endwhile;
				?>
			</div>

			<?php if ( $GLOBALS['wp_query']->max_num_pages > 1 ) : ?>
				<div class="pagination mt-lg" aria-label="<?php esc_attr_e( 'صفحه‌بندی', 'shola-jawid' ); ?>">
					<?php
					$links = paginate_links(
						array(
							'total'     => $GLOBALS['wp_query']->max_num_pages,
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
			<p class="dek"><?php esc_html_e( 'هنوز گزیده‌ای منتشر نشده است.', 'shola-jawid' ); ?></p>
		<?php endif; ?>

	</section>
<?php
get_footer();
