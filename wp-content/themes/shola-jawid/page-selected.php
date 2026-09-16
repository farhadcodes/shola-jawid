<?php
/**
 * Template: page-selected.php — گزیده‌ها (Selected), the full paginated
 * archive of flagged articles/reports (see shola_get_selected_query(),
 * inc/template-tags.php). Assigned automatically to a real WP Page by
 * shola_maybe_seed_selected_page() (inc/setup.php) — added 2026-09-16,
 * linked from the homepage tile's "همهٔ گزیده‌ها" link (front-page.php).
 *
 * @package shola-jawid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

/*
 * `page`, not `paged`: WordPress's rewrite rules expose pagination on a
 * static Page's own URL (`/selected/page/2/`) through the `page` query var
 * — `paged` is reserved for archive/home-style views and is always empty
 * here, a well-known gotcha when a page template runs its own secondary
 * query instead of the main one (confirmed against WP core's rewrite
 * rules for the `page` post type, not assumed).
 */
$paged           = max( 1, (int) get_query_var( 'page' ) );
$selected_query  = shola_get_selected_query(
	array(
		'posts_per_page' => 12,
		'paged'          => $paged,
	)
);
?>
	<section class="wrap section-top">

		<header class="page-header page-header--narrow">
			<div class="kicker-row">
				<p class="section-marker"></p>
				<h1 class="h-page"><?php esc_html_e( 'گزیده‌ها', 'shola-jawid' ); ?></h1>
			</div>
			<p class="dek"><?php esc_html_e( 'مقالات و گزارش‌های برگزیدهٔ سردبیری.', 'shola-jawid' ); ?></p>
		</header>

		<?php if ( $selected_query->have_posts() ) : ?>
			<div class="selected-list">
				<?php
				while ( $selected_query->have_posts() ) :
					$selected_query->the_post();
					get_template_part( 'template-parts/cards/selected-row', null, array( 'post' => get_post() ) );
				endwhile;
				?>
			</div>

			<?php if ( $selected_query->max_num_pages > 1 ) : ?>
				<div class="pagination">
					<?php
					$links = paginate_links(
						array(
							'total'     => $selected_query->max_num_pages,
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
			<p class="dek"><?php esc_html_e( 'هنوز مطلبی در گزیده‌ها نیست.', 'shola-jawid' ); ?></p>
		<?php endif; ?>

		<?php wp_reset_postdata(); ?>

	</section>
<?php
get_footer();
