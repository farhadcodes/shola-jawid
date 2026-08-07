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
			return get_search_link( get_search_query() );
		}

		if ( is_category() || is_tag() || is_tax() ) {
			$term = get_queried_object();
			if ( $term instanceof \WP_Term ) {
				return get_term_link( $term );
			}
		}

		if ( is_post_type_archive() ) {
			return get_post_type_archive_link( get_query_var( 'post_type' ) );
		}

		if ( is_front_page() || is_home() ) {
			return home_url( '/' );
		}

		// 404 and any other context this doesn't special-case: fall back
		// to the raw request URL rather than guessing a "real" page it
		// should canonicalize to — there isn't one for a genuine 404.
		return home_url( add_query_arg( array(), $_SERVER['REQUEST_URI'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- read-only URL reconstruction, output through esc_url() at every call site, never used for a redirect or query.
	}

	/**
	 * Outputs the meta description, Open Graph tags, the canonical link
	 * for the non-singular contexts core doesn't cover, and inert
	 * self-referential hreflang tags. Skipped entirely in wp-admin
	 * (this hook only ever fires front-end via wp_head, but the explicit
	 * is_admin() guard costs nothing and documents the intent).
	 *
	 * hreflang here is scaffolding only — CLAUDE.md §1: English isn't
	 * live in this phase, so both tags point at the same self URL rather
	 * than a real fa/en pair. This avoids having to re-touch this file
	 * when the bilingual rollout happens; it does not claim a second
	 * language exists today.
	 *
	 * @return void
	 */
	public static function output_meta_tags() {
		if ( is_admin() ) {
			return;
		}

		$description = self::get_description();
		$canonical   = self::get_canonical_url();
		$title       = self::get_title();
		$is_singular = is_singular();
		$image_url   = '';

		if ( $is_singular && has_post_thumbnail() ) {
			$image_url = get_the_post_thumbnail_url( null, 'shola_card' );
		}

		echo "\n<!-- Shola Core SEO (class-seo.php) -->\n";

		printf( '<meta name="description" content="%s" />' . "\n", esc_attr( $description ) );

		if ( ! is_singular() ) {
			// Singular already gets rel_canonical() from core; this fills
			// the gap for archives/search/front page only, so we never
			// print two canonical tags on the same response.
			printf( '<link rel="canonical" href="%s" />' . "\n", esc_url( $canonical ) );
		}

		printf( '<link rel="alternate" hreflang="fa" href="%s" />' . "\n", esc_url( $canonical ) );
		printf( '<link rel="alternate" hreflang="x-default" href="%s" />' . "\n", esc_url( $canonical ) );

		printf( '<meta property="og:site_name" content="%s" />' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
		printf( '<meta property="og:type" content="%s" />' . "\n", esc_attr( $is_singular ? 'article' : 'website' ) );
		printf( '<meta property="og:title" content="%s" />' . "\n", esc_attr( $title ) );
		printf( '<meta property="og:description" content="%s" />' . "\n", esc_attr( $description ) );
		printf( '<meta property="og:url" content="%s" />' . "\n", esc_url( $canonical ) );
		printf( '<meta property="og:locale" content="%s" />' . "\n", esc_attr( is_rtl() ? 'fa_AF' : get_locale() ) );

		if ( $image_url ) {
			printf( '<meta property="og:image" content="%s" />' . "\n", esc_url( $image_url ) );
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
