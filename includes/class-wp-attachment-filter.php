<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.lyra-network.com
 * @since      1.0.0
 *
 * @package    Wp_Attachment_Filter
 * @subpackage Wp_Attachment_Filter/includes
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
 * @package    Wp_Attachment_Filter
 * @subpackage Wp_Attachment_Filter/includes
 * @author     LYRA NETWORK <david.fieffe@lyra-network.com>
 */
class Wp_Attachment_Filter {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Wp_Attachment_Filter_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

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

		$this->plugin_name = 'wp-attachment-filter';
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Wp_Attachment_Filter_Loader. Orchestrates the hooks of the plugin.
	 * - Wp_Attachment_Filter_i18n. Defines internationalization functionality.
	 * - Wp_Attachment_Filter_Admin. Defines all hooks for the admin area.
	 * - Wp_Attachment_Filter_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-attachment-filter-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-attachment-filter-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wp-attachment-filter-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wp-attachment-filter-utilities.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wp-attachment-filter-public.php';


		$this->loader = new Wp_Attachment_Filter_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Wp_Attachment_Filter_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Wp_Attachment_Filter_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		global $user_ID;
		$plugin_admin = new Wp_Attachment_Filter_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		//if ( current_user_can('edit_posts') && current_user_can('edit_pages') && !get_user_option('rich_editing') == 'true'){
			//Add a callback to regiser our tinymce plugin
			$this->loader->add_filter("mce_external_plugins",$plugin_admin, "mediabycategory_register_tinymce_plugin");
			// Add a callback to add our button to the TinyMCE toolbar
			$this->loader->add_filter('mce_buttons',$plugin_admin, 'mediabycategory_add_tinymce_button');
		//}
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'wp_admin_menu' );


	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Wp_Attachment_Filter_Public( $this->get_plugin_name(), $this->get_version() );
		$plugin_utilities = new WpAttachmentFilterUtilities( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		//filter
		//$this->loader->add_action( 'init' ,$plugin_public, 'add_tags_to_attachments' );
		//$this->loader->add_action( 'init',$plugin_public, 'attachments_tags', 0 );
		$this->loader->add_filter('upload_mimes',$plugin_public,'add_custom_mime_types');
		//ajax
		$this->loader->add_action( 'wp_ajax_refresh_eml_filters',$plugin_public, 'iOEheoau_ajax_refresh_eml_filters' );
		$this->loader->add_action( 'wp_ajax_nopriv_refresh_eml_filters',$plugin_public, 'iOEheoau_ajax_refresh_eml_filters' );
		//ajx filter
		$this->loader->add_action( 'wp_ajax_filter_eml_media_query',$plugin_public, 'iOEheoau_ajax_filter_eml_media_query' );
		$this->loader->add_action( 'wp_ajax_nopriv_filter_eml_media_query',$plugin_public, 'iOEheoau_ajax_filter_eml_media_query' );
		//shortcode
		$this->loader->add_shortcode( 'mediabycategory',$plugin_public, 'mediabycategory_shortcode' );
		//ajx admin
		$this->loader->add_action( 'wp_ajax_retrieve_media_tax',$plugin_public, 'retrieve_media_tax' );
		$this->loader->add_action( 'wp_ajax_nopriv_retrieve_media_tax',$plugin_public, 'retrieve_media_tax' );
		//enqueue mpf
		$wp_mpf_src = get_option('wp-attachment-filter-mpf');
		if($wp_mpf_src == 'on') {
			$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_mpf_styles' );
			$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_mpf_scripts' );
		}
		add_image_size( 'eml-preview', 150, 80,false );

		$this->loader->add_action( 'wp_ajax_get_pdf',$plugin_utilities, 'get_pdf_uri' );


	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
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
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Wp_Attachment_Filter_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
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
