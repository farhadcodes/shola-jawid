<?php
/**
 * Registers post meta for issue, document, party_publication,
 * party_document, and article/post, per the IA doc §5 content-model table
 * (party_publication added 2026-09-02, party_document added 2026-09-04,
 * both outside that original table — see class-post-types.php's docblock
 * on each), plus the metabox UI editors use to fill them in from
 * wp-admin without touching code.
 *
 * @package SholaCore
 */

namespace SholaCore;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Post meta registration, metabox UI, and PDF MIME validation.
 */
class Meta_Fields {

	/**
	 * Hook registration.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_meta' ) );
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );
		add_action( 'save_post', array( __CLASS__, 'save_meta_boxes' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin_assets' ) );
		add_filter( 'upload_mimes', array( __CLASS__, 'ensure_pdf_mime_allowed' ) );

		/*
		 * hero_section admin-list UX (2026-09-09): a status column so an
		 * editor can see which variant is live without opening each one,
		 * and a one-click "Set as active" row action instead of requiring
		 * them to open an entry and find the checkbox — see
		 * class-post-types.php's hero_section docblock for the feature.
		 */
		add_filter( 'manage_hero_section_posts_columns', array( __CLASS__, 'add_hero_status_column' ) );
		add_action( 'manage_hero_section_posts_custom_column', array( __CLASS__, 'render_hero_status_column' ), 10, 2 );
		add_filter( 'post_row_actions', array( __CLASS__, 'add_hero_set_active_row_action' ), 10, 2 );
		add_action( 'admin_action_shcore_set_active_hero', array( __CLASS__, 'handle_set_active_hero' ) );

		/*
		 * Seed one default, active hero_section entry (تک‌ستونی — today's
		 * existing hero design) so the admin list isn't empty and the
		 * active-flag mechanism is immediately testable, even before
		 * front-page.php is wired to read from this post type. Same
		 * idempotent admin_init + option-flag pattern as
		 * Taxonomies::migrate_legacy_party_documents() — safe to run on
		 * every admin page load, only ever inserts once.
		 */
		add_action( 'admin_init', array( __CLASS__, 'seed_default_hero_section' ) );

		// masthead_section — same admin-list UX + seeding as hero_section
		// above, see class-post-types.php's docblock on this CPT.
		add_filter( 'manage_masthead_section_posts_columns', array( __CLASS__, 'add_masthead_status_column' ) );
		add_action( 'manage_masthead_section_posts_custom_column', array( __CLASS__, 'render_masthead_status_column' ), 10, 2 );
		add_filter( 'post_row_actions', array( __CLASS__, 'add_masthead_set_active_row_action' ), 10, 2 );
		add_action( 'admin_action_shcore_set_active_masthead', array( __CLASS__, 'handle_set_active_masthead' ) );
		add_action( 'admin_init', array( __CLASS__, 'seed_default_masthead_section' ) );

		/*
		 * Hide WordPress's native "Stick to the front page" control on
		 * `post` edit screens — added 2026-09-25. This site already has two
		 * dedicated, purpose-built "feature this content" mechanisms (the
		 * hero_section CPT and the گزیده‌ها `shcore_is_selected` flag right
		 * in this same file), and native Sticky is a third, WordPress-core
		 * one that does something different from both: it silently
		 * force-prepends the flagged post ahead of true date order in *any*
		 * `WP_Query` that doesn't explicitly opt out (`ignore_sticky_posts`),
		 * including bypassing tax_query exclusions. Farhad relayed a client
		 * report of "confusion between the three homepage sections" and
		 * other articles vanishing after marking one — front-page.php's own
		 * queries now all set `ignore_sticky_posts => true` defensively, but
		 * removing the control itself (rather than only neutralizing its
		 * effect) is the real fix for the confusion: an editor should never
		 * be able to reach for a third, unrelated "feature this" checkbox by
		 * mistake when this site only ever wants the other two used.
		 */
		add_action( 'admin_head-post.php', array( __CLASS__, 'hide_native_sticky_control' ) );
		add_action( 'admin_head-post-new.php', array( __CLASS__, 'hide_native_sticky_control' ) );

		/*
		 * Force-purge any cache holding a stale, no-featured-image version
		 * of a post the instant its `_thumbnail_id` is actually written —
		 * added 2026-09-26 after Farhad relayed a client report that a
		 * freshly-published post's featured image (hero/card image
		 * everywhere shola_get_featured_image() is used) doesn't appear on
		 * the live site until the client re-opens the post and re-picks the
		 * same image, confirmed not reproducible on Farhad's local install
		 * (no server-side cache there). The live site runs LiteSpeed Cache
		 * (confirmed by Farhad): the block editor's "Publish" action can
		 * finish attaching the featured image in a moment LiteSpeed's own
		 * automatic purge rules don't cover (its default purge triggers key
		 * off `save_post`/status transitions, not off a postmeta write that
		 * can land via a separate REST request in the same publish flow),
		 * leaving a cached copy of the post/homepage from just before the
		 * image was attached. Re-picking the image forces a fresh
		 * `_thumbnail_id` write, which is exactly what this hook now reacts
		 * to directly, rather than relying on LiteSpeed's own purge timing.
		 *
		 * Same hook pair Image_Optimizer already uses for the same reason
		 * (added_post_meta fires on first set, updated_post_meta if changed
		 * after) — not scoped to any one post type here, unlike
		 * Image_Optimizer's leaflet-only scope, since every content type
		 * this site shows a featured image for (post, issue, document,
		 * party_publication, party_document, leaflet) needs the same
		 * protection.
		 */
		add_action( 'added_post_meta', array( __CLASS__, 'purge_cache_on_thumbnail_set' ), 10, 4 );
		add_action( 'updated_post_meta', array( __CLASS__, 'purge_cache_on_thumbnail_set' ), 10, 4 );
	}

	/**
	 * Purges any cache (WordPress's own object cache, plus LiteSpeed Cache's
	 * page cache when that plugin is active) for a post the moment its
	 * featured image is actually attached — see the `added_post_meta`/
	 * `updated_post_meta` hook registration above for the full reasoning.
	 *
	 * `do_action( 'litespeed_purge_post', ... )` is LiteSpeed Cache's own
	 * documented public integration hook for third-party code to request a
	 * purge of one specific post's cached pages; firing it when the plugin
	 * isn't installed is a harmless no-op (an action with no listeners),
	 * so this never needs a class_exists()/defined() guard around it.
	 *
	 * @param int    $meta_id Meta row ID (unused).
	 * @param int    $post_id Post the meta belongs to.
	 * @param string $meta_key Meta key.
	 * @param mixed  $meta_value New `_thumbnail_id` value (unused).
	 * @return void
	 */
	public static function purge_cache_on_thumbnail_set( $meta_id, $post_id, $meta_key, $meta_value ) {
		if ( '_thumbnail_id' !== $meta_key ) {
			return;
		}

		clean_post_cache( $post_id );
		wp_cache_delete( $post_id, 'post_meta' );

		/**
		 * LiteSpeed Cache public purge-by-post hook.
		 *
		 * @param int $post_id Post ID to purge.
		 */
		do_action( 'litespeed_purge_post', $post_id );
	}

	/**
	 * Prints a tiny inline stylesheet hiding the native "Stick to the front
	 * page" checkbox on `post` edit screens (classic Publish box's
	 * `#sticky-span`, and the block editor's equivalent post-status-panel
	 * row) — see the `admin_head-post.php`/`admin_head-post-new.php` hooks
	 * above for why. Screen-gated to `post` only; every other post type
	 * this site defines never had this control to begin with.
	 *
	 * @return void
	 */
	public static function hide_native_sticky_control() {
		$screen = get_current_screen();
		if ( ! $screen || 'post' !== $screen->post_type ) {
			return;
		}
		?>
		<style>
			/* Classic editor / Publish metabox */
			#sticky-span { display: none; }
			/* Block editor's post-status panel sticky row */
			.editor-post-sticky, .edit-post-post-sticky { display: none; }
		</style>
		<?php
	}

	/**
	 * Register all post meta fields with real sanitize/auth callbacks and
	 * REST exposure (so both the classic metabox below and the block
	 * editor's Custom Fields panel work). Native WP fields already cover
	 * part of the IA doc §5 table and get no meta key here: تاریخ →
	 * post_date, خلاصه/توضیح → post_excerpt, جلد کتاب/پیش‌نمایش → featured
	 * image (thumbnail support, added in Phase 3.1), نشریه/مجموعه →
	 * publication/collection taxonomies (Phase 3.2).
	 *
	 * @return void
	 */
	public static function register_meta() {
		$auth_callback = array( __CLASS__, 'auth_edit_post' );

		// issue.
		register_post_meta(
			'issue',
			'shcore_issue_number',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => $auth_callback,
			)
		);
		register_post_meta(
			'issue',
			'shcore_volume',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => $auth_callback,
			)
		);
		register_post_meta(
			'issue',
			'shcore_pdf_id',
			array(
				'type'              => 'integer',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => array( __CLASS__, 'sanitize_pdf_id' ),
				'auth_callback'     => $auth_callback,
			)
		);
		register_post_meta(
			'issue',
			'shcore_contents',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => array( __CLASS__, 'sanitize_issue_contents' ),
				'auth_callback'     => $auth_callback,
			)
		);
		/*
		 * shcore_hero_pub_description (2026-09-21) — a separate field from
		 * the native excerpt, per Farhad relaying an explicit client
		 * correction: the excerpt/چکیده field must stay untouched for
		 * whatever it's already used for; this is a distinct, purpose-built
		 * field just for the one-line announcement shown on the homepage's
		 * minimal_cover hero publication card (e.g. "نشریه شعله جاوید شمارهٔ
		 * ۳۰ منتشر شد"). Rendered in its own metabox, deliberately placed
		 * directly below the Excerpt box (see add_meta_boxes()'s 'low'
		 * priority comment), not folded into shcore_issue_fields above.
		 */
		register_post_meta(
			'issue',
			'shcore_hero_pub_description',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => $auth_callback,
			)
		);

		/*
		 * shcore_subtitle (2026-09-19) — added to `document`,
		 * `party_publication`, and `party_document` only, per Farhad
		 * relaying a client complaint: some book/document titles are long
		 * enough that cramming the whole thing into the native Title field
		 * rendered oversized and ugly on the single-item page (that page's
		 * title font-size is large by design, meant for short titles).
		 * Optional, plain text, no markup — admins now split a long title
		 * into "main title" (native Title field, still required) and
		 * "subtitle" (this field, optional) instead of one long string.
		 * Not added to `issue` or `post` — Farhad's explicit scope was
		 * "just the library, publications, and documents sections."
		 */
		// document.
		register_post_meta(
			'document',
			'shcore_subtitle',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => $auth_callback,
			)
		);
		register_post_meta(
			'document',
			'shcore_author_source',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => $auth_callback,
			)
		);
		register_post_meta(
			'document',
			'shcore_pdf_id',
			array(
				'type'              => 'integer',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => array( __CLASS__, 'sanitize_pdf_id' ),
				'auth_callback'     => $auth_callback,
			)
		);
		register_post_meta(
			'document',
			'shcore_language',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'default'           => 'fa',
				'sanitize_callback' => array( __CLASS__, 'sanitize_language' ),
				'auth_callback'     => $auth_callback,
			)
		);

		// party_publication (2026-09-02) — deliberately no author-source
		// field like `document` has: these are the party's own
		// books/booklets, not works being cited from an external
		// theorist/author, so there's no one to attribute per-item.
		register_post_meta(
			'party_publication',
			'shcore_subtitle',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => $auth_callback,
			)
		);
		register_post_meta(
			'party_publication',
			'shcore_pdf_id',
			array(
				'type'              => 'integer',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => array( __CLASS__, 'sanitize_pdf_id' ),
				'auth_callback'     => $auth_callback,
			)
		);
		register_post_meta(
			'party_publication',
			'shcore_language',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'default'           => 'fa',
				'sanitize_callback' => array( __CLASS__, 'sanitize_language' ),
				'auth_callback'     => $auth_callback,
			)
		);

		// party_document (2026-09-04) — the party's own archive of
		// internal documents (اسناد حزب), distinct from `document`
		// (کتابخانه, other authors' works) and `party_publication`
		// (انتشارات حزب, the party's finished books/booklets). Detail/
		// description uses native `editor` support (post_content), same
		// as `document`'s "about this text" — no separate meta needed for
		// that field. Date and title are likewise native (post_date,
		// post_title). Only what has no native equivalent gets a meta key
		// here: the serial number.
		register_post_meta(
			'party_document',
			'shcore_subtitle',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => $auth_callback,
			)
		);
		register_post_meta(
			'party_document',
			'shcore_serial_number',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => $auth_callback,
			)
		);
		register_post_meta(
			'party_document',
			'shcore_pdf_id',
			array(
				'type'              => 'integer',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => array( __CLASS__, 'sanitize_pdf_id' ),
				'auth_callback'     => $auth_callback,
			)
		);
		register_post_meta(
			'party_document',
			'shcore_language',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'default'           => 'fa',
				'sanitize_callback' => array( __CLASS__, 'sanitize_language' ),
				'auth_callback'     => $auth_callback,
			)
		);

		/*
		 * hero_section (2026-09-09) — see class-post-types.php's docblock
		 * on this CPT for the full rationale. `shcore_hero_active` is a
		 * singleton in practice (enforced in save_meta_boxes(), not here —
		 * register_post_meta() has no cross-post constraint mechanism), so
		 * exactly one hero_section (or none, before an editor has picked
		 * one) is ever active at a time.
		 */
		register_post_meta(
			'hero_section',
			'shcore_hero_active',
			array(
				'type'              => 'boolean',
				'single'            => true,
				'show_in_rest'      => true,
				'default'           => false,
				'sanitize_callback' => 'rest_sanitize_boolean',
				'auth_callback'     => $auth_callback,
			)
		);
		register_post_meta(
			'hero_section',
			'shcore_hero_layout',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'default'           => 'single',
				'sanitize_callback' => array( __CLASS__, 'sanitize_hero_layout' ),
				'auth_callback'     => $auth_callback,
			)
		);
		register_post_meta(
			'hero_section',
			'shcore_hero_rail_publication',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'default'           => 'shola-jawid',
				'sanitize_callback' => array( __CLASS__, 'sanitize_hero_rail_publication' ),
				'auth_callback'     => $auth_callback,
			)
		);

		/*
		 * masthead_section (2026-09-14) — same singleton-active-flag
		 * pattern as hero_section above, for the sitewide header instead
		 * of the homepage hero. See class-post-types.php's docblock on
		 * this CPT for why it's separate from hero_section.
		 */
		register_post_meta(
			'masthead_section',
			'shcore_masthead_active',
			array(
				'type'              => 'boolean',
				'single'            => true,
				'show_in_rest'      => true,
				'default'           => false,
				'sanitize_callback' => 'rest_sanitize_boolean',
				'auth_callback'     => $auth_callback,
			)
		);
		register_post_meta(
			'masthead_section',
			'shcore_masthead_layout',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'default'           => 'default',
				'sanitize_callback' => array( __CLASS__, 'sanitize_masthead_layout' ),
				'auth_callback'     => $auth_callback,
			)
		);

		// post (article/note).
		register_post_meta(
			'post',
			'shcore_byline',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => $auth_callback,
			)
		);
		register_post_meta(
			'post',
			'shcore_author_note',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_text_field',
				'auth_callback'     => $auth_callback,
			)
		);
		register_post_meta(
			'post',
			'shcore_language',
			array(
				'type'              => 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'default'           => 'fa',
				'sanitize_callback' => array( __CLASS__, 'sanitize_language' ),
				'auth_callback'     => $auth_callback,
			)
		);
		register_post_meta(
			'post',
			'shcore_translation_id',
			array(
				'type'              => 'integer',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'absint',
				'auth_callback'     => $auth_callback,
			)
		);
		/*
		 * گزیده‌ها (Selected) flag — added 2026-09-16, per Farhad relaying
		 * the client's request for a curated homepage section. First built
		 * on top of WordPress's native Sticky Post flag (no new admin UI at
		 * all), but Farhad found live that the "Stick to the front page"
		 * checkbox never appeared for his test account, in either the block
		 * editor or Quick Edit — traced to a genuine WordPress core
		 * restriction, not a bug in this codebase: that checkbox only
		 * renders for a user who can edit *other* users' posts
		 * (`edit_others_posts`, i.e. سردبیر/Editor or مدیر/Administrator —
		 * confirmed nothing in this plugin or theme touches that capability;
		 * grep found zero matches). Farhad then explicitly asked for the
		 * feature to not be role-restricted, so this is a dedicated postmeta
		 * checkbox instead, gated only by `edit_post` via $auth_callback —
		 * the exact same, already-established pattern as every other field
		 * on this post type above — so any role that can edit a given
		 * article (down to نویسنده/Author on their own posts) can mark it.
		 */
		register_post_meta(
			'post',
			'shcore_is_selected',
			array(
				'type'              => 'boolean',
				'single'            => true,
				'show_in_rest'      => true,
				'default'           => false,
				'sanitize_callback' => 'rest_sanitize_boolean',
				'auth_callback'     => $auth_callback,
			)
		);
		/*
		 * Primary topic (2026-09-02): a post can carry several `topic`
		 * terms, but the breadcrumb/card display needs exactly one. This
		 * stores which of the assigned terms the editor picked as primary
		 * — see SholaCore\Taxonomies::get_primary_topic() for the
		 * resolution logic (falls back to the old array_shift() pick if
		 * this is unset or no longer one of the post's actual terms) and
		 * admin/js/primary-topic.js for the picker UI. Sanitized
		 * with absint like shcore_translation_id above; a term_id of 0
		 * (nothing picked) is a valid "no primary set" state, not an error.
		 */
		register_post_meta(
			'post',
			'shcore_primary_topic',
			array(
				'type'              => 'integer',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'absint',
				'auth_callback'     => $auth_callback,
			)
		);
	}

	/**
	 * Reject anything that isn't a real attachment ID whose stored MIME
	 * type (validated by WP core's own finfo-based sniffing at original
	 * upload time, not just the file extension) is application/pdf.
	 * Runs on every update_post_meta()/REST write for this key, per
	 * CLAUDE.md §6 — the client-side picker restriction (meta-boxes.js) is
	 * UX only; this is the real, non-spoofable check.
	 *
	 * @param mixed $value Raw meta value.
	 * @return int
	 */
	public static function sanitize_pdf_id( $value ) {
		$id = absint( $value );
		if ( ! $id || 'application/pdf' !== get_post_mime_type( $id ) ) {
			return 0;
		}
		return $id;
	}

	/**
	 * Restrict to the five hero layouts this feature ships with (see
	 * class-post-types.php's hero_section docblock) — `single` (today's
	 * full-bleed hero), `lead_rail` (headline + a separate, centered
	 * rail column), `overlay` (2026-09-10, third layout per Farhad
	 * relaying a client idea: same full-bleed hero as `single`, with a
	 * white publication card floating over the photo's lower corner
	 * instead of a full side column), `rail_full` (2026-09-10, fourth
	 * layout per a client sketch: visually the same idea as `lead_rail`
	 * — a solid-color panel with the publication card beside the
	 * full-size headline photo — but edge-to-edge full-bleed on the
	 * outer side instead of staying inside the centered content
	 * column), `filmstrip` (2026-09-13, fifth layout per a client
	 * reference screenshot: the same full-bleed headline hero as
	 * `single`, with a horizontally scrolling strip of the site's other
	 * recent articles — not tied to any one publication, so it has no
	 * rail-publication field — added directly below it), and
	 * `minimal_cover` (2026-09-16, sixth layout: the client found
	 * `overlay`'s floating card too busy — description + button removed
	 * entirely, leaving just the issue cover and its title beneath it;
	 * same full-bleed hero and floating-corner position as `overlay`,
	 * see shola_render_hero_publication_card_minimal() in
	 * inc/template-tags.php), and `feature_card` (2026-09-24, seventh
	 * layout, per a client reference screenshot: a genuine two-column
	 * split — a featured-article photo card with a solid white info box
	 * (category label, title, excerpt, byline) overlaid at one corner,
	 * beside a narrower publication-card column reusing
	 * shola_render_hero_publication_card() — not `overlay`/
	 * `minimal_cover`'s floating-card-over-one-photo idea, and not
	 * `lead_rail`/`rail_full`'s plain-text-over-photo headline). An
	 * unrecognized value (e.g. a future layout type removed later) falls
	 * back to the safest option — single, the current site's existing
	 * hero design.
	 *
	 * @param mixed $value Raw meta value.
	 * @return string
	 */
	public static function sanitize_hero_layout( $value ) {
		return in_array( $value, array( 'single', 'lead_rail', 'overlay', 'rail_full', 'filmstrip', 'minimal_cover', 'feature_card' ), true ) ? $value : 'single';
	}

	/**
	 * Restrict to the sitewide-header layouts this feature ships with —
	 * just `default` (today's existing header) for now. New layouts get
	 * added here as they're built, same as sanitize_hero_layout() above.
	 * `two-tier` added 2026-09-18 — see template-parts/masthead/two-tier.php.
	 *
	 * @param mixed $value Raw meta value.
	 * @return string
	 */
	public static function sanitize_masthead_layout( $value ) {
		return in_array( $value, array( 'default', 'logo', 'logo-light', 'logo-radial', 'two-tier' ), true ) ? $value : 'default';
	}

	/**
	 * Restrict to the two fixed `publication` term slugs (شعله جاوید /
	 * جهان برای فتح) a hero_section's rail can source its latest issue
	 * from. Hardcoded like sanitize_language() above rather than checked
	 * against get_terms(), since this is the same kind of small, fixed
	 * vocabulary — not something editors can add to.
	 *
	 * @param mixed $value Raw meta value.
	 * @return string
	 */
	public static function sanitize_hero_rail_publication( $value ) {
		return in_array( $value, array( 'shola-jawid', 'a-world-to-win' ), true ) ? $value : 'shola-jawid';
	}

	/**
	 * Restrict to the two locales this project is bilingual-ready for.
	 * Only `fa` is active per CLAUDE.md §1; `en` is scaffolded, not wired
	 * to anything live.
	 *
	 * @param mixed $value Raw meta value.
	 * @return string
	 */
	public static function sanitize_language( $value ) {
		return in_array( $value, array( 'fa', 'en' ), true ) ? $value : 'fa';
	}

	/**
	 * Standard per-post edit capability check for all fields registered
	 * above.
	 *
	 * @param bool   $allowed Whether the value is allowed.
	 * @param string $meta_key Meta key.
	 * @param int    $post_id Post ID.
	 * @return bool
	 */
	public static function auth_edit_post( $allowed, $meta_key, $post_id ) {
		return current_user_can( 'edit_post', $post_id );
	}

	/**
	 * Ensure PDF stays in the allowed upload MIME list even if it's
	 * restricted elsewhere later (e.g. Phase 6 hardening) — belt-and-
	 * braces per CLAUDE.md §6, since PDF is already a WP core default but
	 * this makes the requirement explicit in code rather than implicit.
	 *
	 * @param array $mimes Allowed mime => extension map.
	 * @return array
	 */
	public static function ensure_pdf_mime_allowed( $mimes ) {
		$mimes['pdf'] = 'application/pdf';
		return $mimes;
	}

	/**
	 * Register the classic metaboxes editors use to fill these fields in
	 * without touching code (renders inside the block editor too, below
	 * the content area, same as any classic metabox).
	 *
	 * @return void
	 */
	public static function add_meta_boxes() {
		add_meta_box( 'shcore_issue_fields', __( 'اطلاعات شماره', 'shola-core' ), array( __CLASS__, 'render_issue_metabox' ), 'issue', 'normal', 'high' );
		/*
		 * Separate box, deliberately 'low' priority (2026-09-21) — WP core
		 * registers the native Excerpt box at 'core' priority in the
		 * 'normal' context, which renders between 'high' and 'low' custom
		 * boxes. Registering this one at 'low' is what actually puts it
		 * directly below Excerpt on the edit screen, matching Farhad's
		 * explicit ask (relayed from the client, annotated screenshot) for
		 * this new field to sit "beneath" it, not mixed into
		 * shcore_issue_fields above or above the Excerpt box.
		 */
		add_meta_box( 'shcore_issue_hero_description', __( 'توضیح کارت صفحهٔ اصلی', 'shola-core' ), array( __CLASS__, 'render_issue_hero_description_metabox' ), 'issue', 'normal', 'low' );
		add_meta_box( 'shcore_document_fields', __( 'اطلاعات سند', 'shola-core' ), array( __CLASS__, 'render_document_metabox' ), 'document', 'normal', 'high' );
		add_meta_box( 'shcore_party_publication_fields', __( 'اطلاعات اثر', 'shola-core' ), array( __CLASS__, 'render_party_publication_metabox' ), 'party_publication', 'normal', 'high' );
		add_meta_box( 'shcore_party_document_fields', __( 'اطلاعات سند', 'shola-core' ), array( __CLASS__, 'render_party_document_metabox' ), 'party_document', 'normal', 'high' );
		add_meta_box( 'shcore_article_fields', __( 'اطلاعات مقاله', 'shola-core' ), array( __CLASS__, 'render_article_metabox' ), 'post', 'normal', 'high' );
		/*
		 * Separate 'side'-context box, added 2026-09-16, for
		 * shcore_is_selected only — deliberately not folded into
		 * shcore_article_fields above (which stays 'normal' context,
		 * rendering below the content editor, unchanged). Farhad found
		 * live that a checkbox living in that 'normal' box wasn't visible
		 * without scrolling past the article content, and asked for it in
		 * the settings sidebar instead — the same place WordPress's own
		 * "Stick to the front page" checkbox would normally live. A new,
		 * minimal box keeps that existing box's position/behavior
		 * completely unchanged for every other field it holds.
		 */
		add_meta_box( 'shcore_selected_field', __( 'گزیده‌ها', 'shola-core' ), array( __CLASS__, 'render_selected_metabox' ), 'post', 'side', 'high' );
		add_meta_box( 'shcore_hero_fields', __( 'تنظیمات هدر', 'shola-core' ), array( __CLASS__, 'render_hero_metabox' ), 'hero_section', 'normal', 'high' );
		add_meta_box( 'shcore_masthead_fields', __( 'تنظیمات هدر سایت', 'shola-core' ), array( __CLASS__, 'render_masthead_metabox' ), 'masthead_section', 'normal', 'high' );
	}

	/**
	 * Render the issue metabox fields.
	 *
	 * @param \WP_Post $post Post object.
	 * @return void
	 */
	public static function render_issue_metabox( $post ) {
		wp_nonce_field( 'shcore_save_meta', 'shcore_meta_nonce' );
		$number = get_post_meta( $post->ID, 'shcore_issue_number', true );
		$volume = get_post_meta( $post->ID, 'shcore_volume', true );
		$rows   = self::get_issue_contents( $post->ID );
		?>
		<p>
			<label for="shcore_issue_number"><strong><?php esc_html_e( 'شمارهٔ شماره', 'shola-core' ); ?></strong></label><br>
			<input type="text" id="shcore_issue_number" name="shcore_issue_number" class="regular-text" value="<?php echo esc_attr( $number ); ?>">
		</p>
		<p class="description"><?php esc_html_e( 'شمارهٔ پیاپی این شماره از نشریه را وارد کنید؛ مثلاً ۳۱.', 'shola-core' ); ?></p>
		<p>
			<label for="shcore_volume"><strong><?php esc_html_e( 'دوره / جلد', 'shola-core' ); ?></strong></label><br>
			<input type="text" id="shcore_volume" name="shcore_volume" class="regular-text" value="<?php echo esc_attr( $volume ); ?>">
		</p>
		<p class="description"><?php esc_html_e( 'دورهٔ یا جلد این شماره را بنویسید؛ اگر ندارد، خالی بگذارید.', 'shola-core' ); ?></p>
		<?php self::render_pdf_field( $post->ID, 'shcore_pdf_id' ); ?>

		<p><strong><?php esc_html_e( 'فهرست مطالب (اختیاری)', 'shola-core' ); ?></strong></p>
		<p class="description">
			<?php esc_html_e( 'نوشته‌های شماره فقط در PDF چاپ می‌شوند و در سایت صفحهٔ مستقل ندارند — این فهرست فقط توضیحی است. هر سه فیلد اختیاری‌اند؛ ردیف‌های کاملاً خالی هنگام ذخیره نادیده گرفته می‌شوند.', 'shola-core' ); ?>
		</p>
		<div class="shcore-toc-repeater-wrap">
			<table class="widefat shcore-toc-repeater">
				<thead>
					<tr>
						<th><?php esc_html_e( 'بخش', 'shola-core' ); ?></th>
						<th><?php esc_html_e( 'عنوان', 'shola-core' ); ?></th>
						<th><?php esc_html_e( 'نویسنده', 'shola-core' ); ?></th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $rows as $i => $row ) : ?>
						<?php self::render_toc_row( $i, $row ); ?>
					<?php endforeach; ?>
				</tbody>
			</table>
			<p><button type="button" class="button shcore-toc-add-row"><?php esc_html_e( '+ افزودن ردیف', 'shola-core' ); ?></button></p>
			<script type="text/html" class="shcore-toc-row-template">
				<?php self::render_toc_row( '__INDEX__', array() ); ?>
			</script>
		</div>
		<?php
	}

	/**
	 * Renders the shcore_hero_pub_description field in its own metabox
	 * (2026-09-21) — deliberately separate from render_issue_metabox()
	 * above and registered at 'low' priority so it lands directly below
	 * WordPress's own core Excerpt box on the edit screen, per Farhad
	 * relaying the client's explicit correction that this must be a new
	 * field, not a reuse of the excerpt.
	 *
	 * @param \WP_Post $post Post object.
	 * @return void
	 */
	public static function render_issue_hero_description_metabox( $post ) {
		wp_nonce_field( 'shcore_save_meta', 'shcore_meta_nonce' );
		$description = get_post_meta( $post->ID, 'shcore_hero_pub_description', true );
		?>
		<p>
			<textarea id="shcore_hero_pub_description" name="shcore_hero_pub_description" class="large-text" rows="2"><?php echo esc_textarea( $description ); ?></textarea>
		</p>
		<p class="description">
			<?php esc_html_e( 'این متن، فقط وقتی که این شماره روی صفحهٔ اصلی به‌صورت «کارت شناور» نمایش داده می‌شود، زیر عنوان دیده می‌شود — جدا از چکیده است و به‌جای آن، در آن کارت نمایش داده می‌شود. کوتاه و خبری بنویسید، شبیه یک اعلان؛ مثلاً: «نشریه شعله جاوید شمارهٔ ۳۰ منتشر شد». اگر خالی بگذارید، آن قسمت از کارت اصلاً نمایش داده نمی‌شود.', 'shola-core' ); ?>
		</p>
		<?php
	}

	/**
	 * Renders one <tr> of the table-of-contents repeater — shared between
	 * existing rows (real index, real values) and the JS row template
	 * (literal `__INDEX__` placeholder, empty values, string-replaced on
	 * clone in admin/js/meta-boxes.js). SECTION options come live from the
	 * `topic` taxonomy plus a fixed TRANSLATION pseudo-option, rather than
	 * a hardcoded slug list, so a new topic term doesn't need a code
	 * change to show up here.
	 *
	 * @param int|string                                  $index Row index (or '__INDEX__' for the template).
	 * @param array{section?: string, title?: string, byline?: string} $row Row values.
	 * @return void
	 */
	private static function render_toc_row( $index, $row ) {
		$section = isset( $row['section'] ) ? $row['section'] : '';
		$title   = isset( $row['title'] ) ? $row['title'] : '';
		$byline  = isset( $row['byline'] ) ? $row['byline'] : '';
		$topics  = get_terms(
			array(
				'taxonomy'   => 'topic',
				'hide_empty' => false,
			)
		); // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- small, fixed-vocabulary taxonomy, admin-only.
		if ( is_wp_error( $topics ) ) {
			$topics = array();
		}
		?>
		<tr>
			<td>
				<select name="shcore_contents[<?php echo esc_attr( $index ); ?>][section]">
					<option value=""><?php esc_html_e( '— بدون بخش —', 'shola-core' ); ?></option>
					<?php foreach ( $topics as $topic ) : ?>
						<option value="<?php echo esc_attr( $topic->slug ); ?>" <?php selected( $section, $topic->slug ); ?>>
							<?php echo esc_html( $topic->name . ' (' . strtoupper( $topic->slug ) . ')' ); ?>
						</option>
					<?php endforeach; ?>
					<option value="TRANSLATION" <?php selected( $section, 'TRANSLATION' ); ?>><?php esc_html_e( 'ترجمه (TRANSLATION)', 'shola-core' ); ?></option>
				</select>
			</td>
			<td><input type="text" class="regular-text" name="shcore_contents[<?php echo esc_attr( $index ); ?>][title]" value="<?php echo esc_attr( $title ); ?>"></td>
			<td><input type="text" class="regular-text" name="shcore_contents[<?php echo esc_attr( $index ); ?>][byline]" value="<?php echo esc_attr( $byline ); ?>"></td>
			<td><button type="button" class="button-link shcore-toc-remove-row" aria-label="<?php esc_attr_e( 'حذف ردیف', 'shola-core' ); ?>">✕</button></td>
		</tr>
		<?php
	}

	/**
	 * Render the document metabox fields.
	 *
	 * @param \WP_Post $post Post object.
	 * @return void
	 */
	public static function render_document_metabox( $post ) {
		wp_nonce_field( 'shcore_save_meta', 'shcore_meta_nonce' );
		$subtitle      = get_post_meta( $post->ID, 'shcore_subtitle', true );
		$author_source = get_post_meta( $post->ID, 'shcore_author_source', true );
		$language      = get_post_meta( $post->ID, 'shcore_language', true );
		?>
		<?php self::render_subtitle_field( $subtitle ); ?>
		<p>
			<label for="shcore_author_source"><strong><?php esc_html_e( 'نویسنده / منبع', 'shola-core' ); ?></strong></label><br>
			<input type="text" id="shcore_author_source" name="shcore_author_source" class="regular-text" value="<?php echo esc_attr( $author_source ); ?>">
		</p>
		<p class="description"><?php esc_html_e( 'نام نویسندهٔ اصلی متن یا منبعی که این سند از آن گرفته‌شده را بنویسید.', 'shola-core' ); ?></p>
		<?php self::render_pdf_field( $post->ID, 'shcore_pdf_id' ); ?>
		<?php self::render_language_field( $language ); ?>
		<?php
	}

	/**
	 * Render the party_publication metabox fields — a PDF and a language
	 * picker only, no author/source field (see register_meta()'s comment
	 * on why: these are the party's own works, not attributed to an
	 * external theorist per item like `document` is).
	 *
	 * @param \WP_Post $post Post object.
	 * @return void
	 */
	public static function render_party_publication_metabox( $post ) {
		wp_nonce_field( 'shcore_save_meta', 'shcore_meta_nonce' );
		$subtitle = get_post_meta( $post->ID, 'shcore_subtitle', true );
		$language = get_post_meta( $post->ID, 'shcore_language', true );
		?>
		<?php self::render_subtitle_field( $subtitle ); ?>
		<?php self::render_pdf_field( $post->ID, 'shcore_pdf_id' ); ?>
		<?php self::render_language_field( $language ); ?>
		<?php
	}

	/**
	 * Render the party_document metabox fields — a serial number, a PDF,
	 * and a language picker. Name/title is the native post title, detail/
	 * description is the native block-editor content area (same pattern
	 * as `document`'s "about this text"), and date is the native
	 * publish/modified date — none of those need a field here.
	 *
	 * @param \WP_Post $post Post object.
	 * @return void
	 */
	public static function render_party_document_metabox( $post ) {
		wp_nonce_field( 'shcore_save_meta', 'shcore_meta_nonce' );
		$subtitle      = get_post_meta( $post->ID, 'shcore_subtitle', true );
		$serial_number = get_post_meta( $post->ID, 'shcore_serial_number', true );
		$language      = get_post_meta( $post->ID, 'shcore_language', true );
		?>
		<?php self::render_subtitle_field( $subtitle ); ?>
		<p>
			<label for="shcore_serial_number"><strong><?php esc_html_e( 'شمارهٔ سریال', 'shola-core' ); ?></strong></label><br>
			<input type="text" id="shcore_serial_number" name="shcore_serial_number" class="regular-text" value="<?php echo esc_attr( $serial_number ); ?>">
		</p>
		<p class="description"><?php esc_html_e( 'شمارهٔ ثبت یا سریال داخلی این سند حزبی را در آرشیو وارد کنید.', 'shola-core' ); ?></p>
		<?php self::render_pdf_field( $post->ID, 'shcore_pdf_id' ); ?>
		<?php self::render_language_field( $language ); ?>
		<?php
	}

	/**
	 * Render the article/post metabox fields.
	 *
	 * @param \WP_Post $post Post object.
	 * @return void
	 */
	public static function render_article_metabox( $post ) {
		wp_nonce_field( 'shcore_save_meta', 'shcore_meta_nonce' );
		$byline         = get_post_meta( $post->ID, 'shcore_byline', true );
		$author_note    = get_post_meta( $post->ID, 'shcore_author_note', true );
		$language       = get_post_meta( $post->ID, 'shcore_language', true );
		$translation_id = get_post_meta( $post->ID, 'shcore_translation_id', true );
		?>
		<p>
			<label for="shcore_byline"><strong><?php esc_html_e( 'نام مستعار نویسنده', 'shola-core' ); ?></strong></label><br>
			<input type="text" id="shcore_byline" name="shcore_byline" class="regular-text" value="<?php echo esc_attr( $byline ); ?>">
		</p>
		<p class="description"><?php esc_html_e( 'در صورت نیاز به نام مستعار به‌جای نام کاربری وردپرس، اینجا وارد کنید؛ اختیاری است.', 'shola-core' ); ?></p>
		<p>
			<label for="shcore_author_note"><strong><?php esc_html_e( 'توضیح همکاری', 'shola-core' ); ?></strong></label><br>
			<input type="text" id="shcore_author_note" name="shcore_author_note" class="large-text" value="<?php echo esc_attr( $author_note ); ?>">
		</p>
		<p class="description"><?php esc_html_e( 'توضیح کوتاه دربارهٔ نحوهٔ همکاری در تولید این نوشته؛ مثلاً «کاری از میز اقتصاد».', 'shola-core' ); ?></p>
		<?php self::render_language_field( $language ); ?>
		<p>
			<label for="shcore_translation_id"><strong><?php esc_html_e( 'شناسهٔ نوشتهٔ ترجمه', 'shola-core' ); ?></strong></label><br>
			<input type="number" id="shcore_translation_id" name="shcore_translation_id" class="small-text" value="<?php echo esc_attr( $translation_id ); ?>">
		</p>
		<p class="description"><?php esc_html_e( 'این فیلد هنوز فعال نیست و در حال حاضر نیازی به تکمیل آن نیست.', 'shola-core' ); ?></p>
		<?php
	}

	/**
	 * Render the گزیده‌ها (Selected) checkbox — a small, standalone
	 * 'side'-context box (see add_meta_boxes() above for why it's separate
	 * from shcore_article_fields). Prints its own nonce field even though
	 * shcore_article_fields already prints an identical one on the same
	 * page — deliberately not shared, so this box keeps saving correctly
	 * even if an editor hides the other box via Screen Options. Two
	 * identical hidden inputs sharing one `name` is harmless (the browser
	 * submits whichever renders, both are the same valid nonce).
	 *
	 * @param \WP_Post $post Post object.
	 * @return void
	 */
	public static function render_selected_metabox( $post ) {
		wp_nonce_field( 'shcore_save_meta', 'shcore_meta_nonce' );
		$is_selected = (bool) get_post_meta( $post->ID, 'shcore_is_selected', true );
		?>
		<p>
			<label>
				<input type="checkbox" id="shcore_is_selected" name="shcore_is_selected" value="1" <?php checked( $is_selected ); ?>>
				<strong><?php esc_html_e( 'نمایش در گزیده‌ها', 'shola-core' ); ?></strong>
			</label>
		</p>
		<p class="description"><?php esc_html_e( 'این نوشته را در بخش «گزیده‌ها»ی صفحهٔ اصلی و آرشیو آن نمایش می‌دهد. محدودیتی بر اساس نقش کاربری ندارد — هر کسی که اجازهٔ ویرایش این نوشته را داشته باشد می‌تواند آن را فعال کند.', 'shola-core' ); ?></p>
		<?php
	}

	/**
	 * Render the hero_section metabox fields: the active-flag checkbox,
	 * the layout picker, and (only meaningful for the "lead_rail" layout)
	 * the rail-publication picker. The headline article itself is
	 * deliberately never a field here — see class-post-types.php's
	 * hero_section docblock.
	 *
	 * @param \WP_Post $post Post object.
	 * @return void
	 */
	public static function render_hero_metabox( $post ) {
		wp_nonce_field( 'shcore_save_meta', 'shcore_meta_nonce' );
		$is_active         = (bool) get_post_meta( $post->ID, 'shcore_hero_active', true );
		$layout            = get_post_meta( $post->ID, 'shcore_hero_layout', true );
		$rail_publication  = get_post_meta( $post->ID, 'shcore_hero_rail_publication', true );
		$layout           = $layout ? $layout : 'single';
		$rail_publication = $rail_publication ? $rail_publication : 'shola-jawid';

		/*
		 * Restricted to exactly the two real publication slugs
		 * sanitize_hero_rail_publication() accepts — a plain
		 * get_terms( parent => 0 ) query also picks up the taxonomy's
		 * auto-created "دسته‌بندی‌نشده" (Uncategorized) top-level term,
		 * which would show as a selectable but meaningless option here
		 * (caught live while testing this screen, 2026-09-09).
		 */
		$publication_terms = array();
		foreach ( array( 'shola-jawid', 'a-world-to-win' ) as $pub_slug ) {
			$term = get_term_by( 'slug', $pub_slug, 'publication' );
			if ( $term && ! is_wp_error( $term ) ) {
				$publication_terms[] = $term;
			}
		}
		?>
		<p>
			<label>
				<input type="checkbox" id="shcore_hero_active" name="shcore_hero_active" value="1" <?php checked( $is_active ); ?>>
				<strong><?php esc_html_e( 'این نسخه هم‌اکنون فعال است', 'shola-core' ); ?></strong>
			</label>
		</p>
		<p class="description"><?php esc_html_e( 'فعال‌سازی این نسخه، آن را در صفحهٔ اصلی نمایش می‌دهد و فعال بودن سایر نسخه‌های هدر را خودکار غیرفعال می‌کند.', 'shola-core' ); ?></p>
		<p>
			<label for="shcore_hero_layout"><strong><?php esc_html_e( 'نوع چیدمان', 'shola-core' ); ?></strong></label><br>
			<select id="shcore_hero_layout" name="shcore_hero_layout">
				<option value="single" <?php selected( $layout, 'single' ); ?>><?php esc_html_e( 'تک‌ستونی (مقالهٔ سرخط)', 'shola-core' ); ?></option>
				<option value="lead_rail" <?php selected( $layout, 'lead_rail' ); ?>><?php esc_html_e( 'مقالهٔ سرخط + ستون نشریه', 'shola-core' ); ?></option>
				<option value="overlay" <?php selected( $layout, 'overlay' ); ?>><?php esc_html_e( 'مقالهٔ سرخط با کارت شناور روی تصویر', 'shola-core' ); ?></option>
				<option value="rail_full" <?php selected( $layout, 'rail_full' ); ?>><?php esc_html_e( 'مقالهٔ سرخط + ستون نشریهٔ تمام‌عرض', 'shola-core' ); ?></option>
				<option value="filmstrip" <?php selected( $layout, 'filmstrip' ); ?>><?php esc_html_e( 'مقالهٔ سرخط + نوار افقی آخرین مقالات', 'shola-core' ); ?></option>
				<option value="minimal_cover" <?php selected( $layout, 'minimal_cover' ); ?>><?php esc_html_e( 'مقالهٔ سرخط با کاور مینیمال روی تصویر', 'shola-core' ); ?></option>
				<option value="feature_card" <?php selected( $layout, 'feature_card' ); ?>><?php esc_html_e( 'دوستونی: کارت مقالهٔ ویژه + ستون نشریه', 'shola-core' ); ?></option>
			</select>
		</p>
		<p class="description"><?php esc_html_e( 'تک‌ستونی: طرح فعلی سایت. دوستونی: ستونی جدا برای آخرین شمارهٔ یک نشریه، کنار مقالهٔ سرخط. کارت شناور: همان تصویر تمام‌عرض تک‌ستونی، با کارتی سفید از آخرین شماره روی گوشهٔ تصویر. ستون تمام‌عرض: مانند دوستونی، با این تفاوت که ستون نشریه تا لبهٔ مرورگر ادامه می‌یابد، نه فقط داخل بخش مرکزی صفحه. فقط در نمایشگرهای بزرگ‌تر (رایانه) دیده می‌شود. نوار افقی: همان تصویر تمام‌عرض تک‌ستونی، با نواری از آخرین مقالات (به‌جز خودِ مقالهٔ سرخط) زیر آن که به‌آرامی و خودکار می‌لغزد و با دو دکمهٔ پیکان هم قابل کنترل دستی است؛ فیلد «نشریهٔ کارت/ستون نشریه» در این چیدمان نادیده گرفته می‌شود. کاور مینیمال: مانند کارت شناور، با این تفاوت که فقط کاور شماره و عنوان آن نمایش داده می‌شود — بدون توضیح و بدون دکمه. کارت مقالهٔ ویژه + ستون نشریه: مقالهٔ سرخط به‌صورت تصویر با کادر سفید اطلاعات (موضوع، عنوان، خلاصه) روی گوشهٔ آن نمایش داده می‌شود، در کنار ستونی جدا برای آخرین شمارهٔ نشریه (بدون عنوان — فقط کاور، خلاصه و دکمه).', 'shola-core' ); ?></p>
		<p>
			<label for="shcore_hero_rail_publication"><strong><?php esc_html_e( 'نشریهٔ کارت/ستون نشریه', 'shola-core' ); ?></strong></label><br>
			<select id="shcore_hero_rail_publication" name="shcore_hero_rail_publication">
				<?php foreach ( $publication_terms as $term ) : ?>
					<option value="<?php echo esc_attr( $term->slug ); ?>" <?php selected( $rail_publication, $term->slug ); ?>><?php echo esc_html( $term->name ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
		<p class="description"><?php esc_html_e( 'در چیدمان دوستونی و کارت شناور استفاده می‌شود؛ آخرین شمارهٔ این نشریه نمایش داده خواهد شد. در چیدمان تک‌ستونی نادیده گرفته می‌شود.', 'shola-core' ); ?></p>
		<?php
	}

	/**
	 * Render the masthead_section metabox fields: active-flag checkbox +
	 * layout picker. Same pattern as render_hero_metabox() above, for
	 * the sitewide header instead of the homepage hero.
	 *
	 * @param \WP_Post $post Post object.
	 * @return void
	 */
	public static function render_masthead_metabox( $post ) {
		wp_nonce_field( 'shcore_save_meta', 'shcore_meta_nonce' );
		$is_active = (bool) get_post_meta( $post->ID, 'shcore_masthead_active', true );
		$layout    = get_post_meta( $post->ID, 'shcore_masthead_layout', true );
		$layout    = $layout ? $layout : 'default';
		?>
		<p>
			<label>
				<input type="checkbox" id="shcore_masthead_active" name="shcore_masthead_active" value="1" <?php checked( $is_active ); ?>>
				<strong><?php esc_html_e( 'این نسخه هم‌اکنون فعال است', 'shola-core' ); ?></strong>
			</label>
		</p>
		<p class="description"><?php esc_html_e( 'فعال‌سازی این نسخه، آن را در سراسر سایت نمایش می‌دهد و فعال بودن سایر نسخه‌های هدر سایت را خودکار غیرفعال می‌کند.', 'shola-core' ); ?></p>
		<p>
			<label for="shcore_masthead_layout"><strong><?php esc_html_e( 'نوع چیدمان', 'shola-core' ); ?></strong></label><br>
			<select id="shcore_masthead_layout" name="shcore_masthead_layout">
				<option value="default" <?php selected( $layout, 'default' ); ?>><?php esc_html_e( 'چیدمان فعلی (نام‌بردهٔ متنی)', 'shola-core' ); ?></option>
				<option value="logo" <?php selected( $layout, 'logo' ); ?>><?php esc_html_e( 'چیدمان با لوگو (پس‌زمینهٔ قرمز)', 'shola-core' ); ?></option>
				<option value="logo-light" <?php selected( $layout, 'logo-light' ); ?>><?php esc_html_e( 'چیدمان با لوگو (پس‌زمینهٔ سفید)', 'shola-core' ); ?></option>
				<option value="logo-radial" <?php selected( $layout, 'logo-radial' ); ?>><?php esc_html_e( 'چیدمان با لوگو (گرادیان شعاعی)', 'shola-core' ); ?></option>
				<option value="two-tier" <?php selected( $layout, 'two-tier' ); ?>><?php esc_html_e( 'چیدمان دو ردیفه (نوار تیره + نوار قرمز)', 'shola-core' ); ?></option>
			</select>
		</p>
		<p class="description"><?php esc_html_e( 'چیدمان فعلی: هدر کنونی سایت (نام‌بردهٔ متنی، پیوندهای ناوبری، جست‌وجو، منو). چیدمان با لوگو (پس‌زمینهٔ قرمز): همان هدر، با این تفاوت که به‌جای نام‌بردهٔ متنی، لوگوی سایت (از بخش نمایش ← شخصی‌سازی ← هویت سایت) نمایش داده می‌شود. چیدمان با لوگو (پس‌زمینهٔ سفید): دقیقاً همان چیدمانِ لوگو، با رنگ‌های معکوس — پس‌زمینه سفید و متن/آیکن‌ها قرمز؛ خودِ لوگو تغییر رنگ نمی‌کند. چیدمان با لوگو (گرادیان شعاعی): دقیقاً همان چیدمانِ لوگوی قرمز، با این تفاوت که پس‌زمینه از سفید در پشت لوگو به‌صورت شعاعی به همان قرمز می‌رسد؛ متن و آیکن‌ها همچنان سفید. چیدمان دو ردیفه: یک نوار تیرهٔ بالایی (جست‌وجو، پیوندهای کمکی، تاریخ، دکمهٔ منو) روی یک نوار پایینی با گرادیان قرمز (لوگو/پرچم، نام سایت، تاریخ).', 'shola-core' ); ?></p>
		<?php
	}

	/**
	 * Shared subtitle field for `document`, `party_publication`, and
	 * `party_document` (2026-09-19) — see register_meta()'s comment on
	 * shcore_subtitle for the full reasoning. Placed first in each of
	 * those three metaboxes, directly under the native Title box, so the
	 * "main title here, long remainder here" split reads naturally.
	 *
	 * @param string $subtitle Current value.
	 * @return void
	 */
	private static function render_subtitle_field( $subtitle ) {
		?>
		<p>
			<label for="shcore_subtitle"><strong><?php esc_html_e( 'زیرعنوان', 'shola-core' ); ?></strong></label><br>
			<input type="text" id="shcore_subtitle" name="shcore_subtitle" class="large-text" value="<?php echo esc_attr( $subtitle ); ?>">
		</p>
		<p class="description"><?php esc_html_e( 'اختیاری. برای عنوان‌های طولانی: بخش اصلی و کوتاه را در کادر «عنوان» بالا و باقی متن را اینجا به‌عنوان زیرعنوان بنویسید؛ در صفحهٔ نمایش، عنوان بزرگ‌تر و زیرعنوان کوچک‌تر زیر آن نشان داده می‌شود.', 'shola-core' ); ?></p>
		<?php
	}

	/**
	 * Shared fa/en select, since both post and document carry a language
	 * field.
	 *
	 * @param string $language Current value.
	 * @return void
	 */
	private static function render_language_field( $language ) {
		if ( ! $language ) {
			$language = 'fa';
		}
		?>
		<p>
			<label for="shcore_language"><strong><?php esc_html_e( 'زبان', 'shola-core' ); ?></strong></label><br>
			<select id="shcore_language" name="shcore_language">
				<option value="fa" <?php selected( $language, 'fa' ); ?>><?php esc_html_e( 'فارسی', 'shola-core' ); ?></option>
				<option value="en" <?php selected( $language, 'en' ); ?>>English</option>
			</select>
		</p>
		<p class="description"><?php esc_html_e( 'زبان اصلی این محتوا را مشخص کنید؛ اکنون فقط فارسی فعال است.', 'shola-core' ); ?></p>
		<?php
	}

	/**
	 * Shared PDF picker field: a hidden attachment-ID input, a filename
	 * display, and a button that opens the media library restricted to
	 * PDFs (client-side UX only — meta-boxes.js). The real enforcement is
	 * sanitize_pdf_id() above, which runs regardless of how the ID got
	 * into the field.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $meta_key Meta key holding the attachment ID.
	 * @return void
	 */
	private static function render_pdf_field( $post_id, $meta_key ) {
		$pdf_id       = (int) get_post_meta( $post_id, $meta_key, true );
		$current_name = $pdf_id ? basename( get_attached_file( $pdf_id ) ) : '';
		?>
		<p class="shcore-pdf-field">
			<label><strong><?php esc_html_e( 'فایل PDF', 'shola-core' ); ?></strong></label><br>
			<input type="hidden" class="shcore-pdf-id" name="<?php echo esc_attr( $meta_key ); ?>" value="<?php echo esc_attr( $pdf_id ); ?>">
			<span class="shcore-pdf-filename"><?php echo esc_html( $current_name ); ?></span>
			<button type="button" class="button shcore-pdf-select"><?php esc_html_e( 'انتخاب فایل PDF', 'shola-core' ); ?></button>
			<button type="button" class="button shcore-pdf-remove" <?php echo $pdf_id ? '' : 'style="display:none"'; ?>><?php esc_html_e( 'حذف', 'shola-core' ); ?></button>
		</p>
		<p class="description"><?php esc_html_e( 'فایل نهایی PDF این محتوا را از کتابخانهٔ رسانه انتخاب یا بارگذاری کنید.', 'shola-core' ); ?></p>
		<?php
	}

	/**
	 * Save all metabox fields for the current post type. update_post_meta()
	 * routes through the sanitize_callback registered in register_meta()
	 * above, so validation (including the real PDF MIME check) applies
	 * here exactly the same as it would via REST.
	 *
	 * @param int      $post_id Post ID.
	 * @param \WP_Post $post Post object.
	 * @return void
	 */
	public static function save_meta_boxes( $post_id, $post ) {
		if ( ! isset( $_POST['shcore_meta_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['shcore_meta_nonce'] ), 'shcore_save_meta' ) ) {
			return;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$fields_by_type = array(
			'issue'             => array( 'shcore_issue_number', 'shcore_volume', 'shcore_pdf_id', 'shcore_contents', 'shcore_hero_pub_description' ),
			'document'          => array( 'shcore_subtitle', 'shcore_author_source', 'shcore_pdf_id', 'shcore_language' ),
			'party_publication' => array( 'shcore_subtitle', 'shcore_pdf_id', 'shcore_language' ),
			'party_document'    => array( 'shcore_subtitle', 'shcore_serial_number', 'shcore_pdf_id', 'shcore_language' ),
			'post'              => array( 'shcore_byline', 'shcore_author_note', 'shcore_language', 'shcore_translation_id', 'shcore_is_selected' ),
			'hero_section'      => array( 'shcore_hero_active', 'shcore_hero_layout', 'shcore_hero_rail_publication' ),
			'masthead_section'  => array( 'shcore_masthead_active', 'shcore_masthead_layout' ),
		);

		if ( ! isset( $fields_by_type[ $post->post_type ] ) ) {
			return;
		}

		foreach ( $fields_by_type[ $post->post_type ] as $field ) {
			if ( 'shcore_hero_active' === $field ) {
				// A checkbox is absent from $_POST entirely when unchecked
				// — unlike every other field here, "not present" is a real,
				// meaningful value (inactive), not "leave the old value
				// alone", so this can't use the generic isset() branch
				// below.
				$is_active = isset( $_POST['shcore_hero_active'] );
				update_post_meta( $post_id, 'shcore_hero_active', $is_active );
				if ( $is_active ) {
					self::deactivate_other_hero_sections( $post_id );
				}
				continue;
			}
			if ( 'shcore_masthead_active' === $field ) {
				// Same "absent means inactive" reasoning as shcore_hero_active above.
				$is_active = isset( $_POST['shcore_masthead_active'] );
				update_post_meta( $post_id, 'shcore_masthead_active', $is_active );
				if ( $is_active ) {
					self::deactivate_other_masthead_sections( $post_id );
				}
				continue;
			}
			if ( 'shcore_is_selected' === $field ) {
				// Same "absent means false" reasoning as shcore_hero_active
				// above — no singleton constraint here, unlike hero/masthead,
				// since a site can have any number of گزیده‌ها articles.
				update_post_meta( $post_id, 'shcore_is_selected', isset( $_POST['shcore_is_selected'] ) );
				continue;
			}
			if ( 'shcore_contents' === $field ) {
				// Repeater rows arrive as shcore_contents[N][section|title|byline].
				// Absent entirely (JS never touched, or every row removed) is a
				// valid, expected state — save as an empty TOC, not an error.
				$rows = isset( $_POST['shcore_contents'] ) && is_array( $_POST['shcore_contents'] )
					? array_values( wp_unslash( $_POST['shcore_contents'] ) )
					: array();
				update_post_meta( $post_id, 'shcore_contents', wp_json_encode( $rows, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
				continue;
			}
			if ( isset( $_POST[ $field ] ) ) {
				update_post_meta( $post_id, $field, wp_unslash( $_POST[ $field ] ) );
			}
		}
	}

	/**
	 * Enforces the hero_section active-flag singleton: whenever one entry
	 * is saved as active, every other entry (any status — a stray active
	 * flag left on a draft would be just as wrong as one on a published
	 * entry) is explicitly set inactive. Runs from save_meta_boxes() only,
	 * never from register_meta()'s sanitize_callback, since that callback
	 * has no way to see or modify *other* posts.
	 *
	 * @param int $active_id Post ID of the hero_section just marked active.
	 * @return void
	 */
	private static function deactivate_other_hero_sections( $active_id ) {
		$others = get_posts(
			array(
				'post_type'      => 'hero_section',
				'posts_per_page' => -1,
				'post__not_in'   => array( $active_id ),
				'post_status'    => 'any',
				'fields'         => 'ids',
			)
		);

		foreach ( $others as $other_id ) {
			update_post_meta( $other_id, 'shcore_hero_active', false );
		}
	}

	/**
	 * Add a "وضعیت" (status) column to the hero_section list table, right
	 * after the title, so an editor can see which variant is live without
	 * opening each one.
	 *
	 * @param array<string, string> $columns Default columns.
	 * @return array<string, string>
	 */
	public static function add_hero_status_column( $columns ) {
		$new = array();
		foreach ( $columns as $key => $label ) {
			$new[ $key ] = $label;
			if ( 'title' === $key ) {
				$new['shcore_hero_status'] = __( 'وضعیت', 'shola-core' );
			}
		}
		return $new;
	}

	/**
	 * Render the "وضعیت" column's value for one row.
	 *
	 * @param string $column Column key being rendered.
	 * @param int    $post_id Post ID for this row.
	 * @return void
	 */
	public static function render_hero_status_column( $column, $post_id ) {
		if ( 'shcore_hero_status' !== $column ) {
			return;
		}
		$is_active = (bool) get_post_meta( $post_id, 'shcore_hero_active', true );
		if ( $is_active ) {
			echo '<strong style="color:#0a7d2c">' . esc_html__( 'فعال', 'shola-core' ) . '</strong>';
		} else {
			echo '<span style="color:#777">' . esc_html__( 'غیرفعال', 'shola-core' ) . '</span>';
		}
	}

	/**
	 * Add a one-click "تنظیم به‌عنوان فعال" row action on inactive
	 * hero_section entries, so switching the live variant doesn't require
	 * opening it and finding the checkbox. Already-active entries get no
	 * such link — there's nothing useful to do from here once it's live.
	 *
	 * @param array<string, string> $actions Existing row actions.
	 * @param \WP_Post              $post Post object for this row.
	 * @return array<string, string>
	 */
	public static function add_hero_set_active_row_action( $actions, $post ) {
		if ( 'hero_section' !== $post->post_type || ! current_user_can( 'edit_post', $post->ID ) ) {
			return $actions;
		}
		if ( (bool) get_post_meta( $post->ID, 'shcore_hero_active', true ) ) {
			return $actions;
		}

		$url = wp_nonce_url(
			admin_url( 'admin.php?action=shcore_set_active_hero&post=' . $post->ID ),
			'shcore_set_active_hero_' . $post->ID
		);

		$actions['shcore_set_active_hero'] = '<a href="' . esc_url( $url ) . '">' . esc_html__( 'تنظیم به‌عنوان فعال', 'shola-core' ) . '</a>';
		return $actions;
	}

	/**
	 * Handles the "Set as active" row-action link: verifies the nonce and
	 * edit capability, marks the requested hero_section active, deactivates
	 * every other one (same helper save_meta_boxes() uses), then redirects
	 * back to the list table.
	 *
	 * @return void
	 */
	public static function handle_set_active_hero() {
		$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0;

		if ( ! $post_id
			|| ! isset( $_GET['_wpnonce'] )
			|| ! wp_verify_nonce( sanitize_key( $_GET['_wpnonce'] ), 'shcore_set_active_hero_' . $post_id )
			|| 'hero_section' !== get_post_type( $post_id )
			|| ! current_user_can( 'edit_post', $post_id )
		) {
			wp_die( esc_html__( 'درخواست نامعتبر است.', 'shola-core' ) );
		}

		update_post_meta( $post_id, 'shcore_hero_active', true );
		self::deactivate_other_hero_sections( $post_id );

		wp_safe_redirect( admin_url( 'edit.php?post_type=hero_section' ) );
		exit;
	}

	/**
	 * Seed one default, active "تک‌ستونی" hero_section entry so the admin
	 * list isn't empty and the active-flag mechanism is testable
	 * immediately — see init()'s comment on why this runs on admin_init
	 * rather than only on plugin activation.
	 *
	 * @return void
	 */
	public static function seed_default_hero_section() {
		if ( get_option( 'shcore_default_hero_seeded' ) ) {
			return;
		}

		$existing = get_posts(
			array(
				'post_type'      => 'hero_section',
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
			)
		);

		if ( ! $existing ) {
			$post_id = wp_insert_post(
				array(
					'post_type'   => 'hero_section',
					'post_status' => 'publish',
					'post_title'  => __( 'هدر پیش‌فرض (تک‌ستونی)', 'shola-core' ),
				)
			);

			if ( $post_id && ! is_wp_error( $post_id ) ) {
				update_post_meta( $post_id, 'shcore_hero_active', true );
				update_post_meta( $post_id, 'shcore_hero_layout', 'single' );
				update_post_meta( $post_id, 'shcore_hero_rail_publication', 'shola-jawid' );
			}
		}

		update_option( 'shcore_default_hero_seeded', true );
	}

	/**
	 * Same singleton-enforcement helper as deactivate_other_hero_sections()
	 * above, for masthead_section instead.
	 *
	 * @param int $active_id Post ID of the masthead_section just marked active.
	 * @return void
	 */
	private static function deactivate_other_masthead_sections( $active_id ) {
		$others = get_posts(
			array(
				'post_type'      => 'masthead_section',
				'posts_per_page' => -1,
				'post__not_in'   => array( $active_id ),
				'post_status'    => 'any',
				'fields'         => 'ids',
			)
		);

		foreach ( $others as $other_id ) {
			update_post_meta( $other_id, 'shcore_masthead_active', false );
		}
	}

	/**
	 * Same "وضعیت" status column as add_hero_status_column() above, for
	 * masthead_section's list table.
	 *
	 * @param array<string, string> $columns Default columns.
	 * @return array<string, string>
	 */
	public static function add_masthead_status_column( $columns ) {
		$new = array();
		foreach ( $columns as $key => $label ) {
			$new[ $key ] = $label;
			if ( 'title' === $key ) {
				$new['shcore_masthead_status'] = __( 'وضعیت', 'shola-core' );
			}
		}
		return $new;
	}

	/**
	 * Render the "وضعیت" column's value for one masthead_section row.
	 *
	 * @param string $column Column key being rendered.
	 * @param int    $post_id Post ID for this row.
	 * @return void
	 */
	public static function render_masthead_status_column( $column, $post_id ) {
		if ( 'shcore_masthead_status' !== $column ) {
			return;
		}
		$is_active = (bool) get_post_meta( $post_id, 'shcore_masthead_active', true );
		if ( $is_active ) {
			echo '<strong style="color:#0a7d2c">' . esc_html__( 'فعال', 'shola-core' ) . '</strong>';
		} else {
			echo '<span style="color:#777">' . esc_html__( 'غیرفعال', 'shola-core' ) . '</span>';
		}
	}

	/**
	 * Same one-click "Set as active" row action as
	 * add_hero_set_active_row_action() above, for masthead_section.
	 *
	 * @param array<string, string> $actions Existing row actions.
	 * @param \WP_Post              $post Post object for this row.
	 * @return array<string, string>
	 */
	public static function add_masthead_set_active_row_action( $actions, $post ) {
		if ( 'masthead_section' !== $post->post_type || ! current_user_can( 'edit_post', $post->ID ) ) {
			return $actions;
		}
		if ( (bool) get_post_meta( $post->ID, 'shcore_masthead_active', true ) ) {
			return $actions;
		}

		$url = wp_nonce_url(
			admin_url( 'admin.php?action=shcore_set_active_masthead&post=' . $post->ID ),
			'shcore_set_active_masthead_' . $post->ID
		);

		$actions['shcore_set_active_masthead'] = '<a href="' . esc_url( $url ) . '">' . esc_html__( 'تنظیم به‌عنوان فعال', 'shola-core' ) . '</a>';
		return $actions;
	}

	/**
	 * Handles the masthead_section "Set as active" row-action link — same
	 * flow as handle_set_active_hero() above.
	 *
	 * @return void
	 */
	public static function handle_set_active_masthead() {
		$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : 0;

		if ( ! $post_id
			|| ! isset( $_GET['_wpnonce'] )
			|| ! wp_verify_nonce( sanitize_key( $_GET['_wpnonce'] ), 'shcore_set_active_masthead_' . $post_id )
			|| 'masthead_section' !== get_post_type( $post_id )
			|| ! current_user_can( 'edit_post', $post_id )
		) {
			wp_die( esc_html__( 'درخواست نامعتبر است.', 'shola-core' ) );
		}

		update_post_meta( $post_id, 'shcore_masthead_active', true );
		self::deactivate_other_masthead_sections( $post_id );

		wp_safe_redirect( admin_url( 'edit.php?post_type=masthead_section' ) );
		exit;
	}

	/**
	 * Seed one default, active masthead_section entry ("چیدمان فعلی") so
	 * the admin list isn't empty and the mechanism is testable
	 * immediately — same reasoning as seed_default_hero_section() above.
	 *
	 * @return void
	 */
	public static function seed_default_masthead_section() {
		if ( get_option( 'shcore_default_masthead_seeded' ) ) {
			return;
		}

		$existing = get_posts(
			array(
				'post_type'      => 'masthead_section',
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
			)
		);

		if ( ! $existing ) {
			$post_id = wp_insert_post(
				array(
					'post_type'   => 'masthead_section',
					'post_status' => 'publish',
					'post_title'  => __( 'هدر پیش‌فرض (چیدمان فعلی)', 'shola-core' ),
				)
			);

			if ( $post_id && ! is_wp_error( $post_id ) ) {
				update_post_meta( $post_id, 'shcore_masthead_active', true );
				update_post_meta( $post_id, 'shcore_masthead_layout', 'default' );
			}
		}

		update_option( 'shcore_default_masthead_seeded', true );
	}

	/**
	 * Parses `shcore_contents` (an issue's optional table of contents)
	 * into structured entries for single-issue.php and the metabox
	 * repeater. Stored as a JSON-encoded array of
	 * `{section, title, byline}` objects (schema changed 2026-08-06 from
	 * a pipe-delimited free-text format to this repeater-backed one — see
	 * docs/CHANGELOG.md for the migration record). All three fields are
	 * genuinely optional: a row with only a title, only a section, or any
	 * other partial combination is valid and rendered as-is; only fully
	 * empty rows are dropped, and that happens on save
	 * (sanitize_issue_contents()), not here. Malformed/non-JSON stored
	 * data (e.g. pre-migration content that was never converted) decodes
	 * to an empty array rather than erroring.
	 *
	 * Deliberately no per-entry page count or link to a real article: per
	 * docs/EXECUTION_PLAN.md's Phase 0.3 resolved assumption, issues are
	 * PDF-only — a table-of-contents entry describes what's in the PDF,
	 * it isn't a real WP post with its own permalink.
	 *
	 * @param int $post_id Issue post ID.
	 * @return array<int, array{section: string, title: string, byline: string}>
	 */
	public static function get_issue_contents( $post_id ) {
		$raw = get_post_meta( $post_id, 'shcore_contents', true );
		if ( ! $raw ) {
			return array();
		}

		$decoded = json_decode( $raw, true );
		if ( ! is_array( $decoded ) ) {
			return array();
		}

		$entries = array();
		foreach ( $decoded as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$entries[] = array(
				'section' => isset( $row['section'] ) ? (string) $row['section'] : '',
				'title'   => isset( $row['title'] ) ? (string) $row['title'] : '',
				'byline'  => isset( $row['byline'] ) ? (string) $row['byline'] : '',
			);
		}

		return $entries;
	}

	/**
	 * Sanitize callback for `shcore_contents`. Accepts a JSON string
	 * (from save_meta_boxes()'s own `wp_json_encode()` of the repeater's
	 * `$_POST` rows, or from REST), sanitizes every field with
	 * `sanitize_text_field()`, and drops rows that are fully empty across
	 * all three fields — a partially-filled row (e.g. title only) is kept
	 * as-is, since every field is independently optional by design, not
	 * just tolerated.
	 *
	 * Real bug found and fixed while building this: `wp_json_encode()`
	 * without `JSON_UNESCAPED_UNICODE` escapes every non-ASCII (i.e.
	 * every Persian) character as `\uXXXX`, and something in WP's own
	 * post-meta save path (empirically confirmed via an isolated
	 * `update_post_meta()` test, not just assumed) applies
	 * `stripslashes()`-style unslashing to the value on the way to the
	 * DB — which silently eats the backslash off every `\uXXXX`
	 * sequence, turning it into literal garbage text (`u0627` instead of
	 * the character it decodes to). `JSON_UNESCAPED_UNICODE` avoids the
	 * problem entirely by never producing a backslash-escape for Persian
	 * text in the first place. `JSON_UNESCAPED_SLASHES` alongside it for
	 * the same reason, applied preventatively (no `/` in real data hit
	 * this yet, but the failure mode would be identical).
	 *
	 * @param string $raw JSON-encoded array of {section, title, byline} rows.
	 * @return string JSON-encoded, sanitized array — or '' if nothing valid remains.
	 */
	public static function sanitize_issue_contents( $raw ) {
		$decoded = json_decode( (string) $raw, true );
		if ( ! is_array( $decoded ) ) {
			return '';
		}

		$clean = array();
		foreach ( $decoded as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$section = isset( $row['section'] ) ? sanitize_text_field( $row['section'] ) : '';
			$title   = isset( $row['title'] ) ? sanitize_text_field( $row['title'] ) : '';
			$byline  = isset( $row['byline'] ) ? sanitize_text_field( $row['byline'] ) : '';

			if ( '' === $section && '' === $title && '' === $byline ) {
				continue; // Fully empty row — nothing to keep.
			}

			$clean[] = array(
				'section' => $section,
				'title'   => $title,
				'byline'  => $byline,
			);
		}

		return $clean ? wp_json_encode( $clean, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) : '';
	}

	/**
	 * Enqueue the PDF-picker admin script, only on the edit screens for
	 * our three post types.
	 *
	 * @param string $hook Current admin page hook.
	 * @return void
	 */
	public static function enqueue_admin_assets( $hook ) {
		if ( 'post.php' !== $hook && 'post-new.php' !== $hook ) {
			return;
		}
		$screen = get_current_screen();
		if ( ! $screen || ! in_array( $screen->post_type, array( 'issue', 'document', 'party_publication', 'party_document' ), true ) ) {
			return;
		}

		wp_enqueue_media();
		wp_enqueue_script(
			'shcore-admin-meta',
			SHCORE_URL . 'admin/js/meta-boxes.js',
			array( 'jquery' ),
			SHCORE_VERSION,
			true
		);
		wp_enqueue_style(
			'shcore-admin-meta',
			SHCORE_URL . 'admin/css/meta-boxes.css',
			array(),
			SHCORE_VERSION
		);
	}
}
