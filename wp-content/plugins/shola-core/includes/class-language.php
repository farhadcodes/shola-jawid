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
	 * Option name for "hide the English switcher link" — lets Farhad turn
	 * off the public EN link (masthead + popup menu) without touching
	 * the /en/ URLs themselves, which stay reachable either way.
	 *
	 * @var string
	 */
	const HIDE_SWITCHER_OPTION = 'shcore_hide_language_switcher';

	/**
	 * Option names for the English site name/tagline — without these, the
	 * <title> tag, meta description fallback, and og:site_name on every
	 * /en/ page show the Persian site identity (get_bloginfo() has no
	 * language variant of its own). Both fall back to the real Persian
	 * value when left empty. Full story: docs/CHANGELOG.md 2026-11-27.
	 *
	 * @var string
	 */
	const SITE_NAME_EN_OPTION    = 'shcore_site_name_en';
	const SITE_TAGLINE_EN_OPTION = 'shcore_site_tagline_en';

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

		// Switches locale to English early, so is_rtl()/dir/dates are
		// correct before Persian Calendar's own init check runs. Full
		// story: docs/CHANGELOG.md 2026-11-28.
		add_action( 'plugins_loaded', array( __CLASS__, 'maybe_switch_locale_for_english_request' ), 1 );
		// WP core resets the locale again after plugins_loaded, so this
		// has to run a second time, later, to actually stick.
		add_action( 'after_setup_theme', array( __CLASS__, 'maybe_switch_locale_for_english_request' ), 999 );

		// search.php/archive-announcement.php/archive-editors_pick.php use
		// WP's main query directly, so pre_get_posts reaches them. Every
		// other listing template calls add_language_filter() on its own
		// query args instead.
		add_action( 'pre_get_posts', array( __CLASS__, 'filter_main_query_by_language' ) );

		// Every post was reachable at both its bare URL and its /en/
		// mirror regardless of its real language — redirects to the one
		// correct URL. See docs/CHANGELOG.md 2026-11-28.
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

		// Settings → English Version: the one public on/off switch for the EN link.
		add_action( 'admin_menu', array( __CLASS__, 'add_settings_page' ) );
		add_action( 'admin_init', array( __CLASS__, 'register_hide_switcher_setting' ) );

		// Swaps the site name/tagline for their English versions on /en/
		// pages — fixes <title>, the meta-description fallback, and
		// og:site_name all at once, since all three read get_bloginfo().
		add_filter( 'bloginfo', array( __CLASS__, 'filter_bloginfo_for_english' ), 10, 2 );

		// A term archive's <title> tag used core's own term name (always
		// Persian) even on /en/ pages, while the page's own H1 already
		// used the term's shcore_term_name_en override — this makes the
		// <title> tag agree with what the page itself shows.
		add_filter( 'document_title_parts', array( __CLASS__, 'filter_document_title_for_english_term' ) );
	}

	/**
	 * Whether the public EN/فارسی switcher link should be hidden right
	 * now. The /en/ URLs themselves keep working either way — this only
	 * controls whether the link to them is shown.
	 *
	 * @return bool
	 */
	public static function is_switcher_hidden() {
		return (bool) get_option( self::HIDE_SWITCHER_OPTION, false );
	}

	/**
	 * Registers the hide-switcher option and the English site-identity
	 * overrides.
	 *
	 * @return void
	 */
	public static function register_hide_switcher_setting() {
		register_setting(
			'shcore_language_settings',
			self::HIDE_SWITCHER_OPTION,
			array(
				'type'              => 'boolean',
				'sanitize_callback' => 'rest_sanitize_boolean',
				'default'           => false,
			)
		);
		register_setting(
			'shcore_language_settings',
			self::SITE_NAME_EN_OPTION,
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => '',
			)
		);
		register_setting(
			'shcore_language_settings',
			self::SITE_TAGLINE_EN_OPTION,
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => '',
			)
		);
	}

	/**
	 * Swaps `get_bloginfo( 'name' )`/`get_bloginfo( 'description' )` for
	 * their English-override option on an /en/ request, falling back to
	 * the real Persian value when no override is saved. Every other
	 * `$show` value (e.g. 'version', 'charset') passes through untouched.
	 *
	 * @param string $output The value get_bloginfo() is about to return.
	 * @param string $show   Which bloginfo field this is.
	 * @return string
	 */
	public static function filter_bloginfo_for_english( $output, $show ) {
		if ( 'en' !== self::current() ) {
			return $output;
		}

		if ( 'name' === $show ) {
			$override = get_option( self::SITE_NAME_EN_OPTION, '' );
		} elseif ( 'description' === $show ) {
			$override = get_option( self::SITE_TAGLINE_EN_OPTION, '' );
		} else {
			return $output;
		}

		return $override ? $override : $output;
	}

	/**
	 * Swaps the 'title' part of a document title for its English override
	 * on an /en/ request — a term archive's `shcore_term_name_en` (same
	 * override the page's own H1 already uses), or a shared/structural
	 * Page's `shcore_page_title_en` (e.g. کتابخانه shown at the same URL
	 * in both languages, so its one title field can't hold both).
	 *
	 * @param array $title_parts Core's own document title parts.
	 * @return array
	 */
	public static function filter_document_title_for_english_term( $title_parts ) {
		if ( 'en' !== self::current() || empty( $title_parts['title'] ) ) {
			return $title_parts;
		}

		$queried_object = get_queried_object();

		if ( $queried_object instanceof \WP_Term ) {
			$name_en = get_term_meta( $queried_object->term_id, 'shcore_term_name_en', true );
			if ( $name_en ) {
				$title_parts['title'] = $name_en;
			}
		} elseif ( $queried_object instanceof \WP_Post ) {
			$title_en = get_post_meta( $queried_object->ID, 'shcore_page_title_en', true );
			if ( $title_en ) {
				$title_parts['title'] = $title_en;
			}
		}

		return $title_parts;
	}

	/**
	 * Registers the settings page under Settings → نسخهٔ انگلیسی.
	 *
	 * @return void
	 */
	public static function add_settings_page() {
		add_options_page(
			__( 'نسخهٔ انگلیسی سایت', 'shola-core' ),
			__( 'نسخهٔ انگلیسی سایت', 'shola-core' ),
			'manage_options',
			'shcore-english-version',
			array( __CLASS__, 'render_settings_page' )
		);
	}

	/**
	 * Renders the settings page: one checkbox.
	 *
	 * @return void
	 */
	public static function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'نسخهٔ انگلیسی سایت', 'shola-core' ); ?></h1>
			<form method="post" action="options.php">
				<?php settings_fields( 'shcore_language_settings' ); ?>
				<table class="form-table">
					<tr>
						<th scope="row"><?php esc_html_e( 'نمایش لینک تغییر زبان', 'shola-core' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="<?php echo esc_attr( self::HIDE_SWITCHER_OPTION ); ?>" value="1" <?php checked( self::is_switcher_hidden() ); ?>>
								<?php esc_html_e( 'لینک «EN / فارسی» را از سربرگ و منوی سایت پنهان کن', 'shola-core' ); ?>
							</label>
							<p class="description"><?php esc_html_e( 'نسخهٔ انگلیسی سایت همچنان در آدرس /en/ در دسترس می‌ماند؛ این گزینه فقط لینک آن را از نوار بالا و منو پنهان می‌کند.', 'shola-core' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'نام سایت به انگلیسی', 'shola-core' ); ?></th>
						<td>
							<input type="text" class="regular-text" dir="ltr" name="<?php echo esc_attr( self::SITE_NAME_EN_OPTION ); ?>" value="<?php echo esc_attr( get_option( self::SITE_NAME_EN_OPTION, '' ) ); ?>">
							<p class="description"><?php esc_html_e( 'در عنوان تب مرورگر و اطلاعات اشتراک‌گذاری صفحات انگلیسی استفاده می‌شود. اگر خالی بماند، همان نام فارسی سایت نشان داده می‌شود.', 'shola-core' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'شعار سایت به انگلیسی', 'shola-core' ); ?></th>
						<td>
							<input type="text" class="regular-text" dir="ltr" name="<?php echo esc_attr( self::SITE_TAGLINE_EN_OPTION ); ?>" value="<?php echo esc_attr( get_option( self::SITE_TAGLINE_EN_OPTION, '' ) ); ?>">
							<p class="description"><?php esc_html_e( 'در توضیح متای صفحات انگلیسی که چکیدهٔ خاص خود را ندارند (مانند صفحهٔ اصلی) استفاده می‌شود. اگر خالی بماند، همان شعار فارسی سایت نشان داده می‌شود.', 'shola-core' ); ?></p>
						</td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Switches the active locale to English for any front-end /en/
	 * request, so is_rtl()/dir/dates become correct for it.
	 *
	 * @return void
	 */
	public static function maybe_switch_locale_for_english_request() {
		if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) || wp_doing_cron() ) {
			return;
		}

		$path = (string) wp_parse_url( isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '', PHP_URL_PATH );
		$path = untrailingslashit( $path );

		/*
		 * The masthead-date REST endpoint (inc/template-tags.php,
		 * shola_register_masthead_date_route()) carries no /en/ URL
		 * prefix at all — it's `/wp-json/shola/v1/masthead-date?lang=en`
		 * — so it needs its own, separate match here. Checked this early
		 * (plugins_loaded priority 1, same as the /en/ page case above)
		 * because Persian Calendar's own Jalali-conversion gate decides
		 * once, early in the request, same reason the page case below
		 * needs its *second* switch at after_setup_theme: waiting until
		 * inside the REST callback itself was too late — found live, not
		 * assumed, when the endpoint kept returning the Jalali date even
		 * with switch_to_locale() called there.
		 */
		$is_english_masthead_date_request = false !== strpos( $path, '/masthead-date' )
			&& isset( $_GET['lang'] ) && 'en' === $_GET['lang']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only route match, not a form submission.

		if ( '/en' === $path || 0 === strpos( $path, '/en/' ) || $is_english_masthead_date_request ) {
			switch_to_locale( 'en_US' );
			self::load_translation_files();
		}
	}

	/**
	 * WordPress only auto-finds a plugin's own .mo files, not a theme's —
	 * themes need this explicit call or their English strings never load.
	 * Called right after switch_to_locale() above (not on plugins_loaded's
	 * own, so it re-reads the now-English locale, not the default one),
	 * and again safe to repeat since this function itself runs twice.
	 *
	 * @return void
	 */
	private static function load_translation_files() {
		load_theme_textdomain( 'shola-jawid', get_template_directory() . '/languages' );
		load_plugin_textdomain( 'shola-core', false, dirname( plugin_basename( SHCORE_PATH . 'shola-core.php' ) ) . '/languages' );
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
	 * Mirrors every existing rewrite rule under an `en/` prefix, so any
	 * URL that works today also works with `en/` in front.
	 *
	 * @param string[] $rules Existing rewrite rules (regex => query string).
	 * @return string[]
	 */
	public static function add_english_prefixed_rules( $rules ) {
		// No real "English version" to mirror: REST, sitemap (handled
		// separately), feeds, embeds.
		$skip_if_contains = array( 'rest_route=', 'sitemap', 'feed=', 'embed=true' );

		$english_rules = array();
		foreach ( $rules as $regex => $query ) {
			foreach ( $skip_if_contains as $needle ) {
				if ( false !== strpos( $query, $needle ) ) {
					continue 2;
				}
			}

			// Keep the ^ anchor at the actual start, now before "en/".
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
	 * Landing-page slugs (نشرات, کتابخانه, ...) that render the same one
	 * Page at the same path in both languages — not a translation pair.
	 *
	 * @var string[]
	 */
	const STRUCTURAL_PAGE_SLUGS = array( 'publications', 'library', 'reports', 'party-documents', 'party-publications', 'topics', 'selected', 'leaflets' );

	/**
	 * Redirects a singular post/Page to its one correct URL when the
	 * /en/ prefix disagrees with that post's real language.
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

		if ( self::current() === $post_lang ) {
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
	 * Same condition as add_language_filter(), applied to WP's own main
	 * query (search.php and the two archive templates that use it directly).
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
	 * One-time fix: posts that existed before shcore_language did have no
	 * real meta row at all (the default only affects reads, not writes),
	 * so they matched neither language's filter. Sets them all to `fa`.
	 *
	 * @return void
	 */
	public static function backfill_legacy_language_meta() {
		// Tracked per post type, not one flag, so adding a new type later
		// (e.g. `page`) doesn't get silently skipped by an already-done flag.
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

		$query->set(
			'meta_query', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query -- admin list screen, same small per-type post counts as every other meta_query in this plugin.
			array(
				array(
					'key'   => 'shcore_language',
					'value' => $choice,
				),
			)
		);
	}
}