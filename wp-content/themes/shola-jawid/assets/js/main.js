/* شعله جاوید — Quiet Press v6 · main.js
   افزایش پیش‌رونده. سایت بدون جاوااسکریپت هم کار می‌کند. */
(function () {
  "use strict";

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
      var speed      = 0.4; // px per animation frame, deliberately slow

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
})();
