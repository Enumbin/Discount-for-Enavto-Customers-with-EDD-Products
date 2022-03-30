<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       smartdatasoft.com
 * @since      1.0.0
 *
 * @package    Edd_Discount_For_Envato_Customers
 * @subpackage Edd_Discount_For_Envato_Customers/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Edd_Discount_For_Envato_Customers
 * @subpackage Edd_Discount_For_Envato_Customers/admin
 * @author     SmartDataSoft <support@smartdatasoft.com>
 */
class Edd_Discount_For_Envato_Customers_Admin extends Edd_Discount_For_Envato_Customers_Provider{

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Edd_Discount_For_Envato_Customers_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Edd_Discount_For_Envato_Customers_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/edd-discount-for-envato-customers-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Edd_Discount_For_Envato_Customers_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Edd_Discount_For_Envato_Customers_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/edd-discount-for-envato-customers-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function edddfe_registered_settings_tabs($registerd_settings){
		return $registerd_settings;
	}

	public function edddfe_settings( $settings ) {
		$main_settings = array(
			array(
				'id'   => 'edddfe_header',
				'name' => '<strong>' . __( 'Discount for Enavto Customers with EDD Products', 'edd-discount-for-envato-customers' ) . '</strong>',
				'desc' => '',
				'type' => 'header',
				'size' => 'medium'
			),
			array(
				'id' => 'edddfe_personal_token',
				'name' => __( 'Personal Token', 'edd-discount-for-envato-customers' ),
				'type' => 'text',
				'size' => 'regular',
			),
			array(
				'id' => 'edddfe_discount_percentage',
				'name' => __( 'Discount Percentage', 'edd-discount-for-envato-customers' ),
				'type' => 'number',
				'desc' => 'Enter the discount percentage. 10 = 10%',
				'size' => 'regular',
				'std'  => '50',
			)
		);
		return array_merge( $settings, $main_settings );
	}

	public function edddfe_add_meta_boxes(){
		add_meta_box( 'edddfe_envato_product_ids', 'Plan for Envato Customers', [$this, 'edddfe_envato_product_ids_func'], 'download' );
	}

	public function edddfe_envato_product_ids_func(){
		global $post;
		$existing = $this->has_discount_on_product($post->ID, 'edd');
		$name = '';
		$ids = '';
		if(isset($existing['plan_names'])){
			$name = $existing['plan_names'][0];
		}
		if(isset($existing['envato_product_ids'])){
			$ids = implode(',',$existing['envato_product_ids']);
		}
		?>
		<div class="edddfe-rendered-form">
			<div class="edddfe-formbuilder-section form-group field-edddfe_plan">
				<label for="edddfe_plan" class="edddfe-formbuilder-text-label"><?php echo  __( 'Plan Name', 'edd-discount-for-envato-customers' ); ?>
					<br>
				</label>
				<input type="text" placeholder="<?php echo  esc_attr__( 'Special Discount', 'edd-discount-for-envato-customers' ); ?>" class="form-control" name="edddfe_plan" access="false" id="edddfe_plan" value="<?php echo esc_attr($name); ?>">
			</div>
			<div class="edddfe-formbuilder-section form-group field-edddfe_envato_product_ids">
				<label for="edddfe_envato_product_ids" class="edddfe-formbuilder-text-label"><?php echo  __( 'Envato Product Ids', 'edd-discount-for-envato-customers' ); ?>
					<br><span class="tooltip-element" tooltip="<?php echo  esc_attr__( 'Enter Comma Separated Envato product Id. Ex. (123456,654321)', 'edd-discount-for-envato-customers' ); ?>"></span></label>
				<input type="text" class="edddfe-envato-product-id form-control" name="edddfe_envato_product_ids" access="false" id="edddfe_envato_product_ids" title="<?php echo esc_attr__("Enter Comma Separated Envato product Id. Ex. (123456,654321)",'edd-discount-for-envato-customers'); ?>" value="<?php echo esc_attr($ids); ?>">
			</div>
		</div>
		<?php 
	}

	public function edddfe_save_metas(){
		if(isset($_POST['edddfe_plan']) && isset($_POST['edddfe_envato_product_ids'])){
			global $post;
			$plan_name = $this->edddfe_sanitization($_POST['edddfe_plan']);
			$envato_ids = $this->edddfe_sanitization($_POST['edddfe_envato_product_ids']);
			$check_existence = $this->has_discount_on_product($post->ID, 'edd');
			
			if(!$check_existence){
				$plans = $this->edddfe_add_plan($post->ID, $envato_ids, $plan_name);
			}else{
				if(isset($check_existence['envato_product_ids'])){
					$envato_ids = explode(',',$envato_ids);
					$diff_ids = array();
					if(count($envato_ids) > count($check_existence['envato_product_ids'])){
						$diff_ids = array_diff($envato_ids,$check_existence['envato_product_ids']);
					}elseif(count($envato_ids) < count($check_existence['envato_product_ids'])){
						$diff_ids = array_diff($check_existence['envato_product_ids'],$envato_ids);
					}
					$add = !empty(array_intersect($diff_ids, $envato_ids));
					if($add){
						$plans = $this->edddfe_add_plan($post->ID, $diff_ids, $plan_name);
					}else{
						$plans = $this->edddfe_delete_plan($post->ID, $diff_ids);
					}
				}
			}
		}
	}

	private function edddfe_add_plan($edd_id, $envato_ids = array(), $plan_name){
		global $wpdb;
		if(!is_array($envato_ids)){
			$envato_ids = explode(',', $envato_ids);
		}
		$envato_ids = array_unique($envato_ids);
		$plans = array();
		foreach($envato_ids as $envato_id){
			$wpdb->insert( 
				$wpdb->prefix . 'edddfe_plans', 
				array( 
					'name' => $plan_name, 
					'edd_product_id' => (int) $edd_id,
					'envato_product_id' => (int) $envato_id
				), 
				array( 
					'%s', 
					'%d',
					'%d'
				) 
			);
			$plans[] = $wpdb->insert_id;
		}
		return $plans;
	}
	private function edddfe_delete_plan($edd_id, $env_ids){
		global $wpdb;
		if(!is_array($env_ids)){
			$env_ids = explode(',', $env_ids);
		}
		$env_ids = array_unique($env_ids);
		foreach($env_ids as $env_id){
			$wpdb->delete( 
				$wpdb->prefix . 'edddfe_plans', 
				array( 
					'edd_product_id' => (int) $edd_id,
					'envato_product_id' => (int) $env_id
				), 
				array( 
					'%d',
					'%d'
				) 
			);
		}
	}
}
