<?php
/**
 * The /en/ URL prefix, which language the current request is in, and
 * the shared helper every content query uses to show only that
 * language's content. See english-version/03-content-architecture/
 * url-structure-and-routing.md for the full design.
 *
 * @package SholaCore
 */

namespace SholaCore;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * English/Persian URL routing and query filtering.
 */
class Language {

	/**
	 * The query var that marks a request as being in the English URL
	 * space. Present and `en` = English page; absent = Persian page.
	 */
	const QUERY_VAR = 'shola_lang';

	/**
	 * User-meta key holding which language an admin/editor is currently
	 * working in — english-version/03-content-architecture/admin-
	 * language-switcher.md. Separate concept from QUERY_VAR above: that's
	 * which language a front-end *visitor* is reading; this is which
	 * language a logged-in *admin* is currently managing content in.
	 */
	const ADMIN_LANG_META_KEY = 'shcore_admin_language';

	/**
	 * Every post type with a `shcore_language` field — the admin-bar
	 * switcher's default-filtering and language column apply to all of
	 * these list screens.
	 *
	 * @var string[]
	 */
	const MANAGED_POST_TYPES = array( 'post', 'issue', 'document', 'party_publication', 'party_document', 'leaflet', 'editors_pick', 'announcement' );

	/**
	 * This request's language, resolved once per request by
	 * set_language_context() and read by current() everywhere else —
	 * one source of truth instead of every piece of code re-deriving it
	 * from the URL on its own.
	 *
	 * @var string|null
	 */
	private static $current = null;

	/**
	 * Hook registration.
	 *
	 * @return void
	 */
	public static function init() {
		add_filter( 'query_vars', array( __CLASS__, 'register_query_var' ) );
		add_filter( 'rewrite_rules_array', array( __CLASS__, 'add_english_prefixed_rules' ) );
		add_action( 'init', array( __CLASS__, 'add_homepage_rule' ) );
		add_action( 'parse_request', array( __CLASS__, 'set_language_context' ) );
		add_action( 'admin_init', array( __CLASS__, 'backfill_legacy_language_meta' ) );

		/*
		 * Root-cause fix, found while building the LTR CSS pass (Phase 3,
		 * english-version/04-design-and-ltr-system/): `is_rtl()` — which
		 * WordPress core's own `language_attributes()` (the `<html dir=
		 * "...">` attribute itself), the Persian Calendar plugin's Jalali-
		 * date conversion, and potentially other code all branch on — is a
		 * GLOBAL, whole-site-locale check, not aware of which request is
		 * in the /en/ URL space. Without this, an English page would still
		 * render dir="rtl" and still show Jalali dates, defeating the
		 * entire point of this rollout. `switch_to_locale()` is
		 * WordPress's own real, native mechanism for exactly this —
		 * correctly updates `$wp_locale` (so `is_rtl()` and `dir`/`lang`
		 * output become request-aware) without touching this site's
		 * actual Persian strings, which are hardcoded Persian text in
		 * source, not pulled through a .mo translation file that this
		 * could disturb.
		 *
		 * Hooked on `plugins_loaded` specifically — has to run before
		 * Persian Calendar's own `init` (priority 10) checks `is_rtl()` to
		 * decide whether to register its date filters at all, so this
		 * can't wait for parse_request (too late) or even `init` itself.
		 * Reads the raw request path directly rather than waiting for
		 * this class's own query-var-based detection (set_language_context()
		 * above) for the same reason — that only resolves at parse_request.
		 */
		add_action( 'plugins_loaded', array( __CLASS__, 'maybe_switch_locale_for_english_request' ), 1 );
		/*
		 * Caught live, not assumed: WordPress core reloads locale data for
		 * its own purposes somewhere between `setup_theme` and
		 * `after_setup_theme` (traced with a temporary debug probe logging
		 * is_rtl() at every bootstrap hook — it read correctly switched at
		 * `setup_theme`, reverted by `after_setup_theme`), which silently
		 * undoes a `plugins_loaded`-time switch_to_locale() before Persian
		 * Calendar's own `init` (priority 10) ever checks it. Reapplying at
		 * `after_setup_theme` priority 999 — after whatever resets it,
		 * still well before `init` priority 10 — is what actually survives
		 * to reach that check.
		 */
		add_action( 'after_setup_theme', array( __CLASS__, 'maybe_switch_locale_for_english_request' ), 999 );

		/*
		 * These three templates run WordPress's own main query directly
		 * (have_posts()/the_post(), no `new WP_Query()` of their own), so
		 * pre_get_posts actually reaches them — same pattern already used
		 * by Post_Types::include_cpts_in_search() and its two per-page
		 * siblings. Every other listing template (taxonomy-*.php,
		 * page-*.php, front-page.php's panels) builds its own query and
		 * needs maybe_add_language_meta_query() called directly on its
		 * own $args instead — see that method's docblock.
		 */
		add_action( 'pre_get_posts', array( __CLASS__, 'filter_main_query_by_language' ) );

		/*
		 * Canonical-URL enforcement for singular content, found 2026-11-28
		 * while verifying Phase 4/5's work live: because the English
		 * rewrite rules only ever ADD an /en/-prefixed mirror of every
		 * existing rule, never remove the original, every single post/Page
		 * is reachable at BOTH its bare URL and its /en/-prefixed one,
		 * regardless of that post's own actual shcore_language — confirmed
		 * live: `/en/about/` rendered the Persian About page with
		 * lang="en-US"/no dir="rtl", and `/about-en/` (no prefix) rendered
		 * the English About page with dir="rtl"/lang="fa-IR". Real
		 * duplicate-content URLs, and actively wrong direction/locale on
		 * whichever one doesn't match the content's real language. See
		 * redirect_mismatched_singular_language() below for the fix.
		 */
		add_action( 'template_redirect', array( __CLASS__, 'redirect_mismatched_singular_language' ) );

		/*
		 * Admin language switcher (english-version/03-content-architecture/
		 * admin-language-switcher.md) — a separate feature from the /en/
		 * routing above: this is about which language an admin/editor is
		 * currently managing content *in*, not what a front-end visitor
		 * sees.
		 */
		add_action( 'admin_bar_menu', array( __CLASS__, 'add_admin_bar_switcher' ), 100 );
		add_action( 'admin_init', array( __CLASS__, 'handle_admin_language_switch' ) );
		add_action( 'pre_get_posts', array( __CLASS__, 'apply_admin_language_default' ) );
		add_action( 'restrict_manage_posts', array( __CLASS__, 'render_admin_language_filter' ) );
		foreach ( self::MANAGED_POST_TYPES as $post_type ) {
			add_filter( "manage_{$post_type}_posts_columns", array( __CLASS__, 'add_admin_language_column' ) );
			add_action( "manage_{$post_type}_posts_custom_column", array( __CLASS__, 'render_admin_language_column' ), 10, 2 );
		}
	}

	/**
	 * Switches WordPress's active locale to English, very early, for any
	 * front-end request whose path starts with `/en/` (or is exactly
	 * `/en`) — see the `plugins_loaded` registration above for why this
	 * has to happen this early, and why a raw path check is used instead
	 * of this class's own later, more careful query-var-based detection.
	 * `en_US` needs no installed translation files to work correctly here
	 * — WordPress's own fallback for a locale with no .mo file is simply
	 * the original (Persian) source string, unchanged; only `is_rtl()`/
	 * `$wp_locale`'s text-direction actually changes, which is the whole
	 * point.
	 *
	 * Admin requests are explicitly excluded — wp-admin's own interface
	 * language is a separate, per-user WordPress setting (Profile →
	 * Language), untouched by this; nothing under `/en/` is an admin URL
	 * anyway (this plugin's own rewrite rules never prefix wp-admin), but
	 * the check is kept explicit rather than relying on that alone.
	 *
	 * @return void
	 */
	public static function maybe_switch_locale_for_english_request() {
		if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) || wp_doing_cron() ) {
			return;
		}

		$path = (string) wp_parse_url( isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '', PHP_URL_PATH );
		$path = untrailingslashit( $path );

		if ( '/en' === $path || 0 === strpos( $path, '/en/' ) ) {
			switch_to_locale( 'en_US' );
		}
	}

	/**
	 * Makes `shola_lang` a recognized query var, so it survives
	 * WordPress's own request parsing instead of being silently dropped.
	 *
	 * @param string[] $vars Existing public query vars.
	 * @return string[]
	 */
	public static function register_query_var( $vars ) {
		$vars[] = self::QUERY_VAR;
		return $vars;
	}

	/**
	 * Mirrors every existing rewrite rule under an `en/` prefix, each one
	 * additionally setting `shola_lang=en` — so any URL that already
	 * works today (an article, a taxonomy archive, a PDF permalink...)
	 * also works with `en/` in front, with no per-URL-pattern list to
	 * hand-maintain. English-prefixed rules go first, so they're matched
	 * before their un-prefixed Persian equivalents.
	 *
	 * @param string[] $rules Existing rewrite rules (regex => query string).
	 * @return string[]
	 */
	public static function add_english_prefixed_rules( $rules ) {
		// Rule types with no real "English version" to speak of — mirroring
		// these would just double the rewrite table for nothing.
		// `rest_route=`: the REST API isn't a page with a language. `sitemap`:
		// handled deliberately in Phase 5, not by blind prefixing here.
		// `feed=`/`embed=true`: out of scope for now, not asked for.
		$skip_if_contains = array( 'rest_route=', 'sitemap', 'feed=', 'embed=true' );

		$english_rules = array();
		foreach ( $rules as $regex => $query ) {
			foreach ( $skip_if_contains as $needle ) {
				if ( false !== strpos( $query, $needle ) ) {
					continue 2;
				}
			}

			// Every existing regex starts with `^` (an anchor, not a
			// literal character) — putting "en/" in front of the whole
			// string would bury that anchor in the middle of the regex
			// instead of at the start, breaking every single one of
			// these silently. Strip it and put it back at the very
			// front, now in front of "en/" instead of behind it.
			$en_regex                   = '^en/' . ltrim( $regex, '^' );
			$english_rules[ $en_regex ] = $query . '&' . self::QUERY_VAR . '=en';
		}
		return $english_rules + $rules;
	}

	/**
	 * The homepage itself (`/en/`) has no regular rewrite rule to mirror
	 * above — WordPress serves the front page directly, not through a
	 * regex match — so it needs its own explicit rule.
	 *
	 * @return void
	 */
	public static function add_homepage_rule() {
		add_rewrite_rule( '^en/?$', 'index.php?' . self::QUERY_VAR . '=en', 'top' );
	}

	/**
	 * Resolves and caches this request's language from the parsed query
	 * vars — called once, early, so current() below never has to guess.
	 *
	 * @param \WP $wp The main WP request object.
	 * @return void
	 */
	public static function set_language_context( $wp ) {
		self::$current = ( isset( $wp->query_vars[ self::QUERY_VAR ] ) && 'en' === $wp->query_vars[ self::QUERY_VAR ] ) ? 'en' : 'fa';
	}

	/**
	 * This request's language — `fa` or `en`. Safe to call from anywhere
	 * (templates, other plugin classes, the language switcher); falls
	 * back to `fa` if asked before parse_request has run (e.g. a WP-CLI
	 * or cron context with no real request).
	 *
	 * @return string
	 */
	public static function current() {
		return self::$current ? self::$current : 'fa';
	}

	/**
	 * `page` slugs that are structural content-type landing pages
	 * (نشرات, کتابخانه, موضوعات, ...) — one single Page row renders at
	 * the *same* path in both languages, filtered by content, not a
	 * pairing. Same set the theme's own `shola_get_language_switch_url()`
	 * (inc/template-tags.php) recognizes by template file rather than
	 * slug; kept as a slug list here instead of calling that function,
	 * same CLAUDE.md §2 reasoning as every other theme-logic duplication
	 * in this class/class-seo.php. Checked by
	 * redirect_mismatched_singular_language() below — these pages never
	 * have a real `shcore_translation_id` pairing (there's nothing to
	 * pair, it's the same Page either way), so without this exclusion
	 * they'd be wrongly treated as "always Persian" and bounced away
	 * from their own `/en/` URL.
	 *
	 * @var string[]
	 */
	const STRUCTURAL_PAGE_SLUGS = array( 'publications', 'library', 'reports', 'party-documents', 'party-publications', 'topics', 'selected', 'leaflets' );

	/**
	 * 301-redirects a singular post/Page to its one correct canonical
	 * URL whenever the URL's own language (the /en/ prefix or its
	 * absence) disagrees with that specific post's real
	 * `shcore_language` value — see init()'s registration above for the
	 * live bug this fixes. Never touches archives/search/the front page:
	 * those aren't tied to one fixed language the way a single post is
	 * (Language::add_language_filter() already makes them show the
	 * correct per-language results at either URL, which is correct,
	 * not a bug), and a mismatch there has no one "right" URL to redirect
	 * to. Also never touches the structural listing Pages in
	 * STRUCTURAL_PAGE_SLUGS above, for the same reason — found live
	 * (`/en/leaflets/` was being wrongly redirected back to `/leaflets/`)
	 * right after shipping the first version of this fix.
	 *
	 * @return void
	 */
	public static function redirect_mismatched_singular_language() {
		if ( is_admin() || is_preview() || ! is_singular() ) {
			return;
		}

		$post = get_queried_object();
		if ( ! ( $post instanceof \WP_Post ) ) {
			return;
		}

		if ( 'page' === $post->post_type && in_array( $post->post_name, self::STRUCTURAL_PAGE_SLUGS, true ) ) {
			return;
		}

		$post_lang = get_post_meta( $post->ID, 'shcore_language', true );
		$post_lang = ( 'en' === $post_lang ) ? 'en' : 'fa';

		if ( $post_lang === self::current() ) {
			return;
		}

		$path   = (string) wp_parse_url( get_permalink( $post ), PHP_URL_PATH );
		$target = ( 'en' === $post_lang ) ? home_url( '/en' . $path ) : home_url( $path );

		wp_safe_redirect( $target, 301 );
		exit;
	}

	/**
	 * Adds a `shcore_language` condition to a query's existing
	 * meta_query, merging rather than replacing anything already there —
	 * the one shared function every content-listing template calls, so
	 * the actual language condition lives in one place, not copy-pasted
	 * into a dozen templates.
	 *
	 * Call this on `$query_args` before passing it to `new WP_Query()` or
	 * `get_posts()` for any template that builds its own query (most of
	 * this site's listing pages do — see the class docblock). For a
	 * template that instead uses WordPress's own main query
	 * (have_posts()/the_post()), filter_main_query_by_language() below
	 * already does this automatically.
	 *
	 * @param array $query_args Query args being built for `new WP_Query()`/`get_posts()`.
	 * @return array Same args, with the language condition merged in.
	 */
	public static function add_language_filter( $query_args ) {
		$condition = array(
			'key'   => 'shcore_language',
			'value' => self::current(),
		);

		if ( empty( $query_args['meta_query'] ) ) {
			$query_args['meta_query'] = array( $condition ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- small per-type post counts, same as every other meta_query already in this codebase.
			return $query_args;
		}

		// Something else already set a meta_query (e.g. a sort-by-views
		// query) — nest both under a shared 'AND' relation rather than
		// overwriting it.
		$query_args['meta_query'] = array(
			'relation' => 'AND',
			$condition,
			$query_args['meta_query'],
		);

		return $query_args;
	}

	/**
	 * Same condition as add_language_filter() above, applied to
	 * WordPress's own main query — for the handful of templates
	 * (search.php, archive-announcement.php, archive-editors_pick.php)
	 * that use have_posts()/the_post() directly instead of building
	 * their own WP_Query. Single posts/pages are left alone — a single
	 * item resolves by its own unique slug regardless of language, same
	 * as every other post type already works.
	 *
	 * @param \WP_Query $query The query being filtered.
	 * @return void
	 */
	public static function filter_main_query_by_language( $query ) {
		if ( is_admin() || ! $query->is_main_query() || $query->is_singular() ) {
			return;
		}

		$existing = $query->get( 'meta_query' );
		$args     = self::add_language_filter( array( 'meta_query' => $existing ) );
		$query->set( 'meta_query', $args['meta_query'] ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- see add_language_filter() above.
	}

	/**
	 * One-time fix for a real bug caught live, not assumed: every piece of
	 * content that existed before this field did has NO actual
	 * `shcore_language` postmeta row at all — `register_post_meta()`'s
	 * `default` only affects what `get_post_meta()` *returns* for a post
	 * with no row, it never writes one. A meta_query exact-match (which is
	 * what every language filter in this class does) only matches a real
	 * stored value, so every pre-existing post was silently excluded from
	 * BOTH the Persian and English queries — confirmed live: the Persian
	 * اطلاعیه‌ها archive dropped from 5 real announcements to 0 the moment
	 * filter_main_query_by_language() went live, not just the English one.
	 *
	 * Fixes it the same way every other one-time content migration in this
	 * plugin does (see Taxonomies::migrate_legacy_party_documents() for the
	 * original pattern this follows): find every post with no row yet, set
	 * it to `fa` explicitly (every pre-existing post actually is Persian),
	 * hooked on admin_init with a persisted flag rather than plugin
	 * activation (a code-only redeploy to an already-active plugin never
	 * re-fires activation).
	 *
	 * @return void
	 */
	public static function backfill_legacy_language_meta() {
		/*
		 * Tracked per post type (an array option), not one single flag —
		 * added 2026-11-27 when `page` joined the list after the first
		 * version of this migration had already run and marked itself
		 * fully done: a single boolean would have left `page` silently
		 * never backfilled, since the function would return before ever
		 * reaching it. Same per-item-tracking pattern as
		 * Taxonomies::seed_publication_periods()'s own `$seeded` array,
		 * for the same reason — this list is expected to grow again
		 * later as more types get `shcore_language`.
		 */
		$post_types = array( 'post', 'page', 'issue', 'document', 'party_publication', 'party_document', 'leaflet', 'editors_pick', 'announcement' );
		$done       = get_option( 'shcore_language_meta_backfilled', array() );

		foreach ( $post_types as $post_type ) {
			if ( ! empty( $done[ $post_type ] ) ) {
				continue;
			}

			$missing_ids = get_posts(
				array(
					'post_type'      => $post_type,
					'post_status'    => 'any',
					'posts_per_page' => -1,
					'fields'         => 'ids',
					'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- one-time migration, not a recurring query.
						array(
							'key'     => 'shcore_language',
							'compare' => 'NOT EXISTS',
						),
					),
				)
			);

			foreach ( $missing_ids as $post_id ) {
				update_post_meta( $post_id, 'shcore_language', 'fa' );
			}

			$done[ $post_type ] = true;
			update_option( 'shcore_language_meta_backfilled', $done );
		}
	}

	/* ------------------------------------------------------------------ */
	/* Admin language switcher — which language an admin/editor is        */
	/* currently managing content in. See the class const docblocks       */
	/* above and english-version/03-content-architecture/admin-language-  */
	/* switcher.md for the full design.                                   */
	/* ------------------------------------------------------------------ */

	/**
	 * The current user's working language — `fa` or `en`, defaulting to
	 * `fa` for anyone who's never picked one.
	 *
	 * @return string
	 */
	public static function get_admin_language() {
		$lang = get_user_meta( get_current_user_id(), self::ADMIN_LANG_META_KEY, true );
		return in_array( $lang, array( 'fa', 'en' ), true ) ? $lang : 'fa';
	}

	/**
	 * Adds the فارسی/English switcher to the admin bar (the dark toolbar
	 * on every wp-admin screen) — same place Polylang/WPML put theirs.
	 * Gated the same way every other editorial feature on this site is —
	 * anyone who can edit content at all, not just admins.
	 *
	 * @param \WP_Admin_Bar $wp_admin_bar Admin bar instance.
	 * @return void
	 */
	public static function add_admin_bar_switcher( $wp_admin_bar ) {
		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		$current = self::get_admin_language();
		$wp_admin_bar->add_node(
			array(
				'id'    => 'shcore-admin-language',
				'title' => __( 'زبان پیشخان: ', 'shola-core' ) . ( 'en' === $current ? 'English' : __( 'فارسی', 'shola-core' ) ),
			)
		);
		$wp_admin_bar->add_node(
			array(
				'id'     => 'shcore-admin-language-fa',
				'parent' => 'shcore-admin-language',
				'title'  => __( 'فارسی', 'shola-core' ),
				'href'   => self::get_admin_language_switch_url( 'fa' ),
			)
		);
		$wp_admin_bar->add_node(
			array(
				'id'     => 'shcore-admin-language-en',
				'parent' => 'shcore-admin-language',
				'title'  => 'English',
				'href'   => self::get_admin_language_switch_url( 'en' ),
			)
		);
	}

	/**
	 * Builds a nonce-protected link that switches the current user's
	 * working language when visited — used by both admin-bar items above.
	 *
	 * @param string $lang Target language.
	 * @return string
	 */
	private static function get_admin_language_switch_url( $lang ) {
		return wp_nonce_url(
			add_query_arg( 'shcore_switch_admin_language', $lang ),
			'shcore_switch_admin_language'
		);
	}

	/**
	 * Handles a click on either admin-bar language link — saves the
	 * choice to this user's account, then redirects back to the exact
	 * page they were already on (with the switch's own query args
	 * stripped off), so clicking the switcher never navigates anywhere
	 * unexpected.
	 *
	 * @return void
	 */
	public static function handle_admin_language_switch() {
		if ( ! isset( $_GET['shcore_switch_admin_language'] ) ) {
			return;
		}
		if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_wpnonce'] ) ), 'shcore_switch_admin_language' ) ) {
			return;
		}

		$lang = sanitize_key( wp_unslash( $_GET['shcore_switch_admin_language'] ) );
		if ( ! in_array( $lang, array( 'fa', 'en' ), true ) ) {
			return;
		}

		update_user_meta( get_current_user_id(), self::ADMIN_LANG_META_KEY, $lang );
		wp_safe_redirect( remove_query_arg( array( 'shcore_switch_admin_language', '_wpnonce' ) ) );
		exit;
	}

	/**
	 * Adds the "زبان" column to every managed post type's admin list
	 * table, right after Title.
	 *
	 * @param string[] $columns Existing columns.
	 * @return string[]
	 */
	public static function add_admin_language_column( $columns ) {
		$new = array();
		foreach ( $columns as $key => $label ) {
			$new[ $key ] = $label;
			if ( 'title' === $key ) {
				$new['shcore_admin_language_col'] = __( 'زبان', 'shola-core' );
			}
		}
		return $new;
	}

	/**
	 * Renders one row's value in the "زبان" column.
	 *
	 * @param string $column  Column being rendered.
	 * @param int    $post_id Post ID for this row.
	 * @return void
	 */
	public static function render_admin_language_column( $column, $post_id ) {
		if ( 'shcore_admin_language_col' !== $column ) {
			return;
		}
		$lang = get_post_meta( $post_id, 'shcore_language', true );
		echo 'en' === $lang ? 'English' : esc_html__( 'فارسی', 'shola-core' );
	}

	/**
	 * Renders the language filter dropdown above a managed post type's
	 * list table — pre-selected to the admin's current working language,
	 * matching what apply_admin_language_default() below does to the
	 * query when no explicit choice has been made yet.
	 *
	 * @return void
	 */
	public static function render_admin_language_filter() {
		global $typenow;
		if ( ! in_array( $typenow, self::MANAGED_POST_TYPES, true ) ) {
			return;
		}

		$selected = isset( $_GET['shcore_language_filter'] ) ? sanitize_key( wp_unslash( $_GET['shcore_language_filter'] ) ) : self::get_admin_language();
		?>
		<select name="shcore_language_filter">
			<option value="all" <?php selected( $selected, 'all' ); ?>><?php esc_html_e( 'همهٔ زبان‌ها', 'shola-core' ); ?></option>
			<option value="fa" <?php selected( $selected, 'fa' ); ?>><?php esc_html_e( 'فارسی', 'shola-core' ); ?></option>
			<option value="en" <?php selected( $selected, 'en' ); ?>>English</option>
		</select>
		<?php
	}

	/**
	 * Filters a managed post type's admin list table by language —
	 * explicit dropdown choice (render_admin_language_filter() above)
	 * wins if present; otherwise defaults to the admin's current working
	 * language, so a list screen always opens already scoped to what
	 * that editor is working on instead of a mix of both languages.
	 * Picking "همهٔ زبان‌ها" (All) shows everything, unfiltered.
	 *
	 * @param \WP_Query $query The query being filtered.
	 * @return void
	 */
	public static function apply_admin_language_default( $query ) {
		global $pagenow, $typenow;
		if ( ! is_admin() || ! $query->is_main_query() || 'edit.php' !== $pagenow || ! in_array( $typenow, self::MANAGED_POST_TYPES, true ) ) {
			return;
		}

		$choice = isset( $_GET['shcore_language_filter'] ) ? sanitize_key( wp_unslash( $_GET['shcore_language_filter'] ) ) : self::get_admin_language();

		if ( ! in_array( $choice, array( 'fa', 'en' ), true ) ) {
			// 'all' (or anything unrecognized) — no filter, show everything.
			return;
		}

		$query->set( 'meta_query', array( array( 'key' => 'shcore_language', 'value' => $choice ) ) ); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- admin list screen, same small per-type post counts as every other meta_query in this plugin.
	}
}