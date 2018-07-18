<?php

add_action( 'wp_enqueue_scripts', function() {
	wp_enqueue_script( 'openlab-bmcc-registration', get_stylesheet_directory_uri() . '/assets/js/register.js', array( 'openlab-registration' ) );

	if ( bp_is_register_page() && ! empty( $_GET['account-key'] ) ) {
		$activation_key = wp_unslash( $_GET['account-key'] );
		$signups = BP_Signup::get(
			array(
				'activation_key' => $activation_key,
			)
		);

		$display_name_default = '';
		if ( $signups['signups'] ) {
			$signup               = $signups['signups'][0];
			$display_name_default = $signup->meta['field_1'];
		}

		wp_localize_script(
			'openlab-bmcc-registration',
			'OpenLabBMCCRegistration',
			array(
				'displayName' => $display_name_default,
			)
		);
	}
} );
