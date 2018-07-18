(function($){
	var enteredName = OpenLabBMCCRegistration.displayName;

	$(document).ajaxComplete( function( event, xhr, settings ) {
		if ( -1 === settings.data.indexOf( 'action=openlab_profile_fields' ) ) {
			return;
		}

		$('#field_1')
		  .val( enteredName )
			.on( 'change', function() {
				enteredName = $(this).val();
			} )
			.after( '<p class="description">Your Name is publicly visible on your profile and throughout the site. We have suggested a name pulled from the BMCC user system, but you may change it if you\'d like.</p>' );

	} );
}(jQuery));
