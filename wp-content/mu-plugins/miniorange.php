<?php

add_filter( 'pre_option_mo_saml_admin_email', function() {
	return 'admin@openlab.citytech.cuny.edu';
} );

add_filter( 'pre_option_mo_saml_admin_customer_key', function() {
	return 12345;
} );
