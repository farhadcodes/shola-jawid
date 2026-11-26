<?php
/**
 * Custom SEO — meta description, Open Graph tags, canonical URL for
 * non-singular views (WP core already outputs rel_canonical for
 * singular content by default — verified live before writing this,
 * not assumed), inert self-referential hreflang scaffolding, and
 * sitemap.xml theming. No SEO plugin, per CLAUDE.md §3.
 *
 * @package SholaCore
 */

namespace SholaCore;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Meta tags + sitemap customization.
 */
class SEO {

	/**
	 * Hook registration.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'wp_head', array( __CLASS__, 'output_meta_tags' ), 1 );
		add_filter( 'wp_sitemaps_add_provider', array( __CLASS__, 'remove_unwanted_sitemap_providers' ), 10, 2 );
		add_filter( 'wp_sitemaps_taxonomies', array( __CLASS__, 'remove_unwanted_sitemap_taxonomies' ) );

		/*
		 * English-version rollout, 2026-11-28 — core's own rel_canonical()
		 * (singular content only) builds its URL from get_permalink(),
		 * which never carries the `/en/` prefix on its own (confirmed live:
		 * nothing in this codebase filters post permalinks to add it — the
		 * prefix is purely a rewrite-rule mirror, not a stored part of any
		 * post's real permalink). Without this filter, an English single
		 * post's own canonical tag would point at its bare, un-prefixed
		 * URL — which Language::redirect_mismatched_singular_language()
		 * then immediately 301s away from, so the "canonical" URL a search
		 * engine would be told about isn't even the URL it resolves to.
		 */
		add_filter( 'get_canonical_url', array( __CLASS__, 'filter_singular_canonical_url' ) );

		/*
		 * Checked live, not assumed (per this plan's own instruction):
		 * does wp_sitemap.xml already include English URLs correctly
		 * once English posts exist? No — every post's sitemap entry uses
		 * `get_permalink()` internally, same bare (never /en/-prefixed)
		 * URL get_canonical_url() had the same problem with above, so an
		 * English post's own listing in wp-sitemap-posts-{type}-N.xml
		 * pointed at the URL that Language::
		 * redirect_mismatched_singular_language() now 301s away from —
		 * confirmed by curling wp-sitemap-posts-post-1.xml directly and
		 * finding the one real English demo article listed without /en/.
		 * Taxonomy-archive sitemap entries are a smaller, deliberately
		 * deferred gap: each term gets one entry regardless of language
		 * today, same as before this fix — adding a second, English
		 * entry per term would mean injecting new sitemap rows, not just
		 * correcting existing ones, a larger change left for a later
		 * round if Farhad wants it.
		 */
		add_filter( 'wp_sitemaps_posts_entry', array( __CLASS__, 'filter_sitemap_post_entry' ), 10, 2 );
	}

	/**
	 * Prefixes an English post's own sitemap entry with `/en/` — see
	 * init()'s registration above for the live bug this fixes.
	 *
	 * @param array    $sitemap_entry Entry data (at least a 'loc' key).
	 * @param \WP_Post $post          The post this entry is for.
	 * @return array
	 */
	public static function filter_sitemap_post_entry( $sitemap_entry, $post ) {
		if ( empty( $sitemap_entry['loc'] ) || 'en' !== get_post_meta( $post->ID, 'shcore_language', true ) ) {
			return $sitemap_entry;
		}

		$path                 = (string) wp_parse_url( $sitemap_entry['loc'], PHP_URL_PATH );
		$sitemap_entry['loc'] = home_url( '/en' . $path );

		return $sitemap_entry;
	}

	/**
	 * Adds the `/en/` prefix to core's own singular canonical URL when
	 * the current request is in the English language context — see
	 * init()'s registration above for why this is needed at all.
	 *
	 * @param string $canonical_url Core's own computed canonical URL.
	 * @return string
	 */
	public static function filter_singular_canonical_url( $canonical_url ) {
		return self::prefix_for_current_language( $canonical_url );
	}

	/**
	 * Builds the page description used for both the meta description tag
	 * and og:description — post excerpt for singular content, term
	 * description for taxonomy/publication archives, falls back to the
	 * site tagline everywhere else (front page, search, 404). Never
	 * empty-string; WordPress's own tagline is the last-resort fallback,
	 * same as v6's own footer tagline text.
	 *
	 * @return string Plain text, not yet escaped for HTML attribute output.
	 */
	private static function get_description() {
		if ( is_singular() ) {
			$excerpt = get_the_excerpt();
			if ( $excerpt ) {
				return wp_strip_all_tags( $excerpt );
			}
		} elseif ( is_category() || is_tag() || is_tax() ) {
			$term = get_queried_object();
			if ( $term instanceof \WP_Term && $term->description ) {
				return wp_strip_all_tags( $term->description );
			}
		}

		return get_bloginfo( 'description' );
	}

	/**
	 * Builds the page title used for og:title — reuses
	 * wp_get_document_title() (the same core function behind the
	 * <title> tag, already correct site-wide via add_theme_support(
	 * 'title-tag' )) rather than reimplementing title logic per
	 * template type.
	 *
	 * @return string
	 */
	private static function get_title() {
		return wp_get_document_title();
	}

	/**
	 * The current page's canonical URL. WP core already outputs
	 * `<link rel="canonical">` via `rel_canonical()` on `wp_head` for
	 * singular content by default (confirmed live, not assumed) — this
	 * covers the gap core leaves open (archives, search, the front
	 * page) and is also reused for og:url everywhere, singular included,
	 * so og:url and the canonical link always agree.
	 *
	 * Real bug caught during live verification: an earlier version
	 * built this from `$wp->request`, which holds only the matched
	 * rewrite *path* — empty for a query-string-only view like search
	 * (`?s=...`), which made every search page's canonical silently
	 * resolve to the front page instead of the search URL. Fixed by
	 * using WP's own per-context URL functions instead of reconstructing
	 * one generically.
	 *
	 * @return string
	 */
	private static function get_canonical_url() {
		if ( is_search() ) {
			return self::prefix_for_current_language( get_search_link( get_search_query() ) );
		}

		if ( is_category() || is_tag() || is_tax() ) {
			$term = get_queried_object();
			if ( $term instanceof \WP_Term ) {
				return self::get_term_url_for_lang( $term, \SholaCore\Language::current() );
			}
		}

		if ( is_post_type_archive() ) {
			return self::prefix_for_current_language( get_post_type_archive_link( get_query_var( 'post_type' ) ) );
		}

		if ( is_front_page() || is_home() ) {
			return ( 'en' === \SholaCore\Language::current() ) ? home_url( '/en/' ) : home_url( '/' );
		}

		// 404 and any other context this doesn't special-case: fall back
		// to the raw request URL rather than guessing a "real" page it
		// should canonicalize to — there isn't one for a genuine 404.
		return home_url( add_query_arg( array(), $_SERVER['REQUEST_URI'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- read-only URL reconstruction, output through esc_url() at every call site, never used for a redirect or query.
	}

	/**
	 * Adds the `/en/` prefix to a bare (Persian-shaped) URL when the
	 * current request is in the English language context — added
	 * 2026-11-28, English-version rollout. Shared by every
	 * get_canonical_url() branch that calls a core/theme function
	 * returning the bare URL (search, post-type archive): those
	 * functions have no language awareness of their own.
	 *
	 * @param string $url Bare URL.
	 * @return string
	 */
	private static function prefix_for_current_language( $url ) {
		if ( 'en' !== \SholaCore\Language::current() || ! $url ) {
			return $url;
		}
		$path = (string) wp_parse_url( $url, PHP_URL_PATH );
		return home_url( '/en' . $path );
	}

	/**
	 * A taxonomy term's URL in a given (explicit, not "current request")
	 * language — the term's own slug segment swapped for its English one
	 * (`shcore_term_slug_en` term-meta) plus the `/en/` prefix. Same
	 * logic as the theme's own `shola_build_term_link_for_lang()`
	 * (inc/template-tags.php), deliberately duplicated rather than
	 * called: CLAUDE.md §2 — this plugin must never call a theme
	 * function, since it has to keep working if the active theme is
	 * ever something other than Shola Jawid. Takes an explicit `$lang`
	 * (rather than always reading `Language::current()`) so
	 * get_hreflang_urls() below can ask for both languages' URLs
	 * regardless of which one the current request is actually in.
	 *
	 * @param \WP_Term $term Term object.
	 * @param string   $lang 'fa' or 'en'.
	 * @return string Empty string if the term has no real link at all.
	 */
	private static function get_term_url_for_lang( $term, $lang ) {
		$fa_link = get_term_link( $term );
		if ( is_wp_error( $fa_link ) ) {
			return '';
		}
		if ( 'en' !== $lang ) {
			return $fa_link;
		}

		$slug_en = get_term_meta( $term->term_id, 'shcore_term_slug_en', true );
		$slug_en = $slug_en ? $slug_en : $term->slug;

		$path     = (string) wp_parse_url( $fa_link, PHP_URL_PATH );
		$segments = array_values( array_filter( explode( '/', $path ) ) );
		foreach ( $segments as $i => $segment ) {
			if ( $segment === $term->slug ) {
				$segments[ $i ] = $slug_en;
			}
		}

		return home_url( '/en/' . implode( '/', $segments ) . '/' );
	}

	/**
	 * This page's real hreflang targets — english-version/05-technical-
	 * implementation-plan/seo-and-sitemap-updates.md's "from inert to
	 * real" fix. Returns an associative array with up to two keys
	 * ('fa'/'en'), each the real URL for that language, or an empty
	 * array if this context has no sensible hreflang pair at all (404
	 * and anything else get_canonical_url() falls through to generically).
	 *
	 * Singular content only gets a second language's URL when a real,
	 * *published* pairing actually exists (`shcore_translation_id`) —
	 * hreflang means "this exact page, translated," never "related
	 * content in another language," so an untranslated post gets only
	 * its own one real tag, not a guessed/fallback URL. Every
	 * non-singular context (archives, search, the front page) always
	 * gets both: the `/en/` URL space structurally mirrors every one of
	 * these regardless of whether English content exists there yet (the
	 * page itself still exists and is reachable, just possibly showing
	 * zero results — a legitimate state, not a missing page).
	 *
	 * @return array{fa?: string, en?: string}
	 */
	private static function get_hreflang_urls() {
		if ( is_singular() ) {
			$post = get_queried_object();
			if ( ! ( $post instanceof \WP_Post ) ) {
				return array();
			}

			/*
			 * Structural listing Pages (نشرات, کتابخانه, ...) -- same
			 * Language::STRUCTURAL_PAGE_SLUGS exclusion as
			 * redirect_mismatched_singular_language(): one Page row
			 * renders at the same path in both languages, so both
			 * hreflang URLs always exist, same as a post-type archive.
			 */
			if ( 'page' === $post->post_type && in_array( $post->post_name, \SholaCore\Language::STRUCTURAL_PAGE_SLUGS, true ) ) {
				$path = (string) wp_parse_url( get_permalink( $post ), PHP_URL_PATH );
				return array( 'fa' => home_url( $path ), 'en' => home_url( '/en' . $path ) );
			}

			$own_lang = get_post_meta( $post->ID, 'shcore_language', true );
			$own_lang = ( 'en' === $own_lang ) ? 'en' : 'fa';
			$own_path = (string) wp_parse_url( get_permalink( $post ), PHP_URL_PATH );
			$urls     = array(
				$own_lang => ( 'en' === $own_lang ) ? home_url( '/en' . $own_path ) : home_url( $own_path ),
			);

			$paired_id = (int) get_post_meta( $post->ID, 'shcore_translation_id', true );
			if ( $paired_id && 'publish' === get_post_status( $paired_id ) ) {
				$other_lang          = ( 'en' === $own_lang ) ? 'fa' : 'en';
				$other_path          = (string) wp_parse_url( get_permalink( $paired_id ), PHP_URL_PATH );
				$urls[ $other_lang ] = ( 'en' === $other_lang ) ? home_url( '/en' . $other_path ) : home_url( $other_path );
			}

			return $urls;
		}

		if ( is_category() || is_tag() || is_tax() ) {
			$term = get_queried_object();
			if ( ! ( $term instanceof \WP_Term ) ) {
				return array();
			}
			$fa = self::get_term_url_for_lang( $term, 'fa' );
			return $fa ? array( 'fa' => $fa, 'en' => self::get_term_url_for_lang( $term, 'en' ) ) : array();
		}

		if ( is_post_type_archive() ) {
			$fa = get_post_type_archive_link( get_query_var( 'post_type' ) );
			if ( ! $fa ) {
				return array();
			}
			$path = (string) wp_parse_url( $fa, PHP_URL_PATH );
			return array( 'fa' => $fa, 'en' => home_url( '/en' . $path ) );
		}

		if ( is_search() ) {
			$fa    = get_search_link( get_search_query() );
			$path  = (string) wp_parse_url( $fa, PHP_URL_PATH );
			$query = (string) wp_parse_url( $fa, PHP_URL_QUERY );
			$en    = home_url( '/en' . $path ) . ( $query ? '?' . $query : '' );
			return array( 'fa' => $fa, 'en' => $en );
		}

		if ( is_front_page() || is_home() ) {
			return array( 'fa' => home_url( '/' ), 'en' => home_url( '/en/' ) );
		}

		return array();
	}

	/**
	 * The image behind og:image — never empty, so every shared link gets
	 * a thumbnail, not just posts with a real featured image. Three
	 * cases, in order: (1) a singular post/document/issue with a real
	 * featured image uses it at the existing `shola_card` size; (2) a
	 * singular post without one falls back to the same
	 * assets/images/fallback.png every card/hero on the site already
	 * falls back to (shola_get_featured_image()'s logic, reused here
	 * rather than duplicated as a new rule) — a `has_post_thumbnail()`
	 * check alone previously missed this case entirely, which is what
	 * caused link previews to show no image at all; (3) every
	 * non-singular view (front page, archives, search) uses a dedicated
	 * site-wide share image, assets/images/og-share.png — Farhad-
	 * approved artwork (crimson background, masthead wordmark, the same
	 * flame/halftone treatment as fallback.png), sized at the standard
	 * 1200×630 og:image dimension.
	 *
	 * @return string
	 */
	private static function get_share_image_url() {
		if ( is_singular() ) {
			if ( has_post_thumbnail() ) {
				return get_the_post_thumbnail_url( null, 'shola_card' );
			}
			return get_theme_file_uri( 'assets/images/fallback.png' );
		}

		return get_theme_file_uri( 'assets/images/og-share.png' );
	}

	/**
	 * Outputs the meta description, Open Graph tags, the canonical link
	 * for the non-singular contexts core doesn't cover, and real hreflang
	 * tags. Skipped entirely in wp-admin (this hook only ever fires
	 * front-end via wp_head, but the explicit is_admin() guard costs
	 * nothing and documents the intent).
	 *
	 * hreflang changed 2026-11-28, English-version rollout, from inert
	 * self-referential scaffolding (both tags pointing at the same self
	 * URL — correct for that phase, since English didn't exist yet) to
	 * real fa/en pairs via get_hreflang_urls() — see that method's own
	 * docblock and english-version/05-technical-implementation-plan/
	 * seo-and-sitemap-updates.md for the full reasoning.
	 *
	 * @return void
	 */
	public static function output_meta_tags() {
		if ( is_admin() ) {
			return;
		}

		$description   = self::get_description();
		$canonical     = self::get_canonical_url();
		$title         = self::get_title();
		$is_singular   = is_singular();
		$image_url     = self::get_share_image_url();
		$hreflang_urls = self::get_hreflang_urls();

		echo "\n<!-- Shola Core SEO (class-seo.php) -->\n";

		printf( '<meta name="description" content="%s" />' . "\n", esc_attr( $description ) );

		if ( ! is_singular() ) {
			// Singular already gets rel_canonical() from core (filtered
			// to carry the /en/ prefix via filter_singular_canonical_url()
			// above); this fills the gap for archives/search/front page
			// only, so we never print two canonical tags on the same
			// response.
			printf( '<link rel="canonical" href="%s" />' . "\n", esc_url( $canonical ) );
		}

		/*
		 * x-default stays pointing at the Persian version when it exists
		 * — Persian is the site's primary/default language (CLAUDE.md
		 * §1) — falling back to whichever single language actually has a
		 * URL here (an English-only context has no Persian URL to point
		 * x-default at instead).
		 */
		if ( isset( $hreflang_urls['fa'] ) ) {
			printf( '<link rel="alternate" hreflang="fa" href="%s" />' . "\n", esc_url( $hreflang_urls['fa'] ) );
		}
		if ( isset( $hreflang_urls['en'] ) ) {
			printf( '<link rel="alternate" hreflang="en" href="%s" />' . "\n", esc_url( $hreflang_urls['en'] ) );
		}
		if ( $hreflang_urls ) {
			$x_default = isset( $hreflang_urls['fa'] ) ? $hreflang_urls['fa'] : reset( $hreflang_urls );
			printf( '<link rel="alternate" hreflang="x-default" href="%s" />' . "\n", esc_url( $x_default ) );
		}

		printf( '<meta property="og:site_name" content="%s" />' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
		printf( '<meta property="og:type" content="%s" />' . "\n", esc_attr( $is_singular ? 'article' : 'website' ) );
		printf( '<meta property="og:title" content="%s" />' . "\n", esc_attr( $title ) );
		printf( '<meta property="og:description" content="%s" />' . "\n", esc_attr( $description ) );
		printf( '<meta property="og:url" content="%s" />' . "\n", esc_url( $canonical ) );
		printf( '<meta property="og:locale" content="%s" />' . "\n", esc_attr( is_rtl() ? 'fa_AF' : get_locale() ) );
		printf( '<meta property="og:image" content="%s" />' . "\n", esc_url( $image_url ) );

		if ( ! $is_singular ) {
			// Only the dedicated site-wide image has known, fixed
			// dimensions worth hinting — a post's real featured image
			// varies per upload, and platforms fetch it to measure
			// anyway.
			printf( '<meta property="og:image:width" content="1200" />' . "\n" );
			printf( '<meta property="og:image:height" content="630" />' . "\n" );
		}
	}

	/**
	 * Drops sitemap providers with no real front-end destination in this
	 * theme, rather than leaving core's full default set: `users` (no
	 * author-archive template exists — author.php was never in the
	 * IA doc's page-to-template map — so publishing usernames/author
	 * URLs here would point at WP's bare fallback, not a real page),
	 * `post_format` (post formats aren't a content destination on this
	 * site, just an internal flag distinguishing مقاله/یادداشت), and
	 * `category` (native WP categories are unused — this project's
	 * classification is the custom `topic` taxonomy instead).
	 *
	 * @param bool|\WP_Sitemaps_Provider $provider Provider instance, or false if already removed by another filter.
	 * @param string                     $name     Provider name.
	 * @return bool|\WP_Sitemaps_Provider
	 */
	public static function remove_unwanted_sitemap_providers( $provider, $name ) {
		if ( in_array( $name, array( 'users' ), true ) ) {
			return false;
		}

		return $provider;
	}

	/**
	 * Removes `post_format` and native `category` from the taxonomies
	 * sitemap — see the class docblock reasoning above
	 * remove_unwanted_sitemap_providers(); this is the correct filter
	 * for excluding individual taxonomies (`wp_sitemaps_add_provider`
	 * only controls whole provider types — posts/taxonomies/users — not
	 * which taxonomies within the taxonomies provider are included).
	 *
	 * @param \WP_Taxonomy[] $taxonomies Taxonomy objects keyed by name.
	 * @return \WP_Taxonomy[]
	 */
	public static function remove_unwanted_sitemap_taxonomies( $taxonomies ) {
		unset( $taxonomies['post_format'], $taxonomies['category'] );

		return $taxonomies;
	}
}
