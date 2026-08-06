/* شعله جاوید — Quiet Press v6 · main.js
   افزایش پیش‌رونده. سایت بدون جاوااسکریپت هم کار می‌کند. */
(function () {
  "use strict";

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

  /* ---------- کلید زبان (نمایشی) ----------
     در وردپرس: به آیتم متناظر en/ پیوند داده می‌شود. */
  var langToggle = document.getElementById("lang-toggle");
  if (langToggle) {
    langToggle.addEventListener("click", function () {
      var html = document.documentElement;
      var toEn = html.getAttribute("dir") === "rtl";
      html.setAttribute("dir",  toEn ? "ltr" : "rtl");
      html.setAttribute("lang", toEn ? "en"  : "fa");
      langToggle.textContent = toEn ? "FA" : "EN";
      langToggle.setAttribute(
        "aria-label",
        toEn ? "تغییر به فارسی" : "Switch to English"
      );
    });
  }

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
