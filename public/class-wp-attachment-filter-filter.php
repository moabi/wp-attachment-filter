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


class Wp_Attachment_Filter_Filter {

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
	 * @param $taxonomy
	 * query only attachment IDs
	 * for a faster query
	 * @return WP_Query
	 */
	public function get_all_attachment_ids(){

		$query_args = array(
			'post_type' => 'attachment',
			'post_status' => 'inherit',//for attachment post type
			'posts_per_page' => 10000,
			'fields '	=> 'ids',
			'cache_results'  => false,
		);

		$attachmentQuery = new WP_Query( $query_args );
		//var_dump($attachmentQuery);
		return $attachmentQuery;
	}




	/**
	 * eml_media_filters
	 * display a block filter with all filtering options
	 *
	 * @param $default_term string taxonomy slug
	 * @param $uuid string unique CSS ID to js target
	 * @return string
	 */
	public function eml_media_filters($default_term, $uuid ){
		$public_waf = new Wp_Attachment_Filter_Public('wp-attachment-filter','v1.0');
		$eml_default_query = $public_waf->eml_default_query($default_term,true);


		$output = '<div class="row " data-default-term="'.$default_term.'"><div id="'.$uuid.'" class="col-md-12 eml-filter-block"><div class="padd-1">';
		$output .= '<h2><img src="'.get_wp_attachment_filter_plugin_uri().'/public/img/filter-outline.svg" class="filter"/>Filter <img src="'.get_wp_attachment_filter_plugin_uri().'/public/img/reload.svg" class="fa-spin js-spin-it"  style="display: none;" /></h2>';


		/**
		 * at init query is general & takes every attachment ids
		 * we use $ids_query to make the optimize the query
		 * if initial query change's for some reason, adapt $ids_query...
		 */
		$ids_query = $this->get_all_attachment_ids();
		$extra_expensive_fields = $this->get_extra_filter($ids_query,$default_term);
		$output .= $extra_expensive_fields['mime'];
		$output .= $extra_expensive_fields['acf'];

		//Taxonomy terms
		$default_to_all = ($default_term == 1) ? 'selected': '';
		$output .= '<ul class="col-md-4">';
		$output .= '<li>';
		$output .= $public_waf->retrieve_media_tax(false,$default_term);
		$output .= '</li>';
		$output .= '</ul>';


		//LAST BLOCK
		$output .= '<div class="row"><div class="col-md-12">';

		// ORDER BY
		$output .= '<div class="col-md-3 "><ul class="horizontal-list">';
		$output .= '<li><input id="eml-name" class="eml-js-filter"  type="radio" name="eml-orderby" value="name" /><label for="eml-name"> '.__("Name","wp-attachment-filter").'</label></li>';
		$output .= '<li><input id="eml-date" class="eml-js-filter" checked type="radio" name="eml-orderby" value="date" /> <label for="eml-date">'.__("Date","wp-attachment-filter").' </label></li>';
		$output .= '</ul></div>';

		// ASC/DES
		$output .= '<div class="col-md-2 "> <ul class=" horizontal-list">';
		$output .= '<li><input id="eml-asc" class="eml-js-filter" type="radio" name="eml-order" value="ASC" /><label for="eml-asc"> '.__("Asc","wp-attachment-filter").'</label> </li>';
		$output .= '<li><input id="eml-desc" class="eml-js-filter" checked type="radio" name="eml-order" value="DESC" /><label for="eml-desc"> '.__("Desc","wp-attachment-filter").'</label> </li>';
		$output .= '</ul></div>';

		//SEARCH TERMS
		$output .= '<ul class="col-md-3">';
		$output .= '<li><input class="eml-js-filter eml-js-term" type="text" name="eml-s" value="" placeholder="'.__("Search terms","wp-attachment-filter").'" />';
		$output .= '</ul>';

		//submit button
		$output .= '<div class="col-md-2 pull-right">';
		$output .= '<input class="btn btn-submit" type="submit" name="eml-submit" value="'.__('Search', 'wp-attachment-filter').'" />';
		$output .= '</div>';

		$output .= '</div></div>';
		//#LAST BLOCK

		$output .= '</div></div></div>';

		//RETURN ELEMENT
		return $output;
	}



	/**
	 * get_extra_filter
	 * loop through the queried post to fetch available custom fields && mime type
	 * if no query
	 * @param $wp_query
	 * @param $default_term
	 * @return string
	 */

	public function get_extra_filter($wp_query,$default_term = false){

		$wpaf_cache = new Wp_Attachment_Filter_Cache('wp-attachment-filter','v1.0');
		//default "All" option value should be 1 instead of 0
		if($default_term == "0"){
			$default_term = 1;
		}
		
		//check if file exist in the cache
		if($wpaf_cache->get($default_term) == false){

			//get selected custom fields
			$acf_wpaf_items_option = get_option('wpaf-acf-items');

			$data = array();
			$custom_fields = array();
			$mimes = array();

			foreach($acf_wpaf_items_option as $acf_field) {
				$custom_fields[$acf_field] = array();
			}
			
			//loop through the query
			if ( $wp_query->have_posts() && $wp_query ) {
				//we should use a cache for this
				while ($wp_query->have_posts()) {
					$wp_query->the_post();
					$attachmentID = get_the_ID();
					//push mime types
					$type = get_post_mime_type($attachmentID);
					array_push($mimes, $type );
					//push custom fields
					foreach($acf_wpaf_items_option as $acf_field) {
						$field_type = get_field($acf_field,$attachmentID);

						if (is_array($field_type)) {
							foreach ($field_type as $type_uniq) {
								if(!in_array($type_uniq,$custom_fields[$acf_field])){
									$custom_fields[$acf_field][] = $type_uniq;
								}
							}
						} else {
							if( !is_null($field_type) && !in_array($field_type,$custom_fields[$acf_field])){
								$custom_fields[$acf_field][] = $field_type;
							}
						}
					}
				}

			} else {

				//if there is no post -> gather every mime types as if there is a result there should be one mime type
				// this query should be for a "no term" query
				$attachmentQuery = $this->get_all_attachment_ids();
				while ($attachmentQuery->have_posts()) {
					global $post;
					$attachmentQuery->the_post();
					$attachmentID = get_the_ID();
					//push mime type
					$type = get_post_mime_type($attachmentID);
					array_push($mimes, $type );
					//push custom field
					foreach($acf_wpaf_items_option as $acf_field) {
						$field_type = get_field($acf_field,$attachmentID);

						if (is_array($field_type)) {
							foreach ($field_type as $type_uniq) {
								if(!in_array($type_uniq,$custom_fields[$acf_field])){
									$custom_fields[$acf_field][] = $type_uniq;
								}
							}
						} else {
							if( !is_null($field_type) && !in_array($field_type,$custom_fields[$acf_field])){
								$custom_fields[$acf_field][] = $field_type;
							}
						}
					}
				}
			}

			//uniq terms please
			$uniq_terms_mimes = array_unique($mimes);


			//MIME TYPE OPERATION
			$data['mime'] = $this->get_mime_type_by_tax($uniq_terms_mimes);
			//Custom fields
			$data['acf'] = $this->get_acf_media_by_tax($custom_fields);
			//var_dump($data['acf']);

			//create the Cached file
			$wpaf_cache->set($default_term, $data);

		} else {
			//file does exist, rely on cache
			$data_file = $wpaf_cache->get($default_term);
			$data = json_decode(json_encode($data_file), True);
			//var_dump($data);
		}


		return $data;


	}

	/**
	 * get_mime_type
	 * display available mime type for a query
	 *
	 * @param $eml_default_query
	 * @return string
	 */
	public function get_mime_type_by_tax($uniq_terms_mimes){
		$public_waf = new Wp_Attachment_Filter_Public('wp-attachment-filter','v1.0');

		/**
		 * MIME TYPE FILTER DISPLAY
		 */

		$output = '';

		$output .= '<div class="col-md-3 eml-mime-type">';
		$output .= '<div class="row"><ul class="dropdown">';
		$output .= '<li><a href="#"><img src="'.get_wp_attachment_filter_plugin_uri().'/public/img/down-chevron.svg" class="chevron"/> '.__(" Document type","wp-attachment-filter").'</a><ul class="submenu">';

		if(!empty($uniq_terms_mimes)){
			$mime_nice_name = new WpAttachmentFilterUtilities('wp-attachment-filter','v1.0');
			foreach($uniq_terms_mimes as $uniq_terms_mime){
				$nice_name = $mime_nice_name->get_ext($uniq_terms_mime);
				$output .= '<li><input id="'.$uniq_terms_mime.'"  class="eml-js-filter" name="eml-mime" type="checkbox" value="'.$uniq_terms_mime.'" /> <label for="'.$uniq_terms_mime.'">'.$nice_name.'</label></li>';
			}

		} else {
			$output .= __("No mime type","wp-attachment-filter");
		}
		$output .= '</ul></li></ul></div>';
		$output .= '</div>';

		return $output;
	}
	
	/**
	 * get_acf_media_by_tax
	 * display a custom field from a query
	 * Filter part
	 * @param $uniq_terms
	 *
	 * @return string
	 */
	public function get_acf_media_by_tax($custom_fields){

		$output = '';

		//var_dump($custom_fields);
		foreach($custom_fields as $custom_field => $value){
			//var_dump($value);
			$output .= '<div id="eml-acf-'.$custom_field.'" class="col-md-2 eml-acf-field">';
			$output .= '<div class="row"><ul class="dropdown">';
			$output .= '<li><a href="#"><img src="'.get_wp_attachment_filter_plugin_uri().'/public/img/down-chevron.svg" class="chevron"/>'.$custom_field.'</a><ul class="submenu">';

			//Loop through terms
			if(!empty($value)){

				$i = 0;
				foreach($value as $custom_field_value){
					if(!empty($custom_field_value)){
						$i++;
						$output .= $custom_field_value != "" ? '<li><input class="eml-js-filter" name="eml-'.$custom_field.'" type="checkbox" value="'.$custom_field_value.'" id="'.$custom_field_value.'" /><label for="'.$custom_field_value.'"> '.$custom_field_value.'</label></li>': '';
					}
				}
				$output .= ($i == 0) ? __("No filter available","wp-attachment-filter"): '';

			} else {
				$output .= __("No filter available","wp-attachment-filter");
			}
			$output .= '</ul></li></ul></div>';
			$output .= '</div>';

		}



		return $output;
	}



	/**
	 * get_attachment_custom_fields
	 * List custom fields for attachment
	 *
	 * @param $post_id integer - optional if you want to retrieve for a uniq attachment
	 * @return array
	 */
	public function get_attachment_custom_fields($post_id = false){
		$values = array();
		if($post_id == false){
			//query all attachment IDs
			$attachmentQuery = $this->get_all_attachment_ids();

			while ($attachmentQuery->have_posts()) {
				$attachmentQuery->the_post();
				$attachmentID = get_the_ID();
				$custom_fields = get_post_custom_keys($attachmentID);
				$keys = array_values($custom_fields);
				foreach ($keys as $key){
					array_push($values, $key );
				}

			}
		} else {
			$custom_fields = get_post_custom_keys($post_id);
			$keys = array_values($custom_fields);
			foreach ($keys as $key){
				array_push($values, $key );
			}
		}

		$uniq_terms = $values;
		//get rid of _ values
		foreach($uniq_terms as $key => $uniq_term){
			//var_dump($uniq_term);
			if(substr($uniq_term,0,1) == '_'){
				unset($uniq_terms[$key]);
			}
		}
		$result = array_unique($uniq_terms);

		return $result;


	}


}
