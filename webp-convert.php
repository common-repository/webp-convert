<?php
/**
 *
 * @link              #
 * @since             1.0.0
 * @package           webp_convert
 *
 * @wordpress-plugin
 * Plugin Name:       Webp Convert
 * Plugin URI:        https://profiles.wordpress.org/maliyaumeshl/
 * Description:       All jpg,jpeg and png images conver into webp formate.
 * Version:           1.0.0
 * Author:            maliyaumeshl
 * Author URI:        https://maliyaumeshl.wordpress.com/
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       webp-convert
 * Domain Path:       /languages
 */

if (!defined('ABSPATH')) {
		exit;
}

define('WEBP_URL', plugin_dir_url(__FILE__));
define('WEBP_PUBLIC_URL', WEBP_URL . 'public/');


/**
 * Activation hook
 *
 * @since       1.0.0
 * @param       string    $plugin_name          Webp Convert.
 * @param       string    $version              1.0.0.
 */

register_activation_hook( __FILE__, 'webp_activation' );
function webp_activation() {
    // Activation rules here
}

/**
 * Deactivation hook
 *
 * @since       1.0.0
 * @param       string    $plugin_name          Webp Convert.
 * @param       string    $version              1.0.0.
 */
register_deactivation_hook( __FILE__, 'webp_deactivation' );
function webp_deactivation() {
  // Deactivation rules here
}

/**
 * Init Hook for plugin
 * @since       1.0.0
 * @param       string    $plugin_name          Webp Convert.
 * @param       string    $version              1.0.0.
 * */
add_action( 'init', 'webp_init' );
function webp_init() {
    include_once plugin_dir_path( __FILE__ ).'admin/admin-menu.php';
    /*include_once plugin_dir_path( __FILE__ ).'admin/webp-settings-form.php';*/
}