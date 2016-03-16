<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.lyra-network.com
 * @since      1.0.0
 *
 * @package    Wp_Attachment_Filter
 * @subpackage Wp_Attachment_Filter/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_Attachment_Filter
 * @subpackage Wp_Attachment_Filter/admin
 * @author     LYRA NETWORK <david.fieffe@lyra-network.com>
 */
class Wp_Attachment_Filter_Admin {

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


		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-attachment-filter-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {


		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-attachment-filter-admin.js', array( 'jquery' ), $this->version, false );

	}



	/**
	 * mediabycategory_register_tinymce_plugin
	 * This callback registers our plug-in - add a button in tinymce
	 *
	 * @param $plugin_array
	 * @return mixed
	 */
	public function  mediabycategory_register_tinymce_plugin($plugin_array) {
		$plugin_array['mediabycategory_button'] = get_wp_attachment_filter_plugin_uri() .'/admin/js/wp-attachment-filter-admin.js';
		return $plugin_array;
	}


	/**
	 * mediabycategory_add_tinymce_button
	 * This callback adds our button to the toolbar
	 * @param $buttons
	 * @return array
	 */
	public function  mediabycategory_add_tinymce_button($buttons) {
		//Add the button ID to the $button array
		$buttons[] = "mediabycategory_button";
		return $buttons;
	}

	/**
	 * Add menu pages in wp admin
	 */
	public function wp_admin_menu(){
		//add settings page
		add_submenu_page( 'plugins.php','Attachment filter', 'Attachment filter', 'publish_pages', 'wp-attachment-filter', array( $this, 'helper' ) );
		//register settings
		add_action( 'admin_init', array($this, 'register_plugins_settings') );
	}

	/**
	 * admin wiews
	 */
	public function helper(){
		$admin_view = plugin_dir_path( __FILE__ ) . 'partials/wp-attachment-filter-admin-display.php';
		include_once $admin_view;
	}

	/**
	 * Register settings
	 */
	public function register_plugins_settings() {
		//register our settings
		register_setting( 'wp-attachment-filter-settings-group', 'wpaf-media-tax' );
		register_setting( 'wp-attachment-filter-settings-group', 'wpaf-acf-items' );
		register_setting( 'wp-attachment-filter-settings-group', 'wp-attachment-filter-mpf' );
	}

}
