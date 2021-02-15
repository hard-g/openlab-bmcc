<?php
/**
* Tag Groups
*
* @package     Tag Groups
* @author      Christoph Amthor
* @copyright   2019 Christoph Amthor (@ Chatty Mango, chattymango.com)
* @license     GPL-3.0+
*
*/

if ( ! class_exists( 'TagGroups_Loader' ) ) {

  class TagGroups_Loader {


    /**
    * absolute path to the plugin main file
    *
    * @var string
    */
    var $plugin_path;


    function __construct( $plugin_path ) {

      $this->plugin_path = $plugin_path;

    }


    /**
    * Provide objects that we'll need frequently
    *
    * @param void
    * @return object $this
    */
    public function provide_globals() {

      global $tag_group_groups, $tag_group_terms;

      $tag_group_groups = new TagGroups_Groups();

      $tag_group_terms = new TagGroups_Terms();

      return $this;

    }


    /**
    * Adds all required classes
    *
    * @param void
    * @return object $this
    */
    public function require_classes()
    {

      /*
      * Require all classes of this plugin
      */
      foreach ( glob( $this->plugin_path . '/include/entities/*.php') as $filename ) {

        require_once $filename;

      }

      foreach ( glob( $this->plugin_path . '/include/helpers/*.php') as $filename ) {

        require_once $filename;

      }

      foreach ( glob( $this->plugin_path . '/include/admin/*.php') as $filename ) {

        require_once $filename;

      }

      /**
      * Must be after helpers
      */
      foreach ( glob( $this->plugin_path . '/include/shortcodes/*.php') as $filename ) {

        require_once $filename;

      }

      return $this;

    }


    /**
    * Sets the version from the plugin main file
    *
    * @param void
    * @return object $this;
    */
    public function set_version()
    {

      if ( defined( 'TAG_GROUPS_VERSION') ) {

        return $this;

      }

      if ( ! function_exists('get_plugin_data') ){

        require_once ABSPATH . '/wp-admin/includes/plugin.php';

      }

      $plugin_header = get_plugin_data( WP_PLUGIN_DIR . '/' . TAG_GROUPS_PLUGIN_BASENAME, false, false );

      if ( isset( $plugin_header['Version'] ) ) {

        $version = $plugin_header['Version'];

      } else {

        $version = '1.0';

      }


      define( 'TAG_GROUPS_VERSION', $version );

    }


    /**
    * Check if WordPress meets the minimum version
    *
    * @param void
    * @return void
    */
    public function check_preconditions() {

      if ( ! defined( 'TAG_GROUPS_MINIMUM_VERSION_WP' ) ) {

        return;

      }

      global $wp_version;

      // Check the minimum WP version
      if ( version_compare( $wp_version, TAG_GROUPS_MINIMUM_VERSION_WP , '<' ) ) {

        TagGroups_Error::log( '[Tag Groups] Insufficient WordPress version for Tag Groups plugin.' );

        TagGroups_Admin_Notice::add( 'error', sprintf( __( 'The plugin %1$s requires WordPress %2$s to function properly.', 'tag-groups'), '<b>Tag Groups</b>', TAG_GROUPS_MINIMUM_VERSION_WP ) .
        __( 'Please upgrade WordPress and then try again.', 'tag-groups' ) );

        return;

      }

    }


    /**
    * adds all hooks
    *
    * @param void
    * @return object $this
    */
    public function add_hooks()
    {

      global $tag_groups_hooks;

      $tag_groups_hooks = new TagGroups_Hooks( $this );

      $tag_groups_hooks->root_all();

      if ( is_admin() ) {

        $tag_groups_hooks->is_admin();

      } else {

        $tag_groups_hooks->not_is_admin();

      }

      return $this;

    }


    /**
    * registers the shortcodes with Gutenberg blocks
    *
    * @param void
    * @return object $this
    */
    public function register_shortcodes_and_blocks()
    {

      /**
      * add Gutenberg functionality
      */
      require_once $this->plugin_path . '/src/init.php';

      // Register shortcodes also for admin so that we can remove them with strip_shortcodes in Ajax call
      TagGroups_Shortcode::register();

      return $this;

    }


    /**
    * registers the REST API
    *
    * @param void
    * @return object $this
    */
    public function register_REST_API()
    {

      TagGroups_REST_API::register_hook();

      return $this;

    }


    /**
    * Loads text domain for internationalization
    */
    public function register_textdomain() {

      load_plugin_textdomain( 'tag-groups', false, TAG_GROUPS_PLUGIN_RELATIVE_PATH . '/languages/' );

    }

    /**
    * registers the CRON routines
    *
    * @param void
    * @return object $this
    */
    public function register_CRON()
    {

      // CRON independent from admin or frontend
      TagGroups_Cron::register_identifiers();

      TagGroups_Cron::schedule_regular( 'hourly', 'tag_groups_check_tag_migration' );

      return $this;

    }

  }

}
