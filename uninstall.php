<?php
/**
 * Better Category Selector for WooCommerce Uninstall
 */

//if uninstall not called from WordPress exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die();
}

hd_bcs_uninstall();

function hd_bcs_uninstall( $blog_id = false ) {

	delete_option( 'hd_bcs_options' );

}


/*

function wcssc_uninstall( $blog_id = false ) {

  // Delete all options
  $option_keys = array( 'HD_BCS_options', 'HD_BCS_db_version' );

  foreach ( $option_keys as $option_key ) {
    delete_option( $option_key );
  }

}
*/