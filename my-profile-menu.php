<?php
/*
Plugin Name: HelloWP! | My Profile Menu
Plugin URI: https://github.com/Lonsdale201/wp-mymenu
Description: MyAccount Dropdown menu and extras.
Version: 2.1
Author: Soczó Kristóf
Author URI: https://hellowp.io/hu/
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}

require dirname(__FILE__) . '/plugin-update-checker/plugin-update-checker.php';

use YahnisElsts\PluginUpdateChecker\v5p0\PucFactory;

define('MPM_PATH', plugin_dir_path(__FILE__));
define('MPM_URL', plugin_dir_url(__FILE__));

// CSS és JS fájlok beillesztése
function mpm_enqueue_assets() {
    wp_enqueue_style('mpm-style', MPM_URL . 'assets/css/my-profile.css');

    if ( is_user_logged_in() ) {
        wp_enqueue_script('mpm-script', MPM_URL . 'assets/js/my-profile.js', array('jquery'), '1.0.0', true);
    }
}
add_action('wp_enqueue_scripts', 'mpm_enqueue_assets');

$myUpdateChecker = PucFactory::buildUpdateChecker(
    'https://plugin-uodater.alex.hellodevs.dev/plugins/wp-mymenu-main.json',
    __FILE__,
    'wp-mymenu-main'
);

function mpm_register_menu() {
    register_nav_menu('dropdown-profile-menu', __('DropDown Profile Menu', 'my-profile-menu'));
}
add_action('init', 'mpm_register_menu');

include_once plugin_dir_path( __FILE__ ) . 'includes/class-mymenu-settings.php';
include_once 'includes/class-monogram.php';
include_once 'includes/class-menu-extensions.php';

function mpm_add_settings_link( $links ) {
    $settings_link = '<a href="' . admin_url( 'options-general.php?page=mymenu-settings' ) . '">' . __('Settings', 'textdomain') . '</a>';
    array_unshift( $links, $settings_link );
    return $links;
}

add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'mpm_add_settings_link' );

new Monogram();
new Menu_Extensions();



