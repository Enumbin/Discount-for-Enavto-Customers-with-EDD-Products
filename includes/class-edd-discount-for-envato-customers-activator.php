<?php

/**
 * Fired during plugin activation
 *
 * @link       smartdatasoft.com
 * @since      1.0.0
 *
 * @package    Edd_Discount_For_Envato_Customers
 * @subpackage Edd_Discount_For_Envato_Customers/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Edd_Discount_For_Envato_Customers
 * @subpackage Edd_Discount_For_Envato_Customers/includes
 * @author     SmartDataSoft <support@smartdatasoft.com>
 */
class Edd_Discount_For_Envato_Customers_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();
		$table_name = $wpdb->prefix . "edddfe_buyer"; 
		$sql[] = "CREATE TABLE $table_name (
			buyer_id mediumint(9) NOT NULL AUTO_INCREMENT,
			name varchar(50) NOT NULL,
			purchase_count int(11) NOT NULL,
			PRIMARY KEY  (buyer_id)
		) $charset_collate;";

		$table_name = $wpdb->prefix . "edddfe_plans"; 
		$sql[] = "CREATE TABLE $table_name (
			plan_id mediumint(15) NOT NULL AUTO_INCREMENT,
			name varchar(255) NOT NULL,
			edd_product_id int(11) NOT NULL,
			envato_product_id int(11) NOT NULL,
			PRIMARY KEY  (plan_id)
		) $charset_collate;";

		$table_name = $wpdb->prefix . "edddfe_license_details"; 
		$sql[] = "CREATE TABLE $table_name (
			license_id mediumint(15) NOT NULL AUTO_INCREMENT,
			buyer_id mediumint(9) NOT NULL,
			envato_product_id int(11) NOT NULL,
			envato_purchase_code varchar(255) NOT NULL,
			plan_id mediumint(15) NOT NULL,
			coupon_id mediumint(15) NOT NULL,
			PRIMARY KEY  (license_id)
		) $charset_collate;";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		foreach($sql as $ql){
			dbDelta($ql);
		}
	}
}
