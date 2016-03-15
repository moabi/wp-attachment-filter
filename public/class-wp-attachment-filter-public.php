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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-attachment-filter-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {


		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-attachment-filter-public.js', array( 'jquery' ), $this->version, false );

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
	 * get_icon_for_attachment
	 * add icon to files by mime type
	 * @param $post_id
	 * @return string
	 */
	public function get_icon_for_attachment($post_id) {

		$type = get_post_mime_type($post_id);

		switch ($type) {
			case 'image/jpeg':
			case 'image/png':
			case 'image/gif':
				return '<i class="fa fa-picture-o"></i>';
				break;
			case 'image/x-icon':
				return '<i class="fa fa-file-image-o"></i>';
				break;
			case 'video/mpeg':
			case 'video/mp4':
			case 'video/quicktime':
				return '<i class="fa fa-video-camera"></i>';
				break;
			case 'image/vnd.adobe.photoshop':
			case 'image/vnd.adobe.illustrator':
				return '<i class="fa fa-file-o"></i>';
				break;
			case 'application/pdf':
				return '<i class="fa fa-file-pdf-o"></i>';
				break;
			case 'application/powerpoint':
			case 'application/mspowerpoint':
			case 'application/x-mspowerpoint':
			case 'application/vnd.ms-powerpoint':
				return '<i class="fa fa-file-powerpoint-o"></i>';
				break;
			case 'application/msword':
				return '<i class="fa fa-file-word-o"></i>';
				break;
			case 'application/excel':
			case 'application/x-excel':
				return '<i class="fa fa-file-excel-o"></i>';
				break;
			case 'application/zip':
			case 'application/x-compressed':
				return '<i class="fa fa-file-archive-o"></i>';
				break;
			case 'text/csv':
			case 'text/plain':
			case 'text/xml':
				return '<i class="fa fa-file-text"></i>';
				break;
			default:
				return '<i class="fa fa-file"></i>';
		}
	}

	/**
	 * getSize
	 * return file size in readable format
	 *
	 * @param $file
	 * @return false|string
	 */
	public function getSize($file){
		$bytes = filesize($file);
		return size_format($bytes);
	}

	/**
	 * eml_default_query
	 * the default wordpress query
	 *
	 * @param $default_term
	 * @return WP_Query
	 */
	public function eml_default_query($default_term){
		wp_reset_postdata();
		wp_reset_query();

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
				'post_status' => 'inherit',
				'posts_per_page' => -1,
				'tax_query' => $filter_item
			);
		} else {

			$query_args = array(
				'post_type' => 'attachment',
				'post_status' => 'inherit',
				'posts_per_page' => -1,
			);
		}



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
		$post_tax = (isset($_POST['value'])) ? $_POST['value'] : '';

		$wp_query = $this->eml_default_query($post_tax);

		$mime = $this->get_mime_type_by_tax($wp_query);
		$acf = $this->get_acf_media_by_tax($wp_query,'utilisation','Use');
		$data = array(
			'mime' => $mime,
			'acf'   => $acf,
		);
		$json_data = json_encode($data);
		print $json_data;
		die();
	}

	/**
	 * get_mime_type
	 * display available mime type for a query
	 *
	 * @param $eml_default_query
	 * @return string
	 */
	public function get_mime_type_by_tax($wp_query){
		$output = '';
		$values = array();
		if ( $wp_query->have_posts() ) {
			while ($wp_query->have_posts()) {
				$wp_query->the_post();
				$attachmentID = get_the_ID();
				$type = get_post_mime_type($attachmentID);
				array_push($values, $type );
			}
		} else {
			//if there is no post -> gather every mime types as if there is a result there should be one mime type
			// this query should be for a "no term" query
			$attachmentQuery = $this->eml_default_query(false);
			while ($attachmentQuery->have_posts()) {
				$attachmentQuery->the_post();
				$attachmentID = get_the_ID();
				$type = get_post_mime_type($attachmentID);
				array_push($values, $type );
			}

		}
		$uniq_terms = array_unique($values);
		$output .= '<div class="col-md-2 eml-mime-type">';
		$output .= '<div class="row"><ul class="dropdown">';
		$output .= '<li><a href="#"><i class="fa fa-chevron-down"></i> Extensions</a><ul class="submenu">';
		if(!empty($uniq_terms)){

			foreach($uniq_terms as $uniq_term){
				$output .= '<li><input id="'.$uniq_term.'"  class="eml-js-filter" name="eml-mime" type="checkbox" value="'.$uniq_term.'" /> <label for="'.$uniq_term.'">'.$uniq_term.'</label></li>';
			}

		} else {
			$output .= 'no mime type';
		}
		$output .= '</ul></li></ul></div>';
		$output .= '</div>';

		return $output;
	}

	/**
	 * get_attachment_custom
	 * List custom fields for attachment
	 *
	 * @param $post_id integer - optional if you want to retrieve for a uniq attachment
	 * @return array
	 */
	public function get_attachment_custom($post_id = false){
		$values = array();
		if($post_id == false){
			$attachmentQuery = eml_default_query(false);
			while ($attachmentQuery->have_posts()) {
				$attachmentQuery->the_post();
				$attachmentID = get_the_ID();
				$custom_fields = get_post_custom($attachmentID);
				array_push($values, $custom_fields );
			}
		} else {
			$custom_fields = get_post_custom($post_id);
			array_push($values, $custom_fields );
		}
		$uniq_terms = array_unique($values);
		foreach($uniq_terms[0] as $key => $uniq_term){
			if(substr($key,0,1) == '_'){
				unset($uniq_terms[0][$key]);
			}
		}
		return $uniq_terms;

	}

	/**
	 * get_acf_media_by_tax
	 * display a custom field from a query
	 *
	 * @param $wp_query
	 * @param $acf_field
	 * @param $field_name
	 * @return string
	 */
	public function get_acf_media_by_tax($wp_query,$acf_field,$field_name){
		$output = '';
		$values = array();
		if ( $wp_query->have_posts() ) {
			while ($wp_query->have_posts()) {
				$wp_query->the_post();
				$type = get_field($acf_field);
				array_push($values, $type );
			}
		} else {
			//if there is no post -> gather every mime types as if there is a result there should be one mime type
			// this query should be for a "no term" query
			$attachmentQuery = $this->eml_default_query(false);
			while ($attachmentQuery->have_posts()) {
				$attachmentQuery->the_post();
				$type = get_field($acf_field);
				array_push($values, $type );
			}
			$output .= '';
		}
		$uniq_terms = array_unique($values);
		$output .= '<div class="col-md-2 eml-acf-field">';
		$output .= '<div class="row"><ul class="dropdown">';
		$output .= '<li><a href="#"><i class="fa fa-chevron-down"></i> '.$field_name.'</a><ul class="submenu">';
		if(!empty($uniq_terms)){

			$i = 0;
			foreach($uniq_terms as $uniq_term){
				if(!empty($uniq_term)){
					$i++;
					$output .= $uniq_term != "" ? '<li><input class="eml-js-filter" name="eml-use" type="checkbox" value="'.$uniq_term.'" id="'.$uniq_term.'" /><label for="'.$uniq_term.'"> '.$uniq_term.'</label></li>': '';
				}
			}
			$output .= ($i == 0) ? 'No choice in this category': '';

		} else {
			$output .= 'no choice';
		}
		$output .= '</ul></li></ul></div>';
		$output .= '</div>';

		return $output;
	}

	/**
	 * eml_media_filters
	 * display a block filter
	 * @param $default_term string taxonomy slug
	 * @param $uuid string
	 * @return string
	 */
	public function eml_media_filters($default_term,$uuid){

		$eml_default_query = $this->eml_default_query($default_term);

		$output = '<div id="'.$uuid.'" class="row eml-filter-block" data-default-term="'.$default_term.'">';
		$output .= '<h2><i class="fa fa-filter"></i>Filter <i class="fa fa-refresh fa-spin js-spin-it" style="display: none;"></i></h2>';

		//MIME TYPE
		$output .= $this->get_mime_type_by_tax($eml_default_query);

		//USE - custom fields
		$output .= $this->get_acf_media_by_tax($eml_default_query,'utilisation','Use');

		// ORDER BY
		$output .= '<ul class="col-md-1">';
		$output .= '<li><input id="eml-name" class="eml-js-filter"  type="radio" name="eml-orderby" value="name" /><label for="eml-name"> Name </label></li>';
		$output .= '<li><input id="eml-date" class="eml-js-filter" checked type="radio" name="eml-orderby" value="date" /> <label for="eml-date">Date </label></li>';
		$output .= '</ul>';

		// ASC/DES
		$output .= '<ul class="col-md-1">';
		$output .= '<li><input id="eml-asc" class="eml-js-filter" type="radio" name="eml-order" value="ASC" /><label for="eml-asc"> ASC</label> </li>';
		$output .= '<li><input id="eml-desc" class="eml-js-filter" checked type="radio" name="eml-order" value="DESC" /><label for="eml-desc"> DESC</label> </li>';
		$output .= '</ul>';



		//Taxonomy terms
		$default_to_all = ($default_term == 1) ? 'selected': '';
		$output .= '<ul class="col-md-3">';
		$output .= '<li>';
		$output .= '<select name="eml-media-tax" class="eml-js-term">';
		$output .= $this->retrieve_media_tax(false,$default_term);
		$output .= '<option '.$default_to_all.' value="">ALL</option>';
		$output .= '</select>';
		$output .= '</li>';
		$output .= '<li><input class="eml-js-filter eml-js-term" type="text" name="eml-s" value="" placeholder="Search terms" />';
		$output .= '</ul>';
		$output .= '<div class="col-md-2">';
		$output .= '<input class="btn btn-submit" type="submit" name="eml-submit" value="Search" />';
		$output .= '</div>';

		$output .= '</div>';

		//RETURN ELEMENT
		return $output;
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
					case 'eml-use':
						$eml_use = $post_value['value'];
						break;
					case 'eml-s':
						$eml_s = $post_value['value'];
						break;
					case 'eml-media-tax':
						$eml_media_tax = $post_value['value'];
						break;
				}
			}
		}
		$post_tax = (isset($eml_media_tax)) ? $eml_media_tax : $_POST['tax'];
		$order = (isset($eml_order)) ? $eml_order : "DESC";
		$orderby = (isset($eml_orderby)) ? $eml_orderby : "date";
		$mime = (isset($eml_mime)) ? $eml_mime : get_post_mime_type();
		$s = (isset($eml_s)) ? $eml_s : '';
		$use = (isset($eml_use)) ? $eml_use : '';

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
			'post_status' => 'inherit',
			'orderby' => $orderby,
			'order' => $order,
			'tax_query' => $default_term_filtering,
			'post_mime_type' => $mime,
			's' => $s,
			'meta_value' => $use

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
		$query_images = new WP_Query( $query_args['args'] );
		$output = '';
		if ( $query_images->have_posts() ) {
			if($query_args['is_search'] == true){
				$count = $query_images->post_count;
				$output .= '<div class="row"><div class="col-md-12"><h2 class="eml-results"> Results ('.$count.'):</h2>';
				//$output .= $query_args['args']['tax_query'][0]['terms'];
				$output .= '<div class="em-filters-active"></div>';
				$output .= '</div></div>';
			}

			if($query_args['shortocode_filter'] == true && $query_args['filter'] == true){
				$uniqid = uniqid('eml-case-');
				$output .=  $this->eml_media_filters($query_args['shortcode_terms'],$uniqid);
				$output .= '<div id="res-'.$uniqid.'" class=" filtering-results-eml"></div>';
			}
			$output .= '<div class="attachment_display row">';


			$output .= '<div class="row">';
			while ( $query_images->have_posts() ) {
				$query_images->the_post();
				$attachmentID = get_the_ID();
				$type = get_post_mime_type($attachmentID);
				$attach = wp_get_attachment_metadata($attachmentID);
				$filetype = wp_check_filetype(wp_get_attachment_url( $attachmentID ));
				$attachment_page = get_attachment_link($attachmentID);
				$wording = get_field( "wording", $attachmentID );
				$fotolia_id = get_field('fotolia');
				$comments_count = get_comments_number( $attachmentID );

				$description = get_post()->post_content;
				//var_dump($description);
				switch ($type) {
					case 'image/jpeg':
					case 'image/png':
					case 'image/gif':
						$img = '<a class="mfp-img" href="'.wp_get_attachment_url( $attachmentID ).'">';
						$img_attr = 'data-mfp-src="'.wp_get_attachment_url( $attachmentID ).'"';
						$img .= wp_get_attachment_image( $attachmentID, 'eml-preview', false,$img_attr );
						//$img .= '<img alt="'.get_the_title($attachmentID).'" src="'.wp_get_attachment_url( $attachmentID ).'" />';
						$img .= '</a>';
						$img .= '<span class="attac-name">'.get_the_title($attachmentID).'</span>';
						break;
					default:
						$img = '<span class="attac-name">'.get_the_title($attachmentID).'</span>';
				}

				$output .= '<div class="col-md-6 col-lg-3"><div class="att-wrapper"> ';

				if($comments_count > 0):
					//  $output .= '<a href="'.$attachment_page.'" class="fs1 comments-c" aria-hidden="true" data-icon="v"><span>'.$comments_count.'</span></a> <br />';
				endif;

				$output .=  $this->get_icon_for_attachment($attachmentID);
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
					$output .=  'Utilisation : '.get_field('utilisation').' <br />' ;
				endif;
				if(!empty($description)){
					$output .=  '<a href="#eml-s'.$attachmentID.'" class="mpf-inline">Description</a> <br />' ;
					$output .=  '<div class="white-popup mfp-hide" id="eml-s'.$attachmentID.'">'.$description.'</div>' ;
				}
				//FOTOLIA
				if(get_field('fotolia')):
					//var_dump(get_fotolia_media($fotolia_id));
					//$output .=  'Utilisation : '.get_field('utilisation').' <br />' ;
				endif;

				$output .= "</div>";
				//$output .= '<a class="page-linker" href="'.$attachment_page.'">'. __('Full detailled page','assets').'</a>';
				$output .=  '<a  target="_blank" class="download-link btn" download="" href="'.wp_get_attachment_url( $attachmentID ).'" class="forcetodownload btn">'.__('download','assets').'</a>';


				//$output .=  wp_get_attachment_url( $attachmentID );

				$output .=  '</div></div>';
			}
			$output .= '</div></div>';
		} else {
			// no attachments found
			$output = "<span class='eml-no-result'>no attachment found</span>";
		}
		wp_reset_postdata();


		if( $query_args['output'] == true){
			echo $output;
		}else {
			return $output;
		}

	}
	/**
	 * get_eml_medias
	 *
	 * $args
	 * @param $default_term string - the media category slug
	 * @param filter
	 * @return string
	 */
	public function get_eml_medias($args = array('filter' => false)){
		//var_dump($args);
		//taxonomy filtering - get a term from shortcode
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
				'post_status' => 'inherit',
				'posts_per_page' => -1,
				'orderby' => 'date',
				'order' => 'DESC',
				'tax_query' => $default_term_filtering
			);

		} else {
			// no term from shortcode - build a general query without tax parameter
			$query_images_args = array(
				'post_type' => 'attachment',
				'post_status' => 'inherit',
				'posts_per_page' => -1,
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

		return $this->get_default_query($query_args);

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
			'tax' => 1,
			'filter' => false
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
		$taxonomies = array(
			'media_category',
		);
		$args = array(
			'orderby'           => 'name',
			'order'             => 'ASC',
			'hide_empty'        => true,
			'exclude'           => array(),
			'exclude_tree'      => array(),
			'include'           => array(),
			'number'            => '',
			'fields'            => 'all',
			'slug'              => '',
			'parent'            => '',
			'hierarchical'      => true,
			'child_of'          => 0,
			'childless'         => false,
			'get'               => '',
			'name__like'        => '',
			'description__like' => '',
			'pad_counts'        => false,
			'offset'            => '',
			'search'            => '',
			'cache_domain'      => 'core'
		);
		$terms = get_terms($taxonomies, $args);
		$output = '';
		//var_dump($terms);
		foreach($terms as $term){
			$selected = ($default_term != false && $default_term == $term->slug) ? 'selected' : '';
			$output .= '<option '.$selected.' value="'.$term->slug.'">'.$term->name.'</option>';
		}
		if($is_ajax == true || $_POST['is_ajax'] == true){
			echo $output;
			die();
		} else {
			return $output;
		}
	}

}
