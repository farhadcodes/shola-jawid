<?php
/**
 * Template: taxonomy-publication.php — issue archive for the
 * `publication` taxonomy (both شعله جاوید and جهان برای فتح use this one
 * template). Converted from
 * 03_UI_Design/shola-jawid-ui/pages/body-publication-shola-jawid.html
 * and body-publication-a-world-to-win.html (Phase 4.2).
 *
 * @package shola-jawid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$term       = get_queried_object();
$is_current = 'shola-jawid' === $term->slug;
$meta_line  = shola_get_publication_meta_line( $term );

$latest_issue    = null;
$exclude_ids     = array();
if ( $is_current ) {
	$latest = get_posts(
		array(
			'post_type'      => 'issue',
			'posts_per_page' => 1,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array(
					'taxonomy' => 'publication',
					'field'    => 'term_id',
					'terms'    => $term->term_id,
				),
			),
		)
	);
	if ( $latest ) {
		$latest_issue  = $latest[0];
		$exclude_ids[] = $latest_issue->ID;
	}
}

$paged = max( 1, get_query_var( 'paged' ) );
$archive_query = new WP_Query(
	array(
		'post_type'      => 'issue',
		'posts_per_page' => 9,
		'paged'          => $paged,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'post__not_in'   => $exclude_ids,
		'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			array(
				'taxonomy' => 'publication',
				'field'    => 'term_id',
				'terms'    => $term->term_id,
			),
		),
	)
);
?>
	<section class="wrap section-top">

		<header class="page-header<?php echo $is_current ? '' : ' page-header--muted'; ?>">
			<p class="section-marker" lang="en">Publication<?php echo $is_current ? '' : ' · Archived'; ?></p>
			<h1 class="h-page"><?php echo esc_html( $term->name ); ?></h1>
			<?php if ( $term->description ) : ?>
				<p class="dek"><?php echo esc_html( $term->description ); ?></p>
			<?php endif; ?>
			<?php if ( $meta_line ) : ?>
				<div class="row row-tight center mt-md">
					<span class="<?php echo $is_current ? 'badge-current' : 'badge-archive'; ?>"><?php echo esc_html( shola_publication_status_label( $term->slug ) ); ?></span>
					<span class="meta-mono" lang="en"><?php echo esc_html( $meta_line ); ?></span>
				</div>
			<?php endif; ?>
		</header>

		<?php if ( $latest_issue ) : ?>
			<div class="publication-current">
				<p class="section-marker" lang="en">Current</p>
				<h2 class="h-section mt-sm mb-lg">
					<?php
					$number = get_post_meta( $latest_issue->ID, 'shcore_issue_number', true );
					echo esc_html( $number ? sprintf( __( 'شمارهٔ %s · جاری', 'shola-jawid' ), shola_to_persian_digits( $number ) ) : get_the_title( $latest_issue ) );
					?>
				</h2>

				<div class="issue-hero issue-hero--embedded">
					<a href="<?php echo esc_url( get_permalink( $latest_issue ) ); ?>" class="issue-cover">
						<?php echo shola_get_featured_image( $latest_issue, 'shola_issue_cover', array( 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</a>
					<div>
						<p class="meta-mono" lang="en">
							<?php
							echo esc_html(
								( $number ? strtoupper( $term->slug ) . '-' . shola_to_persian_digits( $number ) : get_the_title( $latest_issue ) )
								. ' · ' . shola_get_english_month_abbr( $latest_issue ) . ' ' . shola_to_persian_digits( shola_get_gregorian_year( $latest_issue ) )
							);
							?>
						</p>
						<h3 class="h-page mt-sm"><a href="<?php echo esc_url( get_permalink( $latest_issue ) ); ?>" class="link-quiet"><?php echo esc_html( get_the_title( $latest_issue ) ); ?></a></h3>
						<p class="dek mt-sm"><?php echo esc_html( wp_trim_words( get_the_excerpt( $latest_issue ), 30 ) ); ?></p>
						<div class="row mt-md">
							<a class="btn btn-primary" href="<?php echo esc_url( get_permalink( $latest_issue ) ); ?>"><?php esc_html_e( 'دریافت PDF', 'shola-jawid' ); ?></a>
							<a class="btn btn-ghost" href="<?php echo esc_url( get_permalink( $latest_issue ) ); ?>"><?php esc_html_e( 'فهرست شماره', 'shola-jawid' ); ?></a>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( $archive_query->have_posts() ) : ?>
			<p class="section-marker" lang="en">Archive<?php echo $exclude_ids ? ' · ' . shola_to_persian_digits( $archive_query->found_posts ) . ' Issues' : ''; ?></p>
			<h2 class="h-section mt-sm mb-lg"><?php echo $is_current ? esc_html__( 'شماره‌های پیشین', 'shola-jawid' ) : esc_html__( 'همهٔ شماره‌ها', 'shola-jawid' ); ?></h2>

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
				<div class="pagination mt-lg">
					<?php
					$links = paginate_links(
						array(
							'total'   => $archive_query->max_num_pages,
							'current' => $paged,
							'type'    => 'array',
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
