jQuery( document ).on(
	'click',
	'#bulk-price-update-review .notice-dismiss',
	function() {

		var bpu_review_data = {
			action: 'bpu_review_notice',
		};

		jQuery.post(
			ajaxurl,
			bpu_review_data,
			function( response ) {
				if ( response ) {
					console.log( response );
				}
			}
		);
	}
);
