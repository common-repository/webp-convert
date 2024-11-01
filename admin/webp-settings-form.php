<?php
/**
 * WEBP settings form
 */
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<div class="wrap">
	<h1 class="wp-heading-inline"><?php echo _('WEBP Settings','webp-convert'); ?></h1>
<?php	
	if(isset($_POST['submit'])){
		if ( ! isset( $_POST['webp_settings'] ) 
		    || ! wp_verify_nonce( $_POST['webp_settings'], 'webp_settings' ) 
		) {
			?>
			<div class="updated notice-error">
				<p>
					<?php echo _('Something went wrong please try again.','webp-convert'); ?>
				</p>
			</div>
			<?php
		} else {
		   	$data = get_option('webp-convert-settings');
			$data['webp_scope'] = sanitize_text_field($_POST['webp_scope']);
			update_option( 'webp-convert-settings', $data, '', 'yes' );
			?>
			<div class="updated notice-success">
				<p>
					<?php echo _('Data saved.','webp-convert'); ?>
				</p>
			</div>
			<?php
		}
	}
	$data = get_option('webp-convert-settings');
	
	function directoryToArray($directory, $recursive=false) {
	    $array_items = array();
	    if ($handle = opendir($directory)) {
	        while (false !== ($file = readdir($handle))) {
	            if ($file != "." && $file != "..") {
	                if (is_dir($directory. "/" . $file)) {
	                    if($recursive) {
	                        $array_items = array_merge($array_items, directoryToArray($directory. "/" . $file, $recursive));
	                    }
	                } else {
	                    $ext = pathinfo($file, PATHINFO_EXTENSION);
						if(in_array($ext, array('jpg','png','jpeg'))){
							if(!file_exists($directory.'/'.str_replace($ext,'webp',$file))){
	                    		$array_items[] = array("directory"=>$directory, "file"=>$file);
							}
	                    }
	                }
	            }
	        }
	        closedir($handle);
	    }
	    return $array_items;
	}
	
	function removeWebpImages($directory, $recursive=false) {
	    $array_items = array();
	    if ($handle = opendir($directory)) {
	        while (false !== ($file = readdir($handle))) {
	            if ($file != "." && $file != "..") {
	                if (is_dir($directory. "/" . $file)) {
	                    if($recursive) {
	                        $array_items = array_merge($array_items, removeWebpImages($directory. "/" . $file, $recursive));
	                    }
	                } else {
	                    $ext = pathinfo($file, PATHINFO_EXTENSION);
						if(in_array($ext, array('webp'))){
							if(file_exists($directory.'/'.str_replace($ext,'jpg',$file))){
	                    		unlink($directory.'/'.$file);
	                    		$array_items[] = array("directory"=>$directory, "file"=>$file);
							}
							if(file_exists($directory.'/'.str_replace($ext,'png',$file))){
	                    		unlink($directory.'/'.$file);
	                    		$array_items[] = array("directory"=>$directory, "file"=>$file);
							}
							if(file_exists($directory.'/'.str_replace($ext,'jpeg',$file))){
	                    		unlink($directory.'/'.$file);
	                    		$array_items[] = array("directory"=>$directory, "file"=>$file);
							}
	                    }
	                }
	            }
	        }
	        closedir($handle);
	    }
	    return $array_items;
	}

	if(isset($_POST['convert'])){
		if($data['webp_scope']=='uploads'){
			$images = directoryToArray(UPLOADS,true);
			foreach($images as $image){
				webpconvert($image['directory'],$image['file']);
			}
		}
		if($data['webp_scope']=='themes'){
			$images = directoryToArray(get_template_directory(),true);
			foreach($images as $image){
				webpconvert($image['directory'],$image['file']);
			}
		}
		if($data['webp_scope']=='uploads,themes'){
			$images = directoryToArray(UPLOADS,true);
			foreach($images as $image){
				webpconvert($image['directory'],$image['file']);
			}
			$images = directoryToArray(get_template_directory(),true);
			foreach($images as $image){
				webpconvert($image['directory'],$image['file']);
			}
		}
	}

	if(isset($_POST['delete'])){
		removeWebpImages(UPLOADS,true);
		removeWebpImages(get_template_directory(),true);
	}
	?>
	<form class="form" method="post" action="">
		<?php wp_nonce_field( 'webp_settings', 'webp_settings' ); ?>
		<div class="form__linput">
			<label class="form__label" for="webp_scope"><?php echo _('Scope','webp-convert'); ?></label>
			<select name="webp_scope" id="webp_scope" class="webp_scope form__input">
				<option value="uploads" <?php echo esc_attr(($data['webp_scope'])=='uploads')?'selected':''; ?>><?php echo _('Uploads only','webp-convert'); ?></option>
				<option value="themes" <?php echo esc_attr(($data['webp_scope'])=='themes')?'selected':''; ?>><?php echo _('Themes only','webp-convert'); ?></option>
				<option value="uploads,themes" <?php echo esc_attr(($data['webp_scope'])=='uploads,themes')?'selected':''; ?>><?php echo _('Uploads and themes','webp-convert'); ?></option>
			</select>
		</div>
		<div class="form__linput">
			<input type="submit" name="submit" id="submit" class="submit primary-button form__button" value="<?php echo _('Save','webp-convert'); ?>">
		</div>
	</form>
</div>
<div class="wrap">
	<form class="form" method="post" action="">
		<?php wp_nonce_field( 'webp_settings', 'webp_settings' ); ?>
		<div class="form__linput">
			<?php sprintf('<p>%1$s <strong>%2$s</strong> %3$s</p>', _('After save settings click on','webp-convert'), _('Convert','webp-convert'), _('button for convert images jpg,jpeg and png to webp.','webp-convert') ); ?>
			<input type="submit" name="convert" id="submit" class="convert primary-button form__button" value="<?php echo _('Convert','webp-convert'); ?>">
		</div>
	</form>
</div>
<div class="wrap">
	<form class="form" method="post" action="">
		<?php wp_nonce_field( 'webp_settings', 'webp_settings' ); ?>
		<div class="form__linput">
			<?php sprintf('<p>%1$s <strong>%2$s</strong> %3$s</p>', _('If you want to remove converted images please click on ','webp-convert'), _('Delete','webp-convert'), _('button.','webp-convert') ); ?>			
			<input type="submit" name="delete" id="submit" class="delete primary-button form__button" value="<?php echo _('Delete','webp-convert'); ?>">
		</div>
	</form>
</div>