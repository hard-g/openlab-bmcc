<?php
/**
 *  Registration template.
 */

/*
 * @todo Remove/modify Login interface throughout
 * @todo Expire auto-generated signups after short time to avoid mischief
 */
?>

<div class="col-sm-18">
	<?php
	do_action( 'bp_before_register_page' );

	$registration_form_settings = cboxol_get_registration_form_settings();

	$ajaxurl = bp_core_ajax_url();
	$site_name = bp_get_option( 'blogname' );

	$limited_email_domains_message = '';
	$limited_email_domains = get_site_option( 'limited_email_domains' );
	if ( $limited_email_domains ) {
		$led = array();
		foreach ( $limited_email_domains as $d ) {
			$led[] = sprintf( '<span class="limited-email-domain">' . esc_html( $d ) . '</span>' );
		}
		$limited_email_domains_message = sprintf(
			esc_html__( 'Allowed email domains: %s', 'openlab-theme' ),
			implode( ', ', $led )
		);
	}

	$member_types = cboxol_get_member_types();

	$user_name_default = '';

	$activation_key = wp_unslash( $_GET['account-key'] );
	$signups = BP_Signup::get(
		array(
			'activation_key' => $activation_key,
		)
	);

	if ( $signups['signups'] ) {
		$signup = $signups['signups'][0];

		if ( bp_get_signup_username_value() ) {
			$user_name_default = bp_get_signup_username_value();
		} else {
			$user_name_default = sanitize_title_with_dashes( substr( $signup->user_email, 0, strpos( $signup->user_email, '@' ) ) );
		}
	}

	?>

	<div class="page" id="register-page">

		<div id="openlab-main-content"></div>

		<div class="entry-title">
			<h1><?php _e( 'Create an Account', 'openlab-theme' ) ?></h1>
		</div>

		<form action="" name="signup_form" id="signup_form" class="standard-form form-panel" method="post" enctype="multipart/form-data" data-parsley-trigger="blur">

			<?php if ( 'request-details' == bp_get_current_signup_step() ) : ?>

				<div class="panel panel-default">
					<div class="panel-heading semibold"><?php esc_html_e( 'Account Details', 'openlab-theme' ); ?></div>
					<div class="panel-body">

						<?php do_action( 'template_notices' ) ?>

						<p><?php printf( esc_html__( 'Registering for %s is easy. Just fill in the fields below and we\'ll get a new account set up for you in no time.', 'openlab-theme' ), esc_html( $site_name ) ); ?></p>

						<?php do_action( 'bp_before_account_details_fields' ) ?>

						<div class="register-section" id="basic-details-section">

							<div class="form-group">
								<label class="control-label" for="signup_username"><?php esc_html_e( 'Username', 'openlab-theme' ) ?> <?php esc_html_e( '(required)', 'openlab-theme' ) ?> <?php esc_html_e( '(lowercase & no special characters)', 'openlab-theme' ); ?></label>
								<?php do_action( 'bp_signup_username_errors' ) ?>
								<input
									class="form-control"
									type="text"
									name="signup_username"
									id="signup_username"
									value="<?php echo esc_attr( $user_name_default ) ?>"
									data-parsley-lowercase
									data-parsley-nospecialchars
									data-parsley-required
									data-parsley-minlength="4"
									data-parsley-remote="<?php echo add_query_arg( array(
										'action' => 'openlab_unique_login_check',
										'login' => '{value}',
									), $ajaxurl ); ?>"
									data-parsley-remote-message="<?php esc_attr_e( 'That username is already taken.', 'openlab-theme' ); ?>"
								/>
							</div>
							<p class="description">Your username is public, and is used for URLs and @-mentions. You may use your real name, or choose a pseudonym.</p>

						</div><!-- #basic-details-section -->
					</div>
				</div><!--.panel-->

				<?php do_action( 'bp_after_account_details_fields' ) ?>

				<?php if ( bp_is_active( 'xprofile' ) ) : ?>

					<div class="panel panel-default">
						<div class="panel-heading semibold"><?php esc_html_e( 'Public Profile Details', 'openlab-theme' ); ?></div>
						<div class="panel-body">

							<?php do_action( 'bp_before_signup_profile_fields' ) ?>

							<div class="register-section" id="profile-details-section">

								<p><?php esc_html_e( 'Your responses in the form fields below will be displayed on your profile page, which is open to the public. You can always add, edit, or remove information at a later date.', 'openlab-theme' ); ?></p>

								<?php /* @todo Abstract selector? */ ?>
								<div class="editfield form-group account-type-select-ui">
									<?php do_action( 'bp_account_type_errors' ) ?>
									<label class="control-label" for="account-type"><?php esc_html_e( 'Account Type', 'openlab-theme' ); ?> <?php esc_html_e( '(required)', 'openlab-theme' ); ?></label>
									<div class="col-md-24">
										<div class="col-md-8">
											<select name="account-type" class="form-control" id="account-type">
												<option value=""><?php esc_html_e( '- Select Account Type -', 'openlab-theme' ); ?></option>
												<?php foreach ( $member_types as $member_type ) : ?>
													<option value="<?php echo esc_attr( $member_type->get_slug() ); ?>" data-requires-signup-code="<?php echo intval( $member_type->get_requires_signup_code() ); ?>"><?php echo esc_html( $member_type->get_label( 'singular' ) ); ?></option>
												<?php endforeach; ?>
											</select>
										</div>

										<div class="col-md-8">
											<input class="form-control" name="account-type-signup-code" id="account-type-signup-code" placeholder="<?php esc_attr_e( 'Please enter a sign up code', 'openlab-theme' ); ?>" />
										</div>

										<div class="col-md-8 signup-code-message" id="signup-code-message"></div>
									</div>
								</div>

								<div id="openlab-profile-fields"></div>

								<?php echo cboxol_get_academic_unit_selector( array(
									'entity_type' => 'user',
								) ); ?>

								<?php do_action( 'bp_after_signup_profile_fields' ) ?>

							</div><!-- #profile-details-section -->
						</div>
					</div><!--.panel-->



				<?php endif; ?>

				<?php do_action( 'bp_before_registration_submit_buttons' ) ?>

				<p class="sign-up-terms">
					<?php echo $registration_form_settings['confirmationText']; ?>
				</p>

				<p id="submitSrMessage" class="sr-only submit-alert" aria-live="polite"></p>

				<input type="hidden" name="account-key" value="<?php echo esc_attr( $activation_key ); ?>" />

				<div class="submit">
					<input type="submit" name="signup_submit" id="signup_submit" class="btn btn-primary btn-disabled" value="<?php _e( 'Please Complete Required Fields', 'buddypress' ) ?>" />
				</div>

				<?php do_action( 'bp_after_registration_submit_buttons' ) ?>

				<?php wp_nonce_field( 'bp_new_signup' ) ?>

			<?php endif; // request-details signup step    ?>

			<?php if ( 'completed-confirmation' == bp_get_current_signup_step() ) : ?>

				<div class="panel panel-default">
					<div class="panel-heading semibold"><?php _e( 'Sign Up Complete!', 'buddypress' ) ?></div>
					<div class="panel-body">

						<?php do_action( 'template_notices' ) ?>

						<?php if ( bp_registration_needs_activation() ) : ?>
							<p class="bp-template-notice updated no-margin no-margin-bottom"><?php _e( 'You have successfully created your account! To begin using this site you will need to activate your account via the email we have just sent to your address.', 'buddypress' ) ?></p>
						<?php else : ?>
							<p class="bp-template-notice updated no-margin no-margin-bottom"><?php _e( 'You have successfully created your account! Please log in using the username and password you have just created.', 'buddypress' ) ?></p>
						<?php endif; ?>

					</div>
				</div><!--.panel-->

			<?php endif; // completed-confirmation signup step    ?>

			<?php do_action( 'bp_custom_signup_steps' ) ?>

		</form>

	</div>

	<?php do_action( 'bp_after_register_page' ) ?>

	<?php do_action( 'bp_after_directory_activity_content' ) ?>

	<script type="text/javascript">
		jQuery(document).ready(function () {
			if (jQuery('div#blog-details').length && !jQuery('div#blog-details').hasClass('show'))
				jQuery('div#blog-details').toggle();

			jQuery('input#signup_with_blog').click(function () {
				jQuery('div#blog-details').fadeOut().toggle();
			});
		});
	</script>
</div><!--content-->
