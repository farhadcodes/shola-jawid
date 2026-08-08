<?php
/**
 * Template: search.php — جست‌وجو (Search results).
 * Converted from 03_UI_Design/shola-jawid-ui/pages/body-search.html
 * (Phase 4.2). Native WP search (`s=`), extended by
 * shola-core\Post_Types::include_cpts_in_search() to include articles,
 * notes, issues, and documents together — see that method's docblock
 * for the exact rules, including the `result_type` query var behind
 * the filter tabs below. `announcement` is deliberately excluded.
 *
 * @package shola-jawid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$search_query = get_search_query();
$paged        = max( 1, get_query_var( 'paged' ) );
$result_type  = sanitize_key( get_query_var( 'result_type' ) );

$filters = array(
	''         => __( 'همه', 'shola-jawid' ),
	'article'  => __( 'مقاله', 'shola-jawid' ),
	'note'     => __( 'یادداشت', 'shola-jawid' ),
	'issue'    => __( 'شمارهٔ نشریه', 'shola-jawid' ),
	'document' => __( 'سند کتابخانه', 'shola-jawid' ),
);
?>
	<section class="wrap section-top">

		<header class="page-header page-header--narrow">
			<div class="kicker-row">
				<p class="section-marker"></p>
				<h1 class="h-page"><?php esc_html_e( 'جست‌وجو', 'shola-jawid' ); ?></h1>
			</div>
			<p class="dek"><?php esc_html_e( 'در مقالات، شماره‌ها و اسناد کتابخانه — به فارسی یا انگلیسی.', 'shola-jawid' ); ?></p>
		</header>

		<form class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" role="search">
			<label class="label search-form-label" for="q"><?php esc_html_e( 'عبارت جست‌وجو', 'shola-jawid' ); ?></label>
			<div class="search-form-row">
				<input class="field" type="search" id="q" name="s" placeholder="<?php esc_attr_e( 'مثلاً: تورم، دهقان، آب', 'shola-jawid' ); ?>" value="<?php echo esc_attr( $search_query ); ?>">
				<button class="btn btn-primary" type="submit"><?php esc_html_e( 'جست‌وجو', 'shola-jawid' ); ?></button>
			</div>
			<div class="filter-tabs search-filter-tabs">
				<?php foreach ( $filters as $value => $label ) : ?>
					<a
						class="<?php echo $result_type === $value ? 'active' : ''; ?>"
						href="
						<?php
						echo esc_url(
							add_query_arg(
								array_filter(
									array(
										's'           => $search_query,
										'result_type' => $value,
									)
								),
								home_url( '/' )
							)
						);
						?>
								"
					><?php echo esc_html( $label ); ?></a>
				<?php endforeach; ?>
			</div>
		</form>

		<div class="search-results-wrap">
			<?php if ( have_posts() ) : ?>
				<p class="meta-mono search-results-count" lang="en">
					<?php echo esc_html( shola_to_persian_digits( $GLOBALS['wp_query']->found_posts ) ); ?> RESULTS FOR "<?php echo esc_html( $search_query ); ?>"
				</p>

				<ul class="stack-lg">
					<?php
					while ( have_posts() ) :
						the_post();
						get_template_part(
							'template-parts/search/result',
							null,
							array(
								'post'  => get_post(),
								'query' => $search_query,
							)
						);
					endwhile;
					?>
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
			<?php else : ?>
				<p class="dek search-no-results"><?php esc_html_e( 'نتیجه‌ای یافت نشد. عبارت دیگری را امتحان کنید.', 'shola-jawid' ); ?></p>
			<?php endif; ?>
		</div>

	</section>
<?php
get_footer();
