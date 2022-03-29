<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       smartdatasoft.com
 * @since      1.0.0
 *
 * @package    Edd_Discount_For_Envato_Customers
 * @subpackage Edd_Discount_For_Envato_Customers/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Edd_Discount_For_Envato_Customers
 * @subpackage Edd_Discount_For_Envato_Customers/public
 * @author     SmartDataSoft <support@smartdatasoft.com>
 */
class Edd_Discount_For_Envato_Customers_Public extends Edd_Discount_For_Envato_Customers_Provider{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/edd-discount-for-envato-customers-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/edd-discount-for-envato-customers-public.js', array( 'jquery' ), $this->version, false );
		wp_localize_script(
			$this->plugin_name,
			'edddfe_object',
			array(
				'ajax_url' => esc_url( admin_url( 'admin-ajax.php' ) ),
			)
		);
	}

	public function edddfe_purchase_code_field(){
		$output = '';
		ob_start();
		$this->get_form_html();
		$output = ob_get_clean();
		return $output;
	}

	protected function fetch($url, $set = null) {
      	$api_key = $this->edddfe_get_envato_token();
		if(!$api_key){
			$this->show_error(__('No Envato Personal Token Found!!!','edd-discount-for-envato-customers'));
		}

		$data =  $this->curl2($url,$api_key);
		$response = $data['response'];
		if($response['code'] == '200'){
			$body = '';
			if(isset($data['body'])){
				$body = json_decode($data['body'], true);
				return $body;
			}
		}elseif($response['code'] == '401'){
			$body = '';
			if(isset($data['body'])){
				$body = json_decode($data['body'], true);
				$this->show_error($response['message'] . ' : ' .$body['error']);
			}
		}else{
			return false;
		}
   }

   public function curl2( $url,$api_key ) {
		if ( empty( $url) ) {
			return false;
		}
		$response = wp_remote_get($url ,
			array(
				'headers' => array(
					'Authorization' => "Bearer " . $api_key,
					'User-Agent' => __("Enter a description of your app here for the API team",'edd-discount-for-envato-customers')
				)
			)
		);
		if(is_a($response,'WP_Error')){
			$this->show_error(__("Server Error : Please Try Again!!!",'edd-discount-for-envato-customers'));
		}
		return $response;
	}

	private function get_form_html(){
		?>
		<div class="row edddfe-generate-form">
			<div class="edddfe-generate-form-wrapper">
				<form id="edddfe_gen_coupon_form" class="edddfe_form" method="post">
					<fieldset>
						<p class="edddfe-purchase-key">
							<input name="edddfe_purchase_key" id="edddfe_purchase_key" class="edddfe-input" type="text" placeholder="<?php echo esc_attr__( 'Your Envato Purchase Key', 'edd-discount-for-envato-customers' ); ?>">
						</p>
						<p class="edddfe-generate-button">
							<button type="submit" id="edddfe_gen_coupon" class="edddfe_gen_coupon"><?php echo __( 'Generate Coupon', 'edd-discount-for-envato-customers' ); ?></button>
						</p>
					</fieldset>
					<div class="edddfe-response"></div>
				</form>
			</div>
		</div>
		<?php 
	}

	public function edddfe_coupon_generate_func(){
		$p_code = '';
		if(isset($_POST['purchase_code']) && isset($_POST['purchase_code'])){
			$p_code = $this->edddfe_sanitization($_POST['purchase_code']);
		}
		if(!isset($p_code) || $p_code == ''){
			$this->show_error(__("Empty Purchase Code!!!",'edd-discount-for-envato-customers'));
		}
		$url = 'https://api.envato.com/v3/market/author/sale?code=' . $p_code;
		$result = $this->fetch($url, 'verify-purchase');
		if($result){
			$info_license = array();
			$info_license['item_name'] = $result["item"]['name'];
			$info_license['item_id'] = $result["item"]['id'];
			$info_license['licence'] = $p_code;
			$info_license['supported_until'] = $result['supported_until'];
			$info_license['created_at'] = $result["item"]['published_at'];
			$plan_exists = $this->has_plan_for_purchase_code($p_code);
			if(!$plan_exists){
				$has_discount = $this->has_discount_on_product($result["item"]['id'], 'envato');
				if($has_discount){
					$new_coupon_id = $this->create_edd_discount($result['buyer'] ,$has_discount['edd_product_ids'], $plan_exists['plans']);
					$this->edddfe_add_data($result['buyer'],$new_coupon_id,$info_license,$has_discount['plans']);
					$codes = $this->get_discount_codes($new_coupon_id);
					$this->show_coupons($codes);
					$this->edddfe_send_email($result['buyer'],$codes ,$has_discount['plan_names']);
				}else{
					$this->show_error(__("No Discount Plan Available for ",'edd-discount-for-envato-customers') . $info_license['item_name']);
				}
			}else{
				$has_discount = $this->has_discount_on_product($result["item"]['id'], 'envato',$plan_exists['plans']);
				if($has_discount){
					$new_coupon_id = $this->edit_edd_discount($result['buyer'] ,$has_discount['edd_product_ids'], $plan_exists['coupons']);
					$this->edddfe_update_data($result['buyer'],$new_coupon_id,$info_license,$has_discount['plans']);
					$codes = $this->get_discount_codes($new_coupon_id);
					$this->show_coupons($codes);
				}else{
					$coups = array_unique($plan_exists['coupons']);
					$codes = $this->get_discount_codes($coups);
					$this->show_coupons($codes);
				}
			}
		}else{
			$this->show_error(__("Invalid Purchase Code!!!",'edd-discount-for-envato-customers'));
		}
	}

	private function edddfe_send_email($buyer,$codes ,$plans){

		$email_data = $this->edddfe_get_email_data();
		extract($email_data);
		$plans = implode(", ", $plans);
		$codes = implode(", ", $codes);
		$message = sprintf($message, $buyer, $codes, $plans);
		wp_mail($to, $subject, $message);

	}

	private function create_edd_discount($buyer, $discount_info, $plan_exists = array()){
		$percentage = $this->edddfe_get_discount_percentage();
		$disc_code = $buyer . $percentage;
		$prds = array(0);
		$args = array();
		$args['name'] = __("Special Discount for ",'edd-discount-for-envato-customers') . $buyer;
		$args['code'] = $disc_code;
		$args['type'] = 'percent';
		$args['amount'] = $percentage;
		$args['products'] = array_merge($prds, $discount_info);
		$args['product_condition'] = 'any';
		$args['start'] = '';
		$args['expiration'] = '';
		$args['min_price'] = '';
		$args['max'] = sizeof($discount_info);
		$args['status'] = 'active';
		$args['not_global'] = 1;
		$new_discount = edd_store_discount( $args );
		return $new_discount;
	}

	private function edit_edd_discount($buyer, $discount_info, $existing_coupons = array()){
		$percentage = $this->edddfe_get_discount_percentage();
		$old_discount     = new EDD_Discount( (int) $existing_coupons[0]);
		if(isset($old_discount)){	
			if($percentage == $old_discount->amount){
				$percentage = $old_discount->amount;
			}
			$prds = $old_discount->product_reqs;
			$disc_code = $buyer . $percentage;
			$args = array();
			$args['name'] = $old_discount->name;
			$args['code'] = $disc_code;
			$args['type'] = 'percent';
			$args['amount'] = $percentage;
			$args['products'] = array_merge($prds, $discount_info);
			$args['product_condition'] = 'any';
			$args['start'] = '';
			$args['uses'] = edd_get_discount_uses( $old_discount->ID );
			$args['expiration'] = '';
			$args['min_price'] = '';
			$args['max'] = sizeof($args['products']) - $old_discount->uses;
			$args['status'] = 'active';
			$args['not_global'] = 1;
			$new_discount = edd_store_discount( $args, $existing_coupons[0] );
			return $new_discount;
		}
	}

	private function edddfe_add_data($buyer, $coupon_id, $info,$plans){
		global $wpdb;
		$buyer = $wpdb->insert( 
					$wpdb->prefix . 'edddfe_buyer', 
					array( 
						'name' => $buyer, 
						'purchase_count' => 1
					), 
					array( 
						'%s', 
						'%d'
					) 
				);
		foreach($plans as $plan){
			$license_id = $wpdb->insert( 
				$wpdb->prefix . 'edddfe_license_details', 
				array( 
					'buyer_id' => $buyer, 
					'envato_product_id' => $info['item_id'],
					'envato_purchase_code' => $info['licence'],
					'plan_id' => $plan,
					'coupon_id' => $coupon_id
				), 
				array( 
					'%d',
					'%d',
					'%s', 
					'%d',
					'%d'
				) 
			);
		}
	}
	private function edddfe_update_data($buyer, $coupon_id, $info,$plans){

		global $wpdb;
		$sql = "SELECT buyer_id FROM {$wpdb->prefix}edddfe_buyer WHERE name = '$buyer'";
		$results = $wpdb->get_var( $sql);
		foreach($plans as $plan){
			$license_id = $wpdb->insert( 
				$wpdb->prefix . 'edddfe_license_details', 
				array( 
					'buyer_id' => $results, 
					'envato_product_id' => $info['item_id'],
					'envato_purchase_code' => $info['licence'],
					'plan_id' => $plan,
					'coupon_id' => $coupon_id
				), 
				array( 
					'%d',
					'%d',
					'%s', 
					'%d',
					'%d'
				) 
			);
		}
	}
	private function show_coupons($codes){
		$code="";
		foreach($codes as $c){
			$code .= "<br>" . $c;
		}
		$output = '<p class="successfully-login">
			Use The Code Below To Get Discount!!!
			<code>
			'.$code.'
			</code>
		</p>';
		echo wp_kses_post($output);
		die();
	}
	private function show_error($msg = ''){
		$output = '<p class="error-login">
			'.$msg.'
		</p>';
		echo wp_kses_post($output);
		die();
	}
}
