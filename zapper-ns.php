<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin. 
 * 
 * If you have any questions about how to use this plugin, 
 * please reach out to the developer using the contact information below. 
 *
 * @link              https://www.WordPressCenter.net
 * @since             1.0.0
 * @package           zapper_List_Table_ns
 *
 * @wordpress-plugin
 * Plugin Name:       Zapper List Table NS
 * Plugin URI:        https://www.WordPressCenter.net/
 * Description:       A simple plugin to demonstarate the use of WP List Tables in the Admin area of WordPress
 * Version:           1.0.0
 * Author:            Jeff Sabarese
 * Author URI:        https://www.WordPressCenter.net/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       zapper-list-table-ns
 * Domain Path:       /languages
 */

/**
 * The namespce implementation is inspired by a tutorial written by Tom McFarlin avaiable at
 * https://code.tutsplus.com/series/using-namespaces-and-autoloading-in-wordpress-plugins
 */

namespace Zapper_NS;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define Constants
 */

define( __NAMESPACE__ . '\NS', __NAMESPACE__ . '\\' );

define( NS . 'PLUGIN_NAME', 'zapper-list-table-ns' );

define( NS . 'PLUGIN_VERSION', '1.0.0' );

define( NS . 'PLUGIN_NAME_DIR', plugin_dir_path( __FILE__ ) );

define( NS . 'PLUGIN_NAME_URL', plugin_dir_url( __FILE__ ) );

define( NS . 'PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

define( NS . 'PLUGIN_TEXT_DOMAIN', 'zapper-list-table-ns' );


/**
 * Autoload Classes
 */

require_once( PLUGIN_NAME_DIR . 'includes/libraries/autoloader.php' );

/**
 * Register Activation and Deactivation Hooks
 * This action is documented in Includes/Core/class-activator.php
 */

register_activation_hook( __FILE__, array( NS . 'Includes\Core\Activator', 'activate' ) );

/**
 * The code that runs during plugin deactivation.
 * This action is documented Includes/Core/class-deactivator.php
 */

register_deactivation_hook( __FILE__, array( NS . 'Includes\Core\Deactivator', 'deactivate' ) );


/**
 * Plugin Singleton Container
 *
 * Maintains a single copy of the plugin app object
 * Understanding the Singleton Design Pattern:
 * https://phptherightway.com/pages/Design-Patterns.html#singleton
 *
 * @since    1.0.0
 */
class Zapper_NS {

	static $init;
	/**
	 * Loads the plugin
	 *
	 * @access    public
	 */
	public static function init() {

		if ( null == self::$init ) {
			self::$init = new Includes\Core\Init();
			self::$init->run();
		}

		return self::$init;
	}

}

/*
 * Begins execution of the plugin
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * Also returns copy of the app object so 3rd party developers
 * can interact with the plugin's hooks contained within.
 *
 */
function zapper_list_table_ns_init() {
		return Zapper_NS::init();
}

$min_php = '5.6.0';

// Check the minimum required PHP version and run the plugin.
if ( version_compare( PHP_VERSION, $min_php, '>=' ) ) {
		zapper_list_table_ns_init();
}
