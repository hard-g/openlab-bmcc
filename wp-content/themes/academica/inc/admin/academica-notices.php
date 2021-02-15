<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class academica_Notices {
	public $name;
	public $type;
	public $dismiss_url;
	public $current_user_id;

	/**
	 * The constructor.
	 *
	 * @param string $name Notice Name.
	 * @param string $type Notice type.
	 * @param string $dismiss_url Notice permanent dismiss URL.
	 *
	 */
	public function __construct( $name, $type, $dismiss_url ) {
		$this->name                  = $name;
		$this->type                  = $type;
		$this->dismiss_url           = $dismiss_url;
		$this->current_user_id       = get_current_user_id();
	}

	public function notice() {
		if ( ! $this->is_dismiss_notice() ) {
			$this->notice_markup();
		}
	}

	private function is_dismiss_notice() {
		return apply_filters( 'academica_' . $this->name . '_notice_dismiss', true );
	}

	public function notice_markup() {
		echo '';
	}

	public function get_notices() {

		$academica_theme_admin_notices = get_option( 'academica_admin_notices' );
		return $academica_theme_admin_notices;

	}

	public function get_notice_status($notice_id) {

		$theme_admin_notices = $this->get_notices();

		if ( is_array($theme_admin_notices) && in_array($notice_id, $theme_admin_notices) ) {
			$this_notice_was_dismissed = TRUE;
		} else {
			$this_notice_was_dismissed = FALSE;
		}

		return $this_notice_was_dismissed;

	}

	/**
	 * Hide a notice if the GET variable is set.
	 */
	public function hide_notices() {

		if ( isset( $_GET['academica-hide-notice'] ) && isset( $_GET['_academica_notice_nonce'] ) ) {

			if ( ! wp_verify_nonce( wp_unslash( $_GET['_academica_notice_nonce'] ), 'academica_hide_notices_nonce' ) ) {
				wp_die( __( 'Action failed. Please refresh the page and retry.', 'academica' ) );
			}

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( __( 'Cheatin&#8217; huh?', 'academica' ) );
			}

			if ( $_GET['academica-hide-notice'] ) {

				$hide_notice_id = sanitize_text_field( wp_unslash( $_GET['academica-hide-notice'] ) );

				$theme_admin_notices = $this->get_notices();

				if ( is_array($theme_admin_notices) ) {
					if ( !in_array($hide_notice_id, $theme_admin_notices) ) {
						// this notice has never been dismissed before
						$theme_admin_notices[] = $hide_notice_id;
						$run_update = TRUE;
					}
				} else {
					// This is the first time a theme admin notice is being dismissed.
					$theme_admin_notices = array();
					$theme_admin_notices[] = $hide_notice_id;
					$run_update = TRUE;
				}

				if ( isset($run_update) ) {
					update_option( 'academica_admin_notices', $theme_admin_notices );
				}

			}

		}
	}

}