<?php
namespace HelloWP\HWMyMenu\App\Frontend;

use HelloWP\HWMyMenu\App\Services\MonogramGen;
use HelloWP\HWMyMenu\App\Admin\UserMeta;
use HelloWP\HWMyMenu\App\Helper\SettingsConfig;
use HelloWP\HWMyMenu\App\Helper\Dependency;

if (!defined('ABSPATH')) {
    exit;
}

class Monogram {
    private static $instance = null;

    /**
     * Nyomon követjük, hogy egyszer már betöltöttük-e az asset-eket.
     * A CSS betöltés mindig megtörténik ha a shortcode renderelődik,
     * a JS viszont csak bejelentkezett felhasználóknál töltődik be.
     */
    private $assets_enqueued = false;

    private function __construct() {
        add_shortcode('user_monogram', [$this, 'render_shortcode']);
        // Már nem töltjük be itt a script és stílus regisztrációt, csak regisztrálunk, a beillesztés rendernél történik.
        add_action('wp_enqueue_scripts', [$this, 'register_assets'], 20);
    }

    public static function get_instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function register_assets() {
        // Itt csak regisztrálunk, de nem enqueue-olunk
        wp_register_style('hw-my-menu-style', HWMYMENU_FRONTEND_ASSETS . 'my-profile.css', [], '1.0.0');
        wp_register_script('hw-my-menu-script', HWMYMENU_FRONTEND_ASSETS . 'my-profile.js', ['jquery'], '1.0.0', true);
    }

    public function render_shortcode($atts = []) {
        // Ha a shortcode megjelenik, akkor CSS mindenképpen kell:
        wp_enqueue_style('hw-my-menu-style');
    
        // Ha a felhasználó be van jelentkezve, akkor betöltjük a JS-t is
        if (is_user_logged_in()) {
            wp_enqueue_script('hw-my-menu-script');
        }
    
        // Itt jelezzük, hogy az asset-ek már be lettek húzva a shortcode-hoz
        $this->assets_enqueued = true;
    
        $atts = shortcode_atts([
            'theme' => 'dark', // Alapértelmezett téma
        ], $atts, 'user_monogram');
    
        $theme_class = 'hw-theme-' . sanitize_html_class($atts['theme']);
    
        $user = is_user_logged_in() ? wp_get_current_user() : null;
        $monogram = null;
        $display_name = null;
        $active_subs = false; 
        $use_gravatar = SettingsConfig::get('mymenu_show_gravatar', 'no') === 'yes'; // Gravatar beállítás
    
        if ($user) {
            $monogram = get_user_meta($user->ID, UserMeta::MONOGRAM_META_KEY, true);
            if (empty($monogram)) {
                $monogram = MonogramGen::generate($user);
            }
    
            $dropdown_label = SettingsConfig::get('mymenu_dropdown_label', 'nickname');
            $custom_label = SettingsConfig::get('mymenu_custom_label', '');
    
            if ($dropdown_label === 'custom') {
                $display_name = $custom_label;
            } else {
                switch ($dropdown_label) {
                    case 'first_name':
                        $display_name = $user->first_name;
                        break;
                    case 'last_name':
                        $display_name = $user->last_name;
                        break;
                    case 'email':
                        $display_name = strstr($user->user_email, '@', true);
                        break;
                    default:
                        $display_name = $user->nickname;
                }
            }
    
            if (Dependency::is_woocommerce_subscriptions_active() && SettingsConfig::get('mymenu_show_active_subs', false)) {
                $subscriptions = wcs_get_users_subscriptions($user->ID);
                foreach ($subscriptions as $subscription) {
                    if ($subscription->has_status('active')) {
                        $active_subs = true;
                        break;
                    }
                }
            }
    
            // Gravatar 
            if ($use_gravatar) {
                $email_hash = md5(strtolower(trim($user->user_email)));
                $gravatar_url = "https://www.gravatar.com/avatar/$email_hash?d=404";
    
                $response = wp_remote_head($gravatar_url);
                if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
                    $gravatar_exists = true;
                } else {
                    $gravatar_exists = false;
                }
            }
        } else {
            $redirect_url = SettingsConfig::get('mymenu_redirect_url', site_url('/login'));
            $button_text = SettingsConfig::get('mymenu_text_field', '');
            $icon_class = SettingsConfig::get('mymenu_icon_field', ''); 
        }
    
        ob_start();
        include HWMYMENU_FRONTEND_TEMPLATES . 'monogram-template.php';
        return ob_get_clean();
    }
    

}
