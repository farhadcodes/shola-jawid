/* Shola Core — «موضوع اصلی» (primary topic) panel, block editor only.
   `topic` stays a normal multi-select taxonomy (Farhad, 2026-09-02:
   selecting several topics per article is the standard/expected
   behavior — reverted from an earlier single-select-only attempt this
   same session). What actually needed fixing was the breadcrumb/card
   term (array_shift() of get_the_terms(), previously in card.php,
   single.php, and Taxonomies::filter_post_permalink()): with several
   topics assigned, it picked whichever one WordPress's default term
   ordering (alphabetical by name) happened to sort first — not
   necessarily editorial intent.
   This panel adds a second, separate control: a radio list built only
   from the topics currently checked above, letting the editor name
   exactly one as primary. Saved to post meta `shcore_primary_topic`
   (registered in class-meta-fields.php). Resolution/fallback logic
   (what to show if this is empty or points at a topic that's since been
   unchecked) lives server-side in
   SholaCore\Taxonomies::get_primary_topic() — this script only writes
   the editor's choice, it never decides what actually renders. */
(function (wp) {
	"use strict";

	if (!wp || !wp.plugins || !wp.editPost || !wp.data || !wp.element || !wp.components) {
		return;
	}

	var registerPlugin = wp.plugins.registerPlugin;
	var PluginDocumentSettingPanel = wp.editPost.PluginDocumentSettingPanel;
	var el = wp.element.createElement;
	var useSelect = wp.data.useSelect;
	var useDispatch = wp.data.useDispatch;
	var RadioControl = wp.components.RadioControl;
	var __ = wp.i18n.__;

	var termNamesById = {};
	((window.shcoreTopics && window.shcoreTopics.terms) || []).forEach(function (term) {
		termNamesById[term.id] = term.name;
	});

	function PrimaryTopicPicker() {
		var topicIds = useSelect(function (select) {
			var ids = select("core/editor").getEditedPostAttribute("topic");
			return Array.isArray(ids) ? ids : [];
		}, []);
		var primaryTopicId = useSelect(function (select) {
			var meta = select("core/editor").getEditedPostAttribute("meta") || {};
			return meta.shcore_primary_topic || 0;
		}, []);
		var editPost = useDispatch("core/editor").editPost;

		if (!topicIds.length) {
			return el(
				"p",
				{ className: "components-base-control__help" },
				__("ابتدا حداقل یک موضوع را از بالا انتخاب کنید.", "shola-core")
			);
		}

		var options = topicIds
			.filter(function (id) {
				return termNamesById[id];
			})
			.map(function (id) {
				return { label: termNamesById[id], value: String(id) };
			});

		var selected = topicIds.indexOf(primaryTopicId) !== -1 ? String(primaryTopicId) : "";

		return el(
			wp.element.Fragment,
			{},
			el(RadioControl, {
				selected: selected,
				options: options,
				onChange: function (value) {
					editPost({ meta: { shcore_primary_topic: parseInt(value, 10) } });
				},
			}),
			el(
				"p",
				{ className: "components-base-control__help" },
				__("اگر انتخاب نکنید، یکی از موضوعات انتخاب‌شده به‌صورت پیش‌فرض در breadcrumb نمایش داده می‌شود.", "shola-core")
			)
		);
	}

	registerPlugin("shcore-primary-topic", {
		render: function () {
			return el(
				PluginDocumentSettingPanel,
				{ name: "shcore-primary-topic-picker", title: __("موضوع اصلی", "shola-core") },
				el(PrimaryTopicPicker)
			);
		},
	});
})(window.wp);
