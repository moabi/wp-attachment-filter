<div class="wrap">
    <h2>Attachment filter - Settings</h2>
    <?php

    $wpafp = new Wp_Attachment_Filter_Public('wp-attachment-filter-public','1.0');
    $wpaf_filter = new Wp_Attachment_Filter_Filter('wp-attachment-filter-public','1.0');
    $wpaf_admin_cache = new Wp_Attachment_Filter_AdminCache('wp-attachment-filter-public','1.0');
    ?>
    <form method="post" action="options.php">
        <?php settings_fields( 'wp-attachment-filter-settings-group' ); ?>
        <?php do_settings_sections( 'wp-attachment-filter-settings-group' ); ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">
                    Your attachment taxonomy <br>

                </th>
                <td>
                    <input type="text"  name="wpaf-media-tax" placeholder="media_category" value="<?php echo esc_attr( get_option('wpaf-media-tax') ); ?>">
                    <br>
                    </td>
                <td>
                    <em>If you're using EML, chances are your media taxonomy is "media_category" otherwise fill with your own</em>


                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    Filtered custom fields <br>

                </th>
                <td>
                    <?php
                    //get data from options
                    $acf_wpaf_items_option = get_option('wpaf-acf-items');
                    //list available custom fields for attachment type
                    $acf_wpaf_items = $wpaf_filter->get_attachment_custom_fields(false);
                    //var_dump($acf_wpaf_items_option);
                    echo '<ul>';
                    //var_dump($acf_wpaf_items);
                    if($acf_wpaf_items){
                        foreach($acf_wpaf_items as $acf_wpaf_item){
                            if(is_array($acf_wpaf_items_option)){
                                $checked_acf = (in_array($acf_wpaf_item,$acf_wpaf_items_option)) ? 'checked="checked"': '';
                            } else {
                                $checked_acf = '';
                            }

                            echo '<li><input '.$checked_acf.' name="wpaf-acf-items[]" type="checkbox" value="'.$acf_wpaf_item.'" /> <label for="'.$acf_wpaf_item.'">'.$acf_wpaf_item.'</label></li>';
                        }
                    } else {
                        echo 'no custom fields are attached';
                    }

                    echo '</ul>';
                    ?>


                </td>
                <td>
                    <em>
                        Pick the custom fields you want to show on the filter box <br>
                        <b>Caution :</b> may not work properly for all kind custom fields <br>
                        Fields which are not used -> WON'T appear
                    </em>
                </td>
            </tr>

            <tr valign="top">
                <th scope="row">
                    <label for="wp-attachment-filter-mpf">
                    Include the excellent <a target="_blank" href="https://github.com/dimsemenov/Magnific-Popup/">Magnific-Popup</a> by Dimsemenov ?
                    </label>
                </th>
                <td>
                    <?php
                    $wp_payzen_css = get_option('wp-attachment-filter-mpf');
                    if($wp_payzen_css == 'on') {
                        $checked_dim = 'checked';
                    } else {
                        $checked_dim = '';
                    }
                    ?>
                    <input <?php echo $checked_dim; ?> type="checkbox" name="wp-attachment-filter-mpf" id="wp-attachment-filter-mpf" />
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="wp-attachment-filter-loading-bar">
                    Include a loading bar ?
                    </label>
                </th>
                <td>
                    <?php
                    $wp_payzen_loader = get_option('wp-attachment-filter-loading-bar');
                    if($wp_payzen_loader == 'on') {
                        $checked_dim_loader = 'checked';
                    } else {
                        $checked_dim_loader = '';
                    }
                    ?>
                    <input <?php echo $checked_dim_loader; ?> type="checkbox" name="wp-attachment-filter-loading-bar" id="wp-attachment-filter-loading-bar" />
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="wp-attachment-filter-loading-bar">
                       Manually preload ? (will always use, static files,cache won't expire)
                    </label>
                </th>
                <td>
                    <?php
                    $wp_payzen_cache = get_option('wp-attachment-filter-manual-preload');
                    if($wp_payzen_cache == 'on') {
                        $checked_dim_cache = 'checked';
                    } else {
                        $checked_dim_cache = '';
                    }
                    ?>
                    <input <?php echo $checked_dim_cache; ?> type="checkbox" name="wp-attachment-filter-manual-preload" id="wp-attachment-filter-manual-preload" />
                </td>

            </tr>
        </table>

        <?php submit_button(); ?>

    </form>
    <hr>

<h2>Cache settings</h2>

    <p>
        <ul>
        <?php

        $files = glob(get_wp_attachment_filter_plugin_dir().'public/cache/*.json');
        foreach($files as $file){
            $file_name = str_replace(get_wp_attachment_filter_plugin_dir().'public/cache/','',$file);
            echo '<li>'.$file_name.' - ( '.date("F d Y H:i:s.", filectime($file)).' )</li>';
        }
        ?>
    </ul>

    </p>
        <p>
        will create all the filters from all the taxonomies,please don't reload the page while working
        </p>
        <?php
        /**
         * Call $wpaf_admin_cache->create_cached_filters();
         */
        submit_button('Preload the cache','primary','wpaf-js-cache-preloader');
        ?>
    <span class="wpaf-js-loader" style="display: none;"><img src="<?php
echo get_bloginfo('url'); ?>/wp-admin/images/loading.gif" /></span>

    <div class="notice-success updated success wpaf-js-loader-success" style="display:none;">
        <p>Success ! Cache files created</p>
    </div>



    <hr>
    
<h2>Help</h2>
    <p>
        a tinymce button should help you to load the shortcodes <br>
        Main Search box  : <pre>[mediabycategory filter="true" ]</pre>
    <br>
    Taxonomy display attachment : <pre>[mediabycategory tax="YOURTAXONOMYTERM" ]</pre>
    <br>
    Taxonomy display attachment with filter : <pre>[mediabycategory tax="YOURTAXONOMYTERM" filter="true" ]</pre>
    </p>
    <p>
        wp attachment filter use...:
        <ul>
        <li><a href="http://getbootstrap.com/" target="_blank">Bootstrap.</a></li>
        <li><a href="https://jquery.com/" target="_blank">jQuery.</a></li>
    </ul>

    </p>



</div>