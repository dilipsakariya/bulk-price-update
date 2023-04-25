( function( $ ) {

	$(
		function() {

			var pluginSlug = 'bulk-price-update';

			// Code to fire when the DOM is ready.
			$( document ).on(
				'click',
				'tr[data-slug="' + pluginSlug + '"] .deactivate',
				function( e ) {
					e.preventDefault();
					$( '.bpu-popup-overlay' ).addClass( 'bpu-active' );
					$( 'body' ).addClass( 'bpu-hidden' );
				}
			);

			$( document ).on(
				'click',
				'.bpu-popup-button-close',
				function() {
					close_popup();
				}
			);

			$( document ).on(
				'click',
				'.bpu-serveypanel,tr[data-slug="' + pluginSlug + '"] .deactivate',
				function( e ) {
					e.stopPropagation();
				}
			);

			$( document ).click(
				function() {
					close_popup();
				}
			);

			$( '.bpu-reason label' ).on(
				'click',
				function() {
					if ( $( this ).find( 'input[type="radio"]' ).is( ':checked' ) ) {
						  $( this )
						.next()
						.next( '.bpu-reason-input' )
						.show()
						.end()
						.end()
						.parent()
						.siblings()
						.find( '.bpu-reason-input' )
						.hide();
					}
				}
			);

			$( 'input[type="radio"][name="bpu-selected-reason"]' ).on(
				'click',
				function( event ) {
					$( '.bpu-popup-allow-deactivate' ).removeAttr( 'disabled' );
					$( '.bpu-popup-skip-feedback' ).removeAttr( 'disabled' );
					$( '.message.error-message' ).hide();
					$( '.bpu-pro-message' ).hide();
				}
			);

			$( '.bpu-reason-pro label' ).on(
				'click',
				function() {
					if ( $( this ).find( 'input[type="radio"]' ).is( ':checked' ) ) {
						  $( this ).next( '.bpu-pro-message' )
						.show()
						.end()
						.end()
						.parent()
						.siblings()
						.find( '.bpu-reason-input' )
						.hide();

						  $( this ).next( '.bpu-pro-message' ).show()
						  $( '.bpu-popup-allow-deactivate' ).attr( 'disabled', 'disabled' );
						  $( '.bpu-popup-skip-feedback' ).attr( 'disabled', 'disabled' );
					}
				}
			);

			$( document ).on(
				'submit',
				'#bpu-deactivate-form',
				function( event ) {
					event.preventDefault();

					var _reason          = $( 'input[type="radio"][name="bpu-selected-reason"]:checked' ).val();
					var _reason_details  = '';
					var deactivate_nonce = $( '.bpu_deactivation_nonce' ).val();

					if ( _reason == 2 ) {
						  _reason_details = $( this ).find( 'input[type="text"][name="better_plugin"]' ).val();
					} else if ( _reason == 7 ) {
						 _reason_details = $( this ).find( 'input[type="text"][name="other_reason"]' ).val();
					}

					if ( ( _reason == 7 || _reason == 2 ) && _reason_details == '') {
						$( '.message.error-message' ).show();
						return;
					}

					$.ajax(
						{
							url: ajaxurl,
							type: 'POST',
							data: {
								action: 'bpu_deactivation',
								reason_details: _reason,
								reason_details: _reason_details,
								bpu_deactivation_nonce: deactivate_nonce
							},
							beforeSend: function() {
								$( '.bpu-spinner' ).show();
								$( '.bpu-popup-allow-deactivate' ).attr( 'disabled', 'disabled' );
							}
						}
					)
					.done(
						function() {
							$( '.bpu-spinner' ).hide();
							$( '.bpu-popup-allow-deactivate' ).removeAttr( "disabled" );
							window.location.href = $( "tr[data-slug="' + pluginSlug + '"] .deactivate a' ).attr( 'href' );
						}
					);
				}
			);

			$( '.bpu-popup-skip-feedback' ).on(
				'click',
				function(e) {
					window.location.href = $( 'tr[data-slug="' + pluginSlug + '"] .deactivate a' ).attr( 'href' );
				}
			);

			function close_popup() {
				$( '.bpu-popup-overlay' ).removeClass( 'bpu-active' );
				$( '#bpu-deactivate-form' ).trigger( 'reset' );
				$( '.bpu-popup-allow-deactivate' ).attr( 'disabled', 'disabled' );
				$( '.bpu-reason-input' ).hide();
				$( 'body' ).removeClass( 'bpu-hidden' );
				$( '.message.error-message' ).hide();
				$( '.bpu-pro-message' ).hide();
			}
		}
	);

} )( jQuery );
