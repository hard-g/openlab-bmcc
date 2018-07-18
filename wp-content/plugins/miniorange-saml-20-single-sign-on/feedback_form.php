<?php
require_once dirname( __FILE__ ) . '/includes/lib/mo-options-enum.php';

function display_saml_feedback_form() {
	if ( 'plugins.php' != basename( $_SERVER['PHP_SELF'] ) ) {
		return;
	}
	$deactivate_reasons = mo_options_feedback::getConstants();

	wp_enqueue_style( 'wp-pointer' );
	wp_enqueue_script( 'wp-pointer' );
	wp_enqueue_script( 'utils' );
	wp_enqueue_style( 'mo_saml_admin_plugins_page_style', plugins_url( '/includes/css/style_settings.css', __FILE__ ) );
	?>

    </head>
    <body>


    <!-- The Modal -->
    <div id="feedback_modal" class="mo_modal">

        <!-- Modal content -->
        <div class="mo_modal-content">
            <span class="mo_close">&times;</span>
            <h3>Tell us what happened? </h3>

            <form name="f" method="post" action="" id="mo_feedback">
                <input type="hidden" name="mo_feedback" value="mo_feedback"/>
                <div>
                    <p style="margin-left:2%">
						<?php

						foreach ( $deactivate_reasons as $deactivate_reason ) { ?>

                    <div class="radio" style="padding:1px;margin-left:2%">
                        <label style="font-weight:normal;font-size:14.6px" for="<?php echo $deactivate_reason; ?>">
                            <input type="radio" name="deactivate_reason_radio" value="<?php echo $deactivate_reason; ?>"
                                   required>
							<?php echo $deactivate_reason; ?></label>
                    </div>


					<?php } ?>
                    <br>

                    <textarea id="query_feedback" name="query_feedback" rows="4" style="margin-left:2%;width: 330px"
                              placeholder="Write your query here"></textarea>
                    <br><br>
                    <div class="mo_modal-footer">
                        <input type="submit" name="miniorange_feedback_submit"
                               class="button button-primary button-large" value="Submit"/>
                    </div>
                </div>
            </form>
            <form name="f" method="post" action="" id="mo_feedback_form_close">
                <input type="hidden" name="option" value="mo_skip_feedback"/>
            </form>

        </div>

    </div>

    <script>
        jQuery('a[aria-label="Deactivate miniOrange SSO using SAML 2.0"]').click(function () {

            var mo_modal = document.getElementById('feedback_modal');

            var span = document.getElementsByClassName("mo_close")[0];

// When the user clicks the button, open the mo2f_modal 

            mo_modal.style.display = "block";

            // jQuery('#myModal').mo2f_modal('mo2f_toggle');


            jQuery('input:radio[name="deactivate_reason_radio"]').click(function () {
                var reason = jQuery(this).val();
                var query_feedback = jQuery('#query_feedback');
                query_feedback.removeAttr('required')

                if (reason === "Does not have the features I'm looking for") {
                    query_feedback.attr("placeholder", "Let us know what feature are you looking for");
                } else if (reason === "Other Reasons:") {
                    query_feedback.attr("placeholder", "Can you let us know the reason for deactivation");
                    query_feedback.prop('required', true);

                } else if (reason === "Bugs in the plugin") {
                    query_feedback.attr("placeholder", "Can you please let us know about the bug in detail?");

                } else if (reason === "Confusing Interface") {
                    query_feedback.attr("placeholder", "Finding it confusing? let us know so that we can improve the interface");
                }


            });


            // When the user clicks on <span> (x), mo2f_close the mo2f_modal
            span.onclick = function () {
                mo_modal.style.display = "none";
                jQuery('#mo_feedback_form_close').submit();
            }

            // When the user clicks anywhere outside of the mo2f_modal, mo2f_close it
            window.onclick = function (event) {
                if (event.target == mo_modal) {
                    mo_modal.style.display = "none";
                }
            }
            return false;

        });
    </script><?php
}

?>