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


class Wp_Attachment_Filter_Public {

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

	public $NumberOfPosts = 30;

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
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		global $post;
		$post_content = (isset($post->post_content)) ? $post->post_content : null;
		if( has_shortcode( $post_content, 'mediabycategory') ) {
			wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wp-attachment-filter-public.css', array(), $this->version, 'all');
		}
		$wp_payzen_css = get_option('wp-attachment-filter-mpf');
		if($wp_payzen_css == 'on') {
			wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/dimsemenov.css', array(), $this->version, 'all');
		}

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		global $post;
		$post_content = (isset($post->post_content)) ? $post->post_content : null;
		if( has_shortcode($post_content, 'mediabycategory') ) {
			wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/wp-attachment-filter-public.js', array('jquery'), $this->version, true);
		}

	}

	/**
	 * enqueue_mpf_scripts
	 * will add the mpf library
	 */
	public  function enqueue_mpf_scripts(){
			wp_enqueue_script($this->plugin_name.'mpfjs', plugin_dir_url(__FILE__) . 'js/dimsemenov.js', array('jquery',$this->plugin_name), $this->version, true);
	}
	public function enqueue_mpf_styles() {
			wp_enqueue_style($this->plugin_name.'mpfcss', plugin_dir_url(__FILE__) . 'css/dimsemenov.css', array(), $this->version, 'all');
	}

	/**
	 * add_tags_to_attachments
	 * apply tags to attachments
	 */
	public function add_tags_to_attachments() {
		register_taxonomy_for_object_type( 'attachments_medias_tags', 'attachment' );
	}

	/**
	 * Register attachments_tags
	 */
	public function attachments_tags() {

		$labels = array(
			'name'                       => _x( 'attachments_medias_tags', 'Taxonomy General Name', 'medias-manager' ),
			'singular_name'              => _x( 'attachments_medias_tag', 'Taxonomy Singular Name', 'medias-manager' ),
			'menu_name'                  => __( 'Media tags', 'medias-manager' ),
			'all_items'                  => __( 'All Items', 'medias-manager' ),
			'parent_item'                => __( 'Parent Item', 'medias-manager' ),
			'parent_item_colon'          => __( 'Parent Item:', 'medias-manager' ),
			'new_item_name'              => __( 'New Item Name', 'medias-manager' ),
			'add_new_item'               => __( 'Add New Item', 'medias-manager' ),
			'edit_item'                  => __( 'Edit Item', 'medias-manager' ),
			'update_item'                => __( 'Update Item', 'medias-manager' ),
			'view_item'                  => __( 'View Item', 'medias-manager' ),
			'separate_items_with_commas' => __( 'Separate items with commas', 'medias-manager' ),
			'add_or_remove_items'        => __( 'Add or remove items', 'medias-manager' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'medias-manager' ),
			'popular_items'              => __( 'Popular Items', 'medias-manager' ),
			'search_items'               => __( 'Search Items', 'medias-manager' ),
			'not_found'                  => __( 'Not Found', 'medias-manager' ),
			'no_terms'                   => __( 'No items', 'medias-manager' ),
			'items_list'                 => __( 'Items list', 'medias-manager' ),
			'items_list_navigation'      => __( 'Items list navigation', 'medias-manager' ),
		);
		$args = array(
			'labels'                     => $labels,
			'hierarchical'               => false,
			'public'                     => true,
			'show_ui'                    => true,
			'show_admin_column'          => true,
			'show_in_nav_menus'          => true,
			'show_tagcloud'              => true,
		);
		register_taxonomy( 'attachments_medias_tags', array( 'attachments' ), $args );

	}


	/**
	 * add_custom_mime_types
	 * MIMES
	 * allow more mime types to be uploaded
	 */
	public function add_custom_mime_types($mimes){
		return array_merge($mimes,array (
			'ac3' => 'audio/ac3',
			'mpa' => 'audio/MPA',
			'flv' => 'video/x-flv',
			'svg' => 'image/svg+xml',
			'psd' => 'image/vnd.adobe.photoshop',
			'ai' => 'image/vnd.adobe.illustrator',

		));
	}


	/**
	 * getSize
	 * return file size in readable format
	 *
	 * @param $file
	 * @return false|string
	 */
	public function getSize($file){
		if(file_exists($file)){
			$bytes = filesize($file);
			$size = size_format($bytes);
		} else {
			$size = 0;
		}

		return $size;
	}

	/**
	 * eml_default_query
	 * the default wordpress query
	 *
	 * @param $default_term
	 * @return WP_Query
	 */
	public function eml_default_query($default_term,$is_filter = false){
		wp_reset_postdata();
		wp_reset_query();
		$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
		$number_of_post = ($is_filter == true) ? '-1' : $this->NumberOfPosts;
		//get default query
		if(isset($default_term) && !empty($default_term)){
			$filter_item = array(
				array(
					'taxonomy' => 'media_category', // your taxonomy
					'field' => 'slug',
					'terms' => $default_term, // term id (id of the media category)
					'include_children' => false,
				)
			);

			$query_args = array(
				'post_type' => 'attachment',
				'post_status' => 'inherit',//for attachment post type
				'posts_per_page' => $number_of_post,
				'paged' => $paged,
				'tax_query' => $filter_item,

			);
		} else {

			$query_args = array(
				'post_type' => 'attachment',
				'post_status' => 'inherit',//for attachment post type
				'posts_per_page' => $number_of_post,
				'paged' => $paged,

			);
		}

		//general query
		$eml_query = new WP_Query( $query_args );

		return $eml_query;
	}


	/**
	 * refresh_eml_filters
	 * When media Taxonomy is refreshed
	 * acf & mime type are updated to fit the query
	 *
	 * @return WP_Query
	 */
	public function  iOEheoau_ajax_refresh_eml_filters(){
		$filter_waf = new Wp_Attachment_Filter_Filter('wp-attachment-filter','v1.0');
		$post_tax = (isset($_POST['value'])) ? $_POST['value'] : '';

		$wp_query = $this->eml_default_query($post_tax);

		/*
		$mime = $filter_waf->get_mime_type_by_tax($wp_query);

		$acf = array();
		$acf_wpaf_items_option = get_option('wpaf-acf-items');
		foreach($acf_wpaf_items_option as $acf_wpaf_item){
			$acf_data = $filter_waf->get_acf_media_by_tax($acf_wpaf_item);
			array_push($acf,$acf_data);
		}

		$data = array(
			'mime' => $mime,
			'acf'   => $acf,
		);
		*/
		$data = $filter_waf->get_extra_filter($wp_query);
		
		$json_data = json_encode($data);

		print $json_data;
		die();
	}
	


	/**
	 * iOEheoau_ajax_filter_eml_media_query
	 * Ajax request for filtering
	 *
	 * @param $default_term string
	 * @return string
	 * FILTER BY media category
	 * slug
	 *
	 * FILTER BY CUSTOM FIELD
	 * https://codex.wordpress.org/Class_Reference/WP_Query#Custom_Field_Parameters
	 *
	 * FILTER BY MIME TYPE
	 * https://codex.wordpress.org/Class_Reference/WP_Query#Mime_Type_Parameters
	 * mime_type : 'post_mime_type' => 'image/gif'
	 *
	 * FILTER BY Search Parameter
	 * $query = new WP_Query( array( 's' => 'keyword' ) );
	 *
	 * FILTER BY  Tag
	 * array( 'tag' => 'bread,baking' )
	 */
	public function  iOEheoau_ajax_filter_eml_media_query(){


		$post_values = (isset($_POST['values'])) ? $_POST['values'] : '';
		$post_offset = (isset($_POST['offset'])) ? $_POST['offset'] : 0;
		$custom_fields_array = array();
		$acf_wpaf_items_option = get_option('wpaf-acf-items');
		foreach($acf_wpaf_items_option as $acf_wpaf_item){
			$html_name = 'eml-'.$acf_wpaf_item;
			array_push($custom_fields_array,$html_name);
		}
		//var_dump($acf);
		//filter values
		$acf_sel = array();
		if(!empty($post_values) && is_array($post_values)){
			$eml_mime = array();
			foreach($post_values as $post_value){
				switch($post_value['name']){
					case 'eml-order':
						$eml_order = $post_value['value'];
						break;
					case 'eml-orderby':
						$eml_orderby = $post_value['value'];
						break;
					case 'eml-mime':
						array_push( $eml_mime, $post_value['value'] ) ;
						break;
					case 'eml-s':
						$eml_s = $post_value['value'];
						break;
					case 'cs-link':
						$eml_media_tax = $post_value['value'];
						break;
					case (in_array($post_value['name'],$custom_fields_array)):
						array_push($acf_sel,$post_value['value']);
						break;
				}
			}
		}
		$post_tax = (isset($eml_media_tax)) ? $eml_media_tax : $_POST['tax'];
		$order = (isset($eml_order)) ? $eml_order : "DESC";
		$orderby = (isset($eml_orderby)) ? $eml_orderby : "date";
		$mime = (isset($eml_mime)) ? $eml_mime : get_post_mime_type();
		$s = (isset($eml_s)) ? $eml_s : '';

		$use = (!empty($acf_sel)) ? $acf_sel : '';
		//var_dump($acf_sel);
		//taxonomy filtering
		if(!empty($post_tax)){
			$default_term_filtering = array(
				array(
					'taxonomy' => 'media_category', // your taxonomy
					'field' => 'slug',
					'terms' => $post_tax, // term id (id of the media category)
					'include_children' => false,
				),
			);
		} else {
			$default_term_filtering = array();
		}

		// wp query args
		$args = array(
			'post_type' => 'attachment',
			'post_status' => 'inherit',//for attachment post type
			'orderby' => $orderby,
			'order' => $order,
			'tax_query' => $default_term_filtering,
			'post_mime_type' => $mime,
			's' => $s,
			'meta_value' => $use,
			'posts_per_page' => $this->NumberOfPosts,
			'offset' => $post_offset,
			'cache_results'  => false

		);

		//general query args for get_default_query
		$query_args = array(
			'args' => $args,
			'is_search' => true,
			'filter' => false,
			'output' => true,
			'shortocode_filter' => false,
			'shortcode_terms' => false
		);
		echo $this->get_default_query($query_args);
		die();

	}

	/**
	 * get_default_query
	 *
	 * @param $is_search
	 * @param $args
	 * @param bool $filter
	 * @param bool $output
	 * @return bool|string
	 */
	public function get_default_query($query_args){
		$filter_waf = new Wp_Attachment_Filter_Filter('wp-attachment-filter','v1.0');

		wp_reset_postdata();
		wp_reset_query();
		$query_images = new WP_Query( $query_args['args'] );
		global $found_posts;
		$total_found_posts = filter_var( absint( $query_images->found_posts ), FILTER_SANITIZE_NUMBER_INT );
		$paged_num = intval($total_found_posts / $this->NumberOfPosts);
//		echo '<pre>';
//		var_dump( $query_args['args']);
//		echo '</pre>';
		$output = '';
		if ( $query_images->have_posts() ) {
			if($query_args['is_search'] == true){
				$count = $query_images->post_count;
				$output .= '<div class="row"><div class="col-md-12"><h2 class="eml-results"> '.__('Results','wp-attachment-filter').' ('.$total_found_posts.'):';
				if($total_found_posts > $this->NumberOfPosts){
					$offsetN = $query_args['args']['offset'];
					$output .= '<span class="nav-ajx custom-pagination">';
					for ($x = 0; $x <= $paged_num; $x++) {
						$offsetCount = $this->NumberOfPosts * $x;
						$classOffset = ($offsetN == $offsetCount) ? 'current': '';
						$page = $x +1;
						$output .= '<a class="'.$classOffset.'" href="javascript:void(0)" onclick="wpafOffsetQuery('.$offsetCount .')">'.$page .'</a>';
					}
					$output .= '</span>';
				}
				$output .= '<span onclick="closeSearchResults(this)" class="wpaf-close">X</span>';
				$output .= '</h2>';
				//$output .= $query_args['args']['tax_query'][0]['terms'];
				$output .= '<div class="em-filters-active"></div>';
				$output .= '</div></div>';
			}

			//DISPLAY FILTER BLOCK
			if($query_args['shortocode_filter'] == true && $query_args['filter'] == true){
				$uniqid = uniqid('eml-case-');
				//var_dump($query_args);
				$output .=  $filter_waf->eml_media_filters($query_args['shortcode_terms'],$uniqid);
				$output .= '<div id="res-'.$uniqid.'" class=" filtering-results-eml"></div>';
			}
			$output .= '<div class="attachment_display row">';

			while ( $query_images->have_posts() ) {
				$query_images->the_post();
				$attachmentID = get_the_ID();
				$type = get_post_mime_type($attachmentID);
				$attach = wp_get_attachment_metadata($attachmentID);
				$attachment_url = wp_get_attachment_url( $attachmentID );
				$filetype = wp_check_filetype($attachment_url);
				$attachment_page = get_attachment_link($attachmentID);
				$comments_count = get_comments_number( $attachmentID );
				$filename_only = basename( get_attached_file( $attachmentID ) );
				$fullsize_path = get_attached_file( $attachmentID ); // Full path
				$fileimg = get_wp_attachment_filter_plugin_dir() . 'public/pdf/'.$filename_only.'.jpg';
				$file_url = get_wp_attachment_filter_plugin_uri() . 'public/pdf/'.$filename_only.'.jpg';
				$pdf_readonly = get_field('pdf_readonly');
				$utilities_waf = new WpAttachmentFilterUtilities('wp-attachment-filter', 'v1');
				
				$description = get_post()->post_content;
				switch ($type) {
					case 'image/jpeg':
					case 'image/png':
					case 'image/gif':
						$img = '<a title="'.get_the_title($attachmentID).'" class="mfp-img" href="'.wp_get_attachment_url( $attachmentID ).'">';
						$img_attr = 'data-mfp-src="'.wp_get_attachment_url( $attachmentID ).'"';
						$img .= wp_get_attachment_image( $attachmentID, 'eml-preview', false,$img_attr );
						//$img .= '<img alt="'.get_the_title($attachmentID).'" src="'.wp_get_attachment_url( $attachmentID ).'" />';
						$img .= '</a>';
						$img .= '<span class="attac-name">'.get_the_title($attachmentID).'</span>';
						break;
					case 'application/pdf':
						$img = '';
						/**
						 * If file does not exist, try to generate it with imagick
						 * if readonly, display a thumbnail
						 * else no img will be shown
						 */
						if(!file_exists($fileimg) && $pdf_readonly != true){

							if (class_exists('Imagick')) {
								try {

									$utilities_waf->extract($fullsize_path, $fileimg);
									if(file_exists($fileimg)){
										$img .= '<a href="'.$file_url.'" class="mfp-img" title="'.$filename_only.'">';
										$img .= '<img src="'.$file_url.'" alt="'.$filename_only.'"/>';
										$img .= '</a>';
									}
								}
								catch (Exception $e) {
									//$img = 'error'.$e->getMessage();
									//die('Error when creating a thumbnail: ' . $e->getMessage());
								}
							}
						} elseif ($pdf_readonly == true){

							$img .= '<img class="js-pdfreader" data-id="'.$attachmentID.'" src="'.get_wp_attachment_filter_plugin_uri().'/public/img/open-book.png" alt="'.$filename_only.'"/>';

						} else {
							$img .= '<a href="'.$file_url.'" class="mfp-img" title="'.$filename_only.'">';
							$img .= '<img src="'.$file_url.'" alt="'.$filename_only.'"/>';
							$img .= '</a>';

						}
						$img .= '<div id="pdfviewer-'.$attachmentID.'" class="pdf-viewer" style="display: none;">';
						$img .= '<div class="pdf-topbar"><div class="pdf-closer js-pdf-close">'.__('Close','wp-attachment-filter').'</div> <button id="next">'.__('Next','wp-attachment-filter').'</button><div class="pdf-pager">Page: <span id="page_num"></span> / <span id="page_count"></span></div><button id="prev">'.__('Previous','wp-attachment-filter').'</button> </div>';
						$img .= '<div id="pdf-'.$attachmentID.'" class="pdf-canvas-wrapper"><canvas id="pdf-canvas"></canvas></div>';
						$img .= '</div>';
						$img .= '<span class="attac-name">' . get_the_title($attachmentID) . '</span>';
						
						break;
					default:
						$img = '<span class="attac-name">'.get_the_title($attachmentID).'</span>';
				}

				$output .= '<div class="col-md-6 col-lg-3"><div class="att-wrapper"> ';

				if($comments_count > 0):
					//  $output .= '<a href="'.$attachment_page.'" class="fs1 comments-c" aria-hidden="true" data-icon="v"><span>'.$comments_count.'</span></a> <br />';
				endif;

				$output .=  $utilities_waf->get_icon_for_attachment($attachmentID);
				$output .= $img;

				$output .= '<div class="details">';
				$output .= 'Extension : '.$filetype['ext'].'<br />';
				if(!empty($attach)):
					$output .= 'Image size : '.$attach['width'].'px/'.$attach['height'].'px <br />';
				endif;
				$output .=  'File size : '.$this->getSize(get_attached_file( $attachmentID )).' <br />' ;
				if(get_field('copyright')):
					$output .=  'Copyright : '.get_field('copyright').' <br />' ;
				endif;
				if(get_field('utilisation')):
					$media_uses = get_field('utilisation');
					$uses_data = '';
					if(is_array($media_uses)){
						$i = 0;
						foreach ($media_uses as $media_use){
							$i++;
							$sep = (count($media_uses) == $i) ? '.':',';
							$uses_data .= $media_use.$sep.' ';
						}
					} else {
						$uses_data = $media_uses;
					}

					$output .=  __("Use","wp-attachment-filter").': '.$uses_data.' <br />' ;
				endif;
				if(!empty($description)){
					$output .=  '<a href="#eml-s'.$attachmentID.'" class="mpf-inline">'.__('Description','wp-attachment-filter').'</a> <br />' ;
					$output .=  '<div class="white-popup mfp-hide" id="eml-s'.$attachmentID.'">'.$description.'</div>' ;
				}

				$output .= "</div>";
				//$output .= '<a class="page-linker" href="'.$attachment_page.'">'. __('Full detailled page','assets').'</a>';
				if(current_user_can('edit_posts')){
					$output .= '<a class="edit-link" target="_blank" href="'.get_edit_post_link().'">Edit</a>';
				}

				if($pdf_readonly == true ){
					$output .=  '<a class="download-link js-pdfreader btn" href="javascript:void(0)"  data-id="'.$attachmentID.'">'.__('READ','assets').'</a>';

				} else {
					$output .=  '<a  target="_blank" class="download-link btn forcetodownload" download="" href="'.wp_get_attachment_url( $attachmentID ).'" >'.__('download','assets').'</a>';

				}

				$output .=  '</div></div>';
			}
			//pagination
			if($query_args['is_search'] != true) {
				$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
				$output .= $this->custom_pagination($query_images->max_num_pages, "", $paged);
			}


			$output .= '</div>';
		} else {
			// no attachments found
			$output = '<span class="eml-no-result">'.__("No attachment found", "wp-attachment-filter").'</span>';
		}
		wp_reset_postdata();


		if( $query_args['output'] == true){
			echo $output;
		}else {
			return $output;
		}

		return false;
	}
	/**
	 * get_eml_medias
	 *
	 * $args
	 * @param $default_term string - the media category slug
	 * @param filter boolean
	 * @return string
	 */
	public function get_eml_medias($args = array('filter' => false)){

		//var_dump($args);
		//taxonomy filtering - get a term from shortcode
		$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
		if(isset($args['default_term']) && $args['default_term'] != 1){
			$default_term_filtering = array(
				array(
					'taxonomy' => 'media_category', // your taxonomy
					'field' => 'slug',
					'terms' => $args['default_term'], // term id (id of the media category)
					'include_children' => false,
				)
			);
			//query args
			$query_images_args = array(
				'post_type' => 'attachment',
				'update_post_term_cache' => false, // don't retrieve post terms
				'post_status' => 'inherit',//for attachment post type
				'posts_per_page' => $this->NumberOfPosts,
				'paged' => $paged,
				'orderby' => 'date',
				'order' => 'DESC',
				'tax_query' => $default_term_filtering
			);


		} else {
			// no term from shortcode - build a general query without tax parameter
			$query_images_args = array(
				'post_type' => 'attachment',
				'update_post_term_cache' => false, // don't retrieve post terms
				'post_status' => 'inherit',//for attachment post type
				'posts_per_page' => $this->NumberOfPosts,
				'paged' => $paged,
				'orderby' => 'date',
				'order' => 'DESC'
			);
		}

		//build general query
		$query_args = array(
			'args' => $query_images_args,
			'is_search' => false,
			'filter' => true,
			'output' => false,
			'shortocode_filter' => $args['filter'],
			'shortcode_terms' => $args['default_term']
		);

		wp_reset_postdata();
		wp_reset_query();
		return $this->get_default_query($query_args);

	}


	public function custom_pagination($numpages = '', $pagerange = '', $paged='') {
		$output = '';
		if (empty($pagerange)) {
			$pagerange = 2;
		}

		/**
		 * This first part of our function is a fallback
		 * for custom pagination inside a regular loop that
		 * uses the global $paged and global $wp_query variables.
		 *
		 * It's good because we can now override default pagination
		 * in our theme, and use this function in default quries
		 * and custom queries.
		 */
		global $paged;
		if (empty($paged)) {
			$paged = 1;
		}
		if ($numpages == '') {
			global $wp_query;
			$numpages = $wp_query->max_num_pages;
			if(!$numpages) {
				$numpages = 1;
			}
		}

		/**
		 * We construct the pagination arguments to enter into our paginate_links
		 * function.
		 */
		$pagination_args = array(
			'base'            => get_pagenum_link(1) . '%_%',
			'format'          => 'page/%#%',
			'total'           => $numpages,
			'current'         => $paged,
			'show_all'        => False,
			'end_size'        => 1,
			'mid_size'        => $pagerange,
			'prev_next'       => True,
			'prev_text'       => __('&laquo;'),
			'next_text'       => __('&raquo;'),
			'type'            => 'plain',
			'add_args'        => false,
			'add_fragment'    => ''
		);

		$paginate_links = paginate_links($pagination_args);

		if ($paginate_links) {
			$output .= "<nav class='custom-pagination col-md-12'>";
			$output .= "<span class='page-numbers page-num'>Page " . $paged . " of " . $numpages . "</span> ";
			$output .= $paginate_links;
			$output .= "</nav>";
		}

		return $output;

	}

	/**
	 * mediabycategory_shortcode
	 * provide the shortcode
	 *
	 * @param $atts
	 * @return string
	 */
	public function  mediabycategory_shortcode( $atts ) {
		// Attributes
		extract(shortcode_atts(array(
			'tax' => 1,//media taxonomy
			'filter' => false // wether to display a filter box
		), $atts));

		if (!empty($tax)):
			$taxonomy = $tax;
		else:
			$taxonomy = 'no-term';
		endif;
		$filter = (isset($atts['filter'])) ? $atts['filter'] : false;
		$args = array(
			'default_term' => $taxonomy,
			'filter' => $filter
		);
		return $this->get_eml_medias($args);
	}


	/**
	 * retrieve_media_tax
	 * retrieve media taxonomy to create mediabycategory_shortcode
	 *
	 * @param bool $is_ajax
	 * @param bool $default_term
	 */
	public function retrieve_media_tax($is_ajax = true,$default_term = false){
		$get_media_tax = get_option('wpaf-media-tax');
		if(!empty($get_media_tax)){
			$output = '';

			$args = array(
				'show_option_all'    => __('All', 'wp-attachment-filter'),
				'show_option_none'   => '',
				'option_none_value'  => '-1',
				'orderby'            => 'ID',
				'order'              => 'ASC',
				'show_count'         => 1,
				'hide_empty'         => 1,
				'child_of'           => 0,
				'exclude'            => '',
				'echo'               => 0,
				'selected'           => 0,
				'hierarchical'       => 1,
				'name'               => 'cs-link',
				'id'                 => 'cs-link',
				'class'              => 'cs-link',
				'depth'              => 0,
				'tab_index'          => 0,
				'taxonomy'           => $get_media_tax,
				'hide_if_empty'      => true,
				'value_field'	     => 'slug',
			);
			$output .= wp_dropdown_categories( $args );

			if($is_ajax == true || (isset($_POST['is_ajax']) && $_POST['is_ajax'] == true)){
				echo $output;
				die();
			} else {
				return $output;
			}
		} else {
			return false;
		}

	}

}
