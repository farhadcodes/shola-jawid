<?php
/**
 * Template: archive-announcement.php — اطلاعیه‌ها (Announcements archive).
 * Converted from 03_UI_Design/shola-jawid-ui/pages/body-announcements.html
 * (Phase 4.2). No single-announcement template exists in the IA doc's
 * page-to-template map — titles link to the real WP permalink (falls
 * through the default template hierarchy) rather than v6's inert
 * href="#" placeholder, since a real destination is more correct than a
 * dead link and doesn't require inventing new scope.
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

		<header class="page-header page-header--narrow">
			<p class="section-marker" lang="en">Announcements</p>
			<h1 class="h-page"><?php esc_html_e( 'اطلاعیه‌ها', 'shola-jawid' ); ?></h1>
			<p class="dek"><?php esc_html_e( 'خبرهای نشریه، فراخوان‌های ارسال مقاله، و اعلان‌های عمومی.', 'shola-jawid' ); ?></p>
		</header>

		<?php if ( have_posts() ) : ?>
			<ul class="announce-list announce-list--page">
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<li>
						<time datetime="<?php echo esc_attr( shola_get_iso_datetime() ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
						<div>
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
							<p class="meta mt-sm"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 30 ) ); ?></p>
						</div>
					</li>
				<?php endwhile; ?>
			</ul>

			<?php if ( $GLOBALS['wp_query']->max_num_pages > 1 ) : ?>
				<div class="pagination">
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
							$link = shola_to_persian_digits( $link );
							$link = str_replace( 'page-numbers', 'page-num', $link );
							echo wp_kses_post( $link );
						}
					}
					?>
				</div>
			<?php endif; ?>
		<?php endif; ?>

	</section>
<?php
get_footer();
