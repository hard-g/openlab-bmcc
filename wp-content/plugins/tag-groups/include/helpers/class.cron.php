<?php
/**
* Tag Groups
*
* @package    Tag Groups
* @author     Christoph Amthor
* @copyright  2018 Christoph Amthor (@ Chatty Mango, chattymango.com)
* @license    see official vendor website
* @since      1.24.0
*
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
* AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
* LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
* OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
* THE SOFTWARE.
*
*/

if ( ! class_exists( 'TagGroups_Cron' ) ) {

  /**
  * The main purpose of this class is to keep all cron-related information in one place.
  *
  */
  class TagGroups_Cron {


    /**
    * Registers the CRON identifiers and connects them to a method.
    *
    *
    * @param void
    * @return void
    */
    public static function register_identifiers()
    {

      global $tag_group_groups;


      /**
      * Task to migrate tags from the base to the premium plugin in the background
      */
      add_action( 'tag_groups_run_term_migration', array( 'TagGroups_Terms', 'run_term_migration' ) );

      /**
      * Task to check if we need to migrate the tags
      */
      add_action( 'tag_groups_check_tag_migration', array( 'TagGroups_Terms', 'check_if_we_need_to_run_migration' ) );

      /**
      * Task to clear the transient cache tag_groups_group_terms
      */
      add_action( 'tag_groups_clear_tag_groups_group_terms', array( $tag_group_groups, 'clear_tag_groups_group_terms' ) );

    }


    /**
    * Schedules a single event
    *
    * @param int $seconds_from_now Time in seconds after which to execute the task.
    * @param string $identifier What we used in register_identifiers();
    * @return boolean Whether the event was properly scheduled.
    */
    public static function schedule_in_secs( $seconds_from_now, $identifier )
    {

      /**
       * We never schedule the same taske twice. Instead we postpone the execution.
       */
      if ( $timestamp = wp_next_scheduled( $identifier ) ) {

        wp_unschedule_event( $timestamp, $identifier );

      }

      $cron_result = wp_schedule_single_event( time() + $seconds_from_now, $identifier );

      if ( $cron_result === false ) {

        return false;

      }

      return true;

    }


    /**
    * Schedules a regular event
    *
    * @param string $recurrence 'hourly', 'twicedaily' or 'daily'
    * @param string $identifier What we used in register_identifiers();
    * @return boolean Whether the event was properly scheduled.
    */
    public static function schedule_regular( $recurrence, $identifier )
    {

      if ( wp_next_scheduled ( $identifier ) ) {

        return true;

      }

      $cron_result = wp_schedule_event( time(), $recurrence, $identifier );

      if ( $cron_result === false ) {

        return false;

      }

      return true;

    }

  }

}
