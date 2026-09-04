<?php
/**
 * Template: archive-announcement.php — اطلاعیه‌ها (Announcements archive).
 * Converted from 03_UI_Design/shola-jawid-ui/pages/body-announcements.html
 * (Phase 4.2).
 *
 * Announcements were list-only by design, permanently, until 2026-09-04:
 * no single-announcement template existed anywhere in the IA doc's
 * page-to-template map (confirmed by re-reading it directly at the
 * time), matching v6's own inert href="#" title links — an earlier
 * attempt to link titles to the real permalink instead was reverted per
 * Farhad's confirmation (docs/CHANGELOG.md 2026-08-06) specifically
 * because no detail view existed yet, so the link pointed at WP's bare
 * unstyled default template hierarchy, worse than an inert link.
 *
 * That's now reversed on explicit client request (relayed by Farhad,
 * 2026-09-04): single-announcement.php exists, so titles link to it for
 * real below — this is not a repeat of the earlier mistake, since the
 * destination this time is an actual designed template, not the bare
 * fallback that made the original attempt worse than a dead link.
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
