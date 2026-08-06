/* Shola Core — admin metabox PDF picker.
   Client-side UX only: restricts the media-library picker to PDFs so
   editors don't have to guess. The real enforcement is server-side
   (Meta_Fields::sanitize_pdf_id() in class-meta-fields.php) — this script
   never bypasses that, it only makes the happy path convenient. */
(function ($) {
	"use strict";

	$(document).on("click", ".shcore-pdf-select", function (e) {
		e.preventDefault();
		var $field = $(this).closest(".shcore-pdf-field");
		var $input = $field.find(".shcore-pdf-id");
		var $name = $field.find(".shcore-pdf-filename");
		var $remove = $field.find(".shcore-pdf-remove");

		var frame = wp.media({
			title: "انتخاب فایل PDF",
			library: { type: "application/pdf" },
			multiple: false,
			button: { text: "استفاده از این فایل" },
		});

		frame.on("select", function () {
			var attachment = frame.state().get("selection").first().toJSON();
			$input.val(attachment.id);
			$name.text(attachment.filename || attachment.title || "");
			$remove.show();
		});

		frame.open();
	});

	$(document).on("click", ".shcore-pdf-remove", function (e) {
		e.preventDefault();
		var $field = $(this).closest(".shcore-pdf-field");
		$field.find(".shcore-pdf-id").val("");
		$field.find(".shcore-pdf-filename").text("");
		$(this).hide();
	});
})(jQuery);
