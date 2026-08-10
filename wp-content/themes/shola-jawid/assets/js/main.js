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
})();
