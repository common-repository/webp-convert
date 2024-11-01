<?php
/**
 *
 * Admin menu for the webp convert plugin
 *
 * @link       https://indianic.com
 * @since      1.0.0
 *
 * @package    webp-convert
 * @subpackage webp-convert/admin
 */


/**
 * Admin menu hook
 * @since 1.0
 * */
add_action('admin_menu', 'webp_setting_menu');
function webp_setting_menu() { 
	add_menu_page( 
		_('WEBP settings','webp-convert'), 
		_('WEBP settings','webp-convert'), 
		'manage_options', 
		'webp_menu_settings', 
		'webp_setting_menu_callback_function', 
		'dashicons-format-image' 
	);
}


/**
 * Admin menu webp settings callback function
 *
 * @since       1.0.0
 * @param       string    $plugin_name      Webp Convert.
 * @param       string    $version          1.0.0.
 * */
function webp_setting_menu_callback_function(){
	include_once plugin_dir_path( __FILE__ ).'webp-settings-form.php';
}

/**
 * Add CSS and JS to Admin side
 *
 * @since       1.0.0
 * @param       string    $plugin_name      Webp Convert.
 * @param       string    $version          1.0.0.
 * */
function webp_add_admin_jscss(){
    $screen = get_current_screen();
    $webp_screen = 'toplevel_page_webp_menu_settings';
    if( is_object( $screen ) && (($webp_screen == $screen->base)) ) {
        wp_enqueue_style('simple-wp-smtp-open-props',WEBP_URL . '/admin/css/open-props.css');
        wp_enqueue_style('simple-wp-smtpforms',WEBP_URL . '/admin/css/forms.css');
    }
}
add_action('admin_enqueue_scripts','webp_add_admin_jscss');


/**
 * Replace src paths
 *
 * @since       1.0.0
 * @param       string    $plugin_name      Webp Convert.
 * @param       string    $version          1.0.0.
 * */
function webp_supports() {
    if (empty($_SERVER['HTTP_ACCEPT'])) {
        return false;
    } elseif (strpos($_SERVER['HTTP_ACCEPT'], 'image/webp') !== false) {
        return true;
    } else {
        return false;
    }
}

/**
 * Function to replace PNG/JPG images with WebP images in a given string
 *
 * @since       1.0.0
 * @param       string    $plugin_name      Webp Convert.
 * @param       string    $version          1.0.0.
 * */
function webp_replace_images_with_webp($string) {
    preg_match_all('/<img[^>]+>/i', $string, $matches);
    $images = $matches[0];
    foreach ($images as $image) {
        $new_image = '';
        if (webp_supports() && (strpos($image, '.jpg') !== false || strpos($image, '.jpeg') !== false || strpos($image, '.png') !== false)) {
            $new_image = preg_replace('/(\.jpg|\.jpeg|\.png)/', '.webp', $image);
            $new_image = str_replace('<img', '<img loading="lazy"', $new_image);
        } else {
            $new_image = $image;
        }
        $string = str_replace($image, $new_image, $string);
    }
    return $string;
}

/**
 * Replace all images with WebP in the output buffer
 *
 * @since       1.0.0
 * @param       string    $plugin_name      Webp Convert.
 * @param       string    $version          1.0.0.
 * */
function webp_replace_images_in_buffer($buffer) {
    return webp_replace_images_with_webp($buffer);
}

/**
 * Start output buffering
 *
 * @since       1.0.0
 * @param       string    $plugin_name      Webp Convert.
 * @param       string    $version          1.0.0.
 * */
function webp_start_buffering_replace_images() {
    ob_start('webp_replace_images_in_buffer');
}

add_action('wp', 'webp_start_buffering_replace_images');

function webp_webpconvert($path,$img){
    $ext = pathinfo($img, PATHINFO_EXTENSION);
    if($ext=='png'){
        $jpg_png=imagecreatefrompng($path.'/'.$img);
    }else{
        $jpg_png=imagecreatefromjpeg($path.'/'.$img);
    }
    $w=imagesx($jpg_png);
    $h=imagesy($jpg_png);
    $webp=imagecreatetruecolor($w,$h);
    imagecopy($webp,$jpg_png,0,0,0,0,$w,$h);
    imagewebp($webp, $path.'/'.str_replace($ext,'webp',$img), 82);
    imagedestroy($jpg_png);
    imagedestroy($webp);
}

/**
 * Convert Upload image if plugin activated.
 *
 * @since       1.0.0
 * @param       string    $plugin_name      Webp Convert.
 * @param       string    $version          1.0.0.
 * */
add_filter( 'wp_generate_attachment_metadata', 'webp_manipulate_metadata_wpse', 10, 2 );
function webp_manipulate_metadata_wpse( $metadata, $attachment_id ) 
{
    webp_webpconvert(wp_upload_dir()['basedir'],$metadata['file']);
    foreach($metadata['sizes'] as $img){
        $ext = pathinfo($img['file'], PATHINFO_EXTENSION);
        if(in_array($ext, array('jpg','png','jpeg'))){
            webp_webpconvert(wp_upload_dir()['path'],$img['file']);
        }
    }
    return $metadata;
}

/**
 * Remove webp images when media file remove from admin.
 *
 * @since       1.0.0
 * @param       string    $plugin_name      Webp Convert.
 * @param       string    $version          1.0.0.
 * */
function webp_action_maybe_delete( $id ) {
    $metadata = get_post_meta($id,'_wp_attachment_metadata',true);
    unlink(preg_replace('/(\.jpg|\.jpeg|\.png)/', '.webp', wp_upload_dir()['basedir'].'/'.$metadata['file']));
    foreach($metadata['sizes'] as $img){
        $ext = pathinfo($img['file'], PATHINFO_EXTENSION);
        if(in_array($ext, array('jpg','png','jpeg'))){
            unlink(preg_replace('/(\.jpg|\.jpeg|\.png)/', '.webp', wp_upload_dir()['path'].'/'.$img['file']));
        }
    }
};
add_action( 'delete_attachment', 'webp_action_maybe_delete', 10, 1 );