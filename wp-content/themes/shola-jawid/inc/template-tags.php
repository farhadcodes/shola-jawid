<?php
// inc/template-tags.php — presentation-only helper functions used by templates.

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Convert ASCII digits (0-9) to Persian digits (۰-۹).
 *
 * WordPress's number_format_i18n() does NOT do this on this install —
 * confirmed empirically (site locale fa_AF): it only handles thousands
 * separators/decimal points and still outputs Latin digits, incorrectly
 * inserting a thousands separator for a bare year (e.g. "2,026"). v6's
 * own source confirms the actual convention: counts and years use
 * Persian digits everywhere, including inside .meta-mono/lang="en"
 * elements (main.css §… e.g. body-publications.html: `۳۲ ISSUES ·
 * ۲۰۱۸–۲۰۲۶`) — only English month abbreviations and technical units
 * (file sizes in MB/KB, via size_format()) stay Latin. Use this — never
 * number_format_i18n() alone — anywhere a count, year, or other
 * content-facing number is displayed.
 *
 * @param int|string $number Value to convert.
 * @return string
 */
function shola_to_persian_digits( $number ) {
	static $map = array(
		'0' => '۰',
		'1' => '۱',
		'2' => '۲',
		'3' => '۳',
		'4' => '۴',
		'5' => '۵',
		'6' => '۶',
		'7' => '۷',
		'8' => '۸',
		'9' => '۹',
	);
	return strtr( (string) $number, $map );
}

/**
 * Locale-independent uppercase English month abbreviation ("MAR", "DEC")
 * for the `.issue-card-date`/`lang="en"` mono contexts v6 uses (e.g.
 * "MAR ۲۰۲۶"). Uses raw PHP `gmdate()` on the post's timestamp rather
 * than any WordPress date-formatting function (`get_the_date()`,
 * `mysql2date()`, `date_i18n()`) — hardened Phase 4.2 (2026-08-06) so it
 * stays immune to the ParsiDate plugin's global Gregorian→Jalali hook,
 * wherever exactly that hook attaches. get_the_date('M') was previously
 * used here and turned out to be locale-aware (translated to Persian
 * month names on this fa_AF site) even before ParsiDate; `mysql2date()`
 * with `$translate = false` fixed that, but wasn't guaranteed immune to a
 * plugin hooking at a lower level than get_the_date() — gmdate() on a raw
 * timestamp is not interceptable by any WordPress-level date filter.
 *
 * @param int|WP_Post|null $post Post ID/object. Defaults to the current post.
 * @return string
 */
function shola_get_english_month_abbr( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return '';
	}
	return strtoupper( gmdate( 'M', strtotime( $post->post_date ) ) );
}

/**
 * Locale-independent Gregorian year, for the same mono-label convention
 * as shola_get_english_month_abbr() (issue counts/year-ranges, e.g. "۳۲
 * ISSUES · ۲۰۱۸–۲۰۲۶") — v6's own source uses Gregorian years with
 * Persian digits here, not Jalali years. Added alongside the ParsiDate
 * install (Phase 4.2, 2026-08-06): `get_the_date('Y', ...)` would be
 * silently converted to a Jalali year by ParsiDate's global hook once
 * active, which would both show the wrong year and break the "range
 * matches v6's literal reference" requirement. Pass through
 * shola_to_persian_digits() for display, same as everywhere else.
 *
 * @param int|WP_Post|null $post Post ID/object. Defaults to the current post.
 * @return int
 */
function shola_get_gregorian_year( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return 0;
	}
	return (int) gmdate( 'Y', strtotime( $post->post_date ) );
}

/**
 * Machine-readable ISO 8601 timestamp for `<time datetime="...">`
 * attributes. Must never be calendar/locale-converted regardless of what
 * human-readable text is shown next to it — HTML datetime attributes are
 * required to stay machine-parseable (Gregorian, ISO 8601) for
 * accessibility/microformat correctness. Uses get_post_datetime() (WP
 * core, 5.3+), which returns a real DateTimeImmutable from the post's raw
 * date, independent of any date_i18n()/get_the_date() filter — added
 * alongside the ParsiDate install (Phase 4.2, 2026-08-06) specifically so
 * these attributes can't be affected by it.
 *
 * @param int|WP_Post|null $post Post ID/object. Defaults to the current post.
 * @return string
 */
function shola_get_iso_datetime( $post = null ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return '';
	}
	$datetime = get_post_datetime( $post );
	return $datetime ? $datetime->format( DATE_W3C ) : '';
}

/**
 * Rewrites Jalali month names from the Iranian variant (فروردین,
 * اردیبهشت, ...) that the Persian Calendar plugin hardcodes, to the
 * Afghan Dari variant (حمل, ثور, ...) this site actually needs — found by
 * Farhad (2026-08-08), site-wide, matching the project's `fa_AF` locale
 * identity (CLAUDE.md, and the same reasoning that ruled out ParsiDate
 * for this project back in Phase 5.5).
 *
 * Persian Calendar has no built-in setting/filter for this (confirmed by
 * reading its source: the 12 names live in a hardcoded, non-filterable
 * `private $months_fa` property, and the plugin's only locale check
 * anywhere is `is_rtl()`, never `get_locale()`). Its month names only
 * ever reach a page as a substring of what it returns from the six
 * WordPress-core filters it hooks (`date_i18n`, `wp_date`,
 * `get_comment_date`, `get_comment_time`, `get_the_modified_date`,
 * `get_the_modified_time` — all at its own priority 10), so those are
 * the only stable interception points available without forking the
 * plugin. Registered at priority 20 (after the plugin's conversion) on
 * the same six hooks, doing a single-pass `strtr()` swap on the
 * resulting string.
 *
 * Deliberately anchored to the plugin's *public* contract (that it
 * filters these six hooks and outputs literal Persian month-name
 * strings) rather than any internal method/property — that public
 * contract is exactly what an update can't change without breaking the
 * plugin for its whole non-Afghan user base too, which is what makes
 * this survive plugin updates instead of reverting with them.
 *
 * @param string $formatted_date The date string as already formatted
 *                                (and possibly Jalali-converted) by
 *                                WordPress core / Persian Calendar.
 * @return string
 */
function shola_convert_jalali_months_to_dari( $formatted_date ) {
	static $map = array(
		'فروردین'  => 'حمل',
		'اردیبهشت' => 'ثور',
		'خرداد'    => 'جوزا',
		'تیر'      => 'سرطان',
		'مرداد'    => 'اسد',
		'شهریور'   => 'سنبله',
		'مهر'      => 'میزان',
		'آبان'     => 'عقرب',
		'آذر'      => 'قوس',
		'دی'       => 'جدی',
		'بهمن'     => 'دلو',
		'اسفند'    => 'حوت',
	);
	return strtr( $formatted_date, $map );
}
foreach ( array( 'date_i18n', 'wp_date', 'get_comment_date', 'get_comment_time', 'get_the_modified_date', 'get_the_modified_time' ) as $shola_jalali_hook ) {
	add_filter( $shola_jalali_hook, 'shola_convert_jalali_months_to_dari', 20 );
}
unset( $shola_jalali_hook );

/**
 * Site-wide featured-image fallback (Phase 4.2, logged in
 * docs/CHANGELOG.md and CLAUDE.md 2026-08-06): drop-in replacement for
 * get_the_post_thumbnail() — same signature, same return type (an <img>
 * HTML string) — that falls back to assets/images/fallback.png when a
 * post has no featured image, instead of rendering an empty container.
 * Use this everywhere a featured image is pulled, in every template built
 * from here on (card grids, hero, single-post headers, archive listings,
 * etc.) — never call get_the_post_thumbnail() directly in a new template.
 *
 * @param int|WP_Post|null $post Post ID/object. Defaults to the current post.
 * @param string|int[]     $size Registered image size or [width, height].
 * @param string|array     $attr Extra <img> attributes, same as get_the_post_thumbnail().
 * @return string HTML img element, or empty string if $post can't be resolved.
 */
function shola_get_featured_image( $post = null, $size = 'post-thumbnail', $attr = array() ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return '';
	}

	if ( has_post_thumbnail( $post ) ) {
		return get_the_post_thumbnail( $post, $size, $attr );
	}

	$attr = wp_parse_args(
		is_string( $attr ) ? wp_parse_args( $attr ) : $attr,
		array(
			'loading' => 'lazy',
			'alt'     => get_the_title( $post ),
			'class'   => 'shola-fallback-image',
		)
	);

	$attr_html = '';
	foreach ( $attr as $key => $value ) {
		$attr_html .= sprintf( ' %s="%s"', esc_attr( $key ), esc_attr( $value ) );
	}

	return sprintf(
		'<img src="%s"%s>',
		esc_url( get_theme_file_uri( 'assets/images/fallback.png' ) ),
		$attr_html
	);
}

/**
 * The six topic terms cycle through six fixed crimson-family shades in the
 * v6 popup menu (main.css §06, .menu-topic--c1..c6). v6 hardcodes each
 * topic to a specific shade in a fixed order (economy=c1 ... science-and-art
 * =c6); mapped here by slug rather than by term_id/position so it doesn't
 * depend on get_terms() ordering.
 *
 * @param string $slug Topic term slug.
 * @return string Modifier class, e.g. "menu-topic--c1".
 */
function shola_topic_color_class( $slug ) {
	$map = array(
		'economy'                => 'menu-topic--c1',
		'world'                  => 'menu-topic--c2',
		'afghanistan'            => 'menu-topic--c3',
		'women'                  => 'menu-topic--c4',
		'international-movement' => 'menu-topic--c5',
		'science-and-art'        => 'menu-topic--c6',
	);
	return isset( $map[ $slug ] ) ? $map[ $slug ] : 'menu-topic--c1';
}

/**
 * Editor-controlled display order for the `topic` taxonomy terms, read from
 * the real `menu_topics` nav menu (Appearance → Menus) that
 * `shola_maybe_seed_nav_menus()` (inc/setup.php) creates and seeds with
 * today's v6 default order on first run. Until 2026-08-08 this returned a
 * hardcoded array — found by Farhad to silently fail the IA doc's "staff
 * can add a topic without a developer" requirement, since a newly added
 * term was never in the fixed array. get_terms()'s default orderby is
 * alphabetical by name, which doesn't match the intended editorial order,
 * so a real ordered menu is the source of truth now instead. The hardcoded
 * array is kept as a last-resort fallback only (menu not yet seeded/
 * assigned — shouldn't normally happen post-seeding).
 *
 * @return string[] Topic slugs, in display order.
 */
function shola_get_topic_slugs_ordered() {
	$slugs = shola_get_ordered_term_slugs_from_menu( 'menu_topics', 'topic' );
	if ( $slugs ) {
		return $slugs;
	}
	return array( 'economy', 'world', 'afghanistan', 'women', 'international-movement', 'science-and-art' );
}

/**
 * Editor-controlled display order for the `publication` taxonomy terms,
 * read from the real `menu_publications` nav menu. See
 * shola_get_topic_slugs_ordered() docblock for the full history — same
 * mechanism, same fix, applied to the second of the two taxonomies that had
 * this problem.
 *
 * @return string[] Publication slugs, in display order.
 */
function shola_get_publication_slugs_ordered() {
	$slugs = shola_get_ordered_term_slugs_from_menu( 'menu_publications', 'publication' );
	if ( $slugs ) {
		return $slugs;
	}
	return array( 'shola-jawid', 'a-world-to-win' );
}

/**
 * Shared helper: resolves a registered nav menu location to the ordered
 * list of term slugs for one taxonomy, from whichever of that taxonomy's
 * terms are present as menu items at that location. Non-matching item
 * types (custom links, other taxonomies/post types an editor might
 * mistakenly add) are skipped rather than erroring, since this only feeds
 * display order — a stray item type just doesn't contribute a slug.
 *
 * @param string $location Registered nav menu location (see
 *                          register_nav_menus() in inc/setup.php).
 * @param string $taxonomy  Taxonomy slug to filter menu items by.
 * @return string[] Term slugs in menu order; empty array if no menu is
 *                   assigned to the location, or it has no matching items.
 */
function shola_get_ordered_term_slugs_from_menu( $location, $taxonomy ) {
	$menu_locations = get_nav_menu_locations();
	if ( empty( $menu_locations[ $location ] ) ) {
		return array();
	}

	$menu_items = wp_get_nav_menu_items( $menu_locations[ $location ] );
	if ( ! $menu_items ) {
		return array();
	}

	$slugs = array();
	foreach ( $menu_items as $item ) {
		if ( 'taxonomy' !== $item->type || $taxonomy !== $item->object ) {
			continue;
		}
		$term = get_term( $item->object_id, $taxonomy );
		if ( $term && ! is_wp_error( $term ) ) {
			$slugs[] = $term->slug;
		}
	}
	return $slugs;
}

/**
 * Which of the two publication terms is "current" vs. "former" — a fixed
 * editorial fact from the IA doc (PUB-SJ current, PUB-WW former), not
 * something derivable from taxonomy data alone.
 *
 * @param string $slug Publication term slug.
 * @return string
 */
function shola_publication_status_label( $slug ) {
	return 'shola-jawid' === $slug ? __( 'جاری', 'shola-jawid' ) : __( 'آرشیوی', 'shola-jawid' );
}

/**
 * Fixed Latin brand-code used in the masthead runner's mono/lang="en"
 * context ("SHOLA JAWID · شماره ۳۲ · سرطان ۱۴۰۵" in v6) — a running-head
 * mark, not a translation of the site name (`get_bloginfo('name')`
 * already supplies the actual Persian nameplate text elsewhere in
 * header.php). Not wrapped in __() — like "SJ-32" in
 * taxonomy-publication.php, this is a fixed ASCII brand code, not
 * translatable UI copy. Filterable so it isn't hardcoded in more than one
 * place if it's ever needed elsewhere.
 *
 * @return string
 */
function shola_get_masthead_code() {
	return apply_filters( 'shola_masthead_code', 'SHOLA JAWID' );
}

/**
 * Masthead runner text — weekday + date, per Farhad's 2026-08-12 review
 * of the live site (previously "SHOLA JAWID · شماره ۳۲ · ۱۵ اسد ۱۴۰۵";
 * see docs/CHANGELOG.md) and the client-approved weekday addition
 * logged there. Uses the latest published issue's date so the masthead
 * never shows stale placeholder data once real content exists; empty
 * when no issue is published yet (nothing to date it by), rather than
 * falling back to a brand-code string that no longer has a place in
 * this design.
 *
 * get_the_date() here is what actually renders the Jalali date via the
 * Persian Calendar plugin + shola_convert_jalali_months_to_dari() —
 * untouched by this change. The explicit 'l j F Y' format (rather than
 * the empty string / site date_format option used elsewhere) is
 * deliberately scoped to only this one call site: Persian Calendar's
 * own 'l' output is already correct Dari (weekday names, unlike month
 * names, don't diverge between fa_IR and fa_AF, so no sibling filter is
 * needed alongside shola_convert_jalali_months_to_dari()) — confirmed
 * live via `wp eval`. Changing the site-wide date_format option instead
 * would have added the weekday to every date on the site, not just the
 * masthead.
 *
 * @return string
 */
function shola_get_masthead_runner() {
	$latest = get_posts(
		array(
			'post_type'      => 'issue',
			'posts_per_page' => 1,
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);

	if ( ! $latest ) {
		return '';
	}

	return get_the_date( 'l j F Y', $latest[0] );
}

/**
 * Fallback for the `menu_sections` nav location, matching v6's
 * hardcoded defaults (_menu.html) — used until an admin builds a real
 * menu under Appearance → Menus for pages that don't exist yet (Phase
 * 4.2). Same visual result, real <nav>-compatible markup.
 *
 * @return void
 */
function shola_fallback_menu_sections() {
	?>
	<ul class="menu-list">
		<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'صفحهٔ اصلی', 'shola-jawid' ); ?></a></li>
		<li><a href="<?php echo esc_url( home_url( '/publications/' ) ); ?>"><?php esc_html_e( 'نشرات', 'shola-jawid' ); ?></a></li>
		<li><a href="<?php echo esc_url( home_url( '/library/' ) ); ?>"><?php esc_html_e( 'کتابخانه', 'shola-jawid' ); ?></a></li>
		<li><a href="<?php echo esc_url( home_url( '/topics/' ) ); ?>"><?php esc_html_e( 'همهٔ موضوعات', 'shola-jawid' ); ?></a></li>
	</ul>
	<?php
}

/**
 * Fallback for the `menu_more` nav location, matching v6's defaults.
 *
 * @return void
 */
function shola_fallback_menu_more() {
	?>
	<ul class="menu-list menu-list-small">
		<li><a href="<?php echo esc_url( home_url( '/announcements/' ) ); ?>"><?php esc_html_e( 'اطلاعیه‌ها', 'shola-jawid' ); ?></a></li>
		<li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php esc_html_e( 'دربارهٔ ما', 'shola-jawid' ); ?></a></li>
		<li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'ارتباط با حزب', 'shola-jawid' ); ?></a></li>
		<li><a href="<?php echo esc_url( home_url( '/search/' ) ); ?>"><?php esc_html_e( 'جست‌وجو در آرشیو', 'shola-jawid' ); ?></a></li>
	</ul>
	<?php
}

/**
 * Fallback for the `footer_topics` nav location — v6's footer shows a
 * curated 3-of-6 topic subset (economy, afghanistan, women), not all six,
 * plus an "all topics" link. Real term links once the taxonomy exists
 * (Phase 3.2, already live), same curated subset as v6 until an editor
 * chooses to customize it via Appearance → Menus.
 *
 * @return void
 */
function shola_fallback_footer_topics() {
	$slugs = array( 'economy', 'afghanistan', 'women' );
	echo '<ul>';
	foreach ( $slugs as $slug ) {
		$term = get_term_by( 'slug', $slug, 'topic' );
		if ( $term ) {
			printf( '<li><a href="%s">%s</a></li>', esc_url( get_term_link( $term ) ), esc_html( $term->name ) );
		}
	}
	printf( '<li><a href="%s">%s</a></li>', esc_url( home_url( '/topics/' ) ), esc_html__( 'همهٔ موضوعات', 'shola-jawid' ) );
	echo '</ul>';
}

/**
 * Fallback for the `footer_site` nav location, matching v6's defaults.
 *
 * @return void
 */
function shola_fallback_footer_site() {
	?>
	<ul>
		<li><a href="<?php echo esc_url( home_url( '/library/' ) ); ?>"><?php esc_html_e( 'کتابخانه', 'shola-jawid' ); ?></a></li>
		<li><a href="<?php echo esc_url( home_url( '/announcements/' ) ); ?>"><?php esc_html_e( 'اطلاعیه‌ها', 'shola-jawid' ); ?></a></li>
		<li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php esc_html_e( 'دربارهٔ ما', 'shola-jawid' ); ?></a></li>
		<li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php esc_html_e( 'ارتباط با حزب', 'shola-jawid' ); ?></a></li>
	</ul>
	<?php
}

/**
 * Social-link icon rows shared by footer.php and header.php's popup menu
 * (same icon set, same order, previously hardcoded `href="#"` in both
 * places independently — found by Farhad, 2026-08-08). Platform URLs
 * come from the shola-core settings screen (Settings → شبکه‌های
 * اجتماعی) — expanded 2026-08-08 from 2 platforms (Telegram, X) to the
 * fixed 11-platform list `Social_Links_Settings::get_platforms()`
 * defines. RSS is never a setting — it's the site's own feed, always
 * correct via get_feed_link(), not something an editor should type in.
 * A platform is omitted entirely (not rendered) when its URL is empty,
 * per Farhad's explicit call: a missing icon reads as "not used," which
 * is more honest than a dead `#` link — this applies uniformly to all
 * 11 platforms, enforced once in the `array_filter()` below rather than
 * per-platform, so it can't silently stop applying to a subset.
 *
 * Icon paths are hand-drawn simplified single-color glyphs (not brand-
 * official SVG exports), matching the visual weight/style already
 * established for Telegram/X/RSS. Telegram/X/Facebook/WhatsApp are
 * reused verbatim from single.php's share-menu icons (built earlier,
 * same style) rather than redrawn.
 *
 * Guarded per CLAUDE.md §2: the theme must not fatal-error if shola-core
 * is inactive — degrades to every platform except RSS omitted (RSS
 * doesn't depend on the plugin at all).
 *
 * @return array<int, array{key: string, label: string, url: string, icon: string}>
 */
function shola_get_social_links() {
	$urls = array();
	if ( class_exists( '\SholaCore\Social_Links_Settings' ) ) {
		$urls = \SholaCore\Social_Links_Settings::get_links();
	}

	$icons = array(
		'telegram'  => '<path d="M21.9 4.6 18.8 19c-.2 1-.8 1.2-1.7.8l-4.6-3.4-2.2 2.1c-.3.3-.5.5-.9.5l.3-4.6L18 6.9c.4-.3-.1-.5-.5-.2L7 13.2l-4.4-1.4c-1-.3-1-1 .2-1.4L20.6 3.2c.8-.3 1.5.2 1.3 1.4Z"/>',
		'x'         => '<path d="M17.8 3h3l-6.7 7.7L22 21h-6.2l-4.8-6.3L5.4 21h-3l7.2-8.2L2 3h6.3l4.4 5.8L17.8 3Zm-1.1 16.2h1.7L7.3 4.7H5.5l11.2 14.5Z"/>',
		'facebook'  => '<path d="M22 12a10 10 0 1 0-11.5 9.95v-7.04H7.9V12h2.6V9.8c0-2.6 1.55-4 3.9-4 1.1 0 2.3.2 2.3.2v2.5h-1.3c-1.3 0-1.7.8-1.7 1.6V12h2.9l-.5 2.9h-2.4v7.04A10 10 0 0 0 22 12Z"/>',
		'instagram' => '<path fill-rule="evenodd" d="M7 3h10a4 4 0 0 1 4 4v10a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4V7a4 4 0 0 1 4-4Zm0 2a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H7Zm5 3a4 4 0 1 1 0 8 4 4 0 0 1 0-8Zm0 2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm4.8-3.3a1 1 0 1 1 0 2 1 1 0 0 1 0-2Z"/>',
		'whatsapp'  => '<path d="M12 2a10 10 0 0 0-8.5 15.2L2 22l4.9-1.3A10 10 0 1 0 12 2Zm5.8 14.2c-.2.6-1.3 1.2-1.8 1.3-.5.1-1 .1-1.7-.1-.4-.1-.9-.3-1.6-.6-2.8-1.2-4.6-4-4.7-4.2-.1-.2-1.1-1.5-1.1-2.9s.7-2 1-2.3c.2-.3.5-.3.7-.3h.5c.2 0 .4 0 .6.5l.8 1.9c.1.2.1.3 0 .5l-.3.5-.4.4c-.1.1-.3.3-.1.6.2.3.8 1.3 1.7 2.1 1.2 1 2.1 1.4 2.4 1.5.3.1.4.1.6-.1l.7-.8c.2-.3.4-.2.7-.1l1.7.8c.2.1.4.2.5.3.1.2.1.9-.1 1.5Z"/>',
		'youtube'   => '<path fill-rule="evenodd" d="M4 7a3 3 0 0 1 3-3h10a3 3 0 0 1 3 3v10a3 3 0 0 1-3 3H7a3 3 0 0 1-3-3V7Zm2 0v10h12V7H6Zm4.5 2.3 5 2.7-5 2.7V9.3Z"/>',
		'linkedin'  => '<path fill-rule="evenodd" d="M4 3h16a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1Zm1 2v14h14V5H5Zm2.2 2a1.4 1.4 0 1 1 0 2.8 1.4 1.4 0 0 1 0-2.8ZM6.4 10.8h1.8V18H6.4v-7.2Zm4 0h1.7v1a2.6 2.6 0 0 1 2.2-1.2c1.6 0 2.7 1 2.7 3.1V18h-1.8v-3.8c0-1-.4-1.7-1.4-1.7-.8 0-1.4.6-1.6 1.1-.1.2-.1.4-.1.7V18h-1.8v-7.2Z"/>',
		'tiktok'    => '<path d="M14 3h2.1c.2 1.6 1.4 2.8 3 3v2.1c-1.1 0-2.1-.3-3-.9v6.3a4.5 4.5 0 1 1-4.5-4.5c.2 0 .3 0 .5 0v2.2a2.3 2.3 0 1 0 2 2.3V3Z"/>',
		'threads'   => '<path fill-rule="evenodd" d="M12 3a9 9 0 1 0 0 18 9 9 0 0 0 0-18Zm0 2a7 7 0 1 1 0 14 7 7 0 0 1 0-14Zm2.4 3.4c1 .6 1.6 1.7 1.6 3 0 2.1-1.6 3.6-3.9 3.6-1.6 0-2.9-.7-3.6-1.9l1.5-1c.4.8 1.2 1.3 2.1 1.3 1.1 0 1.9-.6 1.9-1.7 0-1-.7-1.6-1.9-1.6h-.8V8.9h.7c1 0 1.6-.5 1.6-1.3 0-.8-.7-1.3-1.6-1.3-.9 0-1.5.4-1.9 1.1l-1.5-.9c.6-1.1 1.9-1.7 3.4-1.7 2 0 3.3 1.1 3.3 2.7 0 1-.5 1.7-1.4 2.2Z"/>',
		'signal'    => '<path fill-rule="evenodd" d="M12 3C6.5 3 2 6.9 2 11.5c0 2.4 1.1 4.5 3 6-.2 1.1-.7 2.3-1.5 3.2 1.5.1 3-.4 4.2-1.3 1.3.5 2.8.8 4.3.8 5.5 0 10-3.8 10-8.5S17.5 3 12 3Zm0 2c4.4 0 8 2.9 8 6.5S16.4 18 12 18c-1.3 0-2.6-.3-3.7-.8l-.6-.3-.6.4c-.4.3-.8.5-1.3.7.3-.6.5-1.3.6-2l.1-.8-.6-.5C4.6 13.8 4 12.7 4 11.5 4 7.9 7.6 5 12 5Z"/>',
		'mastodon'  => '<path fill-rule="evenodd" d="M6 4h12a3 3 0 0 1 3 3v7a3 3 0 0 1-3 3h-5l-3 4v-4H6a3 3 0 0 1-3-3V7a3 3 0 0 1 3-3Zm0 2a1 1 0 0 0-1 1v7a1 1 0 0 0 1 1h5v2l1.5-2H18a1 1 0 0 0 1-1V7a1 1 0 0 0-1-1H6Z"/>',
	);

	$platforms = class_exists( '\SholaCore\Social_Links_Settings' )
		? \SholaCore\Social_Links_Settings::get_platforms()
		: array(
			'telegram' => __( 'تلگرام', 'shola-jawid' ),
			'x'        => __( 'ایکس', 'shola-jawid' ),
		);

	$all = array();
	foreach ( $platforms as $key => $label ) {
		$all[ $key ] = array(
			'key'   => $key,
			'label' => $label,
			'url'   => isset( $urls[ $key ] ) ? $urls[ $key ] : '',
			'icon'  => isset( $icons[ $key ] ) ? $icons[ $key ] : '',
		);
	}

	$all['rss'] = array(
		'key'   => 'rss',
		'label' => __( 'خوراک RSS', 'shola-jawid' ),
		'url'   => get_feed_link(),
		'icon'  => '<path d="M4 4a16 16 0 0 1 16 16h-3A13 13 0 0 0 4 7V4Zm0 6a10 10 0 0 1 10 10h-3a7 7 0 0 0-7-7v-3Zm2 6a2 2 0 1 1 0 4 2 2 0 0 1 0-4Z"/>',
	);

	$with_url = array_filter(
		$all,
		static function ( $item ) {
			return ! empty( $item['url'] );
		}
	);

	return array_values( $with_url );
}

/**
 * Reads one CMS-editable UI label (تازه‌ترین, موضوعات, etc.) — see
 * SholaCore\Label_Settings::get_defaults() for the full key list and
 * where each one renders. Guarded per CLAUDE.md §2: the theme must not
 * fatal-error if shola-core is inactive, so it carries its own inline
 * copy of the same defaults (kept translatable via `__()` under the
 * `shola-jawid` text-domain) rather than depending on the plugin class
 * existing — same pattern as shola_get_social_links()'s platform-name
 * fallback above. An unrecognized key returns an empty string rather
 * than warning, since every call site here passes a literal key it
 * controls, not user input.
 *
 * @param string $key One of Label_Settings::get_defaults()'s keys.
 * @return string
 */
function shola_get_label( $key ) {
	if ( class_exists( '\SholaCore\Label_Settings' ) ) {
		return \SholaCore\Label_Settings::get_label( $key );
	}

	$fallback_defaults = array(
		'home_articles_section_aria'  => __( 'تازه‌ها مقالات', 'shola-jawid' ),
		'home_latest_heading'         => __( 'تازه‌ها', 'shola-jawid' ),
		'home_topics_link_more'       => __( 'همهٔ موضوعات', 'shola-jawid' ),
		'home_topics_section_heading' => __( 'همهٔ موضوعات', 'shola-jawid' ),
		'latest_documents_heading'    => __( 'تازه‌ترین اسناد', 'shola-jawid' ),
		'nav_topics_label'            => __( 'موضوعات', 'shola-jawid' ),
		'nav_more_label'              => __( 'بیشتر', 'shola-jawid' ),
		'breadcrumb_topics_label'     => __( 'موضوعات', 'shola-jawid' ),
		'topic_tab_latest'            => __( 'تازه‌ها', 'shola-jawid' ),
		'topic_tab_most_read'         => __( 'پرخواننده‌ترین', 'shola-jawid' ),
		'topics_page_title'           => __( 'موضوعات', 'shola-jawid' ),
	);

	return isset( $fallback_defaults[ $key ] ) ? $fallback_defaults[ $key ] : '';
}

/**
 * "۳۲ ISSUES · ۲۰۱۸–۲۰۲۶" style meta line for a publication term
 * (page-publications.php) — computed from real published `issue` posts
 * tagged with that term, not fabricated. Returns an empty string if the
 * term has no issues yet (nothing to show, not "0 issues").
 *
 * @param WP_Term $term Publication term.
 * @return string
 */
function shola_get_publication_meta_line( $term ) {
	$issues = get_posts(
		array(
			'post_type'      => 'issue',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- small, fixed-vocabulary taxonomy, not a scale concern.
				array(
					'taxonomy' => 'publication',
					'field'    => 'term_id',
					'terms'    => $term->term_id,
				),
			),
		)
	);

	if ( ! $issues ) {
		return '';
	}

	$years = array();
	foreach ( $issues as $issue ) {
		$years[] = shola_get_gregorian_year( $issue );
	}
	sort( $years );
	$year_range = ( end( $years ) === $years[0] )
		? shola_to_persian_digits( $years[0] )
		: shola_to_persian_digits( $years[0] ) . '–' . shola_to_persian_digits( end( $years ) );

	return sprintf(
		/* translators: 1: issue count, 2: year or year range. */
		_n( '%1$s ISSUE · %2$s', '%1$s ISSUES · %2$s', count( $issues ), 'shola-jawid' ),
		shola_to_persian_digits( count( $issues ) ),
		$year_range
	);
}

/**
 * "سردبیر مسئول" credit shown on single.php — a fixed masthead-level
 * role (same person named on page-about.php's هیئت تحریریه section),
 * not a per-article value, so this is a filterable constant like
 * shola_get_masthead_code() rather than a new post-meta field.
 *
 * @return string
 */
function shola_get_managing_editor() {
	return apply_filters( 'shola_managing_editor', 'م. صالح' );
}

/**
 * Word count + estimated reading time for single.php's sidebar
 * ("۳٬۴۰۰ واژه · ۱۲ دقیقه خواندن" in v6). Computed from post_content
 * rather than stored as meta, so it can never go stale after an edit.
 * 250 words/minute is a standard editorial reading-speed baseline —
 * not measured against this specific audience, a reasonable default
 * like issue-card.php's month+year date simplification.
 *
 * @param WP_Post|null $post Post object. Defaults to the global $post.
 * @return array{words: int, minutes: int}
 */
function shola_get_reading_stats( $post = null ) {
	$post = $post ? $post : get_post();

	$text  = wp_strip_all_tags( strip_shortcodes( $post->post_content ) );
	$words = preg_split( '/\s+/u', trim( $text ), -1, PREG_SPLIT_NO_EMPTY );
	$count = $words ? count( $words ) : 0;

	return array(
		'words'   => $count,
		'minutes' => max( 1, (int) ceil( $count / 250 ) ),
	);
}

/**
 * Wraps every case-insensitive occurrence of the search query in a <mark>,
 * matching body-search.html's highlighted result titles. Takes and returns
 * already-escaped HTML (the caller passes esc_html()'d text) so the <mark>
 * tags themselves aren't escaped away; $query is escaped internally before
 * use, but must not itself already be HTML.
 *
 * @param string $escaped_text Already-esc_html()'d text to search within.
 * @param string $query        Raw (unescaped) search query.
 * @return string HTML with matches wrapped in <mark>, safe to echo directly.
 */
function shola_highlight_search_term( $escaped_text, $query ) {
	$query = trim( (string) $query );

	if ( '' === $query ) {
		return $escaped_text;
	}

	$pattern = '/' . preg_quote( esc_html( $query ), '/' ) . '/iu';

	$highlighted = preg_replace( $pattern, '<mark>$0</mark>', $escaped_text );

	return null === $highlighted ? $escaped_text : $highlighted;
}
