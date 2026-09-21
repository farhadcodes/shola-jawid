/* شعله جاوید — Quiet Press v6 · main.js
   افزایش پیش‌رونده. سایت بدون جاوااسکریپت هم کار می‌کند. */
(function () {
  "use strict";

  /* ---------- صفحه بارگذاری — نمایش هنگام پیمایش (2026-09-15) ----------
     header.php's inline script already handles showing the loader on
     first page load and hiding it once that load finishes (a no-JS-safe,
     main.js-independent core, by design). This block is the enhancement
     layered on top: re-showing the same loader element right before an
     internal navigation, so the transition reads as continuous instead
     of only ever appearing on a fresh page load.
     Absent entirely, not just inert, when the loader is disabled via
     shola-core's settings — header.php doesn't render #page-loader at
     all in that case, so `loaderEl` is null and this whole block no-ops
     via the early return below.
     preventDefault() + double requestAnimationFrame() before actually
     navigating, rather than just letting the click proceed: a classic
     multi-page site's navigation begins tearing down the current page
     almost immediately once the browser processes the click, often
     before the newly-added `.is-visible` class has actually been
     painted — the double rAF guarantees at least one full paint cycle
     has happened first, so the loader is reliably visible for that
     instant rather than a coin-flip depending on browser/timing. */
  var loaderEl = document.getElementById("page-loader");
  if (loaderEl) {
    var showLoaderThenNavigate = function (navigate) {
      loaderEl.classList.add("is-visible");
      requestAnimationFrame(function () {
        requestAnimationFrame(navigate);
      });
    };

    /* Internal <a> clicks — articles, nav links, "read more", pagination,
       everything that's a normal same-site link. Deliberately skips:
       new-tab/new-window links (target != _self), modifier-clicked or
       middle-clicked links (people use these specifically to open
       something in the background without leaving the current page),
       download links, mailto:/tel:, same-page anchor jumps, external
       origins, and anything inside #wpadminbar (admin-bar links go to
       wp-admin, a different, unstyled area this loader has no business
       fronting for). */
    document.addEventListener("click", function (e) {
      if (e.defaultPrevented || e.button !== 0) return;
      if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
      var link = e.target.closest("a[href]");
      if (!link) return;
      if (link.closest("#wpadminbar")) return;
      if (link.target && link.target !== "_self") return;
      if (link.hasAttribute("download")) return;
      /* تراکت lightbox triggers (2026-09-17) — without this exclusion,
         this handler treats the trigger's <a href="{full-size image}">
         as an ordinary internal link, calls its own preventDefault(),
         and schedules its own manual `window.location.href` navigation
         via showLoaderThenNavigate() before the lightbox's own (later-
         registered) click handler ever runs — a real bug caught live:
         the lightbox opened correctly for an instant, then the page
         navigated to the raw image anyway once that scheduled
         navigation fired. Bailing out here leaves the click for the
         lightbox handler to handle entirely on its own. */
      if (link.hasAttribute("data-leaflet-trigger")) return;
      var href = link.getAttribute("href");
      if (!href || href.charAt(0) === "#") return;
      if (/^(mailto|tel):/i.test(href)) return;
      var url;
      try {
        url = new URL(href, window.location.href);
      } catch (err) {
        return;
      }
      if (url.origin !== window.location.origin) return;
      if (
        url.href === window.location.href ||
        (url.pathname === window.location.pathname &&
          url.search === window.location.search &&
          url.hash)
      ) {
        return; // same page, or just a same-page hash change
      }
      e.preventDefault();
      showLoaderThenNavigate(function () {
        window.location.href = url.href;
      });
    });

    /* GET form submissions — this theme's search form specifically.
       Scoped to method="get" only (the default when unspecified, hence
       the fallback below): a GET submission is itself a page navigation,
       exactly like a link click, so it belongs here. POST forms (Contact
       Form 7) are left alone on purpose — CF7 submits via its own AJAX
       and never navigates the page at all, so intercepting it here would
       show a loader that then has nothing to hide it (no navigation, no
       `load` event coming). */
    document.addEventListener("submit", function (e) {
      var form = e.target;
      if (!(form instanceof HTMLFormElement)) return;
      var method = (form.getAttribute("method") || "get").toLowerCase();
      if (method !== "get") return;
      e.preventDefault();
      showLoaderThenNavigate(function () {
        form.submit();
      });
    });
  }

  /* ---------- ارتفاع واقعی هدر برای هیرو (main.css §10) ----------
     .hero-media's height subtracts a hardcoded masthead-height constant
     — found (2026-08-08) to drift from the masthead's *actual* rendered
     height (e.g. the wp-admin toolbar adds extra height above it for
     logged-in visitors, and the constant was never meant to account for
     that), pushing the hero taller than the viewport and the bottom-
     anchored title below the fold on mobile. Measuring the real height
     and exposing it as a CSS custom property is more robust than
     guessing a bigger constant. Falls back to the CSS default (128px)
     until this runs, and if JS is disabled entirely. */
  var masthead = document.querySelector(".masthead");
  if (masthead) {
    var setMastheadHeightVar = function () {
      /* .bottom, not .height: a logged-in wp-admin toolbar (fixed,
         pushes <body> down via margin) sits above the masthead without
         changing the masthead element's own height — .bottom captures
         that offset too, since it's measured from the viewport top. */
      document.documentElement.style.setProperty(
        "--masthead-h",
        masthead.getBoundingClientRect().bottom + "px"
      );
    };
    setMastheadHeightVar();
    window.addEventListener("resize", setMastheadHeightVar);

    /* ---------- ناوبار چسبان و کوچک‌شونده (Phase 13, 2026-09-07) ----------
       Client-requested: full-size masthead at page top, shrinks once the
       page scrolls past a threshold (.masthead gets `.is-scrolled`; every
       compact-mode rule lives in main.css §05). Threshold via
       IntersectionObserver watching #mast-sentinel (header.php — a fixed
       80px from the top of the page) rather than a raw scroll listener,
       same technique already used above for scroll-reveal animations —
       cheaper than recalculating on every scroll frame.
       Deliberately does NOT re-run setMastheadHeightVar() here: --masthead-h
       only ever feeds .hero-media's one-time "fill the rest of the first
       viewport" height calc (main.css §10), which has nothing to do with
       the masthead's *current* (possibly shrunk) height while scrolling —
       doing so was tried and reverted after live testing showed it makes
       .hero-media grow taller exactly when the masthead compacts (a
       smaller --masthead-h means calc(100dvh - masthead-h) is *larger*),
       shifting content under the user mid-scroll. Progressive enhancement:
       .masthead is sticky via plain CSS regardless of JS (main.css);
       without IntersectionObserver support the masthead simply never
       shrinks, staying usable at full size. */
    var mastSentinel = document.getElementById("mast-sentinel");
    if (mastSentinel && "IntersectionObserver" in window) {
      var mastIO = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          masthead.classList.toggle("is-scrolled", !entry.isIntersecting);
        });
      });
      mastIO.observe(mastSentinel);
    }
  }

  /* ---------- تاریخ ماستهد — همیشه امروز، حتی از کش (2026-09-15) ----------
     The masthead date (.mast-runner/.mast-runner--inline, server-
     rendered by shola_get_masthead_runner()) was found frozen a full
     calendar day stale on some pages once the live site's host
     (Hostinger — LiteSpeed cache + their own CDN) started full-page
     caching: the PHP that renders it was always correct, but a cached
     page's HTML is static once written, so "compute today fresh on
     every request" only helps requests that actually reach PHP.
     Fetches inc/template-tags.php's shola_register_masthead_date_route()
     REST endpoint after load and overwrites whatever date the cached
     HTML happened to ship with. A plain REST GET isn't full-page-cached
     the way the document itself is, so this call re-executes PHP and
     returns the real current date regardless of how stale the page
     around it is — the endpoint also sends nocache_headers() itself as
     a second layer of the same guarantee.
     Progressive enhancement: without JS (or if the fetch fails —
     offline, a network hiccup, an unreachable REST API), the server-
     rendered date stays exactly as WordPress rendered it. That's a
     correctness regression only in the specific case a page was served
     from a stale cache entry to begin with — no worse than before this
     existed, never worse than working normally. sholaMastheadDate is
     localized in inc/enqueue.php, not hardcoded here, so this keeps
     working if the site's REST prefix or permalink structure ever
     changes. */
  /* .mast-runner alone is enough — .mast-runner--inline elements
     (header.php) always carry the base .mast-runner class too. */
  var mastRunners = document.querySelectorAll(".mast-runner");
  if (mastRunners.length && window.sholaMastheadDate && window.fetch) {
    fetch(window.sholaMastheadDate.endpoint, { cache: "no-store" })
      .then(function (response) {
        return response.ok ? response.json() : null;
      })
      .then(function (data) {
        if (data && data.date) {
          mastRunners.forEach(function (el) {
            el.textContent = data.date;
          });
        }
      })
      .catch(function () {
        /* Network/API failure: leave the server-rendered date as-is. */
      });
  }

  /* ---------- منوی بازشو (پاپ‌آپ کل‌صفحه) ---------- */
  var menuOpen  = document.getElementById("menu-open");
  var menuClose = document.getElementById("menu-close");
  var menu      = document.getElementById("menu-panel");

  function openMenu() {
    if (!menu) return;
    menu.setAttribute("data-open", "true");
    menu.setAttribute("aria-hidden", "false");
    document.documentElement.style.overflow = "hidden";
    if (menuClose) menuClose.focus();
  }
  function closeMenu() {
    if (!menu) return;
    menu.setAttribute("data-open", "false");
    menu.setAttribute("aria-hidden", "true");
    document.documentElement.style.overflow = "";
    if (menuOpen) menuOpen.focus();
  }

  if (menuOpen)  menuOpen.addEventListener("click", openMenu);
  if (menuClose) menuClose.addEventListener("click", closeMenu);
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape" && menu && menu.getAttribute("data-open") === "true") {
      closeMenu();
    }
  });

  /* ---------- اسکرول‌ریویل ---------- */
  var reduced = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  var reveals = document.querySelectorAll(".reveal");
  if (reveals.length && "IntersectionObserver" in window && !reduced) {
    var io = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (e) {
          if (e.isIntersecting) {
            e.target.classList.add("is-in");
            io.unobserve(e.target);
          }
        });
      },
      { rootMargin: "0px 0px -6% 0px" }
    );
    reveals.forEach(function (el) { io.observe(el); });
  } else {
    reveals.forEach(function (el) { el.classList.add("is-in"); });
  }

  /* ---------- منوی اشتراک‌گذاری (single.php) ---------- */
  var shareMenus = document.querySelectorAll(".share-menu");
  shareMenus.forEach(function (menu) {
    var trigger  = menu.querySelector(".share-trigger");
    var dropdown = menu.querySelector(".share-dropdown");
    if (!trigger || !dropdown) return;

    function closeShare() {
      dropdown.classList.remove("is-open");
      trigger.setAttribute("aria-expanded", "false");
    }
    function openShare() {
      dropdown.classList.add("is-open");
      trigger.setAttribute("aria-expanded", "true");
    }

    trigger.addEventListener("click", function () {
      if (dropdown.classList.contains("is-open")) closeShare(); else openShare();
    });
    document.addEventListener("click", function (e) {
      if (!menu.contains(e.target)) closeShare();
    });
    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape" && dropdown.classList.contains("is-open")) {
        closeShare();
        trigger.focus();
      }
    });

    var copyBtn = dropdown.querySelector(".share-copy");
    if (copyBtn) {
      var label      = copyBtn.querySelector(".share-copy-label");
      var origText   = label ? label.textContent : "";
      var copiedText = copyBtn.getAttribute("data-copied-label") || origText;

      function showCopied() {
        if (!label) return;
        label.textContent = copiedText;
        setTimeout(function () { label.textContent = origText; }, 2000);
      }

      /* Legacy fallback for contexts without the async Clipboard API
         (e.g. non-HTTPS — this API requires a secure context). Still
         widely supported despite being deprecated, and needs no
         permission prompt. */
      function copyFallback(text) {
        var input = document.createElement("textarea");
        input.value = text;
        input.setAttribute("readonly", "");
        input.style.position = "fixed";
        input.style.opacity = "0";
        document.body.appendChild(input);
        input.select();
        try { document.execCommand("copy"); showCopied(); } catch (err) { /* no-op: nothing more we can do */ }
        document.body.removeChild(input);
      }

      copyBtn.addEventListener("click", function () {
        var url = copyBtn.getAttribute("data-url") || "";
        if (navigator.clipboard && navigator.clipboard.writeText) {
          navigator.clipboard.writeText(url).then(showCopied, function () { copyFallback(url); });
        } else {
          copyFallback(url);
        }
      });
    }
  });

  /* ---------- نوار پیشرفت خواندن (فقط در مقالهٔ تکی) ---------- */
  var bar     = document.querySelector(".progress-bar");
  var article = document.querySelector("[data-progress-scope]");
  if (bar && article) {
    var update = function () {
      var rect  = article.getBoundingClientRect();
      var total = rect.height - window.innerHeight;
      var done  = Math.min(Math.max(-rect.top, 0), Math.max(total, 1));
      bar.style.width = (total > 0 ? (done / total) * 100 : 0) + "%";
    };
    window.addEventListener("scroll", update, { passive: true });
    window.addEventListener("resize", update);
    update();
  }

  /* ---------- نوار افقی آخرین مقالات (هیرو "filmstrip", 2026-09-13) ----------
     .hero-filmstrip-track already scrolls natively with zero JS (touch,
     trackpad, keyboard) — main.css §10.5. This section only layers two
     enhancements on top: the two arrow buttons calling scrollBy(), and a
     slow, continuous auto-drift that reverses direction at each end
     (paused while a visitor is actually interacting with the strip).
     Both degrade to nothing if JS is disabled; the strip stays fully
     scrollable by hand either way. */
  var filmstripTrack = document.querySelector(".hero-filmstrip-track");
  if (filmstripTrack) {
    /* RTL scrollLeft sign is not consistent across browsers: some report
       0/negative values scrolling "forward" (toward the start of the
       row) from a natural starting position of 0, others start at the
       maximum positive value and count down. Rather than guess per
       browser, feature-detect it once: nudge scrollLeft by +1 and see
       which way the browser actually interpreted that. `dirSign` then
       lets the rest of this code always think in one consistent
       direction ("+1 step" = further into the row, reading-order
       forward) regardless of the browser underneath it. */
    var dirSign = 1;
    (function detectRtlScrollSign() {
      var start = filmstripTrack.scrollLeft;
      filmstripTrack.scrollBy({ left: 1, behavior: "auto" });
      if (filmstripTrack.scrollLeft <= start) {
        dirSign = -1;
      }
      filmstripTrack.scrollBy({ left: start - filmstripTrack.scrollLeft, behavior: "auto" });
    })();

    function maxScroll() {
      return filmstripTrack.scrollWidth - filmstripTrack.clientWidth;
    }

    /* ---- دکمه‌های پیکان ---- */
    var filmstripArrows = document.querySelectorAll("[data-filmstrip-dir]");
    filmstripArrows.forEach(function (btn) {
      btn.addEventListener("click", function () {
        var step = filmstripTrack.clientWidth * 0.7;
        var dir = parseFloat(btn.getAttribute("data-filmstrip-dir")) || 1;
        filmstripTrack.scrollBy({ left: dirSign * dir * step, behavior: "smooth" });
      });
    });

    /* ---- لغزش خودکار آرام ---- */
    var reducedMotion = window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    if (!reducedMotion && maxScroll() > 4) {
      var autoDir    = 1;   // 1 = reading-order forward, -1 = backward
      var paused     = false;
      var pauseTimer = null;
      var speed      = 0.008; // px per animation frame — cut 0.4 -> 0.15 -> 0.05 -> 0.008, 2026-09-13: still read as too fast after two prior cuts, per Farhad's live feedback each time; this step is a much larger cut (~6x) than the previous two, deliberately, rather than another small increment

      /* Driven via scrollBy(), not a direct `scrollLeft = x` assignment
         — confirmed live (not assumed) that a direct assignment on
         this element silently has no effect at all, while scrollBy()
         reliably moves it (same method the arrow buttons already use
         above). `pendingFraction` accumulates the sub-pixel remainder
         a speed like 0.4px/frame leaves behind — scrollBy() only takes
         a delta, and passing it a fractional 0.4 every frame would
         still round away to a 0px move each time; only ever request a
         whole-pixel delta once the remainder has accumulated to at
         least one. */
      var pendingFraction = 0;

      var pause = function () {
        paused = true;
        if (pauseTimer) { clearTimeout(pauseTimer); }
      };
      var resumeSoon = function () {
        if (pauseTimer) { clearTimeout(pauseTimer); }
        pauseTimer = setTimeout(function () { paused = false; }, 1200);
      };

      ["mouseenter", "focusin", "touchstart", "pointerdown"].forEach(function (evt) {
        filmstripTrack.addEventListener(evt, pause, { passive: true });
      });
      ["mouseleave", "focusout", "touchend", "pointerup"].forEach(function (evt) {
        filmstripTrack.addEventListener(evt, resumeSoon, { passive: true });
      });

      var tick = function () {
        if (!paused) {
          var max = maxScroll();
          var current = filmstripTrack.scrollLeft * dirSign; // 0..max, reading-order-forward units
          pendingFraction += autoDir * speed;
          var deltaPixels = Math.trunc(pendingFraction);
          if (deltaPixels !== 0) {
            // Clamp the requested step so it can't overshoot past 0/max
            // and cause the strip to visibly bump against the end
            // before reversing next frame.
            var next = current + deltaPixels;
            if (next >= max) {
              deltaPixels = Math.round(max - current);
              autoDir = -1;
            } else if (next <= 0) {
              deltaPixels = Math.round(0 - current);
              autoDir = 1;
            }
            filmstripTrack.scrollBy({ left: dirSign * deltaPixels, behavior: "auto" });
            pendingFraction -= deltaPixels; // keep the sub-pixel remainder, don't discard it
          }
        }
        window.requestAnimationFrame(tick);
      };
      window.requestAnimationFrame(tick);
    }
  }

  /* ---------- کتابخانه homepage "shelf" (front-page.php, 2026-09-21) ----------
     .library-shelf-track already scrolls natively with zero JS (touch,
     trackpad, keyboard) — main.css §33. This only layers the two arrow
     buttons on top, same scrollBy()-based approach and RTL scroll-sign
     detection as the hero filmstrip above — but deliberately no
     auto-drift here: this is a content shelf people browse on purpose,
     not a decorative accent, so an unprompted auto-scroll would fight a
     visitor actually looking at the covers instead of adding ambiance. */
  var libraryShelfTrack = document.querySelector(".library-shelf-track");
  if (libraryShelfTrack) {
    var libraryDirSign = 1;
    (function detectLibraryRtlScrollSign() {
      var start = libraryShelfTrack.scrollLeft;
      libraryShelfTrack.scrollBy({ left: 1, behavior: "auto" });
      if (libraryShelfTrack.scrollLeft <= start) {
        libraryDirSign = -1;
      }
      libraryShelfTrack.scrollBy({ left: start - libraryShelfTrack.scrollLeft, behavior: "auto" });
    })();

    document.querySelectorAll("[data-library-shelf-dir]").forEach(function (btn) {
      btn.addEventListener("click", function () {
        var step = libraryShelfTrack.clientWidth * 0.7;
        var dir = parseFloat(btn.getAttribute("data-library-shelf-dir")) || 1;
        libraryShelfTrack.scrollBy({ left: libraryDirSign * dir * step, behavior: "smooth" });
      });
    });
  }

  /* ---------- گالری تمام‌صفحهٔ تراکت (page-leaflets.php, front-page.php,
     2026-09-17) ----------
     A native <dialog>, not a hand-built overlay: .showModal() natively
     traps focus inside it and Escape natively closes it (fires a
     "cancel" event), so neither needs custom code here. Two modes share
     one <dialog> markup (template-parts/leaflets/lightbox.php):
       - Archive page: trigger has data-leaflet-index; prev/next cycle
         through the JSON array page-leaflets.php embeds
         (#leaflet-lightbox-data) — capped to that page's own loaded
         batch, never reaching across a "بارگذاری بیشتر" page boundary
         (confirmed with Farhad).
       - Homepage teaser: trigger has no data-leaflet-index; its own
         data-leaflet-* attributes are read directly instead, prev/next
         controls are hidden, since there is only ever this one image in
         that context.
     No-JS fallback: every trigger is a real <a href="{full-size image
     URL}">; this block only ever runs if JS is enabled at all, and only
     intercepts (preventDefault) the click once it has actually found a
     dialog + a valid item to show. */
  var leafletDialog = document.getElementById("leaflet-lightbox");
  if (leafletDialog && typeof leafletDialog.showModal === "function") {
    var leafletImage   = leafletDialog.querySelector(".leaflet-lightbox-image");
    var leafletCaption = leafletDialog.querySelector(".leaflet-lightbox-caption");
    var leafletPrevBtn = leafletDialog.querySelector(".leaflet-lightbox-prev");
    var leafletNextBtn = leafletDialog.querySelector(".leaflet-lightbox-next");
    var leafletCloseBtn = leafletDialog.querySelector(".leaflet-lightbox-close");

    var leafletFullData = [];
    var leafletDataEl = document.getElementById("leaflet-lightbox-data");
    if (leafletDataEl) {
      try {
        leafletFullData = JSON.parse(leafletDataEl.textContent) || [];
      } catch (err) {
        leafletFullData = [];
      }
    }

    var leafletActiveData = [];  // whichever set is showing right now (the full page batch, or a single-item array)
    var leafletCurrentIndex = -1;
    var leafletIsSingle = false;
    var leafletReturnFocusEl = null;

    /*
     * Whether the prev/next controls exist in the DOM at all, decided
     * once here from what the server actually rendered — not toggled
     * per open() call. Fixed 2026-09-17: an earlier version always
     * rendered both buttons and hid the unused pair with the `hidden`
     * attribute, which left non-functional (but still real, still
     * tabbable) controls behind whenever a dialog only ever shows one
     * item — see lightbox.php's own docblock for the server-side half of
     * this fix. Keyboard arrows and swipe below both gate on this,
     * rather than solely on leafletIsSingle, so the same "no dead
     * affordance" guarantee also covers an archive page that happens to
     * load exactly one leaflet — leafletIsSingle only decides which
     * dataset to render, not whether nav controls exist.
     */
    var leafletHasNav = !!(leafletPrevBtn && leafletNextBtn);

    var leafletIsRtl = function () {
      return getComputedStyle(document.documentElement).direction === "rtl";
    };

    var leafletPreload = function (url) {
      if (!url) return;
      var img = new Image();
      img.src = url;
    };

    var leafletRender = function (index) {
      var item = leafletActiveData[index];
      if (!item) return;
      leafletCurrentIndex = index;
      leafletImage.setAttribute("src", item.image || "");
      leafletImage.setAttribute("alt", item.alt || "");

      /* Caption rebuilt fresh on every render — the title/caption
         paragraph is only ever created via createElement when this
         specific entry actually has one; it is never created-then-
         hidden. The container itself always ends up with at least the
         date paragraph, since native post_date is never empty, so the
         container is never left empty. */
      while (leafletCaption.firstChild) {
        leafletCaption.removeChild(leafletCaption.firstChild);
      }
      if (item.caption) {
        var titleEl = document.createElement("p");
        titleEl.className = "leaflet-lightbox-title";
        titleEl.textContent = item.caption;
        leafletCaption.appendChild(titleEl);
      }
      var dateEl = document.createElement("p");
      dateEl.className = "leaflet-lightbox-date";
      dateEl.textContent = item.date || "";
      leafletCaption.appendChild(dateEl);

      if (!leafletIsSingle) {
        if (leafletActiveData[index + 1]) leafletPreload(leafletActiveData[index + 1].image);
        if (leafletActiveData[index - 1]) leafletPreload(leafletActiveData[index - 1].image);
      }
    };

    var leafletGoNext = function () {
      // "next" = chronologically older = the following item in this
      // newest-first array.
      if (leafletCurrentIndex + 1 < leafletActiveData.length) {
        leafletRender(leafletCurrentIndex + 1);
      }
    };
    var leafletGoPrev = function () {
      if (leafletCurrentIndex - 1 >= 0) {
        leafletRender(leafletCurrentIndex - 1);
      }
    };

    var leafletOpen = function (triggerEl, index, singleItem) {
      leafletReturnFocusEl = triggerEl;
      if (singleItem) {
        leafletIsSingle = true;
        leafletActiveData = [singleItem];
        leafletRender(0);
      } else {
        leafletIsSingle = false;
        leafletActiveData = leafletFullData;
        leafletRender(index);
      }
      leafletDialog.showModal();
      if (leafletCloseBtn) leafletCloseBtn.focus();
      document.documentElement.style.overflow = "hidden";
    };

    /*
     * Close handling is entirely explicit — not built on <dialog>'s
     * native "close" event. Verified live during implementation that
     * this event does not fire in the browser used to test this
     * feature, even from a direct, real dialog.close() method call
     * (confirmed with a synchronous check plus a 100ms wait, ruling out
     * a timing issue) — so nothing here depends on it. leafletClose()
     * is the one function every close path calls; it runs cleanup
     * itself immediately rather than waiting on an event that may not
     * arrive.
     */
    var leafletClose = function () {
      leafletDialog.close();
      document.documentElement.style.overflow = "";
      leafletImage.setAttribute("src", "");
      if (leafletReturnFocusEl) leafletReturnFocusEl.focus();
    };

    if (leafletNextBtn) leafletNextBtn.addEventListener("click", leafletGoNext);
    if (leafletPrevBtn) leafletPrevBtn.addEventListener("click", leafletGoPrev);
    if (leafletCloseBtn) leafletCloseBtn.addEventListener("click", leafletClose);

    /* Click landing on the <dialog> element itself (not one of its
       button/stage children) is "outside the image" — see
       lightbox.php's own comment on why the markup structure makes this
       check work. */
    leafletDialog.addEventListener("click", function (e) {
      if (e.target === leafletDialog) {
        leafletClose();
      }
    });

    /*
     * Escape and the Tab focus trap are both explicit here for the same
     * reason as leafletClose() above — verified live that a real,
     * trusted Escape keypress did not close the dialog natively in the
     * browser used to test this (confirmed with the automation tool's
     * genuine OS-level key-press action, not a synthetic KeyboardEvent,
     * which would not be a fair test of native browser default actions
     * either way). Rather than trust each browser's own level of
     * <dialog> support, both behaviors are implemented directly so they
     * work identically everywhere.
     */
    leafletDialog.addEventListener("keydown", function (e) {
      if (e.key === "Escape") {
        e.preventDefault();
        leafletClose();
        return;
      }
      if (e.key === "Tab") {
        var focusable = leafletDialog.querySelectorAll(
          'button:not([hidden]), a[href]'
        );
        if (!focusable.length) return;
        var first = focusable[0];
        var last = focusable[focusable.length - 1];
        if (e.shiftKey && document.activeElement === first) {
          e.preventDefault();
          last.focus();
        } else if (!e.shiftKey && document.activeElement === last) {
          e.preventDefault();
          first.focus();
        }
        return;
      }
      /* Physical Left/Right arrow keys mapped to whichever control is
         visually on that side, read from the page's actual computed
         direction at runtime rather than hardcoded — confirmed against
         this site's own default dir="rtl": "next" (older) sits visually
         left, "previous" (newer) visually right; reversed under
         dir="ltr". Gated on leafletHasNav (whether the controls actually
         exist), not leafletIsSingle — a dead arrow-key response is the
         same "affordance with nothing to do" problem as a dead click,
         and this covers the rare exactly-one-item archive case too. */
      if (!leafletHasNav) return;
      if (e.key === "ArrowLeft") {
        leafletIsRtl() ? leafletGoNext() : leafletGoPrev();
      } else if (e.key === "ArrowRight") {
        leafletIsRtl() ? leafletGoPrev() : leafletGoNext();
      }
    });

    /* Touch/swipe — real drag-distance tracking (touchstart -> touchend
       delta), not a swipe-triggers-click hack. Gated on leafletHasNav,
       same reasoning as the keyboard handler above: no swipe should
       attempt to navigate (or produce any flicker/reset) when there is
       nothing to navigate to. */
    var leafletTouchStartX = null;
    leafletDialog.addEventListener(
      "touchstart",
      function (e) {
        if (!leafletHasNav || e.touches.length !== 1) return;
        leafletTouchStartX = e.touches[0].clientX;
      },
      { passive: true }
    );
    leafletDialog.addEventListener(
      "touchend",
      function (e) {
        if (!leafletHasNav || leafletTouchStartX === null) return;
        var touch = e.changedTouches && e.changedTouches[0];
        var startX = leafletTouchStartX;
        leafletTouchStartX = null;
        if (!touch) return;
        var deltaX = touch.clientX - startX;
        var threshold = 40;
        if (Math.abs(deltaX) < threshold) return;
        // Swiping toward reading-start (right-to-left drag, deltaX < 0)
        // moves forward in an RTL reading order -> older/next; the
        // opposite drag -> newer/previous. Reversed under dir="ltr".
        if (deltaX < 0) {
          leafletIsRtl() ? leafletGoNext() : leafletGoPrev();
        } else {
          leafletIsRtl() ? leafletGoPrev() : leafletGoNext();
        }
      },
      { passive: true }
    );

    document.addEventListener("click", function (e) {
      var trigger = e.target.closest("[data-leaflet-trigger]");
      if (!trigger) return;
      e.preventDefault();
      if (trigger.hasAttribute("data-leaflet-index")) {
        leafletOpen(trigger, parseInt(trigger.getAttribute("data-leaflet-index"), 10) || 0, null);
      } else {
        leafletOpen(trigger, 0, {
          image: trigger.getAttribute("data-leaflet-image") || "",
          caption: trigger.getAttribute("data-leaflet-caption") || "",
          date: trigger.getAttribute("data-leaflet-date") || "",
          alt: trigger.getAttribute("data-leaflet-alt") || ""
        });
      }
    });
  }
})();
