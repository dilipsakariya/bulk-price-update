;( function ( $, window, undefined ) {

	if ( $( '.bpu-subscription-modal-wrapper' ).length > 0 ) {
		// Show the popup after 5 seconds
		window.setTimeout(
			function() {
				$( '.bpu-subscription-modal-wrapper' ).addClass( 'open' );
			},
			5000
		);

		// Overtake the form submission request
		$( '.bpu-subscription-form' ).on(
			'submit',
			function( e ) {
				e.preventDefault();

				if ( $( '.bpu-subscription-modal' ).hasClass( 'ajaxing' ) ) {
					return; // request is already in progress
				}

				$( '.bpu-subscription-modal' ).addClass( 'ajaxing' );

				$.ajax(
					{
						url: ajaxurl,
						type: 'POST',
						dataType: 'JSON',
						data: {
							action: 'bpu_handle_subscription_request',
							email: $( '.bpu-subscription-form input' ).val(),
							security: $( '.bpu-subscription-form input[name="_wpnonce"]' ).val(),
						},
						success: function( data ) {
							$( '.bpu-subscription-modal-thanks' ).show().delay( 6000 ).fadeOut();

							// auto-close the popup after 5 seconds
							window.setTimeout(
								function() {
									$( '.bpu-subscription-modal-wrapper' ).removeClass( 'open' );
								},
								3000
							);
						}
					}
				)
				.fail(
					function() {
						$( '.bpu-subscription-error' ).show();
					}
				)
				.always(
					function() {
						$( '.bpu-subscription-modal' ).removeClass( 'ajaxing' );
					}
				);
			}
		);

		function store_popup_shown_status() {
			$.ajax(
				{
					url: ajaxurl,
					type: 'POST',
					dataType: 'JSON',
					data: {
						action: 'bpu_subscription_popup_shown'
					},
				}
			);

		}

		$( '.bpu-subscription-skip' ).on(
			'click',
			function( e ) {
				e.preventDefault();
				$( '.bpu-subscription-modal-wrapper' ).removeClass( 'open' );
				store_popup_shown_status();
			}
		);

	}

}( jQuery, window ) );
