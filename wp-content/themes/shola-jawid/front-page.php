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
 * Section order, last changed 2026-09-08 (Phase 16, client decision
 * relayed by Farhad): headline article, تازه‌ترین مقالات, گزارش, نشریات
 * (شمارهٔ جاری), انتشارات حزب, کتابخانه, اسناد حزب, موضوعات. اسناد حزب
 * moved down from right after گزارش (its position since 2026-09-07) to
 * directly above موضوعات at the very bottom — the client wanted it
 * lower in the page's hierarchy, below the other homepage shelves
 * (نشریات/انتشارات حزب/کتابخانه) rather than above them, while staying
 * above the topics list. See docs/CHANGELOG.md for the full record,
 * including the background-band fix this move required (نشریات's
 * background changed plain → cream, since گزارش — also plain — landed
 * directly above it once اسناد حزب moved out from between them).
 *
 * Before that, order changed 2026-09-07 (client decision, relayed by
 * Farhad): تازه‌ها (the standalone "recent" grid) was removed entirely,
 * not just relabeled — the client concluded it and مقالات were
 * duplicating the same content, so the homepage now shows one merged
 * "latest articles" section directly under the hero instead of two (see
 * docs/CHANGELOG.md). A newsletter signup band was in an earlier
 * version of this page and is deliberately not present — removed
 * 2026-08-08, per Farhad (see docs/CHANGELOG.md).
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
 *
 * The headline article itself is never affected by which hero_section
 * variant is active (added 2026-09-10, Phase 17 continued — see
 * shola-core's hero_section CPT docblock): it always stays "latest
 * published post," computed live here exactly as before. Only the
 * *layout wrapped around it* (single vs. lead_rail) and an optional
 * rail alongside it depend on the active hero_section entry.
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

/*
 * Active hero_section lookup (2026-09-10). No active entry (e.g. the
 * CPT exists but every entry was somehow left inactive) degrades to
 * 'single' — today's only layout — rather than breaking the homepage;
 * see class-post-types.php's hero_section docblock for why this CPT
 * never controls *which* article leads, only the layout around it.
 */
$active_hero_query = new WP_Query(
	array(
		'post_type'      => 'hero_section',
		'posts_per_page' => 1,
		'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- single boolean flag, tiny post type, admin-managed.
			array(
				'key'   => 'shcore_hero_active',
				'value' => '1',
			),
		),
	)
);
$active_hero        = $active_hero_query->have_posts() ? $active_hero_query->posts[0] : null;
$hero_layout        = $active_hero ? get_post_meta( $active_hero->ID, 'shcore_hero_layout', true ) : 'single';
$hero_layout        = $hero_layout ? $hero_layout : 'single';
$hero_rail_issue    = null;
$hero_rail_pub_term = null;

if ( $hero && in_array( $hero_layout, array( 'lead_rail', 'overlay', 'rail_full' ), true ) ) {
	$rail_pub_slug = get_post_meta( $active_hero->ID, 'shcore_hero_rail_publication', true );
	$rail_pub_slug = $rail_pub_slug ? $rail_pub_slug : 'shola-jawid';
	$hero_rail_pub_term = get_term_by( 'slug', $rail_pub_slug, 'publication' );

	if ( $hero_rail_pub_term && ! is_wp_error( $hero_rail_pub_term ) ) {
		$hero_rail_query = new WP_Query(
			array(
				'post_type'      => 'issue',
				'posts_per_page' => 1,
				'orderby'        => 'date',
				'order'          => 'DESC',
				'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- small, fixed-vocabulary taxonomy, one row.
					array(
						'taxonomy' => 'publication',
						'field'    => 'term_id',
						'terms'    => $hero_rail_pub_term->term_id,
					),
				),
			)
		);
		$hero_rail_issue = $hero_rail_query->have_posts() ? $hero_rail_query->posts[0] : null;
	}

	// No issue found for the configured publication (none published
	// yet) — fall back to the single layout rather than showing a
	// hero with a half-empty rail.
	if ( ! $hero_rail_issue ) {
		$hero_layout = 'single';
	}
}
?>

<?php if ( $hero && 'single' === $hero_layout ) : ?>
	<section class="hero-lead" aria-label="<?php esc_attr_e( 'مقالهٔ سرخط', 'shola-jawid' ); ?>">
		<a href="<?php echo esc_url( get_permalink( $hero ) ); ?>" class="hero-media" aria-hidden="true" tabindex="-1">
			<?php echo shola_get_featured_image( $hero, 'shola_hero_wide', array( 'loading' => 'eager' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- shola_get_featured_image() escapes internally. ?>
		</a>
		<div class="wrap">
			<div class="hero-body">
				<?php shola_render_hero_body( $hero ); ?>
			</div>
		</div>
	</section>

	<hr class="rule wrap">
<?php elseif ( $hero && 'lead_rail' === $hero_layout ) : ?>
	<?php
	/*
	 * لید + ستون نشریه (2026-09-10, Phase 17 continued): the headline
	 * hero stays visually identical to the `single` layout above (same
	 * .hero-media/.wrap/.hero-body markup, just nested one level deeper
	 * inside .hero-main so the photo's dark scrim/gradient — moved onto
	 * .hero-main in CSS — doesn't stretch across the rail column too).
	 * The rail beside it always shows whichever publication the active
	 * hero_section entry names (شعله جاوید by default), never a
	 * manually-picked issue — see this file's hero_rail_* query above.
	 *
	 * Deliberately no rtl-specific left/right logic here: .hero-main
	 * simply comes first in the DOM and .hero-rail second, so CSS's
	 * normal (logical, not physical) flex ordering already puts the
	 * rail on the visual left in this RTL layout — matching the
	 * client's own description of the request — with no hardcoded side.
	 */
	?>
	<section class="hero-lead hero-lead--with-rail" aria-label="<?php esc_attr_e( 'مقالهٔ سرخط', 'shola-jawid' ); ?>">
		<div class="hero-main">
			<a href="<?php echo esc_url( get_permalink( $hero ) ); ?>" class="hero-media" aria-hidden="true" tabindex="-1">
				<?php echo shola_get_featured_image( $hero, 'shola_hero_wide', array( 'loading' => 'eager' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- shola_get_featured_image() escapes internally. ?>
			</a>
			<div class="wrap">
				<div class="hero-body">
					<?php shola_render_hero_body( $hero ); ?>
				</div>
			</div>
		</div>
		<aside class="hero-rail" aria-label="<?php esc_attr_e( 'شمارهٔ جاری', 'shola-jawid' ); ?>">
			<?php shola_render_hero_publication_card( $hero_rail_issue, $hero_rail_pub_term ); ?>
		</aside>
	</section>

	<hr class="rule wrap">
<?php elseif ( $hero && 'overlay' === $hero_layout ) : ?>
	<?php
	/*
	 * کارت شناور روی تصویر (2026-09-10, Phase 17 continued): a third
	 * layout per a client idea (relayed by Farhad, with a sketch) — same
	 * full-bleed photo/headline as `single` (identical .hero-media/.wrap/
	 * .hero-body, so it shares that layout's CSS untouched), with
	 * .hero-pub-card added as a third child of .hero-lead showing the
	 * same publication-card content as lead_rail's rail.
	 *
	 * .hero-pub-card is deliberately NOT nested inside .wrap: .wrap is
	 * position:absolute, so a static sibling of it inside .hero-lead
	 * (position:relative) naturally renders directly below the photo in
	 * normal document flow at narrow widths (mobile) — no separate
	 * mobile-only markup branch needed. Above the mobile breakpoint,
	 * CSS switches it to position:absolute to float over the photo's
	 * lower corner instead. Same RTL approach as lead_rail: no hardcoded
	 * left/right, positioned via the logical inset-inline-end property.
	 */
	?>
	<section class="hero-lead hero-lead--overlay" aria-label="<?php esc_attr_e( 'مقالهٔ سرخط', 'shola-jawid' ); ?>">
		<a href="<?php echo esc_url( get_permalink( $hero ) ); ?>" class="hero-media" aria-hidden="true" tabindex="-1">
			<?php echo shola_get_featured_image( $hero, 'shola_hero_wide', array( 'loading' => 'eager' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- shola_get_featured_image() escapes internally. ?>
		</a>
		<div class="wrap">
			<div class="hero-body">
				<?php shola_render_hero_body( $hero ); ?>
			</div>
		</div>
		<div class="hero-pub-card" aria-label="<?php esc_attr_e( 'شمارهٔ جاری', 'shola-jawid' ); ?>">
			<?php shola_render_hero_publication_card( $hero_rail_issue, $hero_rail_pub_term ); ?>
		</div>
	</section>

	<hr class="rule wrap">
<?php elseif ( $hero && 'rail_full' === $hero_layout ) : ?>
	<?php
	/*
	 * مقالهٔ سرخط + ستون نشریهٔ تمام‌عرض (2026-09-10, Phase 17 continued):
	 * a fourth layout per a client sketch — visually the same idea as
	 * `lead_rail` (a solid-color rail with the publication card beside
	 * the full-size headline photo), but full-bleed edge-to-edge on the
	 * rail's outer side instead of staying inside the centered 1200px
	 * content column `lead_rail` deliberately uses. Reuses .hero-main
	 * (full generic scrim/text-overlay treatment, no modifier needed)
	 * and .hero-rail (crimson bg, white text, inverted button — all
	 * already scoped to the plain class name, not `.hero-lead--with-
	 * rail` specifically) for the shared inner styling, but a distinct
	 * `.hero-lead--rail-full` outer class so none of `lead_rail`'s own
	 * compact-height clamp or narrowed cover-size rules apply here —
	 * this layout keeps the full viewport-based height `single`/
	 * `overlay` already use, matching the client's sketch (a tall
	 * panel, not a short banner), with its own, separately-tuned rail
	 * width/cover sizing in main.css §10.4.
	 *
	 * DOM order unchanged from `lead_rail`: .hero-main first, .hero-rail
	 * second — RTL's normal reading-order flow puts the rail on the
	 * visual left with no hardcoded side, same as every other multi-
	 * column hero layout this project has built.
	 */
	?>
	<section class="hero-lead hero-lead--rail-full" aria-label="<?php esc_attr_e( 'مقالهٔ سرخط', 'shola-jawid' ); ?>">
		<div class="hero-main">
			<a href="<?php echo esc_url( get_permalink( $hero ) ); ?>" class="hero-media" aria-hidden="true" tabindex="-1">
				<?php echo shola_get_featured_image( $hero, 'shola_hero_wide', array( 'loading' => 'eager' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- shola_get_featured_image() escapes internally. ?>
			</a>
			<div class="wrap">
				<div class="hero-body">
					<?php shola_render_hero_body( $hero ); ?>
				</div>
			</div>
		</div>
		<aside class="hero-rail" aria-label="<?php esc_attr_e( 'شمارهٔ جاری', 'shola-jawid' ); ?>">
			<?php shola_render_hero_publication_card( $hero_rail_issue, $hero_rail_pub_term ); ?>
		</aside>
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
 * see docs/CHANGELOG.md): the latest اطلاعیه‌ها occupy this grid's own
 * visually-leftmost slot (template-parts/cards/announcement-
 * spotlight.php). Article count drops from 6 to 5 whenever a spotlight
 * actually renders, so the grid stays a clean, gap-free 3x2 — falls
 * back to 6 (the original count, no tile) on the rare chance there are
 * zero announcements, so the grid is never short a row for no visible
 * reason.
 *
 * Tile height corrected same day (still Phase 11): originally spanned
 * both grid rows showing only the single latest اطلاعیه, which Farhad
 * flagged from a live screenshot as leaving the tile mostly empty —
 * far more height than one short announcement's text needs, making the
 * whole homepage read as unusually long. Changed to a single-row-height
 * tile (matching one article card, not two) showing the 3 latest
 * اطلاعیه‌ها instead of 1 — the newest rendered prominently, the other 2
 * smaller, so the tile is naturally full rather than empty. Article
 * count adjusted 4 → 5 to match: the tile now occupies 1 of the grid's
 * 6 cells instead of 2, so 5 articles (not 4) fill the rest with no
 * gap.
 */
$announcement_query = new WP_Query(
	array(
		'post_type'      => 'announcement',
		'posts_per_page' => 3,
		'orderby'        => 'date',
		'order'          => 'DESC',
	)
);
$has_spotlight = $announcement_query->have_posts();

$articles_query = new WP_Query(
	array(
		'post_type'      => 'post',
		'posts_per_page' => $has_spotlight ? 5 : 6,
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
			<div class="section-head row-between<?php echo $has_spotlight ? ' section-head--with-spotlight' : ''; ?>">
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
						array( 'posts' => $announcement_query->posts )
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
 *
 * $has_reports captured 2026-09-08 (Phase 16, اسناد حزب reorder): this
 * section's own background (plain/paper) is now نشریات's immediate
 * neighbor whenever گزارش actually has content — but گزارش hides
 * entirely when empty, in which case تازه‌ترین مقالات's cream band
 * becomes نشریات's real neighbor instead. Captured once, before the
 * loop below consumes `have_posts()`, so نشریات's own section tag
 * (further down this file) can pick cream vs. plain correctly for
 * whichever case is actually true, instead of a background hardcoded
 * for only one of the two.
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
$has_reports = $reports_query->have_posts();
?>
<?php if ( $has_reports ) : ?>
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
	 * client-approved).
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
	 *
	 * Background made dynamic, 2026-09-08 (Phase 16, اسناد حزب reorder —
	 * see this file's top docblock and docs/CHANGELOG.md): this section's
	 * background has always been chosen based on whichever section sits
	 * directly above it — plain worked from 2026-08-24 because گزارش's
	 * own paper band always sat there. Now that اسناد حزب moved further
	 * down the page, this section's real neighbor above depends on
	 * whether گزارش actually has content: when it does, گزارش (plain)
	 * sits directly above and this section needs to differ (cream); when
	 * گزارش is empty and hides (its own have_posts() guard), تازه‌ترین
	 * مقالات's cream band becomes the real neighbor instead, and cream
	 * here would clash with it — plain is what's needed in that case.
	 * `$has_reports` (captured above, before گزارش's own loop consumes
	 * it) picks correctly for whichever is actually true, rather than a
	 * background hardcoded for only one of the two — found live: with
	 * zero reports currently published, a hardcoded-cream version
	 * clashed with تازه‌ترین مقالات's cream directly above it.
	 */
	?>
	<section class="<?php echo $has_reports ? 'sect-cream' : ''; ?> sect" aria-label="<?php esc_attr_e( 'نشریات', 'shola-jawid' ); ?>">
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
 * حزب's .sect-tint above.
 *
 * Directly below this section as of 2026-09-08 (Phase 16) is اسناد حزب
 * again — moved here from its earlier position further up the page
 * (see this file's top docblock) — not موضوعات anymore; still no clash,
 * since اسناد حزب's own .sect-tint differs from this section's cream.
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

<?php
/*
 * اسناد حزب (Party Documents) — added 2026-09-05 (Phase 4, Technical
 * Scoping Plan), originally positioned right after گزارش. Moved to here
 * — directly above موضوعات, at the very bottom of the page's content
 * sections — 2026-09-08 (Phase 16), per Farhad relaying the client's
 * decision to lower this section's prominence relative to the other
 * homepage shelves (نشریات, انتشارات حزب, کتابخانه), which all moved up
 * a position as a result. Query/label/link unchanged — genuinely queries
 * `party_document`, matching its label (see the original 2026-09-05 note
 * this replaces for that history).
 *
 * .sect-tint unchanged: still distinct from کتابخانه's .sect-cream
 * directly above and موضوعات's plain band directly below, so
 * background-band alternation holds in this new position exactly as it
 * did in the old one.
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
