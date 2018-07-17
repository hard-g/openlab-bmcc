<?php

/**
 * Adds 'local environment' tab
 */
function bbg_env_type_flag() {
	if ( defined( 'ENV_TYPE' ) && 'production' != ENV_TYPE ) {

		$git_branch = '';
		$git_head_path = ABSPATH . '/.git/HEAD';
		if ( is_readable( $git_head_path ) ) {
			$git_head = file( ABSPATH . '/.git/HEAD' );
			if ( $git_head ) {
				$git_branch = str_replace( 'ref: refs/heads/', '', $git_head[0] );
			}
		}

		?>

		<style type="text/css">
			#env-type-flag {
				position: fixed;
				right: 0;
				bottom: 50px;
				width: 200px;
				padding: 10px 15px 0;
				background: #f00;
				color: #fff;
				font-size: 1.1em;
				line-height: 1.4em;
				border: 2px solid #666;
				z-index: 1000;
			}
		</style>

		<div id="env-type-flag">
			<p>Environment: <strong><?php echo ENV_TYPE ?></strong></p>

			<?php if ( $git_branch ) : ?>
				<p>Branch: <strong><?php echo $git_branch ?></strong></p>
			<?php endif; ?>
		</div>

		<?php
	}
}
add_action( 'wp_footer', 'bbg_env_type_flag' );
add_action( 'admin_footer', 'bbg_env_type_flag' );

?>
