<?php
/**
 * Better Category Selector for WooCommerce Loader Class
 */

defined('ABSPATH') or die('No script kiddies please!');

/**
 * Loader class.
 * @package better-category-selector-for-woocommerce
 * @subpackage includes
 */
class HD_BCS_Loader {

	/**
	 * Plugin Loader init.
	 * @static
	 * @since 1.0
	 */
	public static function init() {
		self::check_for_upgrade();
		self::include_files();
		self::add_wp_hooks();

		// Instantiate settings (admin) class.
		//new HD_BCS_Settings();
	}

	/**
	 * Check to see if plugin has an upgrade.
	 * @static
	 * @since 1.0
	 */
	private static function check_for_upgrade() {
		//widget_css_classes_activation();
	}

	/**
	 * Calls the plugin files for inclusion.
	 * @static
	 * @since 1.0
	 */
	private static function include_files() {
		// Coming Soon !!include_once HD_BCS_PLUGIN_DIR . 'includes/bcs-settings.class.php';
    if(HD_BCS_PLUGIN_VERSION_PRO){
      include_once HD_BCS_PLUGIN_DIR . 'includes/bcs-walker-pro.class.php';
    }
    else{
      include_once HD_BCS_PLUGIN_DIR . 'includes/bcs-walker.class.php';
    }

	}

	/**
	 * Adds WordPress hooks for actions and filters.
	 * @static
	 * @since 1.0
	 */
	private static function add_wp_hooks() {
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts_styles' ) );
		add_filter( 'plugin_action_links', array( 'HD_BCS_Lib', 'add_settings_link' ), 10, 2 );
	}

	/**
	 * Load the plugin CSS, JS and Help tab.
	 * @static
	 * @since 1.0
	 */
	public static function enqueue_scripts_styles() {
		$screen = get_current_screen();

    //echo("|||| screen |||| " . print_r($screen,true) . " ||||<Br><br>");

		// if on the bcs-category-selector.
		if ( in_array($screen->id,array('admin_page_bcs-category-selector','product'))) {
			wp_enqueue_style( 'bcs_css', HD_BCS_PLUGIN_URL . '/css/bcs.css?child-theme-configurator-breaks-versioning=' . HD_BCS_PLUGIN_VERSION . '.' . time(), array(), HD_BCS_PLUGIN_VERSION );
      if(HD_BCS_PLUGIN_VERSION_PRO){
  			wp_enqueue_script( 'bcs_js', HD_BCS_PLUGIN_URL . '/js/bcs-pro.js', array( 'jquery' ), HD_BCS_PLUGIN_VERSION . '.' . time() );
      }
      else{
  			wp_enqueue_script( 'bcs_js', HD_BCS_PLUGIN_URL . '/js/bcs.js', array( 'jquery' ), HD_BCS_PLUGIN_VERSION . '.' . time() );
      }
		}
	}

}
