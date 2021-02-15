<?php

/**
 * Defaults
 */
function liquido_defaults( $defaults = array() ) {

	$liquido_defaults = array(
		"fluida_magazinelayout"		=> 3,
		"fluida_menuheight"			=> 80,
		"fluida_menulayout"			=> 2,
		"fluida_headerheight"		=> 300,
		"fluida_lpsliderimage"		=> get_stylesheet_directory_uri() . '/resources/images/slider/liquido.jpg',
		"fluida_lpblockfouricon1"	=> "no-icon",
		"fluida_lpboxcount1"		=> 8,
		"fluida_lpboxrow1"			=> 4,
		"fluida_lpboxlength1"		=> 20,
		"fluida_lpboxcount2"		=> 6,
		"fluida_lpboxrow2"			=> 3,
		"fluida_elementborder" 		=> 0,
		"fluida_elementshadow" 		=> 0,
		"fluida_elementborderradius"=> 1,

		"fluida_sitebackground" 	=> "#F3F3F3",
		"fluida_sitetext" 			=> "#666666",
		"fluida_headingstext"		=> "#0F0F0F",
		"fluida_contentbackground"	=> "#FBFBFB",
		"fluida_contentbackground2"	=> "",
		"fluida_accent1" 			=> "#BC926B",
		"fluida_accent2" 			=> "#0f0f0f",
		"fluida_menubackground" 	=> "#FFFFFF",
		"fluida_menutext" 			=> "#0F0F0F",
		"fluida_submenutext" 		=> "#555555",
		"fluida_footerbackground"	=> "#F7F5F5",
		"fluida_footertext"			=> "#0E0E0E",
		"fluida_lpblocksbg"			=> "",
		"fluida_lpboxesbg"			=> "",
		"fluida_lptextsbg"			=> "#FFFFFF",

		"fluida_fgeneral" 			=> 'Droid Sans/gfont',
		"fluida_fgeneralgoogle" 	=> '',
		"fluida_fgeneralsize" 		=> '16px',
		"fluida_fgeneralweight" 	=> '400',

		"fluida_fsitetitle" 		=> 'Oswald/gfont',
		"fluida_fsitetitlegoogle"	=> '',
		"fluida_fsitetitlesize" 	=> '130%',
		"fluida_fsitetitleweight"	=> '300',
		"fluida_fmenu" 				=> 'Oswald/gfont',
		"fluida_fmenugoogle"		=> '',
		"fluida_fmenusize" 			=> '95%',
		"fluida_fmenuweight"		=> '300',

		"fluida_ftitles" 			=> 'Oswald/gfont',
		"fluida_ftitlesgoogle"		=> '',
		"fluida_ftitlessize" 		=> '300%',
		"fluida_ftitlesweight"		=> '300',
		"fluida_fheadings" 			=> 'Oswald/gfont',
		"fluida_fheadingsgoogle"	=> '',
		"fluida_fheadingssize" 		=> '100%',
		"fluida_fheadingsweight"	=> '700',

		"fluida_fwtitle" 			=> 'Oswald/gfont',
		"fluida_fwtitlegoogle"		=> '',
		"fluida_fwtitlesize" 		=> '100%',
		"fluida_fwtitleweight"		=> '700',
		"fluida_fwcontent" 			=> 'Droid Sans/gfont',
		"fluida_fwcontentgoogle"	=> '',
		"fluida_fwcontentsize" 		=> '100%',
		"fluida_fwcontentweight"	=> '300',

		"fluida_fheight"			=> 300,
		"fluida_excerptlength"		=> 35,
		"fluida_excerptcont"		=> 'Read more'
	); // liquido_defaults array

	$result = array_merge( $defaults, $liquido_defaults );

	return $result;

} // liquido_defaults()
add_filter( 'fluida_option_defaults_array', 'liquido_defaults' );

// needed? for the .org preview
function liquido_filter_defaults(){
	add_filter( 'fluida_option_defaults_array', 'liquido_defaults' );
} // liquido_filter_defaults()
add_action( 'customize_register', 'liquido_filter_defaults' );


/**
 * Handle theme labels in customize panels
 */
function liquido_about_theme_name( $initial ) {
	return __( 'About Liquido', 'liquido' );
} // liquido_about_theme_name()
add_filter( 'cryout_about_theme_name', 'liquido_about_theme_name' );

function liquido_about_theme_plus_desc( $initial ) {
	$theme = wp_get_theme();
	return '<h3>' . sprintf( esc_attr__( '%1$s is a child theme of %2$s', 'liquido' ), esc_attr( $theme->get( 'Name' ) ), ucwords( esc_attr( $theme->get( 'Template' ) ) ) ) . '</h3>' . $initial;
} // liquido_about_theme_plus_desc()
add_filter( 'cryout_about_theme_plus_desc', 'liquido_about_theme_plus_desc' );

function liquido_about_theme_slug_swap( $initial ){
	return str_replace( array( 'fluida', 'Fluida' ), array( 'liquido', 'Liquido' ), $initial ); 
} // liquido_about_theme_slug_swap()
add_filter( 'cryout_about_theme_review_link', 'liquido_about_theme_slug_swap' );
add_filter( 'cryout_about_theme_manage_link', 'liquido_about_theme_slug_swap' );

// FIN