<?php
/**
 * @package     Tag Groups
 *
 * @author      Christoph Amthor
 * @copyright   2018 Christoph Amthor (@ Chatty Mango, chattymango.com)
 * @license     GPL-3.0+
 */

if ( ! class_exists( 'TagGroups_Base' ) ) {

    /**
     *
     */
    class TagGroups_Base {

        /**
         * Returns the first element of an array without changing the original array
         *
         * @param  array   $array
         * @return mixed
         */
        public static function get_first_element( $array = array() ) {

            if ( ! is_array( $array ) ) {

                TagGroups_Error::log('[Tag Groups] Parameter supplied to get_first_element() must be an array.');

                return;

            }

            return reset( $array );

        }

        /**
         * sanitizes many classes separated by space
         *
         * @param  string   $classes
         * @return string
         */
        public static function sanitize_html_classes( $classes ) {

            // replace multiple spaces by one
            $classes = preg_replace( '!\s+!', ' ', $classes );

            // turn into array
            $classes = explode( ' ', $classes );

            if ( ! empty( $classes ) ) {

                $classes = array_map( 'sanitize_html_class', $classes );

            }

            // turn back
            $classes = implode( ' ', $classes );

            return $classes;

        }

        /**
         * Change the time until the first trial encouragement appears
         *
         * @since 1.19.1
         *
         * @param  int   $sec Default is 24 hours.
         * @return int
         */
        public static function change_time_show_first_trial( $sec ) {

            return 3 * DAY_IN_SECONDS;

        }

        /**
         * Change the time between trial encouragements
         *
         * 
         * @since 1.19.1
         *
         * @param  int   $sec Default is 30 days.
         * @return int
         */
        public static function change_time_reshow_trial( $sec ) {

            // These messages appear only on pages where users go to set up tag groups, not for daily work, that means only a few times per year -> can reshow after 30 days, it effectively will be less

            return 30 * DAY_IN_SECONDS;

        }

        /**
         * Show Freemius admin notice of trial promotion only in Tag Groups own settings or Tag Groups Admin page
         * ("page" parameter starts with tag-groups)
         *
         * @since 1.19.2
         *
         * @param  mixed     $show
         * @param  array     $msg
         * @return boolean
         */
        public static function change_show_admin_notice( $show, $msg ) {

            if (
                'trial_promotion' == $msg['id']
                && ( empty( $_GET['page'] ) || strpos( $_GET['page'], 'tag-groups' ) !== 0 )
            ) {

                // Don't show the trial promotional admin notice.
                return false;

            }

            return true;

        }


        /**
         * Turns a string into a valid JS function name, preserving as much as possible uniqueness
         *
         * @since 1.26.1
         *
         * @param  string   $raw
         * @return string
         */
        static function create_js_fn_name( $raw ) {

            return str_replace( '-', '', sanitize_html_class( $raw ) );

        }

    }

    // class

}
