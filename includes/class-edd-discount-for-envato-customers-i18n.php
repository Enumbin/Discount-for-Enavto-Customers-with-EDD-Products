<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       smartdatasoft.com
 * @since      1.0.0
 *
 * @package    Edd_Discount_For_Envato_Customers
 * @subpackage Edd_Discount_For_Envato_Customers/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Edd_Discount_For_Envato_Customers
 * @subpackage Edd_Discount_For_Envato_Customers/includes
 * @author     SmartDataSoft <support@smartdatasoft.com>
 */
class Edd_Discount_For_Envato_Customers_i18n {
	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'edd-discount-for-envato-customers',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}
}
