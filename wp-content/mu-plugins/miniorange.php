<?php

// For testing only.
add_action( 'init', function() {
	if ( empty( $_GET['saml_test'] ) ) {
		return;
	}

	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		return;
	}

	$user_email = 'boonebgorges+boone1@gmail.com';
	$first_name = 'BooneTest';
	$last_name  = 'GorgesTest';
	$user_name  = 'boonebgorges+boone1@gmail.com';

	mo_saml_login_user( $user_email, $first_name, $last_name, $user_name, [], '', '', '', 'email', '12345', $user_name );
	die;
} );

add_filter( 'pre_option_mo_saml_admin_email', function() {
	return 'admin@openlab.citytech.cuny.edu';
} );

add_filter( 'pre_option_mo_saml_admin_customer_key', function() {
	return 12345;
} );

/**
 * Implementation note: Forked plugin has this hook introduced in mo_saml_login_user(),
 * as part of the `if` clause related to user creation.
 */
add_action( 'mosaml_pre_create_user', function( $args ) {
	global $wpdb;

	// Don't double create.
	$signup = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->signups} WHERE user_email = %s", $args['user_email'] ) );
	if ( ! $signup ) {
		$user_fullname  = $args['first_name'] . ' ' . $args['last_name'];

		$usermeta = array(
			'profile_field_ids' => '1',
			'field_1'           => $user_fullname,
			'first_name'        => $args['first_name'],
			'last_name'         => $args['last_name'],
		);

		// Create WP user, but don't let WP send notification.
		remove_action( 'after_signup_user', 'wpmu_signup_user_notification', 10, 4 );
		wpmu_signup_user( $args['user_email'], $args['user_email'], $usermeta );

		// Get the signup so we can use the activation key in the redirect.
		$signup = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->signups} WHERE user_email = %s", $args['user_email'] ) );
	}

	$activation_key = $signup->activation_key;

	$redirect_to = add_query_arg( 'account-key', $activation_key, bp_get_signup_page() );
	wp_safe_redirect( $redirect_to );
	die;
} );

/**
 * Catch "registration" and run activation routine.
 */
add_action( 'bp_signup_pre_validate', function() {
	global $wpdb;

	if ( empty( $_POST['account-key'] ) ) {
		return;
	}

	$key = wp_unslash( $_POST['account-key'] );

	$signups = BP_Signup::get(
		array(
			'activation_key' => $key,
		)
	);
	$signup = $signups['signups'][0];

	$meta = $signup->meta;
	$meta_profile_field_ids = explode( ',', $meta['profile_field_ids'] );

	$profile_field_ids = explode( ',', $_POST['signup_profile_field_ids'] );
	foreach ( $profile_field_ids as $field_id ) {
		bp_xprofile_maybe_format_datebox_post_data( $field_id );

		if ( isset( $_POST[ 'field_' . $field_id ] ) ) {
			$meta[ 'field_' . $field_id ] = trim( $_POST[ 'field_' . $field_id ] );
			$meta_profile_field_ids[] = $field_id;
		}
	}
	$meta['profile_field_ids'] = implode( ',', array_unique( $meta_profile_field_ids ) );

	$meta = apply_filters( 'bp_signup_usermeta', $meta );

	$wpdb->update(
		buddypress()->members->table_name_signups,
		array(
			'meta' => maybe_serialize( $meta ),
		),
		array(
			'signup_id' => $signup->signup_id,
		)
	);

	$user_id = bp_core_activate_signup( $key );

	$user                = new WP_User( $user_id );
	$user->user_nicename = trim( $_POST['signup_username'] );
	$user->first_name    = $meta['first_name'];
	$user->last_name     = $meta['last_name'];
	wp_update_user( $user );

	// Kill just in case BP double-processes.
	$_POST = array();

	wp_set_auth_cookie( $user_id, true );
	wp_safe_redirect( home_url() );
	die;
} );

/**
 * Don't allow access to Register page without an 'account-key'.
 */
add_action( 'bp_screens', function() {
	if ( ! bp_is_register_page() ) {
		return;
	}

	// BP handles this case.
	if ( is_user_logged_in() ) {
		return;
	}

	$redirect = false;
	if ( isset( $_GET['account-key'] ) ) {
		$signups = BP_Signup::get(
			array(
				'activation_key' => $key,
			)
		);
		$redirect = empty( $signups['signups'] );
	}

	if ( ! $redirect ) {
		return;
	}

	wp_redirect( get_option( 'saml_login_url' ) );
	die;
}, 0 );
