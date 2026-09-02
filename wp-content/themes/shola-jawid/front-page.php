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
 * مقالات (Articles) — latest 6 posts of type `post`, every topic
 * combined, no exclusion against تازه‌ها above. Client-confirmed
 * 2026-09-02 (see docs/CHANGELOG.md): تازه‌ها is the "everything new"
 * feed (articles + reports + documents + issues), مقالات is the
 * "articles only" feed — duplication between the two is expected and
 * fine, so the newest article always shows in both. Previously this
 * excluded anything already in $latest_query (post__not_in) to avoid
 * duplicates; removed per that confirmation.
 */
$articles_query = new WP_Query(
	array(
		'post_type'      => 'post',
		'posts_per_page' => 6,
		'orderby'        => 'date',
		'order'          => 'DESC',
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
 * گزارش (Reports) — a native WP `post_tag` term (not `category`: a
 * `category` term was tried first, but `post` had `category`
 * deliberately disconnected in an earlier phase to avoid a redundant
 * "Uncategorized" editor panel — see
 * SholaCore\Class_Taxonomies::create_default_terms()'s comment for the
 * full finding. `post_tag` has none of that conflict: still fully
 * registered for `post`, normal Add-New-Tag editor UI, correct term
 * counts. Also deliberately not `topic` — confirmed separate from the
 * 9-term topic vocabulary: no موضوعات nav/archive presence, no
 * shola_topic_color_class() entry). Phase B (2026-08-25,
 * docs/CHANGELOG.md). card.php, same anatomy as مقالات (full article
 * cards, dek/byline) — these are normal posts, not documents. Hidden
 * entirely when empty (no heading, no empty grid), same have_posts()
 * guard every other section on this page already uses — not a new
 * empty-state pattern.
 */
$reports_query = new WP_Query(
	array(
		'post_type'      => 'post',
		'posts_per_page' => 6,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'tag'            => 'reports',
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
	<?php
	/*
	 * Restyled from a document-row.php list onto the generalized
	 * issue-card.php partial, Phase B (2026-08-24, see
	 * docs/CHANGELOG.md) — query unchanged (still post_type=document,
	 * no collection restriction), only the rendering. .issue-grid, the
	 * same wrapper class انتشارات حزب uses.
	 *
	 * Relabeled "تازه‌ترین اسناد" → "اسناد حزب" (client-approved,
	 * home_latest_documents_heading — split from the shared
	 * latest_documents_heading key so page-library.php's own heading,
	 * which covers the whole library not just party documents, isn't
	 * silently renamed too; see class-label-settings.php).
	 *
	 * Reordered to sit right after مقالات (was after موضوعات, near the
	 * bottom of the page) and given .sect-cream (was plain/no class).
	 * NOT YET FINAL: this currently sits directly adjacent to مقالات
	 * (also .sect-cream) — a background-band collision — because گزارش
	 * (client-specified to sit between them) hasn't been inserted yet;
	 * its query/term is an open question flagged for Farhad, not
	 * guessed (see TODO comment above). Resolves once گزارش (planned
	 * as plain/paper) is inserted between them. Do not commit with this
	 * collision still present.
	 */
	?>
	<section class="sect-cream sect" aria-label="<?php esc_attr_e( 'اسناد حزب', 'shola-jawid' ); ?>">
		<div class="wrap">
			<div class="section-head row-between">
				<div class="kicker-row">
					<p class="section-marker"></p>
					<h2 class="h-section"><?php echo esc_html( shola_get_label( 'home_latest_documents_heading' ) ); ?></h2>
				</div>
				<a class="link-more" href="<?php echo esc_url( home_url( '/library/' ) ); ?>"><?php esc_html_e( 'همهٔ مجموعه‌ها', 'shola-jawid' ); ?> <span class="arr">←</span></a>
			</div>
			<div class="issue-grid">
				<?php
				while ( $documents_query->have_posts() ) :
					$documents_query->the_post();
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
$publication_terms = get_terms(
	array(
		'taxonomy'   => 'publication',
		'hide_empty' => false,
	)
);
$publication_terms = ( $publication_terms && ! is_wp_error( $publication_terms ) ) ? $publication_terms : array();

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
	 */
	?>
	<section class="sect" aria-label="<?php esc_attr_e( 'شمارهٔ جاری', 'shola-jawid' ); ?>">
		<div class="wrap">
			<div class="section-head">
				<div class="kicker-row">
					<p class="section-marker"></p>
					<h2 class="h-section"><?php esc_html_e( 'شمارهٔ جاری', 'shola-jawid' ); ?></h2>
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
					$pdf_size     = '';
					if ( $pdf_id ) {
						$pdf_file = get_attached_file( $pdf_id );
						$pdf_size = $pdf_file && file_exists( $pdf_file ) ? size_format( filesize( $pdf_file ) ) : '';
					}
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
								<dt><?php esc_html_e( 'تاریخ نشر', 'shola-jawid' ); ?></dt>
								<dd><time datetime="<?php echo esc_attr( shola_get_iso_datetime( $issue ) ); ?>"><?php echo esc_html( get_the_date( '', $issue ) ); ?></time></dd>
								<?php if ( $volume ) : ?>
									<dt><?php esc_html_e( 'دوره', 'shola-jawid' ); ?></dt>
									<dd><?php echo esc_html( $volume ); ?></dd>
								<?php endif; ?>
								<?php if ( $pdf_id ) : ?>
									<dt><?php esc_html_e( 'فایل PDF', 'shola-jawid' ); ?></dt>
									<dd lang="en"><?php echo esc_html( $pdf_size ? $pdf_size : 'PDF' ); ?></dd>
								<?php endif; ?>
							</dl>
							<div class="row mt-sm">
								<a class="btn btn-primary" href="<?php echo esc_url( get_permalink( $issue ) ); ?>"><?php esc_html_e( 'دریافت شماره', 'shola-jawid' ); ?></a>
								<a class="btn btn-ghost" href="<?php echo esc_url( get_term_link( $pub_term ) ); ?>"><?php esc_html_e( 'آرشیو شماره‌ها', 'shola-jawid' ); ?></a>
							</div>
						</div>

					</div>
				<?php endforeach; ?>
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
<?php
/*
 * اطلاعیه‌ها section removed from the homepage, Phase B (2026-08-24,
 * client-approved, see docs/CHANGELOG.md) — homepage-section removal
 * only. The announcement CPT, its archive.php template, and any nav
 * link to /announcements/ are untouched.
 */
?>
<?php get_footer(); ?>
