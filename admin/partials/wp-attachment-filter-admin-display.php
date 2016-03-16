<div class="wrap">
    <h2>Attachment filter - Settings</h2>
    <?php

    $wpafp = new Wp_Attachment_Filter_Public('wp-attachment-filter-public','1.0');
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
                    $acf_wpaf_items = $wpafp->get_attachment_custom();

                    echo '<ul>';
                    foreach($acf_wpaf_items[0] as $key => $acf_wpaf_item){
                        if(is_array($acf_wpaf_items_option)){
                            $checked_acf = (in_array($key,$acf_wpaf_items_option)) ? 'checked="checked"': '';
                        } else {
                            $checked_acf = '';
                        }

                        echo '<li><input '.$checked_acf.' name="wpaf-acf-items[]" type="checkbox" value="'.$key.'" /> <label for="'.$key.'">'.$key.'</label></li>';
                    }
                    echo '</ul>';
                    ?>


                </td>
                <td>
                    <em>
                        Pick the custom fields you want to show on the filter box <br>
                        <b>Caution :</b> may not work properly for all kind custom fields
                    </em>
                </td>
            </tr>
        </table>

        <?php submit_button(); ?>

    </form>



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
        wp attachment filter use...<a href="http://getbootstrap.com/" target="_blank">Bootstrap.</a>
    </p>



</div>