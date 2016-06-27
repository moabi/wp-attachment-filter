<div class="plugin-wrap" style="float:right;width: 80%;clear: both;display: block;position: relative;z-index: 99;">
<?php
/**
 * Uploadscreen for selecting and uploading new media file
 *
 * @author      M�ns Jonasson  <http://www.mansjonasson.se>
 * @copyright   M�ns Jonasson 13 sep 2010
 * @version     $Revision: 2303 $ | $Date: 2010-09-13 11:12:35 +0200 (ma, 13 sep 2010) $
 * @package     wordpress
 * @subpackage  enable-media-replace
 *
 */

if (!current_user_can('upload_files'))
    wp_die(__('You do not have permission to upload files.', 'enable-media-replace'));

global $wpdb;

$table_name = $wpdb->prefix . "posts";

$sql = "SELECT guid, post_mime_type FROM $table_name WHERE ID = " . (int) $_GET["attachment_id"];

list($current_filename, $current_filetype) = $wpdb->get_row($sql, ARRAY_N);

$current_filename = substr($current_filename, (strrpos($current_filename, "/") + 1));


?>
<div class="wrap">
    <div id="icon-upload" class="icon32"><br /></div>
    <h2><?php echo __("Replace Media Upload", "enable-media-replace"); ?></h2>

    <?php
    $url = admin_url( "upload.php?page=wp-attachment-filter/admin/partials/wp-attachment-filter-admin-mediareplace.php&noheader=true&action=media_replace_upload&attachment_id=" . (int) $_GET["attachment_id"]);
    $action = "media_replace_upload";
    $formurl = wp_nonce_url( $url, $action );
    if (FORCE_SSL_ADMIN) {
        $formurl = str_replace("http:", "https:", $formurl);
    }
    ?>

    <form enctype="multipart/form-data" method="post" action="<?php echo $formurl; ?>">
        <?php
        #wp_nonce_field('enable-media-replace');
        ?>
        <input type="hidden" name="ID" value="<?php echo (int) $_GET["attachment_id"]; ?>" />
        <div id="message" class="notice notice-error fade"><p><?php echo __("CAUTION: You are about to replace the media file", "enable-media-replace"); ?> "<?php echo $current_filename?>". <?php echo __("There is no undo. Think about it!", "wp-attachment-filter"); ?></p>
        <p>
            <?php

                echo __("Please note that if you upload a new image, only embeds/links of the original size image will be replaced in your posts.", "wp-attachment-filter");
            echo ' </p> <p>';
            echo __("DO NOT REPLACE IMAGES,only intended for zip, pdf,etc...", "wp-attachment-filter");
            ?>

        </p>
        </div>

        <p><?php echo __("Choose a file to upload from your computer", "enable-media-replace"); ?></p>

        <input type="file" name="userfile" />

        <?php do_action( 'emr_before_replace_type_options' ); ?>

        <?php if ( apply_filters( 'emr_display_replace_type_options', true ) ) : ?>
            <p><?php echo __("Select media replacement type:", "enable-media-replace"); ?></p>

            <label for="replace_type_1"><input CHECKED id="replace_type_1" type="radio" name="replace_type" value="replace"> <?php echo __("Just replace the file", "enable-media-replace"); ?></label>
            <p class="howto"><?php echo __("Note: This option requires you to upload a file of the same type (", "wp-attachment-filter"); ?><?php echo $current_filetype; ?><?php echo __(") as the one you are replacing. The name of the attachment will stay the same (", "wp-attachment-filter"); ?><?php echo $current_filename; ?><?php echo __(") no matter what the file you upload is called.", "wp-attachment-filter"); ?></p>

            <?php if ( apply_filters( 'emr_enable_replace_and_search', true ) ) : ?>
                <label for="replace_type_2"><input id="replace_type_2" type="radio" name="replace_type" value="replace_and_search"> <?php echo __("Replace the file, use new file name and update all links", "enable-media-replace"); ?></label>
                <p class="howto"><?php echo __("Note: If you check this option, the name and type of the file you are about to upload will replace the old file. All links pointing to the current file (", "wp-attachment-filter"); ?><?php echo $current_filename; ?><?php echo __(") will be updated to point to the new file name.", "enable-media-replace"); ?></p>

            <?php endif; ?>
        <?php else : ?>
            <input type="hidden" name="replace_type" value="replace" />
        <?php endif; ?>

        <?php
        submit_button( __("Upload", "enable-media-replace"));
        ?>
        <a href="#" onclick="history.back();"><?php echo __("Cancel", "enable-media-replace"); ?></a>
    </form>
</div>
    </div>
