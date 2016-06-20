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

class Wp_Attachment_Filter_BulkEdit{

    /**
     * @param $response
     * @param $attachment
     * add_filter( 'wp_prepare_attachment_for_js', 'wpaf_prepare_attachment_for_js', 10, 2 );
     * @return mixed
     */
    public function wpaf_prepare_attachment_for_js( $response, $attachment ) {

        foreach ( get_object_taxonomies ( 'attachment', 'names' ) as $taxonomy ) {

            $term_ids = wp_get_object_terms($attachment->ID, $taxonomy, array( 'fields' => 'ids' ) );
            $response['taxonomies'][$taxonomy] = $term_ids;
        }

        return $response;
    }

    /**
     *
     * add_action( 'print_media_templates', ' wpaf_print_media_templates' );
     */
    public function   wpaf_print_media_templates() {

        global $wp_version;


        if ( version_compare( $wp_version, '4.3', '<' ) ) {

            $remove_button = '<a class="close media-modal-icon" href="#" title="' . esc_attr__('Remove') . '"></a>';

            $deselect_button = '<a class="check" href="#" title="' . esc_attr__('Deselect') . '" tabindex="-1"><div class="media-modal-icon"></div></a>';

        }
        else {

            $remove_button = '<button type="button" class="button-link attachment-close media-modal-icon"><span class="screen-reader-text">' . __( 'Remove' ) . '</span></button>';

            $deselect_button = '<button type="button" class="button-link check" tabindex="-1"><span class="media-modal-icon"></span><span class="screen-reader-text">' . __( 'Deselect' ) . '</span></button>';

        } ?>


        <script type="text/html" id="tmpl-attachment-grid-view">

            <div class="attachment-preview js--select-attachment type-{{ data.type }} subtype-{{ data.subtype }} {{ data.orientation }}">
                <div class="eml-attacment-inline-toolbar">
                    <# if ( data.can.save && data.buttons.edit ) { #>
                        <i class="eml-icon dashicons dashicons-edit edit" data-name="edit"></i>
                        <# } #>
                </div>
                <div class="thumbnail">
                    <# if ( data.uploading ) { #>
                        <div class="media-progress-bar"><div style="width: {{ data.percent }}%"></div></div>
                        <# } else if ( 'image' === data.type && data.sizes ) { #>
                            <div class="centered">
                                <img src="{{ data.size.url }}" draggable="false" alt="" />
                            </div>
                            <# } else { #>
                                <div class="centered">
                                    <# if ( data.image && data.image.src && data.image.src !== data.icon ) { #>
                                        <img src="{{ data.image.src }}" class="thumbnail" draggable="false" />
                                        <# } else { #>
                                            <img src="{{ data.icon }}" class="icon" draggable="false" />
                                            <# } #>
                                </div>
                                <div class="filename">
                                    <div>{{ data.filename }}</div>
                                </div>
                                <# } #>
                </div>
                <# if ( data.buttons.close ) { #>
                    <?php echo $remove_button; ?>
                    <# } #>
            </div>
            <# if ( data.buttons.check ) { #>
                <?php echo $deselect_button; ?>
                <# } #>
                    <#
                        var maybeReadOnly = data.can.save || data.allowLocalEdits ? '' : 'readonly';
                        if ( data.describe ) {
                        if ( 'image' === data.type ) { #>
                        <input type="text" value="{{ data.caption }}" class="describe" data-setting="caption"
                               placeholder="<?php esc_attr_e('Caption this image&hellip;'); ?>" {{ maybeReadOnly }} />
                        <# } else { #>
                            <input type="text" value="{{ data.title }}" class="describe" data-setting="title"
                            <# if ( 'video' === data.type ) { #>
                                placeholder="<?php esc_attr_e('Describe this video&hellip;'); ?>"
                                <# } else if ( 'audio' === data.type ) { #>
                                    placeholder="<?php esc_attr_e('Describe this audio file&hellip;'); ?>"
                                    <# } else { #>
                                        placeholder="<?php esc_attr_e('Describe this media file&hellip;'); ?>"
                                        <# } #> {{ maybeReadOnly }} />
                                            <# }
                                                } #>
        </script>


        <script type="text/html" id="tmpl-attachments-details">

            <h3><?php _e( 'Attachments Details', 'enhanced-media-library' ); ?></h3>

            <form class="compat-item">
                <table class="compat-attachment-fields">

                    <?php $wpuxss_eml_tax_options = get_option('wpuxss_eml_tax_options');

                    foreach( get_taxonomies_for_attachments() as $taxonomy ) :

                        $t = (array) get_taxonomy($taxonomy);
                        if ( ! $t['public'] || ! $t['show_ui'] )
                            continue;
                        if ( empty($t['label']) )
                            $t['label'] = $taxonomy;
                        if ( empty($t['args']) )
                            $t['args'] = array();

                        if ( function_exists( 'wp_terms_checklist' ) &&
                            ( (bool) $wpuxss_eml_tax_options['edit_all_as_hierarchical'] || (bool) $t['hierarchical'] ) ) {

                            ob_start();

                            wp_terms_checklist( 0, array( 'taxonomy' => $taxonomy, 'checked_ontop' => false, 'walker' => new Walker_Media_Taxonomy_Checklist() ) );

                            if ( ob_get_contents() != false )
                                $html = '<ul class="term-list">' . ob_get_contents() . '</ul>';
                            else
                                $html = '<ul class="term-list"><li>No ' . $t['label'] . '</li></ul>';

                            ob_end_clean();

                            $t['input'] = 'html';
                            $t['html'] = $html; ?>

                            <tr class="compat-field-<?php echo $taxonomy; ?>">
                                <th scope="row" class="label eml-tax-label">
                                    <label for="attachments-<?php echo $taxonomy; ?>"><span class="alignleft"><?php echo $t['label']; ?></span><br class="clear" /></label>
                                </th>
                                <td class="field eml-tax-field"><?php echo $t['html']; ?></td>
                            </tr>

                        <?php } ?>

                    <?php endforeach; ?>

                </table>

            </form>

        </script>


        <?php if ( version_compare( $wp_version, '4.3', '<' ) ) :
            $select_all_button = '<a class="select-all" href="#">' . __( 'Select All', 'enhanced-media-library' ) . '</a>';
            $edit_selection_button = '<a class="edit-selection" href="#">' . __( 'Edit Selection', 'enhanced-media-library' ) . '</a>';
            $deselect_all_button = '<a class="clear-selection" href="#">' . __( 'Deselect All', 'enhanced-media-library' ) . '</a>';
            $delete_selected_button = '<a class="delete-selected" href="#">' . __( 'Delete Selected', 'enhanced-media-library' ) . '</a>';
        else :
            $select_all_button = '<button type="button" class="button-link select-all">' . __( 'Select All', 'enhanced-media-library' ) . '</button>';
            $edit_selection_button = '<button type="button" class="button-link edit-selection">' . __( 'Edit Selection', 'enhanced-media-library' ) . '</button>';
            $deselect_all_button = '<button type="button" class="button-link clear-selection">' . __( 'Deselect All', 'enhanced-media-library' ) . '</button>';
            $delete_selected_button = '<button type="button" class="button-link delete-selected">' . __( 'Delete Selected', 'enhanced-media-library' ) . '</button>';
        endif; ?>

        <script type="text/html" id="tmpl-media-bulk-selection">

            <div class="selection-info">
                <span class="count"></span>
                <?php echo $select_all_button; ?>
                <# if ( data.clearable ) { #>
                    <?php echo $deselect_all_button; ?>
                    <# } #>
                        <# if ( ! data.uploading ) { #>
                            <?php if ( ! MEDIA_TRASH ):
                                echo $delete_selected_button;
                            endif; ?>
                            <# } #>
            </div>
            <div class="selection-view"></div>

        </script>

    <?php }

    /**
     * add_action( 'wp_ajax_eml-save-attachments', ' wpaf_save_attachments', 0 );
     */
    public function  wpaf_save_attachments() {

        if ( empty( $_REQUEST['attachments'] ) )
            wp_send_json_error();


        $attachments = $_REQUEST['attachments'];
        $new_attachments = array();


        check_ajax_referer( 'eml-bulk-edit-nonce', 'nonce' );

        foreach ( $attachments as $attachment_id => $taxonomies ) {

            if ( ! current_user_can( 'edit_post', intval($attachment_id) ) )
                continue;

            if ( ! $attachment = get_post( intval($attachment_id) ) )
                continue;

            if ( 'attachment' != $attachment->post_type )
                continue;


            foreach( $taxonomies as $taxonomy => $terms ) {

                if ( is_array( $terms ) ) {
                    $terms = array_map( 'intval', $terms );
                }

                wp_set_object_terms( intval($attachment_id), $terms, $taxonomy, false );
            }

            $new_attachments[$attachment_id]['date'] = strtotime( $attachment->post_date_gmt ) * 1000;
            $new_attachments[$attachment_id]['modified'] = strtotime( $attachment->post_modified_gmt ) * 1000;

            if ( function_exists('get_compat_media_markup') ) {
                $new_attachments[$attachment_id]['compat'] = get_compat_media_markup( $attachment_id, array( 'in_modal' => true ) );
            }
        } // foreach

        wp_send_json_success( $new_attachments );
    }

    /**
     *
     * add_action( 'wp_ajax_eml-remove-attachments', ' wpaf_remove_attachments' );
     */
    public function wpaf_remove_attachments() {

        if ( empty( $_REQUEST['attachments'] ) )
            wp_send_json_error();


        $attachments = $_REQUEST['attachments'];
        $removed = array();

        check_ajax_referer( 'eml-bulk-edit-nonce', 'nonce' );

        foreach ( $attachments as $attachment_id ) {

            $attachment_id = intval( $attachment_id );

            if ( ! current_user_can( 'delete_post', $attachment_id ) )
                continue;

            if ( ! $attachment = get_post( $attachment_id ) )
                continue;

            if ( 'attachment' !== $attachment->post_type )
                continue;

            if ( false !== wp_delete_attachment( $attachment_id ) )
                $removed[$attachment_id] = $attachment_id;
        }

        $removed = array_filter( $removed );

        wp_send_json_success( $removed );
    }

}





