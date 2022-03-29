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

class Edd_Discount_For_Envato_Customers{


	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'EDD_DISCOUNT_FOR_ENVATO_CUSTOMERS_VERSION' ) ) {
			$this->version = EDD_DISCOUNT_FOR_ENVATO_CUSTOMERS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'edd-discount-for-envato-customers';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}
	private function load_dependencies() {

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-edd-discount-for-envato-customers-i18n.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-edd-discount-for-envato-customers-provider.php'; 

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-edd-discount-for-envato-customers-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-edd-discount-for-envato-customers-public.php';


	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Edd_Discount_For_Envato_Customers_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Edd_Discount_For_Envato_Customers_i18n();

		add_action( 'plugins_loaded', [$plugin_i18n ,'load_plugin_textdomain'] );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Edd_Discount_For_Envato_Customers_Admin( $this->get_plugin_name(), $this->get_version() );

		add_action( 'admin_enqueue_scripts', [$plugin_admin, 'enqueue_styles'] );

		add_filter( 'edd_registered_settings', array( $plugin_admin, 'edddfe_registered_settings_tabs' ), 15 );
		add_filter( 'edd_settings_extensions', array( $plugin_admin, 'edddfe_settings' ), 15 );

		add_filter( 'add_meta_boxes_download', array( $plugin_admin, 'edddfe_add_meta_boxes' ), 15 );
		add_action( 'save_post_download', array( $plugin_admin, 'edddfe_save_metas' ), 10, 3 );

		
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
		$plugin_public = new Edd_Discount_For_Envato_Customers_Public( $this->get_plugin_name(), $this->get_version() );

		add_action( 'wp_enqueue_scripts', [$plugin_public, 'enqueue_styles'] );
		add_action( 'wp_enqueue_scripts', [$plugin_public, 'enqueue_scripts'] );

		add_shortcode( 'edddfe_purchase_code_shortcode', array( $plugin_public, 'edddfe_purchase_code_field' ) );

		add_action( 'wp_ajax_edddfe_coupon_generate', array( $plugin_public, 'edddfe_coupon_generate_func' ) );
		add_action( 'wp_ajax_nopriv_edddfe_coupon_generate', array( $plugin_public, 'edddfe_coupon_generate_func' ) );

	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}
