<?php
/**
 * Automatic upload-time image optimization for تراکت (leaflet) featured
 * images — added 2026-09-17, per Farhad relaying the client's concern that
 * non-technical staff uploading leaflet/banner/poster photos (phone
 * photos, uncompressed PNGs) would bloat the media library and slow the
 * new archive page down. Scoped to the `leaflet` CPT only — see this
 * class's own docblock below for the full reasoning; every other content
 * type's uploads are completely untouched.
 *
 * @package SholaCore
 */

namespace SholaCore;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resizes, re-encodes, and (where safe) converts a leaflet's featured
 * image on upload, via WP_Image_Editor — WordPress's own image-library
 * abstraction, which auto-selects Imagick when available and falls back
 * to GD transparently. This class never checks which one is active; the
 * same code path works correctly either way.
 */
class Image_Optimizer {

	/**
	 * Longest edge, in pixels, any leaflet image is capped to. 2000px was
	 * chosen against this site's own largest real display context for
	 * this content (the leaflet archive's full-bleed image, capped at the
	 * theme's 1200px content column, `--wrap-wide`) — 2000px covers that
	 * at roughly 1.67x pixel density, a reasonable retina allowance,
	 * without keeping a phone-camera original (often 3000-4000px+) at its
	 * full, wasteful size. Confirmed with Farhad, 2026-09-17.
	 */
	const MAX_DIMENSION = 2000;

	/**
	 * JPEG re-encode quality. 82 matches WordPress core's own default
	 * (`wp_editor_set_quality()`'s baseline since WP 5.3) — this is not a
	 * deviation from core, just making sure it's actually the value in
	 * effect for this CPT's uploads and documenting the trade-off
	 * explicitly rather than relying on an undocumented default: 82 is
	 * visually indistinguishable from source for photographic content, at
	 * roughly 30-50% smaller file size than 90+; it can show very mild
	 * artifacting on hard-edged graphic content (a leaflet with sharp
	 * typography/line art is exactly that) — Farhad confirmed this
	 * trade-off after reviewing a real before/after, 2026-09-17.
	 */
	const JPEG_QUALITY = 82;

	/**
	 * Files already under this size skip optimization entirely — avoids
	 * pointless reprocessing of already-reasonable uploads. 300KB per
	 * Farhad's own suggested value.
	 */
	const SKIP_UNDER_BYTES = 300 * 1024;

	/**
	 * Hook registration.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'added_post_meta', array( __CLASS__, 'maybe_optimize_on_thumbnail_set' ), 10, 4 );
		add_action( 'updated_post_meta', array( __CLASS__, 'maybe_optimize_on_thumbnail_set' ), 10, 4 );
	}

	/**
	 * Fires whenever any post's `_thumbnail_id` postmeta is written —
	 * added_post_meta on first set, updated_post_meta if changed after.
	 * This is the hook actually used, not `add_attachment` (the plan's
	 * first candidate) — found during implementation, 2026-09-17, that
	 * `add_attachment` fires at upload time with the attachment's
	 * `post_parent` already known only for *some* upload flows (e.g. the
	 * classic media modal opened while editing a specific post), but the
	 * block editor's "Set featured image" panel typically uploads the
	 * file as an *unattached* attachment first, then links it via
	 * `set_post_thumbnail()` as a separate step — `post_parent` would
	 * still be 0 at `add_attachment` time for that flow, the one
	 * non-technical staff actually use. Hooking the moment the featured-
	 * image link itself is written instead covers every upload flow
	 * uniformly, since it triggers off "this is now the leaflet's image,"
	 * not off upload timing.
	 *
	 * If the file needs changing (resized and/or converted), this
	 * explicitly re-runs `wp_generate_attachment_metadata()` afterward —
	 * any subsizes WordPress already generated from the pre-optimization
	 * original get correctly regenerated from the optimized file (same
	 * deterministic filenames, so nothing is left orphaned on disk).
	 *
	 * @param int    $meta_id Meta row ID (unused).
	 * @param int    $post_id Post the meta belongs to.
	 * @param string $meta_key Meta key.
	 * @param mixed  $meta_value New `_thumbnail_id` value (attachment ID).
	 * @return void
	 */
	public static function maybe_optimize_on_thumbnail_set( $meta_id, $post_id, $meta_key, $meta_value ) {
		if ( '_thumbnail_id' !== $meta_key ) {
			return;
		}
		if ( 'leaflet' !== get_post_type( $post_id ) ) {
			return;
		}

		self::optimize_attachment( (int) $meta_value );
	}

	/**
	 * Runs the actual resize/re-encode/convert pass on one attachment,
	 * regenerating its metadata afterward if the underlying file changed.
	 *
	 * @param int $attachment_id Attachment ID.
	 * @return void
	 */
	private static function optimize_attachment( $attachment_id ) {
		if ( ! $attachment_id || ! wp_attachment_is_image( $attachment_id ) ) {
			return;
		}

		$file = get_attached_file( $attachment_id );
		if ( ! $file || ! file_exists( $file ) ) {
			return;
		}

		if ( filesize( $file ) < self::SKIP_UNDER_BYTES ) {
			return;
		}

		$editor = wp_get_image_editor( $file );
		if ( is_wp_error( $editor ) ) {
			return;
		}

		$changed = false;

		$size = $editor->get_size();
		if ( $size && max( $size['width'], $size['height'] ) > self::MAX_DIMENSION ) {
			// `crop => false`: resize to *fit within* a MAX_DIMENSION x
			// MAX_DIMENSION box, preserving aspect ratio — never crops.
			// Leaflets/banners/posters vary widely in shape (portrait
			// posters, landscape banners), so a hard crop here would cut
			// off real content; a proportional cap does not.
			$resized = $editor->resize( self::MAX_DIMENSION, self::MAX_DIMENSION, false );
			if ( ! is_wp_error( $resized ) ) {
				$changed = true;
			}
		}

		$current_mime = get_post_mime_type( $attachment_id );
		$target_mime  = $current_mime;

		if ( 'image/png' === $current_mime && self::png_safe_to_convert( $file ) ) {
			$target_mime = 'image/jpeg';
		}

		if ( 'image/jpeg' === $target_mime ) {
			$editor->set_quality( self::JPEG_QUALITY );
		}

		if ( ! $changed && $target_mime === $current_mime ) {
			// Under the dimension cap and no format conversion needed —
			// re-encoding just for quality alone on an already-reasonable
			// file isn't worth the write, matching the same "skip
			// pointless reprocessing" reasoning as the size threshold
			// above.
			return;
		}

		$path_info = pathinfo( $file );
		$extension = ( 'image/jpeg' === $target_mime ) ? 'jpg' : $path_info['extension'];
		$new_file  = $path_info['dirname'] . '/' . $path_info['filename'] . '.' . $extension;

		$saved = $editor->save( $new_file, $target_mime );
		if ( is_wp_error( $saved ) ) {
			return;
		}

		if ( $saved['path'] !== $file ) {
			// Format changed (PNG -> JPEG): point the attachment at the
			// new file and update its MIME type, then remove the old
			// original — never leaves both files behind.
			wp_delete_file( $file );
			update_attached_file( $attachment_id, $saved['path'] );
			wp_update_post(
				array(
					'ID'             => $attachment_id,
					'post_mime_type' => $target_mime,
				)
			);
		}

		if ( ! function_exists( 'wp_generate_attachment_metadata' ) ) {
			require_once ABSPATH . 'wp-admin/includes/image.php';
		}
		$metadata = wp_generate_attachment_metadata( $attachment_id, get_attached_file( $attachment_id ) );
		wp_update_attachment_metadata( $attachment_id, $metadata );
	}

	/**
	 * Reads a PNG file's IHDR color-type byte directly from the file
	 * header — a format-level check that works identically regardless of
	 * which image library (Imagick or GD) WP_Image_Editor is actually
	 * using, rather than relying on a library-specific alpha-detection
	 * API.
	 *
	 * Deliberately conservative: only color types 0 (grayscale) and 2
	 * (truecolor) — the two PNG types that can never carry an alpha
	 * channel or palette transparency — are treated as safe to convert.
	 * Color type 3 (palette) is left alone even though *most* palette
	 * PNGs have no transparency, because some carry a `tRNS` chunk that
	 * does — detecting that reliably needs a second chunk scan, and
	 * silently flattening a genuinely-transparent PNG to an opaque JPEG
	 * would visibly break it. Types 4/6 (grayscale-alpha, truecolor-
	 * alpha) always carry a real alpha channel and are never converted.
	 *
	 * @param string $file Absolute path to a PNG file.
	 * @return bool
	 */
	private static function png_safe_to_convert( $file ) {
		$handle = fopen( $file, 'rb' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fopen -- reading a raw binary header offset; WP_Filesystem has no equivalent partial-read API.
		if ( ! $handle ) {
			return false;
		}
		$header = fread( $handle, 33 ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fread
		fclose( $handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fclose

		if ( 33 !== strlen( $header ) ) {
			return false;
		}

		// PNG signature (8 bytes) + chunk length (4) + "IHDR" (4) +
		// width (4) + height (4) + bit depth (1) + color type (1) — the
		// color-type byte sits at a fixed offset (25) in every valid PNG.
		$color_type = ord( $header[25] );

		return in_array( $color_type, array( 0, 2 ), true );
	}
}
