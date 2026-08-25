<?php
/**
 * Registers the topic, publication, and collection taxonomies and their
 * fixed term vocabularies, per the IA doc §6.
 *
 * @package SholaCore
 */

namespace SholaCore;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Taxonomy registration and fixed-term seeding.
 */
class Taxonomies {

	/**
	 * Hook registration.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_taxonomies' ) );
		add_action( 'init', array( __CLASS__, 'register_topic_rewrite' ) );
		add_action( 'init', array( __CLASS__, 'remove_core_category_from_post' ), 20 );
		add_filter( 'post_link', array( __CLASS__, 'filter_post_permalink' ), 10, 2 );
	}

	/**
	 * Remove WP core's built-in Category taxonomy from the `post` type.
	 * `topic` is the content model's actual categorization taxonomy for
	 * articles (IA doc §6); leaving Categories attached too would show
	 * editors a redundant, unused metabox/panel and let posts silently
	 * default to "Uncategorized" for no reason. Priority 20 so this runs
	 * after core's own post-type/taxonomy registration (both on `init`
	 * priority 10 and 0 respectively) has already happened.
	 *
	 * Both calls are needed, not just one: remove_post_type_support()
	 * controls the classic-editor metabox and post_type_supports() checks;
	 * unregister_taxonomy_for_object_type() controls whether the block
	 * editor's REST-driven taxonomy panel appears at all (Gutenberg reads
	 * the taxonomy's object_type association, not post_type_supports()).
	 *
	 * @return void
	 */
	public static function remove_core_category_from_post() {
		remove_post_type_support( 'post', 'category' );
		unregister_taxonomy_for_object_type( 'category', 'post' );
	}

	/**
	 * Register topic (post), publication (issue), and collection
	 * (document) taxonomies. All three are hierarchical => true, even
	 * though none of them actually nest — in WordPress, `hierarchical`
	 * governs the editor UI/capability model, not just parent/child
	 * structure: true gives the Categories-style checkbox list against the
	 * existing terms and requires manage_categories to create a new one;
	 * false gives the Tags-style free-text box where any edit_posts user
	 * can mint an arbitrary new term on the fly. These three are fixed,
	 * IA-doc-specified vocabularies (6/2/4 terms) that editors must pick
	 * from, not open-ended tagging, so true is correct despite being flat.
	 *
	 * Slugs are the same ASCII kebab-case values already used by the v6
	 * prototype's file names and the IA doc §4 URL table (economy, world,
	 * afghanistan, ... / shola-jawid, a-world-to-win / classics,
	 * international-movement, party-documents, critique-polemic) — set
	 * explicitly per term below rather than auto-generated from the
	 * Persian names, per CLAUDE.md §4's ASCII-slug rule.
	 *
	 * @return void
	 */
	public static function register_taxonomies() {
		register_taxonomy(
			'topic',
			'post',
			array(
				'labels'            => array(
					'name'          => __( 'موضوعات', 'shola-core' ),
					'singular_name' => __( 'موضوع', 'shola-core' ),
					'search_items'  => __( 'جست‌وجوی موضوعات', 'shola-core' ),
					'all_items'     => __( 'همهٔ موضوعات', 'shola-core' ),
					'edit_item'     => __( 'ویرایش موضوع', 'shola-core' ),
					'view_item'     => __( 'مشاهدهٔ موضوع', 'shola-core' ),
					'menu_name'     => __( 'موضوعات', 'shola-core' ),
				),
				'public'            => true,
				'show_in_rest'      => true,
				'hierarchical'      => true,
				'show_admin_column' => true,
				'rewrite'           => array(
					'slug'       => 'topics',
					'with_front' => false,
				),
			)
		);

		register_taxonomy(
			'publication',
			'issue',
			array(
				'labels'            => array(
					'name'          => __( 'نشریات', 'shola-core' ),
					'singular_name' => __( 'نشریه', 'shola-core' ),
					'search_items'  => __( 'جست‌وجوی نشریات', 'shola-core' ),
					'all_items'     => __( 'همهٔ نشریات', 'shola-core' ),
					'edit_item'     => __( 'ویرایش نشریه', 'shola-core' ),
					'view_item'     => __( 'مشاهدهٔ نشریه', 'shola-core' ),
					'menu_name'     => __( 'نشریات', 'shola-core' ),
				),
				'public'            => true,
				'show_in_rest'      => true,
				'hierarchical'      => true,
				'show_admin_column' => true,
				'rewrite'           => array(
					'slug'       => 'publications',
					'with_front' => false,
				),
			)
		);

		register_taxonomy(
			'collection',
			'document',
			array(
				'labels'            => array(
					'name'          => __( 'مجموعه‌ها', 'shola-core' ),
					'singular_name' => __( 'مجموعه', 'shola-core' ),
					'search_items'  => __( 'جست‌وجوی مجموعه‌ها', 'shola-core' ),
					'all_items'     => __( 'همهٔ مجموعه‌ها', 'shola-core' ),
					'edit_item'     => __( 'ویرایش مجموعه', 'shola-core' ),
					'view_item'     => __( 'مشاهدهٔ مجموعه', 'shola-core' ),
					'menu_name'     => __( 'مجموعه‌ها', 'shola-core' ),
				),
				'public'            => true,
				'show_in_rest'      => true,
				'hierarchical'      => true,
				'show_admin_column' => true,
				'rewrite'           => array(
					'slug'       => 'library',
					'with_front' => false,
				),
			)
		);
	}

	/**
	 * Add a %topic% rewrite rule + tag so single articles permalink as
	 * /topics/{topic}/{slug}, per the IA doc §4 single-view table — the
	 * same pattern already used for issue/document in Phase 3.1, applied
	 * to the native `post` type via a rewrite rule (post has no `rewrite`
	 * arg of its own the way a CPT does).
	 *
	 * @return void
	 */
	public static function register_topic_rewrite() {
		add_rewrite_tag( '%topic%', '([^/]+)' );
		add_rewrite_rule(
			'^topics/([^/]+)/([^/]+)/?$',
			'index.php?post_type=post&name=$matches[2]',
			'top'
		);
	}

	/**
	 * Build the /topics/{topic}/{slug} permalink for native posts.
	 *
	 * Hooked on `post_link` — NOT `post_type_link`. `post_type_link` only
	 * fires for custom post types via get_post_permalink() (which is why
	 * issue/document's equivalent filters in Post_Types work correctly);
	 * native `post` permalinks are generated by get_permalink()'s own
	 * category/postname tag-replacement logic and filtered through
	 * `post_link` instead. An earlier version of this method was wired to
	 * the wrong filter and silently never ran for articles, leaving the
	 * site's raw `/%category%/%postname%/` structure (and its
	 * "Uncategorized" fallback) live on the front end — found by Farhad
	 * testing, logged in docs/CHANGELOG.md 2026-08-06.
	 *
	 * @param string   $link Post permalink (ignored — replaced entirely).
	 * @param \WP_Post $post Post object.
	 * @return string
	 */
	public static function filter_post_permalink( $link, $post ) {
		if ( 'post' !== $post->post_type ) {
			return $link;
		}

		$terms = get_the_terms( $post, 'topic' );
		$term  = ( $terms && ! is_wp_error( $terms ) ) ? array_shift( $terms ) : false;
		$slug  = $term ? $term->slug : 'بدون-موضوع';

		return home_url( '/topics/' . $slug . '/' . $post->post_name . '/' );
	}

	/**
	 * Idempotently create the fixed term vocabularies. Safe to call on
	 * every activation — term_exists() guards against duplicates.
	 *
	 * `topic`'s nine-term vocabulary and order per client-approved
	 * Phase C migration (2026-08-24, see docs/CHANGELOG.md) — was six
	 * terms (economy, world, afghanistan, women, international-movement,
	 * science-and-art). "جنبش بین‌المللی" (topic) was renamed/migrated
	 * to "جنبش کمونیستی بین‌المللی" (slug
	 * international-communist-movement) in that migration.
	 *
	 * Historical note, still accurate for `collection`: "جنبش
	 * بین‌المللی" remains deliberately created under `collection` too —
	 * IA doc Open Decision #1, confirmed intentional (articles vs.
	 * documents are different content types). WP scopes term slugs
	 * per-taxonomy (since the 4.2 term-splitting change), so the same
	 * slug in two different taxonomies is fully supported, not a
	 * collision. The `collection` term was explicitly NOT touched by
	 * the Phase C topic migration — confirmed distinct term_id, verified
	 * unaffected post-migration.
	 *
	 * @return void
	 */
	public static function create_default_terms() {
		$topics = array(
			'world'                             => 'جهان',
			'afghanistan'                       => 'افغانستان',
			'labor'                             => 'کارگری',
			'women'                             => 'زنان',
			'politics'                          => 'سیاست',
			'economy'                           => 'اقتصاد',
			'science-and-art'                   => 'علم و هنر',
			'international-communist-movement'  => 'جنبش کمونیستی بین‌المللی',
			'afghanistan-left-movement'         => 'جنبش چپ افغانستان',
		);
		foreach ( $topics as $slug => $name ) {
			self::maybe_insert_term( $name, 'topic', $slug );
		}

		$publications = array(
			'shola-jawid'    => 'شعله جاوید',
			'a-world-to-win' => 'جهان برای فتح',
		);
		foreach ( $publications as $slug => $name ) {
			self::maybe_insert_term( $name, 'publication', $slug );
		}

		$collections = array(
			'classics'               => 'آثار کلاسیک',
			'international-movement' => 'جنبش بین‌المللی',
			'party-documents'        => 'اسناد حزب',
			'critique-polemic'       => 'نقد و پلمیک',
		);
		foreach ( $collections as $slug => $name ) {
			self::maybe_insert_term( $name, 'collection', $slug );
		}

		/*
		 * گزارش, Phase B (2026-08-25, docs/CHANGELOG.md) — the
		 * homepage's گزارش section's source term. Deliberately
		 * `post_tag`, not `category`: `category` was tried first, but
		 * `post` had its `category` object-type association deliberately
		 * removed in an earlier phase
		 * (remove_core_category_from_post(), below) specifically to
		 * avoid a redundant "Uncategorized" editor panel — creating a
		 * `category` term for `post`-type content re-triggers exactly
		 * that conflict: no admin UI to assign it (both the classic
		 * metabox and Gutenberg's taxonomy panel stay hidden), and
		 * WordPress's default term-count updater silently excludes
		 * `post` from `category` counts, so the term's count would
		 * permanently read 0 even with real posts assigned. `post_tag`
		 * has none of these problems — still fully registered for
		 * `post` (confirmed via is_object_in_taxonomy(), and already
		 * actively used/rendered as visible tag chips on single.php) —
		 * so this is deliberately not `topic` either: same reasoning
		 * as `science-and-art` almost shipping without a DB term
		 * (Phase C finding) — confirmed this exists and works, not
		 * assumed.
		 */
		self::maybe_insert_term( 'گزارش', 'post_tag', 'reports' );
	}

	/**
	 * Insert a term with an explicit slug if it doesn't already exist in
	 * that taxonomy.
	 *
	 * @param string $name     Term display name (Persian).
	 * @param string $taxonomy Taxonomy slug.
	 * @param string $slug     Explicit ASCII slug.
	 * @return void
	 */
	private static function maybe_insert_term( $name, $taxonomy, $slug ) {
		if ( term_exists( $slug, $taxonomy ) ) {
			return;
		}
		wp_insert_term( $name, $taxonomy, array( 'slug' => $slug ) );
	}
}
