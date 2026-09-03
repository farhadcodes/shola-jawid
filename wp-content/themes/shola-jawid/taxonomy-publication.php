<?php
/**
 * Template: taxonomy-publication.php — the `publication` taxonomy's
 * archive. Two distinct views share this one file, branching on whether
 * the term being viewed has children:
 *
 * - A top-level term (شعله جاوید / جهان برای فتح itself) — no issue list
 *   here anymore; renders the .topic-list grid of its 4 دوره (period)
 *   sub-terms instead, added 2026-09-02 (client request, relayed by
 *   Farhad: هر نشریه به ۴ دوره تقسیم می‌شود, each browsable separately).
 *   The child terms themselves are seeded by
 *   SholaCore\Taxonomies::seed_publication_periods() — see that method's
 *   docblock for the migration story (existing issues bucketed into
 *   «دورهٔ اول» since there's no way to infer which دوره they actually
 *   belong to; Farhad redistributes from wp-admin).
 * - A دوره term (a leaf — no children of its own) — this is everything
 *   the *whole* template used to do before 2026-09-02: the "current
 *   issue" hero + paginated issue grid, converted from
 *   03_UI_Design/shola-jawid-ui/pages/body-publication-shola-jawid.html
 *   and body-publication-a-world-to-win.html (Phase 4.2). Unchanged
 *   except $is_current now resolves from the term's *parent* slug
 *   (شعله جاوید vs. جهان برای فتح), since a دوره term's own slug is
 *   publication-agnostic (`shola-jawid-dowre-1`,
 *   `a-world-to-win-dowre-1`, ...) — the "is this the actively-
 *   publishing publication" question was never really about the دوره
 *   itself.
 *
 * Both branches also got a `.article-crumb` breadcrumb, 2026-09-03 —
 * this template never had one before (unlike every single-*.php view),
 * and once دوره tiles introduced a second click just to reach an issue
 * list, Farhad found there was no way back up without the browser's own
 * back button. Same "صفحهٔ اصلی / نشرات / ..." pattern already used by
 * single-issue.php/single-document.php, extended one level for the leaf
 * view (Home / نشرات / {parent publication} / {دوره}) so the parent
 * publication's own tile-grid page — the "back" step in this new two-
 * step browse path — is a real link, not just a suggestion.
 *
 * @package shola-jawid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$term = get_queried_object();

$period_terms = get_terms(
	array(
		'taxonomy'   => 'publication',
		'parent'     => $term->term_id,
		'hide_empty' => false,
	)
);
$period_terms = ( $period_terms && ! is_wp_error( $period_terms ) ) ? $period_terms : array();

/*
 * Sorted by the ترتیب term meta Farhad can set per دوره (wp-admin →
 * نشریات — SholaCore\Taxonomies::render_publication_order_*_field()),
 * 2026-09-02: WordPress's default term order for a custom taxonomy
 * isn't guaranteed to be اول/دوم/سوم/چهارم order. Sorted here in PHP
 * with usort() rather than via get_terms()'s own
 * orderby => meta_value_num — that option performs an inner join
 * against term meta and silently *drops* any term that has no
 * `shcore_term_order` value set at all, which would make a new دوره
 * Farhad adds (before he's set a ترتیب for it) vanish from this page
 * instead of just sorting last. A term with no value set sorts after
 * every term that has one, matching the field's own "خالی یعنی آخر
 * فهرست" description; identically-ordered/unordered terms keep their
 * relative get_terms() order (PHP's usort() has been a stable sort
 * since PHP 8.0 — this project already requires PHP 8.1, CLAUDE.md §0).
 */
usort(
	$period_terms,
	function ( $a, $b ) {
		$order_a = get_term_meta( $a->term_id, 'shcore_term_order', true );
		$order_b = get_term_meta( $b->term_id, 'shcore_term_order', true );
		$order_a = ( '' === $order_a ) ? PHP_INT_MAX : (int) $order_a;
		$order_b = ( '' === $order_b ) ? PHP_INT_MAX : (int) $order_b;
		return $order_a <=> $order_b;
	}
);

if ( $period_terms ) {
	?>
	<section class="wrap section-top">

		<nav class="article-crumb mt-lg" aria-label="<?php esc_attr_e( 'مسیر', 'shola-jawid' ); ?>">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'صفحهٔ اصلی', 'shola-jawid' ); ?></a>
			<span aria-hidden="true"> / </span>
			<a href="<?php echo esc_url( home_url( '/publications/' ) ); ?>"><?php esc_html_e( 'نشرات', 'shola-jawid' ); ?></a>
			<span aria-hidden="true"> / </span>
			<a class="active" href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( $term->name ); ?></a>
		</nav>

		<header class="page-header">
			<div class="kicker-row">
				<p class="section-marker"></p>
				<h1 class="h-page"><?php echo esc_html( $term->name ); ?></h1>
			</div>
			<?php if ( $term->description ) : ?>
				<p class="dek"><?php echo esc_html( $term->description ); ?></p>
			<?php endif; ?>
		</header>

		<ul class="topic-list">
			<?php foreach ( $period_terms as $period_term ) : ?>
				<li><a href="<?php echo esc_url( get_term_link( $period_term ) ); ?>">
					<span class="name"><?php echo esc_html( $period_term->name ); ?></span>
					<span class="count"><?php echo esc_html( sprintf( /* translators: %s: issue count. */ _n( '%s شماره', '%s شماره', $period_term->count, 'shola-jawid' ), shola_to_persian_digits( $period_term->count ) ) ); ?></span></a></li>
			<?php endforeach; ?>
		</ul>

	</section>
	<?php
	get_footer();
	return;
}

$parent_term = $term->parent ? get_term( $term->parent, 'publication' ) : false;
$root_slug   = ( $parent_term && ! is_wp_error( $parent_term ) ) ? $parent_term->slug : $term->slug;
$is_current  = 'shola-jawid' === $root_slug;
$meta_line   = shola_get_publication_meta_line( $term );

$latest_issue = null;
$exclude_ids  = array();
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

$paged         = max( 1, get_query_var( 'paged' ) );
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

		<nav class="article-crumb mt-lg" aria-label="<?php esc_attr_e( 'مسیر', 'shola-jawid' ); ?>">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'صفحهٔ اصلی', 'shola-jawid' ); ?></a>
			<span aria-hidden="true"> / </span>
			<a href="<?php echo esc_url( home_url( '/publications/' ) ); ?>"><?php esc_html_e( 'نشرات', 'shola-jawid' ); ?></a>
			<?php if ( $parent_term ) : ?>
				<span aria-hidden="true"> / </span>
				<a href="<?php echo esc_url( get_term_link( $parent_term ) ); ?>"><?php echo esc_html( $parent_term->name ); ?></a>
			<?php endif; ?>
			<span aria-hidden="true"> / </span>
			<a class="active" href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php echo esc_html( $term->name ); ?></a>
		</nav>

		<header class="page-header<?php echo $is_current ? '' : ' page-header--muted'; ?>">
			<div class="kicker-row">
				<p class="section-marker"></p>
				<h1 class="h-page"><?php echo esc_html( $term->name ); ?></h1>
			</div>
			<?php if ( $term->description ) : ?>
				<p class="dek"><?php echo esc_html( $term->description ); ?></p>
			<?php endif; ?>
			<?php if ( $meta_line ) : ?>
				<div class="row row-tight center mt-md">
					<span class="<?php echo $is_current ? 'badge-current' : 'badge-archive'; ?>"><?php echo esc_html( shola_publication_status_label( $root_slug ) ); ?></span>
					<span class="meta-mono" lang="en"><?php echo esc_html( $meta_line ); ?></span>
				</div>
			<?php endif; ?>
		</header>

		<?php if ( $latest_issue ) : ?>
			<div class="publication-current">
				<div class="kicker-row">
					<p class="section-marker"></p>
					<h2 class="h-section mt-sm mb-lg">
						<?php
						$number = get_post_meta( $latest_issue->ID, 'shcore_issue_number', true );
						echo esc_html( $number ? sprintf( /* translators: %s: issue number. */ __( 'شمارهٔ %s · جاری', 'shola-jawid' ), shola_to_persian_digits( $number ) ) : get_the_title( $latest_issue ) );
						?>
					</h2>
				</div>

				<div class="issue-hero issue-hero--embedded">
					<a href="<?php echo esc_url( get_permalink( $latest_issue ) ); ?>" class="issue-cover" aria-hidden="true" tabindex="-1">
						<?php echo shola_get_featured_image( $latest_issue, 'shola_issue_cover', array( 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</a>
					<div>
						<p class="meta-mono" lang="en">
							<?php
							echo esc_html(
								( $number ? strtoupper( $root_slug ) . '-' . shola_to_persian_digits( $number ) : get_the_title( $latest_issue ) )
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
			<div class="kicker-row">
				<p class="section-marker">
					<?php
					if ( $exclude_ids ) {
						echo esc_html(
							sprintf(
								/* translators: %s: archived issue count. */
								_n( '%s شماره', '%s شماره', $archive_query->found_posts, 'shola-jawid' ),
								shola_to_persian_digits( $archive_query->found_posts )
							)
						);
					}
					?>
				</p>
				<h2 class="h-section mt-sm mb-lg"><?php echo $is_current ? esc_html__( 'شماره‌های پیشین', 'shola-jawid' ) : esc_html__( 'همهٔ شماره‌ها', 'shola-jawid' ); ?></h2>
			</div>

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
							'total'     => $archive_query->max_num_pages,
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
