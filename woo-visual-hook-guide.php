<?php
/*
 * Plugin Name:       Woocommerce Visual Hook Guide
 * Description:       Visual hook guide for WooCommerce.
 * Version:           1.0.3
 * Author:            Ayush Malakar & Greg Colley
 * Author URI:        https://www.ayushmalakar.com/
 * Text Domain:       woocommerce-visual-hook-guide
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.html
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );  // prevent direct access
if ( ! defined( 'WVHG_DIR_PATH' ) ) {
	define( 'WVHG_DIR_PATH', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'WVHG_URL_PATH' ) ) {
	define( 'WVHG_PATH', __FILE__ );
}
if ( ! defined( 'WVHG_BASE_PATH' ) ) {
	define( 'WVHG_BASE_PATH', plugin_basename( __FILE__ ) );

}

if ( ! class_exists( 'woo_visual_hook_guide' ) ) :


	class woo_visual_hook_guide {

		/**
		 * Plugin version.
		 *
		 * @var string
		 */
		const VERSION = '1.0.0';

		/**
		 * Instance of this class.
		 *
		 * @var object
		 */
		protected static $instance = null;


		/**
		 * Initialize the plugin.
		 */
		public function __construct() {


			/**
			 * Check if WooCommerce is active
			 **/
			if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
				include_once 'inc/show-hooks.php';
				include_once 'inc/class-utility-qm.php';
			} else {
				add_action( 'admin_init', array( $this, 'wvh_plugin_deactivate' ) );
				add_action( 'admin_notices', array( $this, 'wvhg_woocommerce_missing_notice' ) );
			}
		} // end of contructor
			public static function get_instance() {
				// If the single instance hasn't been set, set it now.
				if ( null == self::$instance ) {
					self::$instance = new self;
				}

				return self::$instance;
			}



		/**
		 * Return an instance of this class.
		 *
		 * @return object A single instance of this class.
		 */


		/**
		 * WooCommerce fallback notice.
		 *
		 * @return string
		 */
		public function wvhg_woocommerce_missing_notice() {
			echo '<div class="error"><p>' . sprintf( __( 'Woocommerce Visual Hook Guide says "%s must be active and installed to take flight!!"', 'woo-visual-hook-guide' ), '<a href="http://www.woothemes.com/woocommerce/" target="_blank">' . __( 'WooCommerce', 'woo-floating-minicart' ) . '</a>' ) . '</p></div>';
			if ( isset( $_GET[ 'activate' ] ) ) {
				unset( $_GET[ 'activate' ] );
			}
		}

		/**
		 * WooCommerce fallback notice.
		 *
		 * @return string
		 */
		public function wvh_plugin_deactivate() {
			deactivate_plugins( plugin_basename( __FILE__ ) );
		}


	}// end of the class

	add_action( 'plugins_loaded', array( 'woo_visual_hook_guide', 'get_instance' ), 0 );

endif;