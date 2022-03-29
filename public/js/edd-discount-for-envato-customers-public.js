(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
	$(document).ready(function() {
		$("#edddfe_gen_coupon_form").submit(function(e) {
			e.preventDefault();
			var purc_code = $("#edddfe_purchase_key").val();
			console.log(purc_code)
			$.ajax({
				type: "POST",
				url: edddfe_object.ajax_url,
				data: {
					action: 'edddfe_coupon_generate',
					purchase_code: purc_code
				},
				success: function(res) {
					$(".edddfe-response").html(res)
				}
			});

			return false;
			
		});
	});
})( jQuery );
