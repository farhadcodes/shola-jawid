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
		add_filter( 'post_type_link', array( __CLASS__, 'filter_post_permalink' ), 10, 2 );
	}

	/**
	 * Register topic (post), publication (issue), and collection
	 * (document) taxonomies. All three are flat, fixed vocabularies, not
	 * open-ended tagging — hierarchical is false because none of them
	 * nest, not because they're tag-like/uncontrolled.
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
				'hierarchical'      => false,
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
				'hierarchical'      => false,
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
				'hierarchical'      => false,
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
	 * Replace the %topic% placeholder with the post's actual topic term
	 * slug when generating its permalink.
	 *
	 * @param string   $link Post permalink.
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
	 * "جنبش بین‌المللی" is deliberately created under both `topic` and
	 * `collection` — IA doc Open Decision #1, confirmed intentional
	 * (articles vs. documents are different content types). WP scopes
	 * term slugs per-taxonomy (since the 4.2 term-splitting change), so
	 * the same slug in two different taxonomies is fully supported, not a
	 * collision.
	 *
	 * @return void
	 */
	public static function create_default_terms() {
		$topics = array(
			'economy'                => 'اقتصاد',
			'world'                  => 'جهان',
			'afghanistan'            => 'افغانستان',
			'women'                  => 'زنان',
			'international-movement' => 'جنبش بین‌المللی',
			'science-and-art'        => 'علم و هنر',
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
			'classics'                => 'آثار کلاسیک',
			'international-movement' => 'جنبش بین‌المللی',
			'party-documents'         => 'اسناد حزب',
			'critique-polemic'        => 'نقد و پلمیک',
		);
		foreach ( $collections as $slug => $name ) {
			self::maybe_insert_term( $name, 'collection', $slug );
		}
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
