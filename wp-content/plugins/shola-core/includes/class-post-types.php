<?php
/**
 * Registers the issue, document, party_publication, party_document, and
 * announcement custom post types.
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
 * Post type registration for issue, document, party_publication, and
 * announcement.
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
		add_action( 'pre_get_posts', array( __CLASS__, 'include_cpts_in_search' ) );
		add_filter( 'query_vars', array( __CLASS__, 'register_search_type_query_var' ) );
	}

	/**
	 * Registers `result_type`, the query var behind search.php's filter
	 * tabs (همه/مقاله/یادداشت/شمارهٔ نشریه/سند کتابخانه).
	 *
	 * @param string[] $vars Public query vars.
	 * @return string[]
	 */
	public static function register_search_type_query_var( $vars ) {
		$vars[] = 'result_type';
		return $vars;
	}

	/**
	 * Native front-end search (`search.php`) must return articles/notes
	 * (`post`), issues, documents, party publications, and party documents
	 * together by default, per IA doc SRCH row and `body-search.html`'s
	 * mixed result list (`party_publication` added 2026-09-02,
	 * `party_document` added 2026-09-04, both alongside their own CPT) —
	 * `announcement` is deliberately excluded, it never appears in v6's
	 * search results or its filter-tab list. Also backs search.php's
	 * filter tabs via the `result_type` query var: مقاله (article,
	 * excludes the aside post format), یادداشت (note, the aside post
	 * format), شمارهٔ نشریه (issue), سند کتابخانه (document), انتشارات
	 * حزب (party_publication), اسناد حزب (party_document). Only touches
	 * the main front-end search query, never wp-admin or any other query
	 * on the site.
	 *
	 * @param WP_Query $query The query being modified.
	 * @return void
	 */
	public static function include_cpts_in_search( $query ) {
		if ( is_admin() || ! $query->is_main_query() || ! $query->is_search() ) {
			return;
		}

		$type = sanitize_key( (string) $query->get( 'result_type' ) );

		switch ( $type ) {
			case 'article':
				$query->set( 'post_type', 'post' );
				$query->set(
					'tax_query', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- small, fixed-vocabulary taxonomy, not a scale concern.
					array(
						array(
							'taxonomy' => 'post_format',
							'field'    => 'slug',
							'terms'    => array( 'post-format-aside' ),
							'operator' => 'NOT IN',
						),
					)
				);
				break;

			case 'note':
				$query->set( 'post_type', 'post' );
				$query->set( 'post_format', 'aside' );
				break;

			case 'issue':
				$query->set( 'post_type', 'issue' );
				break;

			case 'document':
				$query->set( 'post_type', 'document' );
				break;

			case 'party_publication':
				$query->set( 'post_type', 'party_publication' );
				break;

			case 'party_document':
				$query->set( 'post_type', 'party_document' );
				break;

			default:
				$post_type = $query->get( 'post_type' );
				if ( empty( $post_type ) || 'any' === $post_type ) {
					$query->set( 'post_type', array( 'post', 'issue', 'document', 'party_publication', 'party_document' ) );
				}
				break;
		}
	}

	/**
	 * Register the `issue`, `document`, `party_publication`,
	 * `party_document`, and `announcement` post types.
	 *
	 * `issue`, `document`, `party_publication`, and `party_document` all
	 * use has_archive => false: /publications, /library,
	 * /party-publications, and /party-documents are static Pages
	 * (page-publications.php/page-library.php/page-party-publications.php/
	 * page-party-documents.php), so a CPT archive at any of those slugs
	 * would collide with the Page's own rewrite rule.
	 * `issue`/`document` single-item permalinks nest under their parent
	 * taxonomy term via a custom rewrite tag instead (see
	 * register_rewrite_tags()/filter_*_permalink()), and per-term listings
	 * come from the taxonomy archive templates (Phase 3.2);
	 * `party_publication` has no taxonomy (client didn't ask for sub-
	 * categorization here), so its permalinks use the plain
	 * `party-publications/%postname%/` default a `rewrite.slug` alone
	 * already produces — no custom tag/filter needed for it.
	 * `party_document` (added 2026-09-04, Farhad relaying a client
	 * request for اسناد حزب as its own independent section, distinct from
	 * both `document`/`library` and `party_publication`) does carry a
	 * taxonomy (`party_document_category`), but deliberately keeps the
	 * same plain `party-documents/%postname%/` URL shape as
	 * `party_publication` rather than nesting under a category term in
	 * the URL — the client's own description of this feature ("they
	 * should be able to add categories inside this one if they need")
	 * describes an optional, self-managed grouping for the admin list,
	 * not a URL taxonomy structure like `publication`/`collection` have;
	 * category membership is still fully filterable on the front end
	 * without it needing to be embedded in the address.
	 * `announcement` is registered with a real archive since
	 * /announcements is itself a listing template
	 * (archive-announcement.php), not a static Page.
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
				'supports'     => array( 'title', 'thumbnail', 'excerpt' ),
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
				// 'editor' added Phase 4.2 (2026-08-06), building
				// single-document.php: v6's "دربارهٔ این متن" section is
				// 2 paragraphs of real, per-document editorial prose (not
				// a one-line dek — that's post_excerpt, used separately in
				// the hero) — same reasoning already applied to
				// page-about.php's prose: substantive content an editor
				// should write via the block editor, not a code change.
				// Documents seeded before this change simply render that
				// section as absent until an editor fills it in.
				'supports'     => array( 'title', 'thumbnail', 'excerpt', 'editor' ),
				'taxonomies'   => array( 'collection' ),
				'rewrite'      => array(
					'slug'       => 'library/%collection%',
					'with_front' => false,
				),
			)
		);

		/*
		 * انتشارات حزب (party_publication) — the party's own books and
		 * booklets, added 2026-09-02 per Farhad relaying a client
		 * correction: this is a third, genuinely distinct content model
		 * from both `issue` (نشریه — periodical شعله جاوید/جهان برای فتح
		 * numbers, `publication` taxonomy) and `document` (کتابخانه — the
		 * general library of *other* theorists'/authors' works, `collection`
		 * taxonomy, `shcore_author_source` citing who wrote each one). A
		 * homepage section already existed under this exact Persian name
		 * (front-page.php, added 2026-08-12) but was actually built on
		 * `issue` data — confirmed by grepping the whole repo (including
		 * docs/IA-reference/, EXECUTION_PLAN.md) before building this: no
		 * prior IA doc or open-decision note ever described this as its own
		 * content type, so this is new, not a fix to something previously
		 * specified — see docs/CHANGELOG.md for the full record. No
		 * taxonomy for v1 (client didn't ask for sub-categorization here);
		 * add one later if that changes rather than guessing at categories
		 * now.
		 */
		register_post_type(
			'party_publication',
			array(
				'labels'       => array(
					'name'               => __( 'انتشارات حزب', 'shola-core' ),
					'singular_name'      => __( 'اثر حزبی', 'shola-core' ),
					'add_new'            => __( 'افزودن اثر', 'shola-core' ),
					'add_new_item'       => __( 'افزودن اثر جدید', 'shola-core' ),
					'edit_item'          => __( 'ویرایش اثر', 'shola-core' ),
					'new_item'           => __( 'اثر جدید', 'shola-core' ),
					'view_item'          => __( 'مشاهدهٔ اثر', 'shola-core' ),
					'search_items'       => __( 'جست‌وجوی آثار', 'shola-core' ),
					'not_found'          => __( 'اثری یافت نشد', 'shola-core' ),
					'not_found_in_trash' => __( 'اثری در زباله‌دان یافت نشد', 'shola-core' ),
					'all_items'          => __( 'همهٔ آثار', 'shola-core' ),
					'menu_name'          => __( 'انتشارات حزب', 'shola-core' ),
				),
				'public'       => true,
				'show_in_rest' => true,
				'has_archive'  => false,
				'menu_icon'    => 'dashicons-book-alt',
				'supports'     => array( 'title', 'thumbnail', 'excerpt', 'editor' ),
				'rewrite'      => array(
					'slug'       => 'party-publications',
					'with_front' => false,
				),
			)
		);

		/*
		 * اسناد حزب (party_document) — the party's own archive of internal
		 * documents, added 2026-09-04 per Farhad relaying a client
		 * correction: this was previously just one shelf (`اسناد حزب`
		 * term) inside `document`/کتابخانه, but the client clarified it
		 * needs to be a fully independent section, not a public-library
		 * shelf, with its own fields (serial number, language) and a
		 * self-managed category system staff can grow themselves. Existing
		 * content filed under the old `document`/`collection` "اسناد حزب"
		 * shelf is migrated onto this new post type separately (see
		 * Taxonomies::migrate_legacy_party_documents()) — this
		 * registration alone does not move anything.
		 */
		register_post_type(
			'party_document',
			array(
				'labels'       => array(
					'name'               => __( 'اسناد حزب', 'shola-core' ),
					'singular_name'      => __( 'سند حزب', 'shola-core' ),
					'add_new'            => __( 'افزودن سند', 'shola-core' ),
					'add_new_item'       => __( 'افزودن سند جدید', 'shola-core' ),
					'edit_item'          => __( 'ویرایش سند', 'shola-core' ),
					'new_item'           => __( 'سند جدید', 'shola-core' ),
					'view_item'          => __( 'مشاهدهٔ سند', 'shola-core' ),
					'search_items'       => __( 'جست‌وجوی اسناد حزب', 'shola-core' ),
					'not_found'          => __( 'سندی یافت نشد', 'shola-core' ),
					'not_found_in_trash' => __( 'سندی در زباله‌دان یافت نشد', 'shola-core' ),
					'all_items'          => __( 'همهٔ اسناد', 'shola-core' ),
					'menu_name'          => __( 'اسناد حزب', 'shola-core' ),
				),
				'public'       => true,
				'show_in_rest' => true,
				'has_archive'  => false,
				'menu_icon'    => 'dashicons-portfolio',
				'supports'     => array( 'title', 'thumbnail', 'excerpt', 'editor' ),
				'taxonomies'   => array( 'party_document_category' ),
				'rewrite'      => array(
					'slug'       => 'party-documents',
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
				'supports'     => array( 'title', 'editor' ),
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
