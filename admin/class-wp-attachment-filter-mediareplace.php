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

/**
 * Class Wp_Attachment_Filter_Mediareplace
 * based on https://wordpress.org/plugins/enable-media-replace/
 *
 */

class Wp_Attachment_Filter_Mediareplace {

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
	 * Add menu pages in wp admin
	 */
	public function wp_admin_menu(){
		//for media replacement
		add_submenu_page(NULL, __("Replace media", "wp-attachment-filter"), '','upload_files', 'wp-attachment-filter/admin/partials/wp-attachment-filter-admin-mediareplace', $this->emr_options());

	}
	
	/**
	 * enable_media_replace
	 * Add some new fields to the attachment edit panel.
	 * @param $form_fields array form fields edit panel
	 * @param $post
	 *
	 * @return array form fields with enable-media-replace fields added
	 */
	public function enable_media_replace( $form_fields, $post ) {

		$url = admin_url( "upload.php?page=wp-attachment-filter/admin/partials/wp-attachment-filter-admin-mediareplace.php&action=media_replace&attachment_id=" . $post->ID);
		$action = "media_replace";
		$editurl = wp_nonce_url( $url, $action );

		if (FORCE_SSL_ADMIN) {
			$editurl = str_replace("http:", "https:", $editurl);
		}
		$link = "href=\"$editurl\"";
		$form_fields["enable-media-replace"] = array("label" => __("Replace media", "enable-media-replace"), "input" => "html", "html" => "<p><a class='button-secondary'$link>" . __("Upload a new file", "enable-media-replace") . "</a></p>", "helps" => __("To replace the current file, click the link and upload a replacement.", "enable-media-replace"));

		return $form_fields;
	}

	/**
	 * emr_options
	 * Load the replace media panel.
	 * Panel is show on the action 'media-replace' and a given attachement.
	 * Called by GET var ?page=enable-media-replace/enable-media-replace.php
	 */
	public function emr_options() {
		$plugin = get_wp_attachment_filter_plugin_dir();
		if ( isset( $_GET['action'] ) && $_GET['action'] == 'media_replace' ) {
			//check_admin_referer( 'media_replace' ); // die if invalid or missing nonce
			if ( array_key_exists("attachment_id", $_GET) && (int) $_GET["attachment_id"] > 0) {
				include($plugin."admin/partials/popup.php");
			}
		}

		if ( isset( $_GET['action'] ) && $_GET['action'] == 'media_replace_upload' ) {
			$plugin_url =  str_replace("enable-media-replace.php", "", __FILE__);
			//check_admin_referer( 'media_replace_upload' ); // die if invalid or missing nonce
			require_once($plugin."admin/partials/upload.php");
		}

	}


	/**
	 * add_media_action
	 * Function called by filter 'media_row_actions'
	 * Enables linking to EMR straight from the media library
	 *
	 * @param $actions
	 * @param $post
	 *
	 * @return array
	 */
	public function add_media_action( $actions, $post) {
		$url = admin_url( "upload.php?page=wp-attachment-filter/admin/partials/wp-attachment-filter-admin-mediareplace.php&action=media_replace&attachment_id=" . $post->ID);
		$action = "media_replace";
		$editurl = wp_nonce_url( $url, $action );

		if (FORCE_SSL_ADMIN) {
			$editurl = str_replace("http:", "https:", $editurl);
		}
		$link = "href=\"$editurl\"";

		$newaction['adddata'] = '<a ' . $link . ' title="' . __("Replace media", "enable-media-replace") . '" rel="permalink">' . __("Replace media", "enable-media-replace") . '</a>';
		return array_merge($actions,$newaction);
	}


}
