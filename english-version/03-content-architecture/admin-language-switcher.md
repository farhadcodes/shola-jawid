# Admin language switcher (approved 2026-11-27, built 2026-11-27)

The Polylang/WPML-style "which language am I working in right now" admin
experience, built as custom code in `class-language.php` — not a plugin.

## 1. Admin-bar language switcher

A فارسی / English dropdown in the WordPress admin bar (the dark toolbar
on every wp-admin screen), same place Polylang/WPML put theirs.

Picking one sets your current working language. Nothing else on the site
changes just from this click — it only decides what the next three
items below do.

## 2. Stored per-user, not site-wide

Each admin/editor gets their own independent working language, saved to
their own user account.

So a translator working in English and an editor managing Persian
content at the same time never flip each other's view by accident.

## 3. Every content list screen defaults to your current language

Posts, Issues, Documents, Party Publications, Party Documents, Leaflets,
Editor's Picks, Announcements — each gets a "Language" column plus a
filter dropdown, pre-set to your current mode.

The column makes it visible at a glance; the dropdown lets you peek at
the other language for one screen without changing your actual working
mode.

## 4. New content defaults to your current language

Creating a new post while in English mode sets its Language field to
English automatically, instead of always starting at Persian.

Still a normal, changeable field on the post — this only changes the
starting value, never locks it.

## 5. Scope boundary: content language only, not the admin UI chrome

This controls which language's *content* you see and create. It does
**not** translate wp-admin's own menus/buttons into English.

That's WordPress core's own separate feature (each user's
Profile → Language setting) — Polylang/WPML don't duplicate it either,
and neither does this.
