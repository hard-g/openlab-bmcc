<?php

add_action( 'admin_init', function() {
	if ( ! isset( $_GET['bbg_test'] ) ) {
		return;
	}

	$dir = ABSPATH . '/wp-content/uploads';

	die();


} );
