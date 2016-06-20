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
class Wp_Attachment_Filter_AdminCache {

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
	 * create_cached_filters
	 * generate all filters in cache, json format, in \wp-attachment-filter\public\cache
	 * @return bool|string
	 */
	public function create_cached_filters(){

		$wpaf_public = new Wp_Attachment_Filter_Public('wp-attachment-filter','v1.0');
		$wpaf_filter = new Wp_Attachment_Filter_Filter('wp-attachment-filter','v1.0');

		$get_media_tax = get_option('wpaf-media-tax');
		if(!empty($get_media_tax)){
			
			$args = array(
				'taxonomy'               => $get_media_tax,
				'hide_empty'             => true,
				'fields'                 => 'id=>slug',
			);
			$taxonomies = get_terms($args);
			
			//loop through all terms
			//create a stupid false query to get 0 post and force the file creation
			$the_false_query = new WP_Query( array( 'author' => 12786764683 ) );
			foreach($taxonomies as $taxonomy){
				$wp_query = $wpaf_public->eml_default_query($taxonomy,false);
				$wpaf_filter->get_extra_filter($wp_query,$taxonomy);
			}
			//run a general query
			$wpaf_filter->get_extra_filter($the_false_query,'1');

			return true;

		} else {
			return false;
		}


	}

	public function preload_cache(){

		/**
		 * delete all available files
		 */
		$files = glob(get_wp_attachment_filter_plugin_dir().'public/cache/*.json');
		foreach($files as $file){
			unlink($file);
		}
		/**
		 * create all files
		 */
		$this->create_cached_filters();

		wp_die(); // this is required to terminate immediately and return a proper response

	}

}
