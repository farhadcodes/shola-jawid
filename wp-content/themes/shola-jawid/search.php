<?php
/**
 * Template: search.php — جست‌وجو (Search results).
 * Converted from 03_UI_Design/shola-jawid-ui/pages/body-search.html
 * (Phase 4.2). Native WP search (`s=`), extended by
 * shola-core\Post_Types::include_cpts_in_search() to include articles,
 * notes, issues, documents, and party publications together (the last
 * added 2026-09-02, alongside the `party_publication` CPT) — see that
 * method's docblock for the exact rules, including the `result_type`
 * query var behind the filter tabs below. `announcement` is
 * deliberately excluded.
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
	''                  => __( 'همه', 'shola-jawid' ),
	'article'           => __( 'مقاله', 'shola-jawid' ),
	'note'              => __( 'یادداشت', 'shola-jawid' ),
	'issue'             => __( 'شمارهٔ نشریه', 'shola-jawid' ),
	'document'          => __( 'سند کتابخانه', 'shola-jawid' ),
	'party_publication' => __( 'انتشارات حزب', 'shola-jawid' ),
);
?>
	<section class="wrap section-top">

		<?php
		get_template_part(
			'template-parts/breadcrumb',
			null,
			array(
				'items' => array(
					array(
						// No 'url': a search results page has no single
						// fixed canonical address of its own to self-link
						// to (it's whatever ?s= the visitor typed), so this
						// crumb renders as inert text, not a link.
						'label' => __( 'جست‌وجو', 'shola-jawid' ),
						'url'   => '',
					),
				),
			)
		);
		?>

		<header class="page-header page-header--narrow">
			<div class="kicker-row">
				<p class="section-marker"></p>
				<h1 class="h-page"><?php esc_html_e( 'جست‌وجو', 'shola-jawid' ); ?></h1>
			</div>
			<p class="dek"><?php esc_html_e( 'در مقالات، شماره‌ها، اسناد کتابخانه و انتشارات حزب — به فارسی یا انگلیسی.', 'shola-jawid' ); ?></p>
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

		<?php
		/*
		 * Pagination links computed once, 2026-10-01 (spec-audit gap
		 * B12) — rendered both above and below the results list below,
		 * same "build once, echo twice" pattern, so the top copy can't
		 * silently drift from the bottom one.
		 */
		$pagination_links = array();
		if ( $GLOBALS['wp_query']->max_num_pages > 1 ) {
			$raw_links = paginate_links(
				array(
					'total'     => $GLOBALS['wp_query']->max_num_pages,
					'current'   => $paged,
					'type'      => 'array',
					'prev_text' => '→',
					'next_text' => '←',
				)
			);
			if ( $raw_links ) {
				foreach ( $raw_links as $raw_link ) {
					$raw_link            = shola_persian_digits_pagination_link( $raw_link );
					$pagination_links[]  = str_replace( 'page-numbers', 'page-num', $raw_link );
				}
			}
		}
		?>

		<div class="search-results-wrap">
			<?php if ( '' === trim( $search_query ) ) : ?>
				<?php
				/*
				 * Empty-query guard, 2026-10-01 (spec-audit gap B12) —
				 * confirmed live before fixing: `/?s=` (an empty but
				 * present query string) matched WordPress's own search
				 * SQL trivially and listed every single post on the site,
				 * not "no results". A blank search box submit should ask
				 * for a term, not dump the whole site.
				 */
				?>
				<p class="dek search-no-results"><?php esc_html_e( 'برای دیدن نتیجه، عبارتی را در کادر بالا وارد کنید.', 'shola-jawid' ); ?></p>
			<?php elseif ( have_posts() ) : ?>
				<?php
				/*
				 * Bug fix, 2026-09-10: this line was literal hardcoded
				 * English ("N RESULTS FOR \"query\""), not wrapped in any
				 * translation function — caught live by Farhad on a
				 * Persian-only site (CLAUDE.md §1: no hardcoded UI copy in
				 * template files, ever). `lang="en"` removed along with
				 * it — that attribute exists for genuinely-Latin content
				 * (PDF/KB/MB technical units, month abbreviations — see
				 * CLAUDE.md's own list of what's allowed to stay Latin),
				 * not for content that's simply been written in English by
				 * mistake.
				 */
				?>
				<p class="search-results-count">
					<?php
					/*
					 * Reworked 2026-09-16, per Farhad comparing against an
					 * aawsat.com screenshot: the result count and search
					 * term used to render as one plain `.meta-mono` line
					 * (13px, muted stone) — easy to miss entirely. Scoped
					 * deliberately narrow, per his explicit "don't touch
					 * anything else": only this line's own markup/styling
					 * changed (search.php's h1/dek, the form, and the
					 * filter tabs are all untouched). The two dynamic
					 * pieces are now individually wrapped so CSS can give
					 * each its own emphasis (.search-results-count-num
					 * bold, .search-results-count-term bold + accent red)
					 * — "نتیجه برای" stays the one translatable fragment,
					 * same as the rest of the theme's copy; the wrapping
					 * `<span>`s and guillemets are structural markup, not
					 * translated text, no different from the literal «»
					 * the original single string already hardcoded.
					 */
					printf(
						'<span class="search-results-count-num">%1$s</span> %2$s «<span class="search-results-count-term">%3$s</span>»',
						esc_html( shola_to_persian_digits( $GLOBALS['wp_query']->found_posts ) ),
						esc_html__( 'نتیجه برای', 'shola-jawid' ),
						esc_html( $search_query )
					);
					?>
				</p>

				<?php if ( $pagination_links ) : ?>
					<div class="pagination pagination--top">
						<?php foreach ( $pagination_links as $pagination_link ) : ?>
							<?php echo wp_kses_post( $pagination_link ); ?>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

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

				<?php if ( $pagination_links ) : ?>
					<div class="pagination">
						<?php foreach ( $pagination_links as $pagination_link ) : ?>
							<?php echo wp_kses_post( $pagination_link ); ?>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			<?php else : ?>
				<p class="dek search-no-results"><?php esc_html_e( 'نتیجه‌ای یافت نشد. عبارت دیگری را امتحان کنید.', 'shola-jawid' ); ?></p>
			<?php endif; ?>
		</div>

	</section>
<?php
get_footer();
