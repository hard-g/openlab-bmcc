<?php
/**
 * Extend WP_Customize_Control for additional control.
 *
 * Class RADIATE_ADDITIONAL_Control
 *
 * @since 1.3.1
 */

class RADIATE_ADDITIONAL_Control extends WP_Customize_Control {
	public $type = 'textarea';

	public function render_content() {
		?>
		<label>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<textarea rows="5" style="width:100%;" <?php $this->link(); ?>><?php echo esc_textarea( $this->value() ); ?></textarea>
		</label>
		<?php
	}
}
