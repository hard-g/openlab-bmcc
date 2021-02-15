( function( $ ) {

	$( document ).ready( function() {

		// Change post date field.
		$( 'select[name="_gaddon_setting_postDate"]' ).on( 'change', function() {

			// Hide sibling containers.
			$( this ).siblings( 'div:not(.chosen-container)' ).hide();

			// Display appropraite container.
			switch ( $( this ).val() ) {

				case 'custom':
					$( this ).siblings( '.gform_advancedpostcreation_date_custom' ).show();
					break;

				case 'field':
					$( this ).siblings( '.gform_advancedpostcreation_date_field' ).show();
					break;

				default:
					break;

			}

		} );

		// Use Select2.
		$( '#postSettings select:not(#postAuthor), #postThumbnail' ).select2();
		$( 'select[id="postMedia[]"]' ).select2( { placeholder: $( 'select[id="postMedia[]"]' ).attr( 'placeholder' ) } );

		// Add Select2 support to post meta mapping.
		gform.addAction( 'gform_fieldmap_add_row', function( obj, $elem, item ) {

			$elem.find( 'select' ).each( function() {

				var $select = $( this );

				// If there are more than 100 options, do not use Select2.
				if ( $select.find( 'option' ).length > 100 ) {
					return;
				}

				if ( ! $select.data( 'select2' ) ) {
					$select.select2();
				}

				if ( 'gf_custom' === $select.val() ) {
					$select.siblings( '.select2-container' ).hide();
				}

			} );

		} );

		// Add Select2 to post author field.
		$( '#postAuthor' ).select2( {
		    allowClear:  false,
		    placeholder: gform_advancedpostcreation_form_settings_strings.select_user,
			ajax:        {
				url:      ajaxurl,
				dataType: 'json',
				delay:    250,
				data:     function( params ) {
					return {
						action: 'gform_advancedpostcreation_author_search',
						nonce:  gform_advancedpostcreation_form_settings_strings.nonce_author,
						query:  params.term,
					}
				}
			}
		} );

	} );

} )( jQuery );
