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
		 * ترتیب (manual sort order) for `publication` terms — added
		 * 2026-09-02, Farhad reported the دوره tiles (see
		 * seed_publication_periods() above) weren't rendering in
		 * اول/دوم/سوم/چهارم order (WordPress's default term order for a
		 * custom taxonomy isn't creation order or name order in any
		 * guaranteed way) and asked for a CMS field to fix that himself,
		 * for these and any دوره he adds later. No such field exists on a
		 * custom taxonomy by default, so this adds one via term meta —
		 * shown as a plain number field on both the "Add New" and "Edit"
		 * term screens, plus a read-only column on the term list so the
		 * current values are visible at a glance (not made click-to-sort,
		 * to avoid the extra `terms_clauses` filtering that would need —
		 * not asked for, and the front-end sort, not the admin table, is
		 * what actually matters here).
		 */
		add_action( 'init', array( __CLASS__, 'register_publication_order_meta' ) );
		add_action( 'publication_add_form_fields', array( __CLASS__, 'render_publication_order_add_field' ) );
		add_action( 'publication_edit_form_fields', array( __CLASS__, 'render_publication_order_edit_field' ) );
		add_action( 'created_publication', array( __CLASS__, 'save_publication_order' ) );
		add_action( 'edited_publication', array( __CLASS__, 'save_publication_order' ) );
		add_filter( 'manage_edit-publication_columns', array( __CLASS__, 'add_publication_order_column' ) );
		add_filter( 'manage_publication_custom_column', array( __CLASS__, 'render_publication_order_column' ), 10, 3 );
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
	 * Registers `shcore_term_order` on the `publication` taxonomy — see
	 * the init() comment above for why this exists.
	 *
	 * @return void
	 */
	public static function register_publication_order_meta() {
		register_term_meta(
			'publication',
			'shcore_term_order',
			array(
				'type'              => 'integer',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => 'absint',
				'auth_callback'     => function () {
					return current_user_can( 'manage_categories' );
				},
			)
		);
	}

	/**
	 * ترتیب field on the "افزودن دستهٔ جدید" (Add New Term) side panel —
	 * WP core's own `<div class="form-field">` markup convention for that
	 * screen, matching every core field around it.
	 *
	 * @return void
	 */
	public static function render_publication_order_add_field() {
		?>
		<div class="form-field">
			<label for="shcore-term-order"><?php esc_html_e( 'ترتیب', 'shola-core' ); ?></label>
			<input type="number" name="shcore_term_order" id="shcore-term-order" step="1" min="0">
			<p><?php esc_html_e( 'عددی کوچک‌تر زودتر نمایش داده می‌شود (مثلاً ۱ برای دورهٔ اول). خالی یعنی آخر فهرست.', 'shola-core' ); ?></p>
		</div>
		<?php
	}

	/**
	 * ترتیب field on the term "ویرایش" (Edit) screen — WP core's own
	 * `<tr class="form-field">` markup convention for that screen.
	 *
	 * @param \WP_Term $term Term being edited.
	 * @return void
	 */
	public static function render_publication_order_edit_field( $term ) {
		$order = get_term_meta( $term->term_id, 'shcore_term_order', true );
		?>
		<tr class="form-field">
			<th scope="row"><label for="shcore-term-order"><?php esc_html_e( 'ترتیب', 'shola-core' ); ?></label></th>
			<td>
				<input type="number" name="shcore_term_order" id="shcore-term-order" step="1" min="0" value="<?php echo esc_attr( $order ); ?>">
				<p class="description"><?php esc_html_e( 'عددی کوچک‌تر زودتر نمایش داده می‌شود (مثلاً ۱ برای دورهٔ اول). خالی یعنی آخر فهرست.', 'shola-core' ); ?></p>
			</td>
		</tr>
		<?php
	}

	/**
	 * Saves ترتیب on both create and edit — `created_publication`/
	 * `edited_publication` both fire with the term ID as their first
	 * arg, so one handler covers both hooks. No nonce check here
	 * deliberately: both hooks only ever fire as a result of
	 * `wp_insert_term()`/`wp_update_term()` being called from WP core's
	 * own admin term-management flows (the "Add New Term" AJAX handler,
	 * the term-edit page's save handler), each of which already verifies
	 * its own nonce — a different one per flow — before ever reaching
	 * that call; adding a second, hand-picked nonce check here would
	 * just be guessing at (and could easily mismatch) core's internal
	 * nonce action names. The capability check below is what actually
	 * matters. Empty input clears the value (falls back to "last") rather
	 * than being coerced to 0, which would instead sort it *first* — `''`
	 * and `'0'` are meaningfully different here, so this checks for an
	 * empty string specifically, not falsiness.
	 *
	 * @param int $term_id Term ID.
	 * @return void
	 */
	public static function save_publication_order( $term_id ) {
		if ( ! current_user_can( 'manage_categories' ) || ! isset( $_POST['shcore_term_order'] ) ) {
			return;
		}

		$raw = sanitize_text_field( wp_unslash( $_POST['shcore_term_order'] ) );
		if ( '' === $raw ) {
			delete_term_meta( $term_id, 'shcore_term_order' );
			return;
		}

		update_term_meta( $term_id, 'shcore_term_order', absint( $raw ) );
	}

	/**
	 * Adds the ترتیب column to the `publication` term list table, between
	 * توضیح and شمار (count) — a plain read-only display, not click-to-
	 * sort (see the init() comment for why).
	 *
	 * @param string[] $columns Existing column id => label map.
	 * @return string[]
	 */
	public static function add_publication_order_column( $columns ) {
		$new_columns = array();
		foreach ( $columns as $key => $label ) {
			$new_columns[ $key ] = $label;
			if ( 'description' === $key ) {
				$new_columns['shcore_term_order'] = __( 'ترتیب', 'shola-core' );
			}
		}
		return $new_columns;
	}

	/**
	 * Renders the ترتیب column's value for one row.
	 *
	 * @param string $content     Existing column content (always '' for a
	 *                            custom column — WP core never fills this in).
	 * @param string $column_name Column being rendered.
	 * @param int    $term_id     Term ID for this row.
	 * @return string
	 */
	public static function render_publication_order_column( $content, $column_name, $term_id ) {
		if ( 'shcore_term_order' !== $column_name ) {
			return $content;
		}
		$order = get_term_meta( $term_id, 'shcore_term_order', true );
		return '' === $order ? '—' : esc_html( $order );
	}
}
