<?php
/**
 * Custom SEO — meta description, Open Graph tags, canonical URL for
 * non-singular views (WP core already outputs rel_canonical for
 * singular content by default — verified live before writing this,
 * not assumed), real hreflang tags, and sitemap.xml theming. No SEO
 * plugin, per CLAUDE.md §3.
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
		// Core's own canonical tag for singular content never carries
		// the /en/ prefix on its own — fixes that.
		add_filter( 'get_canonical_url', array( __CLASS__, 'filter_singular_canonical_url' ) );
		// Checked live: core's sitemap doesn't add /en/ either. Fixes it
		// per entry.
		add_filter( 'wp_sitemaps_posts_entry', array( __CLASS__, 'filter_sitemap_post_entry' ), 10, 2 );
	}

	/**
	 * Prefixes an English post's own sitemap entry with `/en/`.
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
	 * Adds the `/en/` prefix to core's own singular canonical URL.
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
	 * The current page's canonical URL, language-prefixed. Covers the
	 * gap core's own rel_canonical() leaves open (archives, search,
	 * front page); also reused for og:url everywhere.
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

		// 404 and anything else: fall back to the raw request URL, there's no "real" page to canonicalize to.
		return home_url( add_query_arg( array(), $_SERVER['REQUEST_URI'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- read-only URL reconstruction, output through esc_url() at every call site, never used for a redirect or query.
	}

	/**
	 * Adds the `/en/` prefix to a bare URL, only when the current
	 * request is in the English language context.
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
	 * A taxonomy term's URL in an explicitly given language (not
	 * necessarily the current request's). Duplicates the theme's own
	 * shola_build_term_link_for_lang() rather than calling it — a
	 * plugin must not depend on a theme function (CLAUDE.md §2).
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
	 * This page's real hreflang targets, as an array with up to two keys
	 * ('fa'/'en'). Singular content gets a second language only with a
	 * real, published shcore_translation_id pairing; every archive/
	 * search/front-page context always gets both (the /en/ space always
	 * structurally exists there).
	 *
	 * @return array{fa?: string, en?: string}
	 */
	private static function get_hreflang_urls() {
		if ( is_singular() ) {
			$post = get_queried_object();
			if ( ! ( $post instanceof \WP_Post ) ) {
				return array();
			}

			// Structural listing Pages render the same Page at the same path in both languages.
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
	 * for the non-singular contexts core doesn't cover, and real
	 * hreflang tags. Skipped entirely in wp-admin.
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
			// Singular already gets rel_canonical() from core; this fills the gap for everything else.
			printf( '<link rel="canonical" href="%s" />' . "\n", esc_url( $canonical ) );
		}

		// x-default stays on the Persian URL when one exists — Persian is the site's default language.
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
			// Only the dedicated site-wide image has fixed, known dimensions worth hinting.
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
