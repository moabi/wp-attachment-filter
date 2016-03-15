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

	/*
 * Add custom shortcode to retrieve medias by category
 * */

	/*
     * shortcode for custom buttons
     */
// init process for registering our button
	public function mediabycategory_shortcode_button_init() {

		//Add a callback to regiser our tinymce plugin
		add_filter("mce_external_plugins", "mediabycategory_register_tinymce_plugin");

		// Add a callback to add our button to the TinyMCE toolbar
		add_filter('mce_buttons', 'mediabycategory_add_tinymce_button');
	}



	/**
	 * mediabycategory_register_tinymce_plugin
	 * This callback registers our plug-in - add a button in tinymce
	 *
	 * @param $plugin_array
	 * @return mixed
	 */
	public function  mediabycategory_register_tinymce_plugin($plugin_array) {
		$plugin_array['mediabycategory_button'] = get_stylesheet_directory_uri() .'/js/admin/mediabycategory.js';
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

}
