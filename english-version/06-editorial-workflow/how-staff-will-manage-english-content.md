# How staff will actually use this, day to day

Written for Farhad and whoever at the client's side manages content — not a
technical audience. This describes the experience once Phase 1-3 of the
build are done.

## Writing a new English article

1. Write the article as a normal new post in wp-admin, exactly like every
   Persian article today.
2. In the post's editing screen, there's one small new field: **Language**
   (Persian/English — defaults to Persian, so nobody has to think about it
   unless they're specifically writing in English).
3. If this English article is a translation of an existing Persian one,
   there's one more small field: **Translation of** — a search box to pick
   the original Persian post. Picking it automatically links both posts to
   each other; nothing else to configure.
4. Publish exactly as normal. The English article now has its own real URL
   under `/en/`, and the Persian original's "EN" button now takes readers
   straight to it.

## What happens if an English translation isn't written yet

Nothing breaks. The Persian article's "EN" button, until a translation is
paired to it, simply takes the reader to the English homepage instead of a
dead link or an error. As soon as someone writes the translation and links
it (step 3 above), the button starts pointing at the real article
immediately — no republishing of the Persian original needed.

## Categories/topics

These don't need re-creating for English — the same 9 topics, same
publications, same library collections are shared between both languages.
A one-time setup step (done once per category, not per article) adds the
English name and English web-address version of each existing category.
After that one-time setup, every article just gets tagged the same way
regardless of which language it's written in.

## About the demo content you'll see at first

Before the client writes any real English content, the site won't look
empty — Claude fills in clearly-marked placeholder content ("[نمونه]"
in the title, plus an unmissable notice on the post's own edit screen)
so there's something real to look at in every section. Replacing it is
simple: open wp-admin, every demo post is visibly flagged, delete it as
you publish the real version in its place. Nothing technical is needed
to "turn off" demo mode — a demo post behaves exactly like any other
post. Full policy: `../03-content-architecture/demo-content-policy.md`.

## What this project does NOT include

Translating the existing library of Persian articles, issues, and documents
into English is a separate, ongoing editorial effort — this plan builds the
*capability*, it doesn't translate the back-catalog. How much gets
translated, by whom, and on what schedule is entirely Farhad's and the
client's call, made incrementally over time, the same way new Persian
content gets added today.
