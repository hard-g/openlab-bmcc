<?php
/**
 * @package Academica
 */
?>
<form method="get" id="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label for="s" class="assistive-text hidden"><?php _e( 'Search', 'academica' ); ?></label>
	<input id="s" type="text" name="s" placeholder="<?php esc_attr_e( 'Search', 'academica' ); ?>">
	<button id="searchsubmit" name="submit" type="submit"><?php _e( 'Search', 'academica' ); ?></button>
</form>