<?php
/**
 * Plugin Name: Simple My Menu
 * Description: MyAccount Dropdown menu and some extras.
 * Version: 3.0
 * Author: Soczó Kristóf
 * Author URI: https://hellowp.io/hu/
 * Plugin URI: https://github.com/Lonsdale201/wp-mymenu
 * Text Domain: hw-my-menu
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 8.0
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace HelloWP\HWMyMenu;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define('HWMYMENU_PATH', plugin_dir_path(__FILE__));
define('HWMYMENU_URL', plugin_dir_url(__FILE__));
define('HWMYMENU_ADMIN_ASSETS', HWMYMENU_URL . 'app/admin/assets/');
define('HWMYMENU_FRONTEND_ASSETS', HWMYMENU_URL . 'app/frontend/assets/');
define('HWMYMENU_FRONTEND_TEMPLATES', HWMYMENU_PATH . 'app/frontend/templates/');


require_once __DIR__ . '/vendor/autoload.php';
require dirname(__FILE__) . '/plugin-update-checker/plugin-update-checker.php';

use YahnisElsts\PluginUpdateChecker\v5p0\PucFactory;

/**
 * The main class for the My Menu plugin
 */
final class HWMyMenu {

    const MINIMUM_PHP_VERSION = '8.0';
    const MINIMUM_WORDPRESS_VERSION = '6.0';

    private static $_instance = null;

    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct() {
        $this->add_hooks();
    }

    private function add_hooks() {
        add_action('init', [$this, 'load_plugin_textdomain'], -999);
        add_action('plugins_loaded', [$this, 'on_plugins_loaded']);
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), [$this, 'add_settings_link']);
    }

    public function load_plugin_textdomain() {
        if ( version_compare( $GLOBALS['wp_version'], '6.7', '<' ) ) {
            load_plugin_textdomain( 'hw-my-menu', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
        } else {
            load_textdomain( 'hw-my-menu', HWMYMENU_PATH . 'languages/hw-my-menu-' . determine_locale() . '.mo' );
        }
    }

    public function add_settings_link($links) {
        $settings_link = '<a href="' . esc_url(admin_url('admin.php?page=hw-my-menu-settings')) . '">' . __('Settings', 'hw-my-menu') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    public function on_plugins_loaded() {
        if ( ! $this->is_compatible() ) {
            return;
        }

        \HelloWP\HWMyMenu\App\Admin\AdminSettings::get_instance();
        \HelloWP\HWMyMenu\App\Admin\UserMeta::get_instance();
        \HelloWP\HWMyMenu\App\Admin\MenuExtensions::init();
        \HelloWP\HWMyMenu\App\Admin\UserBulkActions::init();
        \HelloWP\HWMyMenu\App\Frontend\Monogram::get_instance();

        $myUpdateChecker = PucFactory::buildUpdateChecker(
            'https://plugin-uodater.alex.hellodevs.dev/plugins/wp-mymenu-main.json',
            __FILE__,
            'wp-mymenu-main'
        );
    }

    public function is_compatible() {
        if ( version_compare(get_bloginfo('version'), self::MINIMUM_WORDPRESS_VERSION, '<') ) {
            add_action('admin_notices', [$this, 'admin_notice_minimum_wordpress_version']);
            return false;
        }

        if ( version_compare(PHP_VERSION, self::MINIMUM_PHP_VERSION, '<') ) {
            add_action('admin_notices', [$this, 'admin_notice_minimum_php_version']);
            return false;
        }

        return true;
    }

    public function admin_notice_minimum_wordpress_version() {
        if ( ! current_user_can('manage_options') ) {
            return;
        }
        echo '<div class="notice notice-warning is-dismissible"><p>';
        echo sprintf(__('This plugin requires WordPress %s or later. Please update WordPress.', 'hw-my-menu'), self::MINIMUM_WORDPRESS_VERSION);
        echo '</p></div>';
    }

    public function admin_notice_minimum_php_version() {
        if ( ! current_user_can('manage_options') ) {
            return;
        }
        echo '<div class="notice notice-warning is-dismissible"><p>';
        echo sprintf(__('This plugin requires PHP %s or later. Please update your PHP version.', 'hw-my-menu'), self::MINIMUM_PHP_VERSION);
        echo '</p></div>';
    }
}

HWMyMenu::instance();
