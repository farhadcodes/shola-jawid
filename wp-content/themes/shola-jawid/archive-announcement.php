<?php
/**
 * Template: archive-announcement.php — اطلاعیه‌ها (Announcements archive).
 * Converted from 03_UI_Design/shola-jawid-ui/pages/body-announcements.html
 * (Phase 4.2). Announcements are list-only by design, permanently — no
 * single-announcement template exists anywhere in the IA doc's
 * page-to-template map (confirmed by re-reading it directly, not
 * assumed), matching v6's own inert href="#" title links. An earlier
 * version of this file linked titles to the real permalink instead,
 * reasoning that a real destination beats a dead link — wrong: it
 * pointed at WP's bare unstyled default template hierarchy, a worse
 * experience than an inert link, since no detail view is actually
 * planned. Reverted per Farhad's confirmation, logged in
 * docs/CHANGELOG.md 2026-08-06.
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
			<div class="kicker-row">
				<p class="section-marker"></p>
				<h1 class="h-page"><?php esc_html_e( 'اطلاعیه‌ها', 'shola-jawid' ); ?></h1>
			</div>
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
							<a href="#"><?php the_title(); ?></a>
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
