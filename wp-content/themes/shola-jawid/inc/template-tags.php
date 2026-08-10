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
 * Masthead runner text ("SHOLA JAWID · شماره ۳۲ · سرطان ۱۴۰۵" in v6) —
 * built from the latest published issue's number + date if one exists,
 * falling back to just the brand code so the masthead never shows stale
 * placeholder data once real content exists.
 *
 * Bug fixed 2026-08-06 (found by Farhad comparing page-topics.php and
 * page-publications.php against v6): this previously used
 * get_bloginfo('name') — the Persian site title — for the first segment,
 * when v6's actual source uses the fixed Latin brand code instead. The
 * Persian nameplate is already shown separately in header.php; this
 * function only builds the mono-label runner beneath it.
 *
 * @return string
 */
function shola_get_masthead_runner() {
	$code = shola_get_masthead_code();

	$latest = get_posts(
		array(
			'post_type'      => 'issue',
			'posts_per_page' => 1,
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);

	if ( ! $latest ) {
		return $code;
	}

	$number = get_post_meta( $latest[0]->ID, 'shcore_issue_number', true );
	if ( ! $number ) {
		return $code;
	}

	return sprintf(
		/* translators: 1: fixed Latin brand code (not translatable), 2: issue number, 3: issue date. */
		__( '%1$s · شماره %2$s · %3$s', 'shola-jawid' ),
		$code,
		$number,
		get_the_date( '', $latest[0] )
	);
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
