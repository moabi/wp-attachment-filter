<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.lyra-network.com
 * @since      1.0.0
 *
 * @package    Wp_Attachment_Filter
 * @subpackage Wp_Attachment_Filter/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_Attachment_Filter
 * @subpackage Wp_Attachment_Filter/public
 * @author     LYRA NETWORK <david.fieffe@lyra-network.com>
 */


class Wp_Attachment_Filter_Cache {

	public $timeout = 100000;


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
	 * Get cache file.
	 * $wp_payzen_cache == 'on' means we will rely on cacheds files, however old they are
	 * @param $file
	 * @return array|mixed|null|object
	 */
	public function get($type) {
		$wp_payzen_cache = get_option('wp-attachment-filter-manual-preload');

		//get file time
		$file = get_wp_attachment_filter_plugin_dir() .'public/cache/'. $type.'.json';
		$file_age = filemtime($file) + $this->timeout;
		$file_timed_out = intval($file_age - time());

		$is_time_ok = ($file_timed_out > 0) ? true : false;
		$rely_on_cache = ($wp_payzen_cache == 'on') ? true : $is_time_ok;

		//check if file exist and is still valid
		if (file_exists($file) && $rely_on_cache) {
			$content = json_decode(file_get_contents($file));
			return $content;

		} else {
			return false;
		}
	}
	/**
	 * Set cache file.
	 * create static file
	 * @param $file string name of the file
	 * @param $content string content of the file
	 */
	public function set($file, $content) {
		@file_put_contents(get_wp_attachment_filter_plugin_dir() .'public/cache/'. $file.'.json', json_encode($content));
	}

	/**
	 * clear
	 * delete all files
	 */
	public function clear() {
		$files = glob(get_wp_attachment_filter_plugin_dir() .'public/cache/*.json');
		foreach ($files as $file) {
			if (is_file($file)) {
				@unlink($file);
			}
		}
	}
}
