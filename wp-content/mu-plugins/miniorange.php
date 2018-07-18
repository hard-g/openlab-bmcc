<?php

// For testing only.
add_action( 'init', function() {
	if ( empty( $_GET['saml_test'] ) ) {
		return;
	}

	$user_email = 'boonebgorges+boone1@gmail.com';
	$first_name = 'BooneTest';
	$last_name  = 'GorgesTest';
	$user_name  = 'boonebgorges+boone1@gmail.com';

	mo_saml_login_user( $user_email, $first_name, $last_name, $user_name, [], '', '', '', 'email', '12345', $user_name );
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

	$password = wp_generate_password();

	$user_fullname  = $args['first_name'] . ' ' . $args['last_name'];

	$usermeta = array(
		'profile_field_ids' => array( 1 ),
		'field_1'           => $user_fullname,
		'first_name'        => $args['first_name'],
		'last_name'         => $args['last_name'],
	);

	// Create WP user, but don't let WP send notification.
	remove_action( 'after_signup_user', 'wpmu_signup_user_notification', 10, 4 );
	wpmu_signup_user( $args['user_email'], $args['user_email'], $usermeta );

	// Get the signup so we can use the activation key in the redirect.
	$signup = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->signups} WHERE user_email = %s", $args['user_email'] ) );

	$activation_key = $signup->activation_key;

	$redirect_to = add_query_arg( 'account-key', $activation_key, bp_get_signup_page() );
	wp_safe_redirect( $redirect_to );
	die;
} );
