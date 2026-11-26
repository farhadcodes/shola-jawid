# Effort estimate and risks

Rough sizing, for planning purposes only — not a quote. Assumes the
recommended architecture (`02-architecture-decision/`) is approved as-is;
picking either rejected alternative would be meaningfully larger, not
smaller, for the reasons already given there.

## Rough effort by phase

| Phase | Size | Why |
|---|---|---|
| 0 — Governance | Small | Mostly decisions/sign-off, one file edit + changelog entry |
| 1 — Infrastructure | Medium-large | New meta fields + admin UI, rewrite rules, query filtering — the real technical core of the project |
| 2 — Language switcher | Small-medium | One function, one UI element, but needs testing across every page type |
| 3 — LTR CSS pass | Medium | Mostly verification given the existing logical-properties discipline, but real fixes for icons/drop-caps/number-formatting, checked per template |
| 4 — Content population | Ongoing | Not a fixed-size task — editorial work, continues indefinitely |
| 5 — SEO/sitemap | Small | Small, targeted changes to one already-well-structured file |
| 6 — POT regeneration | Small | One WP-CLI command each, plus confirming nothing looks wrong in the output |
| 7 — QA | Medium | Thorough by design — this project touches nearly every template file at least lightly |
| 8 — Launch | Small | Sign-off and a git tag |

## Real risks, in order of how likely they are to actually bite

1. **Term-meta translation approach assumes the fixed vocabularies stay
   genuinely small and rarely change.** If `topic` or `collection` ever
   grow into large, frequently-edited vocabularies, the "one term, two
   labels via meta" approach still works but the one-time admin-UI setup
   cost per term starts to add up. Not currently a concern given the
   audited vocabulary sizes (9, 2+8, 4 terms).
2. **Content-type scope creep.** "Does every content type need English" is
   flagged as an open decision for a reason — if the answer turns out to be
   "yes, eventually, everything," Phase 1's field-extension work grows
   proportionally. Recommendation in that file (articles + issues + core
   pages first) keeps this contained.
3. **ModamPro's Latin coverage is unverified.** Low risk either way (the
   font-stack fallback already handles it gracefully), but worth confirming
   early in Phase 3 rather than discovering a visual surprise during QA.
4. **Staff bilingual-authoring capacity is outside this plan's control.**
   The technical capability being "done" doesn't mean the English site has
   meaningful content on day one — that depends entirely on translation
   throughput, which is a client-side resourcing question, not a technical
   one.
5. **The icon-mirroring work in the LTR CSS pass is the one place effort
   could run longer than expected**, specifically because it can only be
   properly found by looking at real rendered pages one at a time, not by
   grepping CSS — the exact number of icons needing a mirrored variant
   isn't knowable until Phase 3's actual walkthrough happens.

## What's deliberately NOT a risk, worth saying explicitly

Because of how disciplined the existing Persian build already is (100%
logical CSS properties, 100% text-domain-wrapped strings, existing
`shcore_language`/`shcore_translation_id` scaffolding), **the core technical
risk of "is this architecturally even feasible without a messy rewrite" is
low.** The biggest unknowns in this project are editorial/content ones
(what gets translated, by whom, how fast), not technical ones.
