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
 * Section order, locked 2026-09-05 (Phase 4, Technical Scoping Plan —
 * see docs/CHANGELOG.md for the full history of how this order and each
 * section's content settled): headline article, تازه‌ها, مقالات, اسناد
 * حزب, گزارش, نشریات (شمارهٔ جاری), انتشارات حزب, کتابخانه, موضوعات.
 * اطلاعیه‌ها and a newsletter signup band were both in earlier versions
 * of this page and are deliberately not present — removed 2026-08-24 and
 * 2026-08-08 respectively, per Farhad (see docs/CHANGELOG.md); their own
 * CPT/archive/nav presence elsewhere on the site is untouched.
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

<?php if ( $latest_posts ) : ?>
	<section class="wrap sect" aria-label="<?php echo esc_attr( shola_get_label( 'home_articles_section_aria' ) ); ?>">
		<div class="section-head row-between">
			<div class="kicker-row">
				<p class="section-marker"></p>
				<h2 class="h-section"><?php echo esc_html( shola_get_label( 'home_latest_heading' ) ); ?></h2>
			</div>
			<?php
			/*
			 * "همهٔ موضوعات ←" (link to /topics/) removed here 2026-09-05
			 * per Farhad: it mislabeled this section (تازه‌ها is the
			 * mixed "everything new" feed, not topic-scoped) and duplicated
			 * the موضوعات section further down this same page, which
			 * already lists every topic directly — no "view all" needed
			 * for a link that just repeats content already on the page.
			 * The home_topics_link_more label (inc/template-tags.php)
			 * is now unused here but left defined in case a real "view
			 * all" destination is added for this section later.
			 */
			?>
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
 * مقالات (Articles) — latest 6 posts of type `post`, every topic
 * combined, no exclusion against تازه‌ها above. Client-confirmed
 * 2026-09-02 (see docs/CHANGELOG.md): تازه‌ها is the "everything new"
 * feed (articles + reports + documents + issues), مقالات is the
 * "articles only" feed — duplication between the two is expected and
 * fine, so the newest article always shows in both. Previously this
 * excluded anything already in $latest_query (post__not_in) to avoid
 * duplicates; removed per that confirmation.
 *
 * `report` exclusion added 2026-09-05 (Phase 4, Technical Scoping Plan):
 * unlike its relationship with تازه‌ها above, this section specifically
 * must NOT show reports — Farhad relayed the client's instruction that
 * موضوعات/مقالات and گزارش are two separate feeds, reports only ever
 * belong in their own homepage section and archive.
 */
$articles_query = new WP_Query(
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
				'operator' => 'NOT IN',
			),
		),
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
 * اطلاعیه‌ها section removed from the homepage, Phase B (2026-08-24,
 * client-approved, see docs/CHANGELOG.md) — homepage-section removal
 * only. The announcement CPT, its archive.php template, and any nav
 * link to /announcements/ are untouched.
 *
 * A second, unused `$documents_query` used to sit here too (dead code —
 * nothing below it ever rendered from it, confirmed by reading the rest
 * of the file). Removed 2026-09-05 (Phase 4, Technical Scoping Plan)
 * while reworking this page's document queries elsewhere, rather than
 * left in place as leftover clutter.
 */
?>
<?php get_footer(); ?>
