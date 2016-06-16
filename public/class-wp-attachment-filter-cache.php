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

	public $timeout = 10000;


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
	 * @param $file
	 * @return array|mixed|null|object
	 */
	public function get($type) {
		$file = get_wp_attachment_filter_plugin_dir() .'public/cache/'. $type.'.json';

		$file_age = filemtime($file) + $this->timeout;

		$file_timed_out = intval($file_age - time());
		//check if file exist and is still valid
		if (file_exists($file) && $file_timed_out > 0) {
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
