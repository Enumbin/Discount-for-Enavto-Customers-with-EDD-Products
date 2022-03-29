<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              smartdatasoft.com
 * @since             1.0.0
 * @package           Edd_Discount_For_Envato_Customers
 *
 * @wordpress-plugin
 * Plugin Name:       Discount for Enavto Customers with EDD Products
 * Plugin URI:        wordpress.org/plugins/edd-discount-for-envato-customers/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            SmartDataSoft
 * Author URI:        smartdatasoft.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       edd-discount-for-envato-customers
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'EDD_DISCOUNT_FOR_ENVATO_CUSTOMERS_VERSION', '1.0.0' );
define( 'EDD_DISCOUNT_FOR_ENVATO_CUSTOMERS_INCLUDES_DIR', plugin_dir_path( __FILE__ ) . '/includes');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-edd-discount-for-envato-customers-activator.php
 */
function activate_edd_discount_for_envato_customers() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-edd-discount-for-envato-customers-activator.php';
	Edd_Discount_For_Envato_Customers_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-edd-discount-for-envato-customers-deactivator.php
 */
function deactivate_edd_discount_for_envato_customers() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-edd-discount-for-envato-customers-deactivator.php';
	Edd_Discount_For_Envato_Customers_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_edd_discount_for_envato_customers' );
register_deactivation_hook( __FILE__, 'deactivate_edd_discount_for_envato_customers' );
/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-edd-discount-for-envato-customers.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_edd_discount_for_envato_customers() {
	$plugin = new Edd_Discount_For_Envato_Customers();
}
run_edd_discount_for_envato_customers();
