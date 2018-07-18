<?php

/**
 * Login/signup panel on home page.
 *
 * @since 1.0.0
 */

?>

<?php if ( is_user_logged_in() ) :

	echo '<div id="open-lab-login" class="log-box">';
	echo '<h1 class="title inline-element semibold">Welcome,</h1><h2 class="title inline-element">' . bp_core_get_user_displayname( bp_loggedin_user_id() ) . '</h2>';
	do_action( 'bp_before_sidebar_me' )
	?>

<?php
$brand_pages = cboxol_get_brand_pages();

$help_link = '';
if ( isset( $brand_pages['help'] ) ) {
	$help_link = $brand_pages['help']['preview_url'];
}

$contact_link = '';
if ( isset( $brand_pages['contact-us'] ) ) {
	$contact_link = $brand_pages['contact-us']['preview_url'];
}

?>

	<div id="sidebar-me" class="clearfix">
		<div id="user-info">
			<a class="avatar" href="<?php echo bp_loggedin_user_domain() ?>">
				<img class="img-responsive" src="<?php bp_loggedin_user_avatar( array( 'type' => 'full', 'html' => false ) ); ?>" alt="Avatar for <?php echo bp_core_get_user_displayname( bp_loggedin_user_id() ); ?>" />
			</a>

			<div class="welcome-link-my-profile">
				<a href="<?php echo	esc_url( bp_loggedin_user_domain() ); ?>"><?php esc_html_e( 'My Profile', 'openlab-theme' ); ?></a>
			</div>

			<ul class="content-list">
				<li class="no-margin no-margin-bottom"><a class="button logout font-size font-12 roll-over-loss" href="<?php echo wp_logout_url( bp_get_root_domain() ) ?>">Not <?php echo bp_core_get_username( bp_loggedin_user_id() ); ?>?</a></li>
				<li class="no-margin no-margin-bottom"><a class="button logout font-size font-12 roll-over-loss" href="<?php echo wp_logout_url( bp_get_root_domain() ) ?>"><?php _e( 'Log Out', 'buddypress' ) ?></a></li>
			</ul>
			</span><!--user-info-->
		</div>
		<?php do_action( 'bp_sidebar_me' ) ?>
	</div><!--sidebar-me-->

	<?php do_action( 'bp_after_sidebar_me' ) ?>

	<?php echo '</div>'; ?>

	<div id="login-help" class="log-box">
		<h4 class="title"><?php esc_html_e( 'Need Help?', 'openlab-theme' ); ?></h4>
		<?php /* translators: 1. help link, 2. contact link */ ?>
		<p class="font-size font-14"><?php printf( 'Visit the <a class="roll-over-loss" href="%1$s">Help section</a> or <a class="roll-over-loss" href="%2$s">contact us</a> with a question.', esc_attr( $help_link ), esc_attr( $contact_link ) ); ?></p>
	</div><!--login-help-->

<?php else : ?>
	<?php echo '<div id="open-lab-join" class="log-box">'; ?>
	<?php echo '<h2 class="title"><span class="fa fa-plus-circle flush-left"></span> ' . esc_html__( 'Sign Up', 'openlab-theme' ) . '</h2>'; ?>
	<?php printf(
		'<p><a class="btn btn-default btn-primary link-btn pull-right semibold" href="%s">%s</a> <span class="font-size font-14">%s</span></p>',
		esc_attr( bp_get_signup_page() ),
		esc_html__( 'Sign up', 'openlab-theme' ),
		'Use your BMCC credentials to register for an account in just a few minutes.'
	); ?>
	<?php echo '</div>'; ?>
	<?php echo '<div id="open-lab-login" class="log-box">'; ?>
	<?php do_action( 'bp_after_sidebar_login_form' ) ?>
	<?php echo '</div>'; ?>

	<div id="user-login" class="log-box">

		<?php echo '<h2 class="title"><span class="fa fa-arrow-circle-right"></span> Log in</h2>'; ?>
		<?php do_action( 'bp_before_sidebar_login_form' ) ?>

		<?php printf(
			'<p><a class="btn btn-default btn-primary link-btn pull-right semibold" href="%s">%s</a> <span class="font-size font-14">%s</span></p>',
			esc_attr( wp_login_url() ),
			esc_html__( 'Log in', 'openlab-theme' ),
			'Already have an account? Log in using your BMCC username and password.'
		); ?>
	</div>
<?php endif; ?>
