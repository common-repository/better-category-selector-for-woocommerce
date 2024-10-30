<?php
/**
 * Plugin Name: Better Category Selector for WooCommerce
 * Version: 1.0.5
 * Description: Better Category Selector for WooCommerce provides an alternative, easier to use GUI for putting products in categories.
 * Author: NO.BrainerAPPs / HisDesigns LLC
 * Author URI: http://no.brainerapps.com
 * Text Domain: better-category-selector-for-woocommerce
 *
 * Copyright: (c) 2022, HisDesigns LLC
 *
 */

defined('ABSPATH') or die('No script kiddies please!');

add_action( 'init', 'hd_bcs_loader' );

// Hijack the default product category meta box.
// This has to be done here because this has to be
// called before woocommerce's taxonomies have been
// registered.
function hd_bcs_tweak_woocommerce_product_cat_taxonomy( $tax_array ) {
  return array_merge($tax_array, array('meta_box_cb' => 'hd_bcs_add_bcs_link_to_categories_meta_box'));
}
add_filter( 'woocommerce_taxonomy_args_product_cat', 'hd_bcs_tweak_woocommerce_product_cat_taxonomy' );

add_action('admin_menu', 'hd_bcs_add_bcs_category_selector_page');
function hd_bcs_add_bcs_category_selector_page(){
  //add_menu_page('','Better WooCommerce Category Selector','manage_options','bcs-category-selector','bcs_include_bcs_category_selector_php');
  add_submenu_page( '', 'Better WooCommerce Category Selector', '', 'manage_options', 'bcs-category-selector', 'hd_bcs_include_bcs_category_selector_php' );
}

/*
add_submenu_page( '', 'Better WooCommerce Category Selector', '', 'manage_options', 'bcs-category-selector', 'bcs_include_bcs_category_selector_php' )
*/

function hd_bcs_include_bcs_category_selector_php(){
  include_once( dirname( __FILE__ ) . '/bcs-category-selector.php' );
}

function hd_bcs_body_classes( $classes ) {
  $classes .= ' hd-bcs-category-selector-free';
  return $classes;
}
add_filter( 'admin_body_class', 'hd_bcs_body_classes' );

/**
 * Define constants and load the plugin
 * @since 1.0
 */
function hd_bcs_loader() {

  /* Hide until languages can be developed
  $languages_path = plugin_basename( dirname( __FILE__ ) . '/languages' );
  load_plugin_textdomain( 'better-category-selector-for-woocommerce', false, $languages_path );
  */

  // Load plugin settings
  include_once 'includes/bcs-library.class.php';
  //HD_BCS_Lib::set_settings( get_option( HD_BCS_Lib::$settings_key ) );

  if ( is_admin() ) {

    if ( ! defined( 'HD_BCS_PLUGIN_VERSION' ) ) define( 'HD_BCS_PLUGIN_VERSION', '1.0.4' );
    if ( ! defined( 'HD_BCS_PLUGIN_VERSION_PRO' ) ) define( 'HD_BCS_PLUGIN_VERSION_PRO', false);
    if ( ! defined( 'HD_BCS_FILE' ) ) define( 'HD_BCS_FILE', __FILE__ );
    if ( ! defined( 'HD_BCS_BASENAME' ) ) define( 'HD_BCS_BASENAME', plugin_basename( __FILE__ ) );
    if ( ! defined( 'HD_BCS_PLUGIN_DIR' ) ) define( 'HD_BCS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
    if ( ! defined( 'HD_BCS_PLUGIN_URL' ) ) define( 'HD_BCS_PLUGIN_URL', plugins_url( '', __FILE__ ) );

    include_once 'includes/bcs-loader.class.php';
    HD_BCS_Loader::init();

  }
}

/**
 * hd_bcs_add_bcs_link_to_categories_meta_box
 *
 * @since 1.0
 *
 * @param WP_Post $post Post object.
 * @param array   $box {
 *     Categories meta box arguments.
 *
 *     @type string   $id       Meta box 'id' attribute.
 *     @type string   $title    Meta box title.
 *     @type callable $callback Meta box display callback.
 *     @type array    $args {
 *         Extra meta box arguments.
 *
 *         @type string $taxonomy Taxonomy. Default 'category'.
 *     }
 * }
 */
function hd_bcs_add_bcs_link_to_categories_meta_box( $post, $box ) {
  ?>
<p class="hide-if-no-js hide_old_meta_box" id="bcs_show_selector_link"><a href="<?php echo esc_url(get_site_url()); ?>/wp-admin/admin.php?page=bcs-category-selector&post_id=<?php echo esc_attr($post->ID); ?>&TB_iframe=1" id="set-post-categories" class="thickbox">Better Category Selector for WooCommerce</a><input type="hidden" id="bcs_track_cat_meta_box_changes" name="bcs_track_cat_meta_box_changes" value=''></p>

<p class="hide-if-no-js hide_old_meta_box" id="bcs_show_old_meta_box_link"><a href="#">Show Old Style Category Selector</a></p>

<p class="hide-if-no-js hide_old_meta_box" id="bcs_hide_old_meta_box_link"><a href="#" class="">Hide Old Style Category Selector</a></p>

<?php
  post_categories_meta_box( $post, $box );
}

/**
 * bcs_popular_terms_checklist
 *
 * @since 1.0.2
 *
 * Remake of wp_popular_terms_checklist to add
 * post as an argument since wp_popular_terms_checklist 
 * wasn't able to find the post from BCS TB Frame

 * Retrieve a list of the most popular terms from the specified taxonomy.
 *
 * If the $echo argument is true then the elements for a list of checkbox
 * `<input>` elements labelled with the names of the selected terms is output.
 * If the $post_ID global isn't empty then the terms associated with that
 * post will be marked as checked.
 *
 * @since 2.5.0
 *
 * @param string $taxonomy Taxonomy to retrieve terms from.
 * @param int    $default  Not used.
 * @param int    $number   Number of terms to retrieve. Defaults to 10.
 * @param bool   $echo     Optionally output the list as well. Defaults to true.
 *
 * @param obj   $post     Current post we are editing.
 *
 * @return int[] Array of popular term IDs.
 */

function hd_bcs_popular_terms_checklist( $taxonomy, $default = 0, $number = 10, $echo = true, $post = false ) {

  // If not post then attempt to get post.
  if(!$post){
    $post = get_post();
  }
 
  if ( $post && $post->ID ) {
    $checked_terms = wp_get_object_terms( $post->ID, $taxonomy, array( 'fields' => 'ids' ) );
  } else {
    $checked_terms = array();
  }
 
  $terms = get_terms(
      array(
          'taxonomy'     => $taxonomy,
          'orderby'      => 'count',
          'order'        => 'DESC',
          'number'       => $number,
          'hierarchical' => false,
      )
  );
 
  $tax = get_taxonomy( $taxonomy );
 
  $popular_ids = array();
 
  foreach ( (array) $terms as $term ) {
    $popular_ids[] = $term->term_id;
    if ( ! $echo ) { // Hack for Ajax use.
      continue;
    }
    $id      = "popular-$taxonomy-$term->term_id";
    $checked = in_array( $term->term_id, $checked_terms, true ) ? 'checked="checked"' : '';
    ?>

    <li id="<?php echo esc_attr($id); ?>" class="popular-category">
        <label class="selectit">
            <input id="in-<?php echo esc_attr($id); ?>" type="checkbox" <?php echo esc_attr($checked); ?> value="<?php echo (int) esc_attr($term->term_id); ?>" <?php disabled( ! current_user_can( $tax->cap->assign_terms ) ); ?> />
            <?php
            /** This filter is documented in wp-includes/category-template.php */
            echo esc_html( apply_filters( 'the_category', $term->name, '', '' ) );
            ?>
        </label>
    </li>

    <?php
  }
  return $popular_ids;
}

/**
 * Install plugin
 */
function hd_bcs_activation() {
  global $wp_version;

  if ( version_compare( $wp_version, '5.4.9', '<' ) ) {
    // Add admin notice.
    add_action( 'admin_notices', 'bcs_notice_wp_version' );
    // Deactivate.
    //require_once ABSPATH . 'wp-admin/includes/plugin.php';
    //deactivate_plugins( plugin_basename( __FILE__ ) );
    //return;
  }

  if ( ! defined( 'HD_BCS_BASENAME' ) ) define( 'HD_BCS_BASENAME', plugin_basename( __FILE__ ) );
  // This is the version of this plugin saved in the database.
  // NOT the database (MySQL) version.
	if ( ! defined( 'HD_BCS_DB_VERSION' ) ) define( 'HD_BCS_DB_VERSION', '1.0.4');
  if ( ! defined( 'HD_BCS_FILE' ) ) define( 'HD_BCS_FILE', __FILE__ );
  include_once 'includes/bcs-library.class.php';

	if ( get_option( 'HD_BCS_db_version' ) ) {
		$installed_ver = get_option( 'HD_BCS_db_version' );
	} else {
		$installed_ver = 0;
	}

	// if the installed version is not the same as the current version, run the install function
	if ( (string) HD_BCS_DB_VERSION !== (string) $installed_ver ) {
		HD_BCS_Lib::install_plugin();
	}
}

register_activation_hook( __FILE__, 'hd_bcs_activation' );

/**
 * Wrong WP version admin notice.
 */
function hd_bcs_notice_wp_version() {
  echo esc_html('<div class="notice notice-error is-dismissible"><p>');
  echo sprintf(
    // Translators: %1$s stands for the WP version and %2$s stands for "Please update" (a link).
    esc_html__( 'Better Category Selector for WooCommerce has only been tested with WordPress %1$s or newer. It might/probably will work with your version but it would be better if you updated.  %2$s', 'better-category-selector-for-woocommerce' ),
    '3.3',
    '<a target="_blank" href="http://codex.wordpress.org/Upgrading_WordPress">' . esc_html__( 'Please update', 'better-category-selector-for-woocommerce' ) . '.</a>'
  );
  echo esc_html('</p></div>');
}

/*
if (is_plugin_active('woocommerce/woocommerce.php')) {
} elseif (is_admin()) {

  add_action('admin_notices', function() {
    $message = __('Better Category Selector for WooCommerce needs WooCommerce to run. Please, install and active WooCommerce plugin.', 'better-category-selector-for-woocommerce');
    printf('<div class="%1$s"><p>%2$s</p></div>', 'notice notice-error', $message);
  });
}
*/