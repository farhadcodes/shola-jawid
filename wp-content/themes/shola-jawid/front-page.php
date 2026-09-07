<?php
/**
 * Template: front-page.php — homepage.
 *
 * Converted from 03_UI_Design/shola-jawid-ui/pages/body-index.html
 * (Phase 4.2). Zero inline style="" attributes — all replaced with
 * classes already in assets/css/main.css or added during this
 * conversion (see the "WP conversion (Phase 4.2)" comments in that
 * file).
 *
 * Section order, changed 2026-09-07 (client decision, relayed by
 * Farhad): headline article, تازه‌ترین مقالات, گزارش, اسناد حزب, نشریات
 * (شمارهٔ جاری), انتشارات حزب, کتابخانه, موضوعات. This reverses the
 * previous 2026-09-05 order — تازه‌ها (the standalone "recent" grid) is
 * removed entirely, not just relabeled: the client concluded it and
 * مقالات were duplicating the same content, so the homepage now shows
 * one merged "latest articles" section directly under the hero instead
 * of two (see docs/CHANGELOG.md). A newsletter signup band was in an
 * earlier version of this page and is deliberately not present —
 * removed 2026-08-08, per Farhad (see docs/CHANGELOG.md).
 *
 * اطلاعیه‌ها is back on the homepage as of 2026-09-07 (Phase 11) — not as
 * its own full section like the one removed 2026-08-24, but as a
 * spotlight tile embedded inside تازه‌ترین مقالات's own grid (see that
 * section's query comment below and template-parts/cards/announcement-
 * spotlight.php). The 2026-08-24 removal note above is history, not a
 * standing rule against اطلاعیه‌ها appearing on the homepage at all.
 *
 * @package shola-jawid
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

/*
 * Hero — single latest مقاله. Unchanged behavior/markup from the
 * previous تازه‌ها query's hero (same shola_get_featured_image() call,
 * same fields) — only the standalone "recent grid" this hero used to
 * sit above is gone, merged into تازه‌ترین مقالات below (2026-09-07;
 * see this file's own top docblock and docs/CHANGELOG.md).
 */
$hero_query = new WP_Query(
	array(
		'post_type'      => 'post',
		'posts_per_page' => 1,
		'orderby'        => 'date',
		'order'          => 'DESC',
	)
);
$hero = $hero_query->have_posts() ? $hero_query->posts[0] : null;
?>

<?php if ( $hero ) : ?>
	<section class="hero-lead" aria-label="<?php esc_attr_e( 'مقالهٔ سرخط', 'shola-jawid' ); ?>">
		<a href="<?php echo esc_url( get_permalink( $hero ) ); ?>" class="hero-media" aria-hidden="true" tabindex="-1">
			<?php echo shola_get_featured_image( $hero, 'shola_hero_wide', array( 'loading' => 'eager' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- shola_get_featured_image() escapes internally. ?>
		</a>
		<div class="wrap">
			<div class="hero-body">
				<?php
				$hero_term = shola_get_primary_topic( $hero );
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
				/*
				 * Byline (author/username) removed site-wide, 2026-09-02,
				 * per the client's explicit instruction (relayed by
				 * Farhad) — the date stays.
				 */
				?>
				<p class="card-byline"><time datetime="<?php echo esc_attr( shola_get_iso_datetime( $hero ) ); ?>"><?php echo esc_html( get_the_date( '', $hero ) ); ?></time></p>
			</div>
		</div>
	</section>

	<hr class="rule wrap">
<?php endif; ?>

<?php
/*
 * تازه‌ترین مقالات (renamed from مقالات, 2026-09-07) — latest 6 posts of
 * type `post`, every topic combined. This used to be a separate section
 * sitting below a standalone تازه‌ها "recent" grid (hero + 6 more
 * articles), with deliberate content overlap between the two,
 * client-confirmed 2026-09-02 (see docs/CHANGELOG.md). The client later
 * concluded تازه‌ها and مقالات were showing the same thing twice with no
 * real distinction, so تازه‌ها's grid was removed entirely (2026-09-07)
 * and this section — renamed to make its "recent articles" role
 * explicit — now sits directly under the hero instead, taking over that
 * position. No exclusion against the hero above (same
 * accepted-duplication reasoning as before, 2026-09-02).
 *
 * `report` exclusion added 2026-09-05 (Phase 4, Technical Scoping Plan):
 * this section specifically must NOT show reports — Farhad relayed the
 * client's instruction that موضوعات/مقالات and گزارش are two separate
 * feeds, reports only ever belong in their own homepage section and
 * archive.
 *
 * اطلاعیه spotlight tile added 2026-09-07 (Phase 11, client-requested —
 * see docs/CHANGELOG.md): the latest اطلاعیه now occupies this grid's
 * own visually-leftmost slot (template-parts/cards/announcement-
 * spotlight.php, spanning both rows on desktop via .card-spotlight,
 * assets/css/main.css). Article count drops from 6 to 4 whenever a
 * spotlight actually renders, so the grid stays a clean 2x2 of articles
 * beside it instead of an uneven leftover column — falls back to 6 (the
 * original count) on the rare chance there are zero announcements, so
 * the grid is never short a row for no visible reason.
 */
$announcement_query = new WP_Query(
	array(
		'post_type'      => 'announcement',
		'posts_per_page' => 1,
		'orderby'        => 'date',
		'order'          => 'DESC',
	)
);
$has_spotlight = $announcement_query->have_posts();

$articles_query = new WP_Query(
	array(
		'post_type'      => 'post',
		'posts_per_page' => $has_spotlight ? 4 : 6,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- small, single-term taxonomy, not a scale concern.
			array(
				'taxonomy' => 'report',
				'field'    => 'slug',
				'terms'    => 'reports',
				'operator' => 'NOT IN',
			),
		),
	)
);
?>

<?php if ( $articles_query->have_posts() ) : ?>
	<section class="sect-cream sect" aria-label="<?php esc_attr_e( 'تازه‌ترین مقالات', 'shola-jawid' ); ?>">
		<div class="wrap">
			<div class="section-head row-between">
				<div class="kicker-row">
					<p class="section-marker"></p>
					<h2 class="h-section"><?php esc_html_e( 'تازه‌ترین مقالات', 'shola-jawid' ); ?></h2>
				</div>
				<a class="link-more" href="<?php echo esc_url( home_url( '/topics/' ) ); ?>"><?php esc_html_e( 'همهٔ مقالات', 'shola-jawid' ); ?> <span class="arr">←</span></a>
			</div>
			<div class="grid-cards<?php echo $has_spotlight ? ' grid-cards--with-spotlight' : ''; ?>">
				<?php
				if ( $has_spotlight ) {
					get_template_part(
						'template-parts/cards/announcement-spotlight',
						null,
						array( 'post' => $announcement_query->posts[0] )
					);
				}
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

<?php
/*
 * گزارش (Reports) — a dedicated `report` taxonomy (Phase B, 2026-08-25,
 * originally a `post_tag`; converted 2026-09-05 after Farhad found the
 * client couldn't discover the free-text tag field as a way to mark a
 * report — see class-taxonomies.php's `report` registration for the
 * full reasoning). card.php, same anatomy as مقالات (full article
 * cards, dek/byline) — these are normal posts, not documents. Hidden
 * entirely when empty (no heading, no empty grid), same have_posts()
 * guard every other section on this page already uses — not a new
 * empty-state pattern.
 *
 * «همهٔ گزارش‌ها» link added 2026-09-04, once page-reports.php gave this
 * section somewhere to actually point to — every other homepage section
 * with a "see all" link already had one; this was the one exception,
 * for the same reason it had no archive at all until now.
 */
$reports_query = new WP_Query(
	array(
		'post_type'      => 'post',
		'posts_per_page' => 6,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- small, single-term taxonomy, not a scale concern.
			array(
				'taxonomy' => 'report',
				'field'    => 'slug',
				'terms'    => 'reports',
			),
		),
	)
);
?>
<?php if ( $reports_query->have_posts() ) : ?>
	<section class="wrap sect" aria-label="<?php echo esc_attr( shola_get_label( 'home_reports_heading' ) ); ?>">
		<div class="section-head row-between">
			<div class="kicker-row">
				<p class="section-marker"></p>
				<h2 class="h-section"><?php echo esc_html( shola_get_label( 'home_reports_heading' ) ); ?></h2>
			</div>
			<a class="link-more" href="<?php echo esc_url( home_url( '/reports/' ) ); ?>"><?php esc_html_e( 'همهٔ گزارش‌ها', 'shola-jawid' ); ?> <span class="arr">←</span></a>
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
/*
 * اسناد حزب (Party Documents) — added 2026-09-05 (Phase 4, Technical
 * Scoping Plan), replacing what this position used to hold: a
 * post_type=document query with no collection filter, labeled "اسناد
 * حزب" even though اسناد حزب had by then already become its own
 * independent post type (party_document) elsewhere on the site (see
 * shola-core\Post_Types' docblock on that CPT). That mismatch is what
 * this change fixes — this section now genuinely queries
 * party_document, matching its label. The general-library query that
 * used to live here moved to its own new «کتابخانه» section, after
 * انتشارات حزب below, rather than being deleted.
 */
$party_documents_query = new WP_Query(
	array(
		'post_type'      => 'party_document',
		'posts_per_page' => 4,
		'orderby'        => 'date',
		'order'          => 'DESC',
	)
);
?>
<?php if ( $party_documents_query->have_posts() ) : ?>
	<section class="sect-tint sect" aria-label="<?php echo esc_attr( shola_get_label( 'home_latest_documents_heading' ) ); ?>">
		<div class="wrap">
			<div class="section-head row-between">
				<div class="kicker-row">
					<p class="section-marker"></p>
					<h2 class="h-section"><?php echo esc_html( shola_get_label( 'home_latest_documents_heading' ) ); ?></h2>
				</div>
				<a class="link-more" href="<?php echo esc_url( home_url( '/party-documents/' ) ); ?>"><?php esc_html_e( 'همهٔ اسناد', 'shola-jawid' ); ?> <span class="arr">←</span></a>
			</div>
			<div class="issue-grid">
				<?php
				while ( $party_documents_query->have_posts() ) :
					$party_documents_query->the_post();
					get_template_part( 'template-parts/cards/issue-card', null, array( 'post' => get_post() ) );
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php
/*
 * شمارهٔ جاری — one card per publication (شعله جاوید, جهان برای فتح),
 * each showing that publication's own latest issue, side by side.
 * Previously a single card for the single latest issue across both
 * publications combined (whichever one happened to publish more
 * recently) — Farhad relayed the client's correction, 2026-09-02: these
 * are two distinct, both-still-publishing publications from the same
 * organization, and readers need to see both, not just whichever one
 * happened to be newest.
 */
/*
 * `parent => 0` added 2026-09-03: this ran unscoped from 2026-08-31
 * until the دوره (period) sub-terms existed (2026-09-02) with no
 * visible bug, since `publication` only had 2 (top-level) terms then —
 * once seed_publication_periods() added the 8 دوره child terms, this
 * silently started looping over *those* too, showing a card for every
 * دوره that had at least one issue instead of one card per publication.
 * Farhad caught it live: 4-5 cards instead of 2.
 */
$publication_terms = get_terms(
	array(
		'taxonomy'   => 'publication',
		'parent'     => 0,
		'hide_empty' => false,
	)
);
$publication_terms = ( $publication_terms && ! is_wp_error( $publication_terms ) ) ? $publication_terms : array();

/*
 * get_terms() defaults to alphabetical order, which happens to sort
 * "جهان برای فتح" ahead of "شعله جاوید" (ج before ش) — Farhad flagged
 * this as backwards, 2026-09-02: شعله جاوید is this organization's
 * main/flagship publication and needs to lead. Stable-sorts
 * 'shola-jawid' to the front rather than hardcoding a fixed two-item
 * order, so a future third publication term still appears (just after
 * these two, in whatever order get_terms() already gave it) instead of
 * silently disappearing from this section.
 */
usort(
	$publication_terms,
	function ( $a, $b ) {
		if ( 'shola-jawid' === $a->slug ) {
			return -1;
		}
		if ( 'shola-jawid' === $b->slug ) {
			return 1;
		}
		return 0;
	}
);

$current_issues = array();
foreach ( $publication_terms as $pub_term ) {
	$pub_issue_query = new WP_Query(
		array(
			'post_type'      => 'issue',
			'posts_per_page' => 1,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'tax_query'      => array(
				array(
					'taxonomy' => 'publication',
					'field'    => 'term_id',
					'terms'    => $pub_term->term_id,
				),
			),
		)
	);
	if ( $pub_issue_query->have_posts() ) {
		$current_issues[] = array(
			'term'  => $pub_term,
			'issue' => $pub_issue_query->posts[0],
		);
	}
}
?>
<?php if ( $current_issues ) : ?>
	<?php
	/*
	 * شمارهٔ جاری reordered ahead of انتشارات حزب, 2026-08-24 (Phase A,
	 * client-approved). Background changed from .sect-cream to plain
	 * (paper), 2026-08-24 (Phase C, گزارشات-section removal) — its
	 * cream banding depended on گزارشات's paper band sitting directly
	 * above it; with that paper band gone, plain keeps alternation
	 * intact against its neighbors.
	 *
	 * aria-label corrected from "شمارهٔ جاری و کتابخانه" to just
	 * "شمارهٔ جاری", Phase B (2026-08-24, see docs/CHANGELOG.md) — this
	 * section has only ever contained شمارهٔ جاری content; کتابخانه is
	 * (and always was) a fully separate section elsewhere in this file,
	 * confirmed before this reorder rather than assumed. The combined
	 * label was stale/inaccurate, not a sign the two needed splitting
	 * apart.
	 *
	 * Heading/aria-label retitled "شمارهٔ جاری" → "نشریات", 2026-09-05
	 * (Phase 4, Technical Scoping Plan) — text only, per Farhad: "همه‌چیز
	 * خوب است، همین‌طور که هست باقی بماند" for everything else about this
	 * section (query, layout, position relative to انتشارات حزب below).
	 */
	?>
	<section class="sect" aria-label="<?php esc_attr_e( 'نشریات', 'shola-jawid' ); ?>">
		<div class="wrap">
			<div class="section-head">
				<div class="kicker-row">
					<p class="section-marker"></p>
					<h2 class="h-section"><?php esc_html_e( 'نشریات', 'shola-jawid' ); ?></h2>
				</div>
			</div>

			<div class="current-issues">
				<?php
				foreach ( $current_issues as $current_issue_entry ) :
					$pub_term     = $current_issue_entry['term'];
					$issue        = $current_issue_entry['issue'];
					$issue_number = get_post_meta( $issue->ID, 'shcore_issue_number', true );
					$volume       = get_post_meta( $issue->ID, 'shcore_volume', true );
					$pdf_id       = (int) get_post_meta( $issue->ID, 'shcore_pdf_id', true );
					$pdf_size     = shola_get_pdf_size( $pdf_id );
					?>
					<div class="issue-hero issue-hero--embedded">

						<a href="<?php echo esc_url( get_permalink( $issue ) ); ?>" class="issue-cover reveal">
							<?php echo shola_get_featured_image( $issue, 'shola_issue_cover', array( 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- shola_get_featured_image() escapes internally. ?>
						</a>

						<div class="reveal">
							<h3 class="h-page"><a href="<?php echo esc_url( get_permalink( $issue ) ); ?>" class="link-quiet"><?php echo esc_html( $pub_term->name ); ?></a></h3>
							<p class="dek mt-sm"><?php echo esc_html( wp_trim_words( get_the_excerpt( $issue ), 30 ) ); ?></p>
							<dl class="issue-meta">
								<?php if ( $issue_number ) : ?>
									<dt><?php esc_html_e( 'شماره', 'shola-jawid' ); ?></dt>
									<dd><?php echo esc_html( $issue_number ); ?></dd>
								<?php endif; ?>
								<?php if ( $volume ) : ?>
									<dt><?php esc_html_e( 'دوره', 'shola-jawid' ); ?></dt>
									<dd><?php echo esc_html( $volume ); ?></dd>
								<?php endif; ?>
								<dt><?php esc_html_e( 'تاریخ نشر', 'shola-jawid' ); ?></dt>
								<dd><time datetime="<?php echo esc_attr( shola_get_iso_datetime( $issue ) ); ?>"><?php echo esc_html( get_the_date( '', $issue ) ); ?></time></dd>
								<?php if ( $pdf_id ) : ?>
									<dt><?php esc_html_e( 'فایل PDF', 'shola-jawid' ); ?></dt>
									<dd lang="en"><?php echo esc_html( $pdf_size ? $pdf_size : 'PDF' ); ?></dd>
								<?php endif; ?>
							</dl>
							<div class="row mt-sm">
								<a class="btn btn-sm btn-primary" href="<?php echo esc_url( get_permalink( $issue ) ); ?>"><?php esc_html_e( 'دریافت شماره', 'shola-jawid' ); ?></a>
								<a class="btn btn-sm btn-ghost" href="<?php echo esc_url( get_term_link( $pub_term ) ); ?>"><?php esc_html_e( 'آرشیو شماره‌ها', 'shola-jawid' ); ?></a>
							</div>
						</div>

					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php
/*
 * انتشارات حزب (Party Publications) — the party's own books/booklets.
 * Corrected 2026-09-02 (Farhad relaying a client correction): this
 * section carried the right Persian heading from the start, but was
 * actually querying `issue` (نشریه — periodical شعله جاوید/جهان برای
 * فتح numbers), which the client identified as a distinct, wrongly-
 * merged content type — see shola-core\Post_Types' docblock on the new
 * `party_publication` CPT this now queries instead. issue-card.php, not
 * card.php: structurally distinct anatomy (portrait cover, box-shadow,
 * no dek/byline) confirmed in docs/CHANGELOG.md Phase 1.2 — not a
 * variant of the article card, and already post-type-agnostic so it
 * needed no changes to work here. .issue-grid, the same shelf-density
 * wrapper class taxonomy-publication.php already uses (1 col mobile ->
 * 3 -> 4 -> 5 col desktop), not .grid-cards.
 *
 * Reordered to appear after شمارهٔ جاری (was before it), 2026-08-24
 * (Phase A, client-approved) — see docs/CHANGELOG.md. .sect-tint
 * unchanged; still distinct from شمارهٔ جاری's cream band directly
 * above and موضوعات's paper band directly below, so background-band
 * alternation still holds with no adjacent repeats.
 *
 * `posts_per_page` capped at 5, 2026-09-05 (Phase 5, Technical Scoping
 * Plan) — was 10, well past what a homepage shelf like this is meant to
 * show; Farhad asked for a hard, deliberate limit here (and on every
 * homepage shelf) instead of one that just happened to be under control
 * by coincidence. The full archive at /party-publications/ is unaffected
 * — this only limits the homepage teaser.
 */
$party_publications_query = new WP_Query(
	array(
		'post_type'      => 'party_publication',
		'posts_per_page' => 5,
		'orderby'        => 'date',
		'order'          => 'DESC',
	)
);
?>
<?php if ( $party_publications_query->have_posts() ) : ?>
	<section class="sect-tint sect" aria-label="<?php esc_attr_e( 'انتشارات حزب', 'shola-jawid' ); ?>">
		<div class="wrap">
			<div class="section-head row-between">
				<div class="kicker-row">
					<p class="section-marker"></p>
					<h2 class="h-section"><?php esc_html_e( 'انتشارات حزب', 'shola-jawid' ); ?></h2>
				</div>
				<a class="link-more" href="<?php echo esc_url( home_url( '/party-publications/' ) ); ?>"><?php esc_html_e( 'همهٔ آثار', 'shola-jawid' ); ?> <span class="arr">←</span></a>
			</div>
			<div class="issue-grid">
				<?php
				while ( $party_publications_query->have_posts() ) :
					$party_publications_query->the_post();
					get_template_part( 'template-parts/cards/issue-card', null, array( 'post' => get_post() ) );
				endwhile;
				wp_reset_postdata();
				?>
			</div>
		</div>
	</section>
<?php endif; ?>

<?php
/*
 * کتابخانه (Library) — added 2026-09-05 (Phase 4, Technical Scoping
 * Plan). The homepage previously had no Library section at all; this is
 * the general-library query (post_type=document, no collection filter)
 * that used to live right after مقالات under the "اسناد حزب" label —
 * moved here, relabeled to its accurate name, once اسناد حزب got its
 * own real section using its own real content type in that earlier
 * position. .sect-cream, same class that position's query previously
 * had, keeping background-band alternation intact against انتشارات
 * حزب's .sect-tint above and موضوعات's plain band below.
 *
 * `posts_per_page` set to 5, 2026-09-05 (Phase 5, Technical Scoping
 * Plan) — was 4, already under Farhad's stated 5-item limit for this
 * shelf, raised to exactly 5 rather than left as a coincidental number
 * under the cap, matching انتشارات حزب's identical treatment just above.
 */
$library_documents_query = new WP_Query(
	array(
		'post_type'      => 'document',
		'posts_per_page' => 5,
		'orderby'        => 'date',
		'order'          => 'DESC',
	)
);
?>
<?php if ( $library_documents_query->have_posts() ) : ?>
	<section class="sect-cream sect" aria-label="<?php echo esc_attr( shola_get_label( 'home_library_heading' ) ); ?>">
		<div class="wrap">
			<div class="section-head row-between">
				<div class="kicker-row">
					<p class="section-marker"></p>
					<h2 class="h-section"><?php echo esc_html( shola_get_label( 'home_library_heading' ) ); ?></h2>
				</div>
				<a class="link-more" href="<?php echo esc_url( home_url( '/library/' ) ); ?>"><?php esc_html_e( 'همهٔ مجموعه‌ها', 'shola-jawid' ); ?> <span class="arr">←</span></a>
			</div>
			<div class="issue-grid">
				<?php
				while ( $library_documents_query->have_posts() ) :
					$library_documents_query->the_post();
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
/*
 * اطلاعیه‌ها had no full section of its own here from Phase B (2026-08-24,
 * client-approved, see docs/CHANGELOG.md) until Phase 11 (2026-09-07),
 * when it returned in a different form — a spotlight tile inside تازه‌ترین
 * مقالات's own grid, not a standalone section — see this file's top
 * docblock and template-parts/cards/announcement-spotlight.php. Left
 * this historical note in place rather than deleting it outright, since
 * it still correctly describes why there's no *dedicated* اطلاعیه‌ها
 * section at this position in the page.
 *
 * A second, unused `$documents_query` used to sit here too (dead code —
 * nothing below it ever rendered from it, confirmed by reading the rest
 * of the file). Removed 2026-09-05 (Phase 4, Technical Scoping Plan)
 * while reworking this page's document queries elsewhere, rather than
 * left in place as leftover clutter.
 */
?>
<?php get_footer(); ?>
