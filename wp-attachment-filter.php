<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.lyra-network.com
 * @since             1.0.0
 * @package           Wp_Attachment_Filter
 *
 * @wordpress-plugin
 * Plugin Name:       wp Attachment filter
 * Plugin URI:        https://payzen.eu
 * Description:       Display medias by taxonomy, filter them
 * Version:           1.2
 * Author:            LYRA NETWORK
 * Author URI:        https://www.lyra-network.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-attachment-filter
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

function get_wp_attachment_filter_plugin_dir(){
	$url_plugins =  plugin_dir_path( __FILE__ );
	return $url_plugins;
}

function get_wp_attachment_filter_plugin_uri(){
	$url_plugins = plugin_dir_url(__FILE__);
	return $url_plugins;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-attachment-filter-activator.php
 */
function activate_wp_attachment_filter() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-attachment-filter-activator.php';
	Wp_Attachment_Filter_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-attachment-filter-deactivator.php
 */
function deactivate_wp_attachment_filter() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-attachment-filter-deactivator.php';
	Wp_Attachment_Filter_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wp_attachment_filter' );
register_deactivation_hook( __FILE__, 'deactivate_wp_attachment_filter' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wp-attachment-filter.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wp_attachment_filter() {

	$plugin = new Wp_Attachment_Filter();
	$plugin->run();

}
run_wp_attachment_filter();
