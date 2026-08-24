<?php
/**
 * Template: front-page.php — homepage.
 *
 * Converted from 03_UI_Design/shola-jawid-ui/pages/body-index.html
 * (Phase 4.2): hero, latest grid (articles + the confirmed document-in-
 * card exception, see docs/CHANGELOG.md 2026-08-06), current issue,
 * topics table, latest documents, announcements. (The original
 * conversion also had a newsletter signup band; removed 2026-08-08 per
 * Farhad — no working subscription mechanism ever existed behind it,
 * see docs/CHANGELOG.md.)
 * Zero inline style="" attributes — all replaced with classes already in
 * assets/css/main.css or added during this conversion (see the "WP
 * conversion (Phase 4.2)" comments in that file).
 *
 * @package shola-jawid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

// Latest across articles + documents (the confirmed mixed-stream case),
// newest first. First result is the hero if it's an article (v6 only
// ever shows an article in hero position); the rest fill the grid.
$latest_query = new WP_Query(
	array(
		'post_type'      => array( 'post', 'document' ),
		'posts_per_page' => 7,
		'orderby'        => 'date',
		'order'          => 'DESC',
	)
);
$latest_posts = $latest_query->posts;

$hero = null;
foreach ( $latest_posts as $i => $p ) {
	if ( 'post' === $p->post_type ) {
		$hero = $p;
		unset( $latest_posts[ $i ] );
		break;
	}
}
$latest_posts = array_slice( array_values( $latest_posts ), 0, 6 );
?>

<?php if ( $hero ) : ?>
	<section class="hero-lead" aria-label="<?php esc_attr_e( 'مقالهٔ سرخط', 'shola-jawid' ); ?>">
		<a href="<?php echo esc_url( get_permalink( $hero ) ); ?>" class="hero-media" aria-hidden="true" tabindex="-1">
			<?php echo shola_get_featured_image( $hero, 'shola_hero_wide', array( 'loading' => 'eager' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- shola_get_featured_image() escapes internally. ?>
		</a>
		<div class="wrap">
			<div class="hero-body">
				<?php
				$hero_terms = get_the_terms( $hero, 'topic' );
				$hero_term  = ( $hero_terms && ! is_wp_error( $hero_terms ) ) ? array_shift( $hero_terms ) : false;
				?>
				<p class="type-label">
					<svg class="glyph" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M2 2h9a3 3 0 0 1 3 3v9H5a3 3 0 0 1-3-3V2Zm1 1v8a2 2 0 0 0 2 2h8V5a2 2 0 0 0-2-2H3Z"/></svg>
					<span><?php echo has_post_format( 'aside', $hero ) ? esc_html__( 'یادداشت', 'shola-jawid' ) : esc_html__( 'مقاله', 'shola-jawid' ); ?></span>
					<?php if ( $hero_term ) : ?>
						<span class="divider">/</span>
						<a href="<?php echo esc_url( get_term_link( $hero_term ) ); ?>"><?php echo esc_html( $hero_term->name ); ?></a>
					<?php endif; ?>
				</p>
				<h1 class="h-display">
					<a href="<?php echo esc_url( get_permalink( $hero ) ); ?>"><?php echo esc_html( get_the_title( $hero ) ); ?></a>
				</h1>
				<p class="dek"><?php echo esc_html( wp_trim_words( get_the_excerpt( $hero ), 34 ) ); ?></p>
				<?php
				$hero_byline_meta = get_post_meta( $hero->ID, 'shcore_byline', true );
				$hero_byline      = $hero_byline_meta ? $hero_byline_meta : get_the_author_meta( 'display_name', $hero->post_author );
				?>
				<p class="card-byline"><?php echo esc_html( $hero_byline ); ?> · <time datetime="<?php echo esc_attr( shola_get_iso_datetime( $hero ) ); ?>"><?php echo esc_html( get_the_date( '', $hero ) ); ?></time></p>
			</div>
		</div>
	</section>

	<hr class="rule wrap">
<?php endif; ?>

<?php if ( $latest_posts ) : ?>
	<section class="wrap sect" aria-label="<?php echo esc_attr( shola_get_label( 'home_articles_section_aria' ) ); ?>">
		<div class="section-head row-between">
			<div class="kicker-row">
				<p class="section-marker"></p>
				<h2 class="h-section"><?php echo esc_html( shola_get_label( 'home_latest_heading' ) ); ?></h2>
			</div>
			<a class="link-more" href="<?php echo esc_url( home_url( '/topics/' ) ); ?>"><?php echo esc_html( shola_get_label( 'home_topics_link_more' ) ); ?> <span class="arr">←</span></a>
		</div>
		<div class="grid-cards">
			<?php
			foreach ( $latest_posts as $p ) {
				get_template_part(
					'template-parts/cards/card',
					null,
					array(
						'post' => $p,
						'type' => 'document' === $p->post_type ? 'document' : 'article',
					)
				);
			}
			?>
		</div>
	</section>
<?php endif; ?>

<?php
/*
 * گزارشات (Reports) — articles tagged with the گزارشات topic term.
 * Computed here, ahead of its own section below, because مقالات's
 * exclusion pool (next) needs its post IDs — گزارشات is itself one of
 * the موضوعات topics مقالات draws from, so without this exclusion the
 * same article could legitimately appear in both new sections at
 * once. Not part of the original spec's explicit instructions (that
 * part was left as an open question); resolved this way and logged in
 * docs/CHANGELOG.md, not decided silently. get_term_by( 'name', ... )
 * rather than 'slug' — this term's slug is Persian and gets URL-
 * encoded by sanitize_title(), 'name' avoids that entirely.
 */
$reports_term  = get_term_by( 'name', 'گزارشات', 'topic' );
$reports_query = null;
if ( $reports_term ) {
	$reports_query = new WP_Query(
		array(
			'post_type'      => 'post',
			'posts_per_page' => 6,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array(
					'taxonomy' => 'topic',
					'field'    => 'term_id',
					'terms'    => $reports_term->term_id,
				),
			),
		)
	);
}

/*
 * مقالات (Articles) — every topic combined, explicitly excluding
 * anything already shown in تازه‌ترین‌ها above (same query args as
 * $latest_query, including the post that became the hero — it was
 * fetched by that exact query before the hero-extraction logic ran,
 * so it counts as "shown" too) plus گزارشات's own posts (see comment
 * above). post__not_in, not an offset/date cutoff, per the explicit
 * requirement: no drift if either query's args ever change
 * independently. This makes مقالات a self-refreshing "next tier
 * down" — as newer posts rotate into تازه‌ترین‌ها, whatever ages out
 * starts appearing here automatically, no manual curation.
 */
$articles_excluded_ids = wp_list_pluck( $latest_query->posts, 'ID' );
if ( $reports_query ) {
	$articles_excluded_ids = array_merge( $articles_excluded_ids, wp_list_pluck( $reports_query->posts, 'ID' ) );
}
$articles_query = new WP_Query(
	array(
		'post_type'      => 'post',
		'posts_per_page' => 6,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'post__not_in'   => $articles_excluded_ids,
	)
);
?>

<?php if ( $articles_query->have_posts() ) : ?>
	<section class="sect-cream sect" aria-label="<?php esc_attr_e( 'مقالات', 'shola-jawid' ); ?>">
		<div class="wrap">
			<div class="section-head row-between">
				<div class="kicker-row">
					<p class="section-marker"></p>
					<h2 class="h-section"><?php esc_html_e( 'مقالات', 'shola-jawid' ); ?></h2>
				</div>
				<a class="link-more" href="<?php echo esc_url( home_url( '/topics/' ) ); ?>"><?php esc_html_e( 'همهٔ مقالات', 'shola-jawid' ); ?> <span class="arr">←</span></a>
			</div>
			<div class="grid-cards">
				<?php
				while ( $articles_query->have_posts() ) :
					$articles_query->the_post();
					get_template_part(
						'template-parts/cards/card',
						null,
						array(
							'post' => get_post(),
							'type' => 'article',
						)
					);
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php if ( $reports_query && $reports_query->have_posts() ) : ?>
	<section class="wrap sect" aria-label="<?php esc_attr_e( 'گزارشات', 'shola-jawid' ); ?>">
		<div class="section-head row-between">
			<div class="kicker-row">
				<p class="section-marker"></p>
				<h2 class="h-section"><?php esc_html_e( 'گزارشات', 'shola-jawid' ); ?></h2>
			</div>
			<a class="link-more" href="<?php echo esc_url( get_term_link( $reports_term ) ); ?>"><?php esc_html_e( 'همهٔ گزارش‌ها', 'shola-jawid' ); ?> <span class="arr">←</span></a>
		</div>
		<div class="grid-cards">
			<?php
			while ( $reports_query->have_posts() ) :
				$reports_query->the_post();
				get_template_part(
					'template-parts/cards/card',
					null,
					array(
						'post' => get_post(),
						'type' => 'article',
					)
				);
			endwhile;
			wp_reset_postdata();
			?>
		</div>
	</section>
<?php endif; ?>

<?php
$current_issue_query = new WP_Query(
	array(
		'post_type'      => 'issue',
		'posts_per_page' => 1,
		'orderby'        => 'date',
		'order'          => 'DESC',
	)
);
$current_issue       = $current_issue_query->have_posts() ? $current_issue_query->posts[0] : null;
?>
<?php if ( $current_issue ) : ?>
	<?php
	/*
	 * شمارهٔ جاری reordered ahead of انتشارات حزب, 2026-08-24 (Phase A,
	 * client-approved) — .sect-cream unchanged (still distinct from
	 * گزارشات's paper band directly above it). See docs/CHANGELOG.md.
	 */
	?>
	<section class="sect-cream sect" aria-label="<?php esc_attr_e( 'شمارهٔ جاری و کتابخانه', 'shola-jawid' ); ?>">
		<div class="wrap">
			<div class="section-head row-between">
				<div class="kicker-row">
					<p class="section-marker"></p>
					<h2 class="h-section"><?php esc_html_e( 'شمارهٔ جاری', 'shola-jawid' ); ?></h2>
				</div>
				<?php
				$issue_pub_terms = get_the_terms( $current_issue, 'publication' );
				$issue_pub_term  = ( $issue_pub_terms && ! is_wp_error( $issue_pub_terms ) ) ? array_shift( $issue_pub_terms ) : false;
				?>
				<?php if ( $issue_pub_term ) : ?>
					<a class="link-more" href="<?php echo esc_url( get_term_link( $issue_pub_term ) ); ?>"><?php esc_html_e( 'آرشیو شماره‌ها', 'shola-jawid' ); ?> <span class="arr">←</span></a>
				<?php endif; ?>
			</div>

			<div class="issue-lead">
				<div class="issue-hero issue-hero--embedded">

					<a href="<?php echo esc_url( get_permalink( $current_issue ) ); ?>" class="issue-cover reveal">
						<?php echo shola_get_featured_image( $current_issue, 'shola_issue_cover', array( 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- shola_get_featured_image() escapes internally. ?>
					</a>

					<div class="reveal">
						<?php $issue_number = get_post_meta( $current_issue->ID, 'shcore_issue_number', true ); ?>
						<p class="badge-current">
							<?php
							if ( $issue_number ) {
								/* translators: %s: issue number. */
								printf( esc_html__( 'شمارهٔ %s · جاری', 'shola-jawid' ), esc_html( $issue_number ) );
							} else {
								esc_html_e( 'جاری', 'shola-jawid' );
							}
							?>
						</p>
						<h3 class="h-page mt-sm"><a href="<?php echo esc_url( get_permalink( $current_issue ) ); ?>" class="link-quiet"><?php echo esc_html( ( $issue_pub_term ? $issue_pub_term->name . ' · ' : '' ) . ( $issue_number ? sprintf( /* translators: %s: issue number. */ __( 'شمارهٔ %s', 'shola-jawid' ), $issue_number ) : get_the_title( $current_issue ) ) ); ?></a></h3>
						<p class="dek mt-sm"><?php echo esc_html( wp_trim_words( get_the_excerpt( $current_issue ), 30 ) ); ?></p>
						<dl class="issue-meta">
							<dt><?php esc_html_e( 'تاریخ نشر', 'shola-jawid' ); ?></dt>
							<dd><time datetime="<?php echo esc_attr( shola_get_iso_datetime( $current_issue ) ); ?>"><?php echo esc_html( get_the_date( '', $current_issue ) ); ?></time></dd>
							<?php $volume = get_post_meta( $current_issue->ID, 'shcore_volume', true ); ?>
							<?php if ( $volume ) : ?>
								<dt><?php esc_html_e( 'دوره / جلد', 'shola-jawid' ); ?></dt>
								<dd><?php echo esc_html( $volume ); ?></dd>
							<?php endif; ?>
							<?php
							$pdf_id = (int) get_post_meta( $current_issue->ID, 'shcore_pdf_id', true );
							if ( $pdf_id ) :
								$pdf_file = get_attached_file( $pdf_id );
								$pdf_size = $pdf_file && file_exists( $pdf_file ) ? size_format( filesize( $pdf_file ) ) : '';
								?>
								<dt><?php esc_html_e( 'فایل PDF', 'shola-jawid' ); ?></dt>
								<dd lang="en"><?php echo esc_html( $pdf_size ? $pdf_size : 'PDF' ); ?></dd>
							<?php endif; ?>
						</dl>
						<div class="row mt-sm">
							<a class="btn btn-primary" href="<?php echo esc_url( get_permalink( $current_issue ) ); ?>"><?php esc_html_e( 'دریافت شماره', 'shola-jawid' ); ?></a>
							<?php if ( $issue_pub_term ) : ?>
								<a class="btn btn-ghost" href="<?php echo esc_url( get_term_link( $issue_pub_term ) ); ?>"><?php esc_html_e( 'آرشیو شماره‌ها', 'shola-jawid' ); ?></a>
							<?php endif; ?>
						</div>
					</div>

				</div>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php
// انتشارات حزب (Party Publications) — recent issues across both
// publications, no publication-term restriction. issue-card.php, not
// card.php: structurally distinct anatomy (portrait cover, box-shadow,
// no dek/byline) confirmed in docs/CHANGELOG.md Phase 1.2 — not a
// variant of the article card. .issue-grid, the same shelf-density
// wrapper class taxonomy-publication.php already uses (1 col mobile ->
// 3 -> 4 -> 5 col desktop), not .grid-cards.
//
// Reordered to appear after شمارهٔ جاری (was before it), 2026-08-24
// (Phase A, client-approved) — see docs/CHANGELOG.md. .sect-tint
// unchanged; still distinct from شمارهٔ جاری's cream band directly
// above and موضوعات's paper band directly below, so background-band
// alternation still holds with no adjacent repeats.
$party_issues_query = new WP_Query(
	array(
		'post_type'      => 'issue',
		'posts_per_page' => 10,
		'orderby'        => 'date',
		'order'          => 'DESC',
	)
);
?>
<?php if ( $party_issues_query->have_posts() ) : ?>
	<section class="sect-tint sect" aria-label="<?php esc_attr_e( 'انتشارات حزب', 'shola-jawid' ); ?>">
		<div class="wrap">
			<div class="section-head row-between">
				<div class="kicker-row">
					<p class="section-marker"></p>
					<h2 class="h-section"><?php esc_html_e( 'انتشارات حزب', 'shola-jawid' ); ?></h2>
				</div>
				<a class="link-more" href="<?php echo esc_url( home_url( '/publications/' ) ); ?>"><?php esc_html_e( 'همهٔ نشرات', 'shola-jawid' ); ?> <span class="arr">←</span></a>
			</div>
			<div class="issue-grid">
				<?php
				while ( $party_issues_query->have_posts() ) :
					$party_issues_query->the_post();
					get_template_part( 'template-parts/cards/issue-card', null, array( 'post' => get_post() ) );
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		</div>
	</section>
<?php endif; ?>

<section class="wrap sect" aria-label="<?php esc_attr_e( 'موضوعات', 'shola-jawid' ); ?>">
	<div class="section-head center">
		<div class="kicker-row">
			<p class="section-marker"></p>
			<h2 class="h-section"><?php echo esc_html( shola_get_label( 'home_topics_section_heading' ) ); ?></h2>
		</div>
	</div>
	<ul class="topic-list">
		<?php foreach ( shola_get_topic_slugs_ordered() as $slug ) : ?>
			<?php
			$term = get_term_by( 'slug', $slug, 'topic' );
			if ( ! $term ) {
				continue;
			}
			?>
			<li><a href="<?php echo esc_url( get_term_link( $term ) ); ?>">
				<span class="name"><?php echo esc_html( $term->name ); ?></span>
				<?php /* translators: %s: number of articles. */ ?>
				<span class="count"><?php echo esc_html( sprintf( _n( '%s مقاله', '%s مقاله', $term->count, 'shola-jawid' ), shola_to_persian_digits( $term->count ) ) ); ?></span></a></li>
		<?php endforeach; ?>
	</ul>
</section>

<?php
$documents_query = new WP_Query(
	array(
		'post_type'      => 'document',
		'posts_per_page' => 4,
		'orderby'        => 'date',
		'order'          => 'DESC',
	)
);
?>
<?php if ( $documents_query->have_posts() ) : ?>
	<section class="wrap sect" aria-label="<?php esc_attr_e( 'کتابخانه', 'shola-jawid' ); ?>">
		<div class="section-head row-between">
			<div class="kicker-row">
				<p class="section-marker"></p>
				<h2 class="h-section"><?php echo esc_html( shola_get_label( 'latest_documents_heading' ) ); ?></h2>
			</div>
			<a class="link-more" href="<?php echo esc_url( home_url( '/library/' ) ); ?>"><?php esc_html_e( 'همهٔ مجموعه‌ها', 'shola-jawid' ); ?> <span class="arr">←</span></a>
		</div>
		<ul>
			<?php
			while ( $documents_query->have_posts() ) :
				$documents_query->the_post();
				get_template_part( 'template-parts/rows/document-row', null, array( 'post' => get_post() ) );
			endwhile;
			wp_reset_postdata();
			?>
		</ul>
	</section>
<?php endif; ?>

<?php
$announcements_query = new WP_Query(
	array(
		'post_type'      => 'announcement',
		'posts_per_page' => 3,
		'orderby'        => 'date',
		'order'          => 'DESC',
	)
);
?>
<?php if ( $announcements_query->have_posts() ) : ?>
	<section class="wrap sect" aria-label="<?php esc_attr_e( 'اطلاعیه‌ها', 'shola-jawid' ); ?>">
		<div class="section-head row-between">
			<div class="kicker-row">
				<p class="section-marker"></p>
				<h2 class="h-section"><?php esc_html_e( 'اطلاعیه‌ها', 'shola-jawid' ); ?></h2>
			</div>
			<a class="link-more" href="<?php echo esc_url( home_url( '/announcements/' ) ); ?>"><?php esc_html_e( 'همهٔ اطلاعیه‌ها', 'shola-jawid' ); ?> <span class="arr">←</span></a>
		</div>
		<ul class="announce-list">
			<?php
			while ( $announcements_query->have_posts() ) :
				$announcements_query->the_post();
				?>
				<li>
					<time datetime="<?php echo esc_attr( shola_get_iso_datetime() ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
					<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
				</li>
			<?php endwhile; ?>
			<?php wp_reset_postdata(); ?>
		</ul>
	</section>
<?php endif; ?>

<?php get_footer(); ?>
