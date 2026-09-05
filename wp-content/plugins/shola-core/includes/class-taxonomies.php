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
		add_action( 'enqueue_block_editor_assets', array( __CLASS__, 'enqueue_primary_topic_assets' ) );
		add_action( 'admin_init', array( __CLASS__, 'seed_publication_periods' ) );

		/*
		 * ترتیب (manual sort order) for `publication` terms used to be
		 * registered here, publication-only — generalized 2026-09-05 to
		 * every managed taxonomy (topic, collection,
		 * party_document_category, report, publication) and moved to
		 * Category_Manager (class-category-manager.php), alongside that
		 * same generalization's Uncategorized-term/reassign-before-delete
		 * work. See that file for the full history.
		 */

		/*
		 * Single-select `publication` term picker on the `issue` edit
		 * screen — added 2026-09-04, Farhad reported (screenshot) that the
		 * default checkbox tree let an editor tick both a top-level نشریه
		 * and one of its دوره children, or several دوره at once, when an
		 * issue must always belong to exactly one. WordPress has no
		 * built-in single-select mode for a hierarchical taxonomy's admin
		 * checklist, so this swaps in a radio-button walker (see
		 * class-term-radio-walker.php) for `issue` specifically —
		 * `topic`/`post` and `collection`/`document` are unaffected and
		 * keep their normal multi-select checkbox trees.
		 */
		add_action( 'add_meta_boxes', array( __CLASS__, 'use_single_select_publication_metabox' ) );
		add_action( 'set_object_terms', array( __CLASS__, 'enforce_single_publication_term' ), 10, 6 );

		/*
		 * One-time migration: move any `document` posts filed under the
		 * old "اسناد حزب" Library shelf onto the new, independent
		 * party_document post type — added 2026-09-04 alongside that CPT.
		 * Same admin_init + options-flag pattern as
		 * seed_publication_periods() above, for the same reason (a
		 * code-only plugin re-upload doesn't re-fire activation hooks).
		 */
		add_action( 'admin_init', array( __CLASS__, 'migrate_legacy_party_documents' ) );

		/*
		 * Same self-healing migration pattern, for گزارش's move from a
		 * `post_tag` to the new `report` taxonomy — see
		 * register_taxonomies()'s comment on `report` for why.
		 */
		add_action( 'admin_init', array( __CLASS__, 'migrate_legacy_reports_tag' ) );

		/*
		 * Keep every one of this plugin's taxonomy checklists in their
		 * fixed registered order in wp-admin — never reordered by what's
		 * currently checked. Added 2026-09-05: Farhad reported this for
		 * `publication` specifically (fixed directly at its own
		 * wp_terms_checklist() call, render_publication_radio_metabox()
		 * above), then asked for the same check across every other
		 * hierarchical taxonomy so a non-technical editor never sees a
		 * term's position shift around and lose its visible place in the
		 * hierarchy. `topic`/`collection`/`report` render through
		 * WordPress core's own default checkbox meta box
		 * (post_categories_meta_box()), which has no argument to opt out
		 * of this — core always defaults `checked_ontop` to `true` — so
		 * this filters it centrally instead of writing a custom meta box
		 * for each one. `party_document_category` included too: seeded
		 * with zero terms by design (see register_taxonomies()) so it has
		 * no visible nesting today, but nothing stops an editor from
		 * giving a term a parent later, so it's covered pre-emptively
		 * rather than only once someone notices the same confusion there.
		 */
		add_filter( 'wp_terms_checklist_args', array( __CLASS__, 'disable_checked_ontop_for_own_taxonomies' ), 10, 2 );

		/*
		 * `party_document_category`'s panel in the `party_document` block
		 * editor is normally WordPress's own React hierarchical-term-
		 * selector (since the taxonomy is show_in_rest => true), which has
		 * its own "bump checked terms to the top" behavior baked into
		 * WordPress core — completely separate from, and not reachable by,
		 * the wp_terms_checklist_args filter above (that only affects the
		 * classic PHP checklist, which the block editor skips in favor of
		 * its own JS panel for any REST-enabled taxonomy). Added
		 * 2026-09-05, Farhad: this is the one taxonomy on the site that's
		 * self-managed (categories added freely by staff, unlike the fixed
		 * topic/collection/report vocabularies), so it's the one place a
		 * real parent/child structure — and the same ambiguity already
		 * fixed for نشریات — could appear later. Swaps in WP core's own
		 * `post_categories_meta_box()` (the exact function WordPress itself
		 * would otherwise use, not a reimplementation) under a fresh meta
		 * box ID so the block editor's automatic removal of the taxonomy's
		 * *default* box ID (`party_document_categorydiv`) doesn't also
		 * remove this one — same technique as
		 * use_single_select_publication_metabox() above. Since
		 * post_categories_meta_box() calls wp_terms_checklist() internally,
		 * it already inherits checked_ontop => false from the filter above
		 * with no further change needed — this box keeps the full
		 * "+ افزودن دسته" quick-add form WP core provides (unlike
		 * publication's radio box, which deliberately drops that form —
		 * publication is a fixed vocabulary, this one is not).
		 */
		add_action( 'add_meta_boxes', array( __CLASS__, 'use_fixed_order_party_document_category_metabox' ) );
	}

	/**
	 * Swaps the `party_document_category` taxonomy's default block-editor
	 * panel (WordPress's own React hierarchical term selector) for a
	 * classic meta box rendered via `post_categories_meta_box()` — see the
	 * `init()` comment above this is registered from for why.
	 *
	 * @return void
	 */
	public static function use_fixed_order_party_document_category_metabox() {
		remove_meta_box( 'party_document_categorydiv', 'party_document', 'side' );
		add_meta_box(
			'shcore-party-document-category-fixed-order',
			__( 'دسته‌های اسناد حزب', 'shola-core' ),
			'post_categories_meta_box',
			'party_document',
			'side',
			'core',
			array( 'taxonomy' => 'party_document_category' )
		);
	}

	/**
	 * Forces `checked_ontop` off for this plugin's own taxonomies' term
	 * checklists (see the `init()` comment above this filter is
	 * registered from for the full reasoning). Leaves any other
	 * taxonomy's checklist (WordPress core's `category`, or anything a
	 * future plugin adds) untouched — this only ever narrows behavior for
	 * taxonomies this plugin itself owns and registers.
	 *
	 * @param array $args    Args as passed to wp_terms_checklist().
	 * @param int   $post_id Post ID (unused — kept for filter signature match).
	 * @return array
	 */
	public static function disable_checked_ontop_for_own_taxonomies( $args, $post_id ) {
		$fixed_order_taxonomies = array( 'topic', 'collection', 'party_document_category', 'report' );
		if ( isset( $args['taxonomy'] ) && in_array( $args['taxonomy'], $fixed_order_taxonomies, true ) ) {
			$args['checked_ontop'] = false;
		}
		return $args;
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
	 * Enqueue the block-editor script for the «موضوع اصلی» (primary topic)
	 * panel (Farhad, 2026-09-02, second pass — a first pass this same
	 * session made `topic` single-select outright, which Farhad then
	 * corrected: multi-select is the standard/wanted behavior, only the
	 * breadcrumb pick needed fixing). `topic`'s own checkbox panel is left
	 * completely alone here; this only adds a second panel that lets the
	 * editor name one of the checked topics as primary. `post` only —
	 * `topic` isn't attached to any other post type.
	 *
	 * @return void
	 */
	public static function enqueue_primary_topic_assets() {
		$screen = get_current_screen();
		if ( ! $screen || 'post' !== $screen->post_type ) {
			return;
		}

		$terms   = get_terms(
			array(
				'taxonomy'   => 'topic',
				'hide_empty' => false,
			)
		);
		$options = array();
		foreach ( $terms as $term ) {
			$options[] = array(
				'id'   => $term->term_id,
				'name' => $term->name,
			);
		}

		wp_enqueue_script(
			'shcore-primary-topic',
			SHCORE_URL . 'admin/js/primary-topic.js',
			array( 'wp-plugins', 'wp-edit-post', 'wp-element', 'wp-components', 'wp-data', 'wp-i18n' ),
			SHCORE_VERSION,
			true
		);
		wp_localize_script( 'shcore-primary-topic', 'shcoreTopics', array( 'terms' => $options ) );

		// Sidebar panel order (Farhad, 2026-09-02): موضوعات, موضوع اصلی,
		// برچسب‌ها. Depends on 'shcore-primary-topic' only so its panel
		// exists in the DOM before this looks for it; see
		// admin/js/panel-order.js for why this can't be a simple render-
		// order/enqueue-order fix.
		wp_enqueue_script(
			'shcore-panel-order',
			SHCORE_URL . 'admin/js/panel-order.js',
			array( 'wp-dom-ready', 'shcore-primary-topic' ),
			SHCORE_VERSION,
			true
		);
	}

	/**
	 * Resolve which of a post's `topic` terms should be shown as *the*
	 * topic — the breadcrumb, the card's type-label term, and the
	 * /topics/{slug}/ permalink (filter_post_permalink() below) all need
	 * exactly one, even though a post can carry several.
	 *
	 * Prefers `shcore_primary_topic` (post meta, set via the «موضوع اصلی»
	 * block-editor panel — admin/js/primary-topic.js) if it's actually one
	 * of the post's current terms; a stored primary that's since been
	 * unchecked, or never set at all, falls back to the pre-2026-09-02
	 * behavior (array_shift() of get_the_terms(), i.e. WordPress's default
	 * term ordering — alphabetical by name) rather than showing nothing.
	 *
	 * @param int|\WP_Post $post Post ID or object.
	 * @return \WP_Term|false
	 */
	public static function get_primary_topic( $post ) {
		$terms = get_the_terms( $post, 'topic' );
		if ( ! $terms || is_wp_error( $terms ) ) {
			return false;
		}

		$primary_id = (int) get_post_meta( is_object( $post ) ? $post->ID : $post, 'shcore_primary_topic', true );
		if ( $primary_id ) {
			foreach ( $terms as $term ) {
				if ( $term->term_id === $primary_id ) {
					return $term;
				}
			}
		}

		return array_shift( $terms );
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

		/*
		 * party_document_category — added 2026-09-04 alongside the
		 * party_document CPT. Deliberately not seeded with any terms in
		 * create_default_terms() below, unlike topic/publication/collection's
		 * fixed vocabularies: the client asked specifically for a
		 * self-managed grouping staff can grow themselves as اسناد حزب
		 * accumulates, not a fixed IA-doc-specified list. hierarchical =>
		 * true for the same reason as the other three (Categories-style
		 * admin UI, matches how Farhad already manages Library shelves and
		 * دوره periods) — not because any sub-nesting is expected.
		 */
		register_taxonomy(
			'party_document_category',
			'party_document',
			array(
				'labels'            => array(
					'name'          => __( 'دسته‌های اسناد حزب', 'shola-core' ),
					'singular_name' => __( 'دستهٔ سند', 'shola-core' ),
					'search_items'  => __( 'جست‌وجوی دسته‌ها', 'shola-core' ),
					'all_items'     => __( 'همهٔ دسته‌ها', 'shola-core' ),
					'edit_item'     => __( 'ویرایش دسته', 'shola-core' ),
					'view_item'     => __( 'مشاهدهٔ دسته', 'shola-core' ),
					'menu_name'     => __( 'دسته‌ها', 'shola-core' ),
				),
				'public'            => true,
				'show_in_rest'      => true,
				'hierarchical'      => true,
				'show_admin_column' => true,
				'rewrite'           => array(
					'slug'       => 'party-documents-category',
					'with_front' => false,
				),
			)
		);

		/*
		 * report — added 2026-09-05, replacing گزارش's original design as a
		 * plain `post_tag` term (Phase B, 2026-08-25). That worked
		 * mechanically (homepage shelf, now the /reports/ archive too) but
		 * turned out not to work for Farhad's client in practice: a
		 * WordPress "tag" is a type-to-add free-text field, with no
		 * visible, clickable option the way موضوعات's checkbox list has —
		 * confirmed the actual problem by checking the block editor's
		 * sidebar directly, not assumed. hierarchical => true for the same
		 * reason as topic/publication/collection above: the
		 * Categories-style checkbox UI, not the Tags-style free-text box —
		 * even though, like those three, this isn't structurally
		 * hierarchical (this one only ever has the single "گزارش" term).
		 * `migrate_legacy_reports_tag()` below moves any posts already
		 * marked under the old `post_tag` term onto this one and removes
		 * that now-unused tag.
		 */
		register_taxonomy(
			'report',
			'post',
			array(
				'labels'            => array(
					'name'          => __( 'گزارش', 'shola-core' ),
					'singular_name' => __( 'گزارش', 'shola-core' ),
					'search_items'  => __( 'جست‌وجوی گزارش‌ها', 'shola-core' ),
					'all_items'     => __( 'همهٔ گزارش‌ها', 'shola-core' ),
					'edit_item'     => __( 'ویرایش گزارش', 'shola-core' ),
					'view_item'     => __( 'مشاهدهٔ گزارش', 'shola-core' ),
					'menu_name'     => __( 'گزارش', 'shola-core' ),
				),
				'public'            => false,
				'show_ui'           => true,
				'show_in_rest'      => true,
				'hierarchical'      => true,
				'show_admin_column' => true,
				'rewrite'           => false,
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

		$term = self::get_primary_topic( $post );
		$slug = $term ? $term->slug : 'بدون-موضوع';

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
			'world'                            => 'جهان',
			'afghanistan'                      => 'افغانستان',
			'labor'                            => 'کارگری',
			'women'                            => 'زنان',
			'politics'                         => 'سیاست',
			'economy'                          => 'اقتصاد',
			'science-and-art'                  => 'علم و هنر',
			'international-communist-movement' => 'جنبش کمونیستی بین‌المللی',
			'afghanistan-left-movement'        => 'جنبش چپ افغانستان',
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
		 * گزارش, Phase B (2026-08-25) originally seeded as a `post_tag`
		 * term. Replaced 2026-09-05 (see register_taxonomies()'s comment
		 * on the new `report` taxonomy for why) with this custom taxonomy
		 * instead — a fresh custom taxonomy attached to `post` doesn't
		 * carry the same conflict `category` itself has here: the removal
		 * in remove_core_category_from_post() below targets WP's built-in
		 * `category` taxonomy specifically, not any taxonomy that happens
		 * to attach to `post` (`topic` already proves this — it's worked
		 * on `post` since Phase 3.2 with no such conflict).
		 * migrate_legacy_reports_tag() moves any posts already marked
		 * under the old `post_tag` term onto this one.
		 */
		self::maybe_insert_term( 'گزارش', 'report', 'reports' );
	}

	/**
	 * Insert a term with an explicit slug if it doesn't already exist in
	 * that taxonomy. `$parent` added 2026-09-02 for the دوره (publication
	 * period) sub-terms below — optional and defaults to 0 (top-level),
	 * so every existing call site is unaffected. Returns the term's ID
	 * (existing or newly created) rather than void, since the دوره
	 * seeding needs it to migrate issues onto the right term.
	 *
	 * @param string $name     Term display name (Persian).
	 * @param string $taxonomy Taxonomy slug.
	 * @param string $slug     Explicit ASCII slug.
	 * @param int    $parent   Parent term ID, or 0 for a top-level term.
	 * @return int Term ID, or 0 if insertion failed.
	 */
	private static function maybe_insert_term( $name, $taxonomy, $slug, $parent = 0 ) {
		$existing = term_exists( $slug, $taxonomy );
		if ( $existing ) {
			return is_array( $existing ) ? (int) $existing['term_id'] : (int) $existing;
		}

		$result = wp_insert_term( $name, $taxonomy, array( 'slug' => $slug, 'parent' => $parent ) ); // phpcs:ignore WordPress.Arrays.MultipleStatementAlignment.DoubleArrowNotAligned
		return is_wp_error( $result ) ? 0 : (int) $result['term_id'];
	}

	/**
	 * Seeds 4 دوره (period) child terms under each of the two `publication`
	 * top-level terms — «دورهٔ اول» through «دورهٔ چهارم» — then migrates
	 * any `issue` still tagged with only the parent publication term (i.e.
	 * every issue that existed before this feature, since the دوره concept
	 * didn't exist yet) into that publication's «دورهٔ اول», so nothing
	 * that used to be browsable from /publications/... silently disappears
	 * once taxonomy-publication.php starts showing دوره tiles instead of a
	 * flat issue list for a top-level term. Farhad redistributes them into
	 * the correct دوره from wp-admin afterwards — this is a starting
	 * bucket, not a real classification, since there's no way to infer
	 * which دوره existing content actually belongs to.
	 *
	 * Hooked on `admin_init` rather than plugin activation (the more usual
	 * place for one-time seeding) because activation hooks don't re-fire
	 * on a code-only deploy (zip re-upload) to an already-active plugin —
	 * same reasoning, and the same self-healing/idempotent-by-construction
	 * pattern, as Roles::maybe_grant_editor_menu_access(). Both halves are
	 * naturally idempotent without a separate "done" flag:
	 * maybe_insert_term() already no-ops on an existing slug, and the
	 * migration query only ever matches issues tagged with the *parent*
	 * term specifically (include_children => false) — once an issue is
	 * moved onto a دوره child term, it no longer matches that query, so a
	 * second run finds nothing left to do. This also means a future issue
	 * that somehow ends up tagged with only the parent term (never a
	 * دوره) will self-heal into «دورهٔ اول» on the next admin page load —
	 * intentional, not a bug: every issue should end up under some دوره.
	 *
	 * 2026-09-04 fix (Farhad report): the above was true for the *migration*
	 * half, but the *creation* half over-reached — running unconditionally
	 * on every admin_init meant that if Farhad deleted a دوره term he didn't
	 * want (e.g. جهان برای فتح turned out to only ever have had one real
	 * دوره, per the client), the very next wp-admin page load recreated it
	 * from scratch, both in the term list and back on the front end. A term
	 * that no longer exists and one that was never created look identical to
	 * maybe_insert_term()'s term_exists() check, so there was no way to tell
	 * "not seeded yet" from "seeded, then deliberately removed." Fixed by
	 * recording, per publication, that seeding has run at all (the
	 * `shcore_periods_seeded` option) — once set, that publication is never
	 * touched again, so any term Farhad deletes afterward stays deleted.
	 * This still covers the original code-only-redeploy case (the option
	 * lives in the database, not the plugin files, so it survives a zip
	 * re-upload) while finally respecting manual curation after that first
	 * run.
	 *
	 * @return void
	 */
	public static function seed_publication_periods() {
		$periods = array(
			1 => __( 'دورهٔ اول', 'shola-core' ),
			2 => __( 'دورهٔ دوم', 'shola-core' ),
			3 => __( 'دورهٔ سوم', 'shola-core' ),
			4 => __( 'دورهٔ چهارم', 'shola-core' ),
		);

		$seeded = get_option( 'shcore_periods_seeded', array() );

		foreach ( array( 'shola-jawid', 'a-world-to-win' ) as $pub_slug ) {
			if ( ! empty( $seeded[ $pub_slug ] ) ) {
				continue;
			}

			$parent_term = get_term_by( 'slug', $pub_slug, 'publication' );
			if ( ! $parent_term || is_wp_error( $parent_term ) ) {
				continue;
			}

			$first_period_id = 0;
			foreach ( $periods as $n => $period_name ) {
				$period_id = self::maybe_insert_term( $period_name, 'publication', $pub_slug . '-dowre-' . $n, $parent_term->term_id );
				if ( $period_id && '' === get_term_meta( $period_id, 'shcore_term_order', true ) ) {
					// Only on first creation — never overwrites a value
					// Farhad has since set himself (empty-string check,
					// since get_term_meta() returns '' for "never set").
					update_term_meta( $period_id, 'shcore_term_order', $n );
				}
				if ( 1 === $n ) {
					$first_period_id = $period_id;
				}
			}

			if ( ! $first_period_id ) {
				continue;
			}

			$seeded[ $pub_slug ] = true;
			update_option( 'shcore_periods_seeded', $seeded );

			$unmigrated_issue_ids = get_posts(
				array(
					'post_type'      => 'issue',
					'posts_per_page' => -1,
					'fields'         => 'ids',
					'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- small, fixed-vocabulary taxonomy, one-time migration, not a recurring query.
						array(
							'taxonomy'         => 'publication',
							'field'            => 'term_id',
							'terms'            => $parent_term->term_id,
							'include_children' => false,
						),
					),
				)
			);

			foreach ( $unmigrated_issue_ids as $issue_id ) {
				wp_set_object_terms( $issue_id, array( $first_period_id ), 'publication', false );
			}
		}
	}

	/**
	 * One-time move of any `document` posts filed under the old "اسناد
	 * حزب" Library shelf onto the new, independent `party_document` post
	 * type — see init()'s comment for why this exists and why it's
	 * hooked on `admin_init` with an options flag rather than an
	 * activation hook.
	 *
	 * A straight `post_type` change (rather than creating a new post and
	 * deleting the old one) is deliberate: title, excerpt, editor content,
	 * featured image, and the `shcore_pdf_id`/`shcore_language` meta all
	 * mean the same thing on both post types, so changing just the type
	 * carries every field across untouched, including the post's ID (so
	 * any existing link to it, e.g. from a menu, keeps working — though
	 * its /library/... permalink does change to /party-documents/... as a
	 * result, since it's no longer a `document`).
	 *
	 * The `collection` term relationship is explicitly cleared first,
	 * rather than left for the post-type change to sort out — WordPress
	 * has no built-in cleanup for a taxonomy no longer valid for a post's
	 * (new) type, so an untouched relationship row would just sit there
	 * unreachable through any admin UI. The now-empty "اسناد حزب"
	 * `collection` term is then deleted outright: Library's shelf count
	 * dropping from 4 to 3 is the explicit point of this migration, not
	 * an accidental side effect.
	 *
	 * @return void
	 */
	public static function migrate_legacy_party_documents() {
		if ( get_option( 'shcore_party_documents_migrated' ) ) {
			return;
		}

		$term = get_term_by( 'slug', 'party-documents', 'collection' );

		if ( $term && ! is_wp_error( $term ) ) {
			$legacy_ids = get_posts(
				array(
					'post_type'      => 'document',
					'post_status'    => 'any',
					'posts_per_page' => -1,
					'fields'         => 'ids',
					'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- small taxonomy, one-time migration, not a recurring query.
						array(
							'taxonomy' => 'collection',
							'field'    => 'term_id',
							'terms'    => $term->term_id,
						),
					),
				)
			);

			foreach ( $legacy_ids as $post_id ) {
				wp_set_object_terms( $post_id, array(), 'collection' );
				wp_update_post(
					array(
						'ID'        => $post_id,
						'post_type' => 'party_document',
					)
				);
			}

			wp_delete_term( $term->term_id, 'collection' );
		}

		update_option( 'shcore_party_documents_migrated', 1 );
	}

	/**
	 * One-time move of any `post` still marked with the old `post_tag`
	 * "reports" term onto the new `report` taxonomy's "گزارش" term — see
	 * register_taxonomies()'s comment on `report` for the full reasoning
	 * (a WordPress tag's type-to-add field wasn't a discoverable way for
	 * an editor to mark a post as a report). Same admin_init + options-
	 * flag pattern as migrate_legacy_party_documents() above.
	 *
	 * Adds the new term rather than replacing, then explicitly removes
	 * the old tag relationship and deletes the now-unused tag term —
	 * mirrors migrate_legacy_party_documents()'s handling of the old
	 * `collection` term, for the same reason: a relationship left
	 * pointing at a taxonomy no longer meaningfully in use for this
	 * purpose would just sit there with no admin UI surfacing it.
	 *
	 * Seeds the "گزارش" term itself here via maybe_insert_term(), rather
	 * than assuming create_default_terms() already created it — that
	 * method only runs on plugin activation, which doesn't re-fire on a
	 * code-only zip redeploy to an already-active plugin (the exact bug
	 * seed_publication_periods() was fixed for on 2026-09-04). Without
	 * this, a redeploy where the `report` taxonomy is brand new to the
	 * site would find no "reports" term under it, silently do nothing,
	 * and mark itself migrated anyway.
	 *
	 * @return void
	 */
	public static function migrate_legacy_reports_tag() {
		if ( get_option( 'shcore_reports_tag_migrated' ) ) {
			return;
		}

		$new_term_id = self::maybe_insert_term( 'گزارش', 'report', 'reports' );
		$old_term    = get_term_by( 'slug', 'reports', 'post_tag' );

		if ( $old_term && ! is_wp_error( $old_term ) && $new_term_id ) {
			$legacy_ids = get_posts(
				array(
					'post_type'      => 'post',
					'post_status'    => 'any',
					'posts_per_page' => -1,
					'fields'         => 'ids',
					'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- small taxonomy, one-time migration, not a recurring query.
						array(
							'taxonomy' => 'post_tag',
							'field'    => 'term_id',
							'terms'    => $old_term->term_id,
						),
					),
				)
			);

			foreach ( $legacy_ids as $post_id ) {
				wp_set_object_terms( $post_id, array( $new_term_id ), 'report', true );
				wp_remove_object_terms( $post_id, $old_term->term_id, 'post_tag' );
			}

			wp_delete_term( $old_term->term_id, 'post_tag' );
		}

		update_option( 'shcore_reports_tag_migrated', 1 );
	}

	/**
	 * Re-entrancy guard for enforce_single_publication_term() — that
	 * method calls wp_set_object_terms() itself when it needs to trim a
	 * post down to one `publication` term, which would otherwise re-fire
	 * the `set_object_terms` action it's hooked to and loop forever.
	 *
	 * @var bool
	 */
	private static $enforcing_single_publication = false;

	/**
	 * Swaps WordPress core's default checkbox tree for the `publication`
	 * taxonomy on `issue` posts for a radio-button version (see
	 * class-term-radio-walker.php's Term_Radio_Walker) — an issue must
	 * always belong to exactly one نشریه/دوره, never several, per
	 * Farhad's report (screenshot, 2026-09-04) that the default checklist
	 * let more than one be ticked at once.
	 *
	 * @return void
	 */
	public static function use_single_select_publication_metabox() {
		remove_meta_box( 'publicationdiv', 'issue', 'side' );
		add_meta_box(
			'shcore-publication-radio',
			__( 'نشریات', 'shola-core' ),
			array( __CLASS__, 'render_publication_radio_metabox' ),
			'issue',
			'side',
			'core'
		);
	}

	/**
	 * Renders the radio-button `publication` picker. Mirrors WP core's own
	 * markup for the default checkbox meta box (post_categories_meta_box(),
	 * wp-admin/includes/meta-boxes.php) closely enough to inherit its
	 * existing admin CSS (indentation, spacing, scroll box), minus the
	 * "add new term" inline form — editors always pick from the fixed
	 * دوره vocabulary managed on Nشریات → wp-admin, never invent one here.
	 * No custom nonce needed: the field name (`tax_input[publication][]`)
	 * is exactly what WordPress's own post-save handler
	 * (wp-admin/includes/post.php's edit_post()) already expects and
	 * saves under the main post-edit nonce.
	 *
	 * @param \WP_Post $post Post being edited.
	 * @return void
	 */
	public static function render_publication_radio_metabox( $post ) {
		?>
		<div id="taxonomy-publication" class="categorydiv">
			<div id="publication-all" class="tabs-panel">
				<ul id="publicationchecklist" class="categorychecklist form-no-clear">
					<?php
					wp_terms_checklist(
						$post->ID,
						array(
							'taxonomy'      => 'publication',
							'walker'        => new Term_Radio_Walker(),
							/*
							 * WP core's own default here is true, which
							 * bubbles the checked term (and its parent) to
							 * the top of the list, out of hierarchical
							 * order — Farhad reported (screenshot,
							 * 2026-09-05) that reopening an issue for edit
							 * then shows its دوره sitting above its actual
							 * نشریه parent, with no visual link between
							 * them, making it impossible to tell which
							 * نشریه a shuffled-to-top دوره belongs to. This
							 * taxonomy's whole point is the fixed
							 * نشریه → دوره hierarchy, so it must never
							 * reorder regardless of what's selected.
							 */
							'checked_ontop' => false,
						)
					);
					?>
				</ul>
			</div>
		</div>
		<?php
	}

	/**
	 * Safety net that keeps an `issue` down to exactly one `publication`
	 * term regardless of how its terms were set — Quick Edit, the REST
	 * API, or an import all use their own code paths that don't go
	 * through render_publication_radio_metabox() above and so aren't
	 * limited to one choice by the radio markup alone. Hooked on
	 * `set_object_terms` (fires after WordPress has already written the
	 * relationship rows for any taxonomy), so this only has to look at
	 * whether more than one ended up assigned, not try to intercept the
	 * write itself. Keeps whichever term was part of the call that just
	 * ran (the newest choice), falling back to the post's first existing
	 * term if that call didn't add anything new to choose from.
	 *
	 * @param int    $object_id  Post ID.
	 * @param array  $terms      Terms passed to the wp_set_object_terms() call that fired this.
	 * @param int[]  $tt_ids     Term taxonomy IDs for $terms (unused).
	 * @param string $taxonomy   Taxonomy of the call that fired this.
	 * @param bool   $append     Whether that call appended or replaced (unused).
	 * @param int[]  $old_tt_ids Term taxonomy IDs the object had before this call (unused).
	 * @return void
	 */
	public static function enforce_single_publication_term( $object_id, $terms, $tt_ids, $taxonomy, $append, $old_tt_ids ) {
		if ( 'publication' !== $taxonomy || self::$enforcing_single_publication ) {
			return;
		}

		$current = wp_get_object_terms( $object_id, 'publication', array( 'fields' => 'ids' ) );
		if ( is_wp_error( $current ) || count( $current ) < 2 ) {
			return;
		}

		$new_ids = array_map( 'intval', (array) $terms );
		$keep    = $new_ids ? end( $new_ids ) : (int) $current[0];

		self::$enforcing_single_publication = true;
		wp_set_object_terms( $object_id, array( $keep ), 'publication', false );
		self::$enforcing_single_publication = false;
	}
}
