/* Shola Core — reorders the block-editor's document-settings sidebar
   panels for the `post` screen to: موضوعات, موضوع اصلی, گزارش, برچسب‌ها
   (Farhad, 2026-09-02; گزارش added 2026-09-05 when it became a real
   taxonomy instead of a post_tag — see class-taxonomies.php).

   There is no public Gutenberg API to set panel order when mixing
   built-in taxonomy panels (موضوعات/topic, برچسب‌ها/post_tag) with a
   custom PluginDocumentSettingPanel (موضوع اصلی — admin/js/
   primary-topic.js): both kinds render as plain sibling
   `.components-panel__body` elements under the same
   `.editor-sidebar__panel` container, in whatever order the editor's
   internal panel registry produced, with no `position`/`order` prop
   exposed on either mechanism. Confirmed live (2026-09-02) rather than
   assumed, by inspecting the actual rendered DOM.

   This reorders those three sibling nodes in place by matching each
   panel's visible title text — deliberately conservative: it only acts
   when every panel currently in that container matches a name in
   DESIRED_ORDER, so if a future core panel (Featured image, Excerpt,
   Discussion — currently unused on this screen) is ever enabled, or
   another custom panel is added, this leaves the whole group alone
   rather than guessing where the new one belongs.

   A MutationObserver (not a one-time reorder at load) is required
   because the block editor re-renders this panel list on state changes
   (e.g. toggling a topic checkbox re-renders «موضوع اصلی»'s own
   content, which re-renders the whole sibling group) — scoped to the
   sidebar panel container itself, once it first appears, specifically
   to avoid reacting to unrelated DOM churn elsewhere on the screen
   (the post content area updates on every keystroke; observing
   document-wide would run this on every keystroke too). */
(function (wp) {
	"use strict";

	if (!wp || !wp.domReady) {
		return;
	}

	var DESIRED_ORDER = ["موضوعات", "موضوع اصلی", "گزارش", "برچسب‌ها"];

	function getPanelTitle(panelBody) {
		var btn = panelBody.querySelector(
			".components-panel__body-title button, .components-panel__body-toggle"
		);
		return btn ? btn.textContent.trim() : "";
	}

	function reorderGroup(parent, panels) {
		var titled = panels.map(function (p) {
			return { el: p, title: getPanelTitle(p) };
		});

		var allMatch = titled.every(function (p) {
			return DESIRED_ORDER.indexOf(p.title) !== -1;
		});
		if (!allMatch) {
			return;
		}

		var sorted = titled.slice().sort(function (a, b) {
			return DESIRED_ORDER.indexOf(a.title) - DESIRED_ORDER.indexOf(b.title);
		});

		var alreadyInOrder = sorted.every(function (item, i) {
			return item.el === titled[i].el;
		});
		if (alreadyInOrder) {
			return;
		}

		sorted.forEach(function (item) {
			parent.appendChild(item.el);
		});
	}

	/**
	 * The three panels aren't direct children of `.editor-sidebar__panel`
	 * — each sits inside its own generated wrapper `<div>` (an
	 * Emotion/styled-components class, not a stable selector to hardcode),
	 * and all three wrappers are themselves siblings under that container.
	 * So this groups every `.components-panel__body` in the sidebar by its
	 * *actual* immediate parent (found empirically, not assumed) rather
	 * than assuming they're direct children of the outer container.
	 *
	 * @param {Element} container `.editor-sidebar__panel.components-panel`.
	 * @return {void}
	 */
	function reorder(container) {
		var panels = container.querySelectorAll(".components-panel__body");
		if (panels.length < 2) {
			return;
		}

		var groups = new Map();
		Array.prototype.forEach.call(panels, function (p) {
			var parent = p.parentElement;
			if (!groups.has(parent)) {
				groups.set(parent, []);
			}
			groups.get(parent).push(p);
		});

		groups.forEach(function (groupPanels, parent) {
			if (groupPanels.length > 1) {
				reorderGroup(parent, groupPanels);
			}
		});
	}

	wp.domReady(function () {
		var containerObserver = null;

		var bodyObserver = new MutationObserver(function () {
			var container = document.querySelector(".editor-sidebar__panel.components-panel");
			if (!container) {
				return;
			}

			bodyObserver.disconnect();
			reorder(container);

			containerObserver = new MutationObserver(function () {
				reorder(container);
			});
			containerObserver.observe(container, { childList: true, subtree: true });
		});

		bodyObserver.observe(document.body, { childList: true, subtree: true });
	});
})(window.wp);
