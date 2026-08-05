<?php
/**
 * Registers the issue, document, and announcement custom post types.
 *
 * Regular articles/notes (مقاله/یادداشت) use WP's native `post` type per
 * the IA doc's content model — no CPT needed there, only the `topic`
 * taxonomy (Phase 3.2).
 *
 * @package SholaCore
 */

namespace SholaCore;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Post type registration for issue, document, and announcement.
 */
class Post_Types {

	/**
	 * Hook registration.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_post_types' ) );
		add_action( 'init', array( __CLASS__, 'register_rewrite_tags' ) );
		add_filter( 'post_type_link', array( __CLASS__, 'filter_issue_permalink' ), 10, 2 );
		add_filter( 'post_type_link', array( __CLASS__, 'filter_document_permalink' ), 10, 2 );
	}

	/**
	 * Register the `issue`, `document`, and `announcement` post types.
	 *
	 * `issue` and `document` use has_archive => false: /publications and
	 * /library are static Pages (page-publications.php/page-library.php),
	 * so a CPT archive at either slug would collide with the Page's own
	 * rewrite rule. Their single-item permalinks nest under their parent
	 * taxonomy term via a custom rewrite tag instead (see
	 * register_rewrite_tags()/filter_*_permalink()), and per-term listings
	 * come from the taxonomy archive templates (Phase 3.2). `announcement`
	 * is registered with a real archive since /announcements is itself a
	 * listing template (archive-announcement.php), not a static Page.
	 *
	 * No `/fa/` locale prefix — see docs/CHANGELOG.md 2026-08-05.
	 *
	 * @return void
	 */
	public static function register_post_types() {
		register_post_type(
			'issue',
			array(
				'labels'       => array(
					'name'               => __( 'شماره‌های نشریه', 'shola-core' ),
					'singular_name'      => __( 'شمارهٔ نشریه', 'shola-core' ),
					'add_new'            => __( 'افزودن شماره', 'shola-core' ),
					'add_new_item'       => __( 'افزودن شمارهٔ جدید', 'shola-core' ),
					'edit_item'          => __( 'ویرایش شماره', 'shola-core' ),
					'new_item'           => __( 'شمارهٔ جدید', 'shola-core' ),
					'view_item'          => __( 'مشاهدهٔ شماره', 'shola-core' ),
					'search_items'       => __( 'جست‌وجوی شماره‌ها', 'shola-core' ),
					'not_found'          => __( 'شماره‌ای یافت نشد', 'shola-core' ),
					'not_found_in_trash' => __( 'شماره‌ای در زباله‌دان یافت نشد', 'shola-core' ),
					'all_items'          => __( 'همهٔ شماره‌ها', 'shola-core' ),
					'menu_name'          => __( 'شماره‌های نشریه', 'shola-core' ),
				),
				'public'       => true,
				'show_in_rest' => true,
				'has_archive'  => false,
				'menu_icon'    => 'dashicons-media-document',
				'supports'     => array( 'title', 'thumbnail', 'excerpt', 'custom-fields' ),
				'taxonomies'   => array( 'publication' ),
				'rewrite'      => array(
					'slug'       => 'publications/%publication%',
					'with_front' => false,
				),
			)
		);

		register_post_type(
			'document',
			array(
				'labels'       => array(
					'name'               => __( 'اسناد کتابخانه', 'shola-core' ),
					'singular_name'      => __( 'سند کتابخانه', 'shola-core' ),
					'add_new'            => __( 'افزودن سند', 'shola-core' ),
					'add_new_item'       => __( 'افزودن سند جدید', 'shola-core' ),
					'edit_item'          => __( 'ویرایش سند', 'shola-core' ),
					'new_item'           => __( 'سند جدید', 'shola-core' ),
					'view_item'          => __( 'مشاهدهٔ سند', 'shola-core' ),
					'search_items'       => __( 'جست‌وجوی اسناد', 'shola-core' ),
					'not_found'          => __( 'سندی یافت نشد', 'shola-core' ),
					'not_found_in_trash' => __( 'سندی در زباله‌دان یافت نشد', 'shola-core' ),
					'all_items'          => __( 'همهٔ اسناد', 'shola-core' ),
					'menu_name'          => __( 'کتابخانه', 'shola-core' ),
				),
				'public'       => true,
				'show_in_rest' => true,
				'has_archive'  => false,
				'menu_icon'    => 'dashicons-media-text',
				'supports'     => array( 'title', 'thumbnail', 'excerpt', 'custom-fields' ),
				'taxonomies'   => array( 'collection' ),
				'rewrite'      => array(
					'slug'       => 'library/%collection%',
					'with_front' => false,
				),
			)
		);

		register_post_type(
			'announcement',
			array(
				'labels'       => array(
					'name'               => __( 'اطلاعیه‌ها', 'shola-core' ),
					'singular_name'      => __( 'اطلاعیه', 'shola-core' ),
					'add_new'            => __( 'افزودن اطلاعیه', 'shola-core' ),
					'add_new_item'       => __( 'افزودن اطلاعیهٔ جدید', 'shola-core' ),
					'edit_item'          => __( 'ویرایش اطلاعیه', 'shola-core' ),
					'new_item'           => __( 'اطلاعیهٔ جدید', 'shola-core' ),
					'view_item'          => __( 'مشاهدهٔ اطلاعیه', 'shola-core' ),
					'search_items'       => __( 'جست‌وجوی اطلاعیه‌ها', 'shola-core' ),
					'not_found'          => __( 'اطلاعیه‌ای یافت نشد', 'shola-core' ),
					'not_found_in_trash' => __( 'اطلاعیه‌ای در زباله‌دان یافت نشد', 'shola-core' ),
					'all_items'          => __( 'همهٔ اطلاعیه‌ها', 'shola-core' ),
					'menu_name'          => __( 'اطلاعیه‌ها', 'shola-core' ),
				),
				'public'       => true,
				'show_in_rest' => true,
				'has_archive'  => 'announcements',
				'menu_icon'    => 'dashicons-megaphone',
				'supports'     => array( 'title', 'editor', 'custom-fields' ),
				'rewrite'      => array(
					'slug'       => 'announcements',
					'with_front' => false,
				),
			)
		);
	}

	/**
	 * Register the %publication%/%collection% permalink placeholders used
	 * by issue/document's rewrite slugs above.
	 *
	 * @return void
	 */
	public static function register_rewrite_tags() {
		add_rewrite_tag( '%publication%', '([^/]+)' );
		add_rewrite_tag( '%collection%', '([^/]+)' );
	}

	/**
	 * Replace the %publication% placeholder with the issue's actual
	 * publication term slug.
	 *
	 * @param string   $link Post permalink.
	 * @param \WP_Post $post Post object.
	 * @return string
	 */
	public static function filter_issue_permalink( $link, $post ) {
		if ( 'issue' !== $post->post_type || false === strpos( $link, '%publication%' ) ) {
			return $link;
		}

		$terms = get_the_terms( $post, 'publication' );
		$term  = ( $terms && ! is_wp_error( $terms ) ) ? array_shift( $terms ) : false;
		$slug  = $term ? $term->slug : 'بدون-نشریه';

		return str_replace( '%publication%', $slug, $link );
	}

	/**
	 * Replace the %collection% placeholder with the document's actual
	 * collection term slug.
	 *
	 * @param string   $link Post permalink.
	 * @param \WP_Post $post Post object.
	 * @return string
	 */
	public static function filter_document_permalink( $link, $post ) {
		if ( 'document' !== $post->post_type || false === strpos( $link, '%collection%' ) ) {
			return $link;
		}

		$terms = get_the_terms( $post, 'collection' );
		$term  = ( $terms && ! is_wp_error( $terms ) ) ? array_shift( $terms ) : false;
		$slug  = $term ? $term->slug : 'بدون-مجموعه';

		return str_replace( '%collection%', $slug, $link );
	}
}
