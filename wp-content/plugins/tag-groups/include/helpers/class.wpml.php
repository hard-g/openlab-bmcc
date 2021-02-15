<?php
/**
* Tag Groups
*
* @package     Tag Groups Premium
* @author      Christoph Amthor
* @copyright   2017 Christoph Amthor (@ Chatty Mango, chattymango.com)
* @license     see official vendor website
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

if ( ! class_exists('TagGroups_WPML') ) {

  class TagGroups_WPML {


    /**
     * Returns the language code (by default ICL_LANGUAGE_CODE)
     *
     * @return string
     */
    static function get_current_language()
    {

      if ( defined( 'ICL_LANGUAGE_CODE' ) ) {

        return (string) ICL_LANGUAGE_CODE;

      }

      if ( function_exists( 'pll_current_language' ) ) {

        $current_language = pll_current_language();

        if ( $current_language ) {

          return (string) $current_language;

        }

        if ( isset( $_GET[ 'lang' ] ) ) {

          return sanitize_key( $_GET[ 'lang' ] );

        }

      }

      return '';

    }
    

    // TODO: add unit test
    /**
    * Get the transient name for tag_groups_group_terms
    *
    * In case we use the WPML plugin: consider the language
    *
    * @param void
    * @return string
    */
    public static function get_tag_groups_group_terms_transient_name() {

      $current_language = self::get_current_language();

      if ( $current_language ) {

        return 'tag_groups_group_terms-' . $current_language;

      } else {

        return 'tag_groups_group_terms';

      }

    }


    // TODO: add unit test
    /**
    * Get the transient name for tag_groups_post_counts
    *
    * In case we use the WPML plugin: consider the language
    * Use $language if provided, else use current language
    *
    * @param string $language
    * @return string
    */
    public static function get_tag_groups_post_counts_transient_name( $language_code = null ) {

      if ( ! empty( $language_code ) ) {

        return 'tag_groups_post_counts-' . (string) $language_code;

      }

      $current_language = self::get_current_language();

      if ( $current_language ) {

          return 'tag_groups_post_counts-' . $current_language;

      } else {

        return 'tag_groups_post_counts';

      }

    }

  }

}
