<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       smartdatasoft.com
 * @since      1.0.0
 *
 * @package    Edd_Discount_For_Envato_Customers
 * @subpackage Edd_Discount_For_Envato_Customers/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Edd_Discount_For_Envato_Customers
 * @subpackage Edd_Discount_For_Envato_Customers/includes
 * @author     SmartDataSoft <support@smartdatasoft.com>
 */
class Edd_Discount_For_Envato_Customers_Provider {

	protected function has_discount_on_product($id, $for, $existing = array()){
		if(!$id){
			return false;
		}
		global $wpdb;
		$pl_q_part = "";
		if(isset($existing) && !empty($existing)){
			if(is_array($existing)){
				$plans = implode(",",$existing);
			}else{
				$plans = $existing;
			}
			$pl_q_part = " AND pl.plan_id NOT IN ($plans)";
		}
		$for = $for .= '_product_id';
		$query = "SELECT * FROM `{$wpdb->prefix}edddfe_plans` AS pl WHERE $for = " . $id  . $pl_q_part;
		$results = $wpdb->get_results($query ,ARRAY_A);
		if(isset($results) && !empty($results)){
			$return = array();
			foreach($results as $result){
				$return['edd_product_ids'][] = $result['edd_product_id'];
				$return['envato_product_ids'][] = $result['envato_product_id'];
				$return['plans'][] = $result['plan_id'];
				$return['plan_names'][] = $result['name'];
			}
			return $return;
		}
		return false;
	}

	protected function has_plan_for_purchase_code($code){
		global $wpdb;
		$results = $wpdb->get_results( "SELECT plan_id,coupon_id  FROM `{$wpdb->prefix}edddfe_license_details` WHERE envato_purchase_code = '{$code}'", ARRAY_A);
		if(isset($results) && !empty($results)){
			$return = array();
			foreach($results as $result){
				$return['plans'][] = $result['plan_id'];
				$return['coupons'][] = $result['coupon_id'];
			}
			return $return;
		}else{
			return false;
		}
		return true;

	}

	protected function get_discount_codes($coupon_ids){
		$codes = array();
		if(is_array($coupon_ids)){
			foreach($coupon_ids as $coupon_id){
				$old_discount     = new EDD_Discount( (int) $coupon_id);
				$codes[] = $old_discount->code;
			}
		}else{
			$old_discount     = new EDD_Discount( (int) $coupon_ids);
			$codes[] = $old_discount->code;
		}
		
		return $codes;
	}

	protected function edddfe_get_email_data(){

		$args = array(
			"to" => "classydevs@gmail.com",
			"subject" => "New Discont Code Generated",
			"message" => "The Envato Buyer '%s' has created new Coupon Codes '%s' for The Plans '%s'"
		);
		return $args;
	}

	protected function edddfe_get_envato_token(){
		$token = edd_get_option( 'edddfe_personal_token', '' );
		return $token;
	}

	protected function edddfe_get_discount_percentage(){
		$percentage = edd_get_option( 'edddfe_discount_percentage', '50' );
		return $percentage;
	}

	public function edddfe_sanitization($value){
		if( is_array( $value ) ) {
			$sanitized = array_map( 'sanitize_text_field', $value );
		}
		else {
			$sanitized = sanitize_text_field( $value );
		}
		return $sanitized;
	}
}
