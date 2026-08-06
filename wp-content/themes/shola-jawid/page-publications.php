<?php
/**
 * Template: page-publications.php — نشرات (Publications listing).
 * Converted from 03_UI_Design/shola-jawid-ui/pages/body-publications.html
 * (Phase 4.2). Applies to the Page with slug `publications`.
 *
 * @package shola-jawid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
	<section class="wrap section-top">

		<header class="page-header">
			<p class="section-marker" lang="en">Publications</p>
			<h1 class="h-page"><?php esc_html_e( 'نشرات', 'shola-jawid' ); ?></h1>
			<p class="dek"><?php esc_html_e( 'دو نشریه با آرشیو کامل PDF. «شعله جاوید» جاری منتشر می‌شود؛ «جهان برای فتح» به‌صورت آرشیو تاریخی نگه‌داری می‌شود.', 'shola-jawid' ); ?></p>
		</header>

		<div class="publications-list">
			<?php foreach ( shola_get_publication_slugs_ordered() as $slug ) : ?>
				<?php
				$term = get_term_by( 'slug', $slug, 'publication' );
				if ( ! $term ) {
					continue;
				}
				$is_current  = 'shola-jawid' === $slug;
				$meta_line   = shola_get_publication_meta_line( $term );
				$latest_args = array(
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
				);
				$latest_issue = get_posts( $latest_args );
				?>
				<article class="publication-item<?php echo $is_current ? '' : ' publication-item--archived'; ?>">
					<div>
						<p class="<?php echo $is_current ? 'badge-current' : 'badge-archive'; ?>"><?php echo esc_html( shola_publication_status_label( $slug ) ); ?></p>
						<h2 class="h-page"><?php echo esc_html( $term->name ); ?></h2>
						<?php if ( $term->description ) : ?>
							<p class="dek"><?php echo esc_html( $term->description ); ?></p>
						<?php endif; ?>
						<?php if ( $meta_line ) : ?>
							<p class="meta-mono" lang="en"><?php echo esc_html( $meta_line ); ?></p>
						<?php endif; ?>
						<div class="row">
							<a class="btn <?php echo $is_current ? 'btn-primary' : 'btn-ghost'; ?>" href="<?php echo esc_url( get_term_link( $term ) ); ?>">
								<?php echo $is_current ? esc_html__( 'آرشیو شماره‌ها', 'shola-jawid' ) : esc_html__( 'آرشیو کامل', 'shola-jawid' ); ?>
							</a>
							<?php if ( $is_current && $latest_issue ) : ?>
								<a class="btn btn-ghost" href="<?php echo esc_url( get_permalink( $latest_issue[0] ) ); ?>"><?php esc_html_e( 'شمارهٔ جاری', 'shola-jawid' ); ?></a>
							<?php endif; ?>
						</div>
					</div>
				</article>
			<?php endforeach; ?>
		</div>

	</section>
<?php
get_footer();
