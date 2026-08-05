# IA reference documents — index

Read-only reference copies. Do not edit these in place; if a document changes,
replace the file and note it in `docs/CHANGELOG.md`.

| File | Role |
|---|---|
| `03_Bilingual_Publishing_Site_IA_Sitemap_v1.0.docx` / `03_معماری_اطلاعات_و_نقشه_سایت_FA_v1_0.docx` | IA & sitemap — page list, URL structure, content model, open decisions (§9) |
| `04_Shola_Jawid_Brand_Guidelines_v1.0.docx` / `.html` | Brand guide v1.0 (English) — Ink-dominant, 60/30/10 |
| `04_Shola_Jawid_Brand_Guidelines_FA_v1.0.docx` / `.html` | Brand guide v1.0 (Persian) |
| `Aeon_Design_System_Analysis_v1_0.docx` | Aeon design-system analysis — informed the v6 prototype's card/menu/sidebar patterns |

## Which brand version governs this build

**Neither v1.0 nor v2.0 (45/40/15 "Crimson Field") directly — the finished v6
"Quiet Press" static prototype in `03_UI_Design/shola-jawid-ui/` is the sole
source of truth for this build.** Per its own README (`shola-jawid-ui/README.md`,
"چرا v6؟" and "نسخه‌ها" sections):

- v6 explicitly abandons v2.0's 45/40/15 field rule and returns to v1.0's
  60/30/10 discipline, reinterpreted in an Aeon-like paper-dominant register
  (crimson as editorial ink, never a field/masthead block).
- This was decided after v5 (which followed v2.0) was live-reviewed with the
  client and read as "corporate" — v6 is the client-validated correction, not
  an open question.
- All eleven brand tokens from the v1.0 guide are preserved; only their
  *application* changed (see v6 README for the exact per-token rules).

**Conclusion: this is not a pending decision.** The WordPress build is a
faithful, pixel-accurate port of the v6 prototype exactly as built — per
`CLAUDE.md` §9, any visual deviation from v6 (including "reverting" to a
literal v1.0 or v2.0 treatment) is a bug, not a design decision. The v1.0 and
v2.0 documents above are kept for historical/token reference only (e.g.
confirming the eleven token names/values); they do not independently govern
any visual decision where they conflict with the v6 prototype.

Logged as resolved in `docs/CHANGELOG.md`.
