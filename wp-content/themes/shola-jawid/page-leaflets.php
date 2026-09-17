<?php
/**
 * Template: page-leaflets.php — تراکت‌ها (Leaflets), the full
 * reverse-chronological archive of leaflet/banner/poster uploads (see
 * shola_get_leaflets_query(), inc/template-tags.php). Assigned
 * automatically to a real WP Page by shola_maybe_seed_leaflets_page()
 * (inc/setup.php) — added 2026-09-17, linked from the homepage teaser's
 * "مشاهده آرشیو تراکت‌ها" link (front-page.php). Not yet in any nav menu
 * location — Farhad's explicit instruction (2026-09-17), pending a
 * separate client confirmation.
 *
 * Pagination is a single "بارگذاری بیشتر" next-page link, not a numbered
 * pager — per the design brief's explicit ask, and per this project's
 * plan-approval discussion (2026-09-17): building real fetch-based
 * infinite scroll would be this project's first such JS pattern for a
 * single-feature archive, so a plain server-rendered next-page link
 * (works with JS fully disabled, same progressive-enhancement floor as
 * every other template on this site) was chosen instead.
 *
 * Fullscreen lightbox (added 2026-09-17, same session): prev/next in the
 * lightbox is deliberately capped to whichever items are on *this*
 * page's own loaded batch — confirmed with Farhad — since "بارگذاری
 * بیشتر" above is a full page reload, not a cumulative DOM append, there
 * is no larger in-memory set for it to reach into without a page
 * navigation. See template-parts/leaflets/lightbox.php and main.js.
 *
 * @package shola-jawid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

/*
 * `page`, not `paged` — same static-Page pagination gotcha already
 * documented in page-selected.php: WordPress exposes pagination on a
 * static Page's own URL (`/leaflets/page/2/`) through the `page` query
 * var, not `paged`.
 */
$leaflets_paged = max( 1, (int) get_query_var( 'page' ) );
$leaflets_query = shola_get_leaflets_query(
	array(
		'posts_per_page' => 12,
		'paged'          => $leaflets_paged,
	)
);
?>
	<section class="wrap section-top">

		<header class="page-header page-header--narrow">
			<div class="kicker-row">
				<p class="section-marker"></p>
				<h1 class="h-page"><?php esc_html_e( 'تراکت‌ها', 'shola-jawid' ); ?></h1>
			</div>
			<p class="dek"><?php esc_html_e( 'آرشیو تراکت‌ها، بنرها و پوسترهای تبلیغاتی حزب برای تظاهرات و اعتراضات.', 'shola-jawid' ); ?></p>
		</header>

		<?php if ( $leaflets_query->have_posts() ) : ?>
			<div class="leaflet-stream">
				<?php
				$leaflet_i          = 0;
				$leaflet_lightbox_data = array();
				while ( $leaflets_query->have_posts() ) :
					$leaflets_query->the_post();
					$leaflet_post_obj = get_post();
					get_template_part(
						'template-parts/leaflets/leaflet-item',
						null,
						array(
							'post'  => $leaflet_post_obj,
							'index' => $leaflet_i,
						)
					);

					/*
					 * Conditional-caption mechanism, decided server-side —
					 * the `caption` array key only exists when there's real
					 * title text, so main.js's rendering code has no way to
					 * accidentally create an empty caption element: there's
					 * nothing there to read. Date is always included — it's
					 * native post_date, never empty.
					 */
					$leaflet_thumb_id = get_post_thumbnail_id( $leaflet_post_obj );
					$leaflet_caption  = trim( get_the_title( $leaflet_post_obj ) );
					$leaflet_entry    = array(
						'image' => wp_get_attachment_image_url( $leaflet_thumb_id, 'full' ),
						'date'  => get_the_date( '', $leaflet_post_obj ),
						'alt'   => get_post_meta( $leaflet_thumb_id, '_wp_attachment_image_alt', true ) ?: $leaflet_caption,
					);
					if ( '' !== $leaflet_caption ) {
						$leaflet_entry['caption'] = $leaflet_caption;
					}
					$leaflet_lightbox_data[] = $leaflet_entry;
					++$leaflet_i;
				endwhile;
				?>
			</div>

			<?php if ( $leaflets_paged < $leaflets_query->max_num_pages ) : ?>
				<div class="leaflet-load-more">
					<a class="btn btn-ghost" rel="next" href="<?php echo esc_url( get_pagenum_link( $leaflets_paged + 1 ) ); ?>"><?php esc_html_e( 'بارگذاری بیشتر', 'shola-jawid' ); ?></a>
				</div>
			<?php endif; ?>

			<script type="application/json" id="leaflet-lightbox-data"><?php echo wp_json_encode( $leaflet_lightbox_data ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_json_encode() output inside a script/application-json tag is not HTML context; every string value within it was already escaped for its own purpose (URLs via wp_get_attachment_image_url(), text via WordPress's own title/date functions) and JSON-encoding itself escapes special characters safely for this context. ?></script>
			<?php
			/*
			 * nav => count > 1 — computed from the real, final item count
			 * on *this* page's batch, not hardcoded true just because this
			 * is the archive template. Fixed 2026-09-17: a page that
			 * happens to load exactly one leaflet (the very first one ever
			 * published, or the tail end of pagination) has nothing for
			 * prev/next to do either, and gets the same no-controls
			 * treatment as the homepage teaser rather than being assumed
			 * "the multi-item case" by virtue of which template this is.
			 */
			get_template_part(
				'template-parts/leaflets/lightbox',
				null,
				array( 'nav' => count( $leaflet_lightbox_data ) > 1 )
			);
			?>
		<?php else : ?>
			<p class="dek"><?php esc_html_e( 'هنوز تراکتی منتشر نشده است.', 'shola-jawid' ); ?></p>
		<?php endif; ?>

		<?php wp_reset_postdata(); ?>

	</section>
<?php
get_footer();
