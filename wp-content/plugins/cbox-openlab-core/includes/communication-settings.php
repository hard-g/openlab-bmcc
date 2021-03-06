<?php

function cboxol_communication_admin_page_email() {
	$url_base = get_admin_url( cboxol_get_main_site_id() );

	$settings = array(
		array(
			'title'       => __( 'Email Appearance', 'commons-in-a-box' ),
			'url'         => add_query_arg(
				array(
					'post_type' => 'bp-email',
					'page'      => 'bp-emails-customizer-redirect',
				),
				$url_base . 'edit.php'
			),
			'description' => __( 'Customize the visual design of emails that will be sent to community members.', 'commons-in-a-box' ),
		),
		array(
			'title'       => __( 'Email Templates', 'commons-in-a-box' ),
			'url'         => add_query_arg(
				array(
					'post_type' => 'bp-email',
				),
				$url_base . 'edit.php'
			),
			'description' => __( 'Manage the content that’s included in emails sent automatically to members based on triggers (e.g. a welcome email sent to new members of a group).', 'commons-in-a-box' ),
		),
	);

	if ( function_exists( 'ass_admin_menu' ) ) {
		if ( is_plugin_active_for_network( 'buddypress-group-email-subscription/bp-activity-subscription.php' ) ) {
			$ges_base = network_admin_url( 'settings.php' );
		} else {
			$ges_base = $url_base . 'options-general.php';
		}

		$settings[] = array(
			'title'       => __( 'Group Email Options', 'commons-in-a-box' ),
			'url'         => add_query_arg(
				array(
					'page' => 'ass_admin_options',
				),
				$ges_base
			),
			'description' => __( 'Update settings related to Daily Digests & Weekly Summaries of group activities in your community; toggle global unsubscribe links; modify group admin abilities related to email subscription settings; and establish spam prevention guidelines.', 'commons-in-a-box' ),
		);
	}

	?>
	<div class="cboxol-admin-content">
		<p><?php esc_html_e( 'Members of your community can be sent various notifications via email. Manage all settings related to emails here.', 'commons-in-a-box' ); ?></p>

		<?php cboxol_communication_settings_markup( $settings ); ?>
	</div>
	<?php
}

function cboxol_communication_admin_page_invitations() {
	if ( bp_core_do_network_admin() ) {
		$base = network_admin_url( 'settings.php' );
	} else {
		$base = get_admin_url( cboxol_get_main_site_id(), 'options-general.php' );
	}

	$settings = array(
		array(
			'title'       => __( 'Invite Anyone', 'commons-in-a-box' ),
			'url'         => add_query_arg(
				array(
					'page' => 'invite-anyone',
				),
				$base
			),
			'description' => __( 'Manage the invite email content template, control which member types are able to send various kinds of invitations, control address book integration, view sent invitations and related statistics.', 'commons-in-a-box' ),
		),
	);

	?>
	<div class="cboxol-admin-content">
		<p><?php esc_html_e( 'Invite Anyone allows community members to invite non-members to your community and its groups via email.', 'commons-in-a-box' ); ?></p>

		<?php cboxol_communication_settings_markup( $settings ); ?>
	</div>
	<?php
}

/**
 * Badges settings panel under Communication Settings.
 *
 * @since 1.2.0
 */
function cboxol_badges_admin_page() {
	$url = add_query_arg(
		'page',
		'openlab-badges',
		network_admin_url( 'admin.php' )
	);

	$settings = array(
		array(
			'title'       => __( 'OpenLab Badges', 'commons-in-a-box' ),
			'url'         => $url,
			'description' => __( 'Create and manage group badges on the Badges admin page.', 'commons-in-a-box' ),
		),
	);

	?>
	<div class="cboxol-admin-content">
		<p><?php esc_html_e( 'OpenLab Badges allows the network administrator to create custom badges that can be awarded to groups. These badges are displayed in group directories and on group home pages, and can be used to find and filter groups in the directories.', 'commons-in-a-box' ); ?></p>

		<?php cboxol_communication_settings_markup( $settings ); ?>
	</div>
	<?php
}

/**
 * Helper function for building markup on Communication Settings panels.
 *
 * @since 1.1.0
 *
 * @param array $settings {
 *   Values for the various parts of the panel interface.
 *
 *   @type string $title       Panel title.
 *   @type string $url         URL for the link.
 *   @type string $description Description text.
 * }
 */
function cboxol_communication_settings_markup( $settings ) {
	?>
		<ul class="cboxol-communication-settings">
			<?php foreach ( $settings as $setting ) : ?>
			<li>
				<div class="setting-link"><a href="<?php echo esc_url( $setting['url'] ); ?>"><?php echo esc_html( $setting['title'] ); ?></a></div>
				<div class="setting-description">
					<p class="description"><?php echo esc_html( $setting['description'] ); ?></p>
				</div>
			</li>
			<?php endforeach; ?>
		</ul>
	<?php
}
