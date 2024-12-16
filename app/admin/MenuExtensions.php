<?php

namespace HelloWP\HWMyMenu\App\Admin;
use HelloWP\HWMyMenu\App\Helper\SettingsConfig;
use HelloWP\HWMyMenu\App\Helper\Dependency;

if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}

class MenuExtensions {
    private static $instance = null;

    private function __construct() {
        $this->register_hooks();
    }

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function register_hooks() {
        add_action('init', [$this, 'register_menus']);
        add_action('wp_nav_menu_item_custom_fields', [$this, 'custom_menu_item_fields'], 10, 4);
        add_action('wp_update_nav_menu_item', [$this, 'save_custom_menu_item_fields'], 10, 2);
        add_filter('wp_nav_menu_objects', [$this, 'filter_nav_menu_items'], 10, 2);
        add_filter('walker_nav_menu_start_el', [$this, 'add_icon_to_menu_item'], 10, 4);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
    }

    public function register_menus() {
        register_nav_menu('dropdown-profile-menu', __('DropDown Profile Menu', 'my-profile-menu'));
    }

    public function enqueue_admin_scripts() {
        global $pagenow;
        if ($pagenow === 'nav-menus.php') {
            wp_enqueue_style('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css');
            wp_enqueue_script('select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', ['jquery'], null, true);
        }
    }

    public function add_icon_to_menu_item($item_output, $item, $depth, $args) {
        $enable_icon_menu = SettingsConfig::get('mymenu_enable_icon_menu', false);
    
        $icon_html = '';
        $badge_html = '';
        $description_html = ''; 
    
        // Ikon hozzáadása
        $icon_class = get_post_meta($item->ID, '_mpm_menu_icon', true);
        if ($enable_icon_menu && $icon_class) {
            $icon_html = '<i class="' . esc_attr($icon_class) . '"></i> ';
        }
    
        // Badge hozzáadása
        $badge_text = get_post_meta($item->ID, '_mpm_menu_badge', true);
        if (!empty($badge_text)) {
            $badge_html = '<span class="hw-mbadge">' . esc_html($badge_text) . '</span>';
        }
    
        // Csak a "dropdown-profile-menu" helyre alkalmazzuk a leírást
        if (isset($args->theme_location) && $args->theme_location === 'dropdown-profile-menu') {
            if (!empty($item->description)) {
                $description_html = '<span class="hw-menu-description">' . esc_html($item->description) . '</span>';
            }
        }
    
        // HTML összeállítása
        $item_output = preg_replace(
            '/(<a\s.*?>)(.*?)(<\/a>)/i',
            '$1' . $icon_html . '$2' . $badge_html . '$3' . $description_html,
            $item_output
        );
    
        return $item_output;
    }
    
    
    
    
    
    public function custom_menu_item_fields($item_id, $item, $depth, $args) {
        global $wp_roles;
        $roles = $wp_roles->roles;
        $selected_role = get_post_meta($item_id, '_mpm_user_role', true);
        $badge_text = get_post_meta($item_id, '_mpm_menu_badge', true);
    
        echo '<div class="hw-mymenu-container">';
    
        // My Menu Extra Header
        echo '<div class="hw-mymenu-header">';
        echo '<span class="hw-mymenu-separator"></span>';
        echo '<span class="hw-mymenu-title">' . __('My Menu Extra', 'my-profile-menu') . '</span>';
        echo '<span class="hw-mymenu-separator"></span>';
        echo '</div>';
    
        echo '<div class="hw-mymenu-fields">';
    
        // Badge Text Field
        echo '<div class="hw-mymenu-field">';
        echo '<label for="edit-menu-item-badge-' . esc_attr($item_id) . '">' . __('Menu Badge Text', 'my-profile-menu') . '</label>';
        echo '<input type="text" id="edit-menu-item-badge-' . esc_attr($item_id) . '" name="menu-item-badge[' . esc_attr($item_id) . ']" value="' . esc_attr($badge_text) . '" />';
        echo '<span class="hw-mymenu-description">' . __('Optional badge text displayed on the right.', 'my-profile-menu') . '</span>';
        echo '</div>';
    
        // AffiliateWP Field
        if ($this->is_affiliatewp_active()) {
            $field_value = get_post_meta($item_id, '_mpm_affiliate_only', true);
            echo '<div class="hw-mymenu-field">';
            echo '<label for="edit-menu-item-affiliate-only-' . esc_attr($item_id) . '">';
            echo '<input type="checkbox" id="edit-menu-item-affiliate-only-' . esc_attr($item_id) . '" name="menu-item-affiliate-only[' . esc_attr($item_id) . ']" ' . checked($field_value, 'yes', false) . ' />';
            echo __('Affiliate Partner Only (AffiliateWP)', 'my-profile-menu');
            echo '</label>';
            echo '</div>';
        }
    
        // WooCommerce Subscriptions Field
        if ($this->is_woocommerce_subscriptions_active()) {
            $has_active_subscription_value = get_post_meta($item_id, '_mpm_has_active_subscription', true);
            echo '<div class="hw-mymenu-field">';
            echo '<label for="edit-menu-item-has-active-subscription-' . esc_attr($item_id) . '">';
            echo '<input type="checkbox" id="edit-menu-item-has-active-subscription-' . esc_attr($item_id) . '" name="menu-item-has-active-subscription[' . esc_attr($item_id) . ']" ' . checked($has_active_subscription_value, 'yes', false) . ' />';
            echo __('Only if user has active subscriptions (Woo Subscriptions)', 'my-profile-menu');
            echo '</label>';
            echo '</div>';
        }
    
        // Icon Field
        $enable_icon_menu = SettingsConfig::get('mymenu_enable_icon_menu', false);
        if ($enable_icon_menu) {
            $icon_class = get_post_meta($item_id, '_mpm_menu_icon', true);
            echo '<div class="hw-mymenu-field">';
            echo '<label for="edit-menu-item-icon-' . esc_attr($item_id) . '">' . __('Icon Class', 'my-profile-menu') . '</label>';
            echo '<input type="text" id="edit-menu-item-icon-' . esc_attr($item_id) . '" name="menu-item-icon[' . esc_attr($item_id) . ']" value="' . esc_attr($icon_class) . '" />';
            echo '<span class="hw-mymenu-description">' . __('Example: fa fa-home', 'my-profile-menu') . '</span>';
            echo '</div>';
        }
    
        // User Role Field
        echo '<div class="hw-mymenu-field">';
        echo '<label for="edit-menu-item-role-' . esc_attr($item_id) . '">' . __('User Role', 'my-profile-menu') . '</label>';
        echo '<select class="mpm-select2" id="edit-menu-item-role-' . esc_attr($item_id) . '" name="menu-item-role[' . esc_attr($item_id) . ']">';
        echo '<option value="">' . __('Select a role', 'my-profile-menu') . '</option>';
        foreach ($roles as $role_key => $role) {
            echo '<option value="' . esc_attr($role_key) . '" ' . selected($selected_role, $role_key, false) . '>' . esc_html($role['name']) . '</option>';
        }
        echo '</select>';
        echo '<span class="hw-mymenu-description">' . __('Assign a user role to this menu item.', 'my-profile-menu') . '</span>';
        echo '</div>';

        // Device Type Field
        $device_types = get_post_meta($item_id, '_mpm_device_type', true) ?: [];
        $available_devices = [
            'mobile' => __('Mobile', 'my-profile-menu'),
            'tablet' => __('Tablet', 'my-profile-menu'),
            'desktop' => __('Desktop', 'my-profile-menu'),
        ];

        echo '<div class="hw-mymenu-field">';
        echo '<label for="edit-menu-item-device-type-' . esc_attr($item_id) . '">' . __('Device Type', 'my-profile-menu') . '</label>';
        echo '<select class="mpm-select2" multiple="multiple" id="edit-menu-item-device-type-' . esc_attr($item_id) . '" name="menu-item-device-type[' . esc_attr($item_id) . '][]">';
        foreach ($available_devices as $device_key => $device_label) {
            $selected = in_array($device_key, (array) $device_types) ? 'selected' : '';
            echo '<option value="' . esc_attr($device_key) . '" ' . esc_attr($selected) . '>' . esc_html($device_label) . '</option>';
        }
        echo '</select>';
        echo '<span class="hw-mymenu-description">' . __('Restrict menu item visibility to specific devices. Leave empty for all devices.', 'my-profile-menu') . '</span>';
        echo '</div>';

    
        // Visibility Field
        $visibility_value = get_post_meta($item_id, '_mpm_visibility', true);
        echo '<div class="hw-mymenu-field">';
        echo '<label>' . __('Menu visibility', 'my-profile-menu') . '</label>';
        echo '<label><input type="radio" name="menu-item-visibility[' . esc_attr($item_id) . ']" value="any" ' . checked($visibility_value, 'any', false) . ' />' . __('Anybody', 'my-profile-menu') . '</label>';
        echo '<label><input type="radio" name="menu-item-visibility[' . esc_attr($item_id) . ']" value="logged_in" ' . checked($visibility_value, 'logged_in', false) . ' />' . __('Logged-in', 'my-profile-menu') . '</label>';
        echo '<label><input type="radio" name="menu-item-visibility[' . esc_attr($item_id) . ']" value="logged_out" ' . checked($visibility_value, 'logged_out', false) . ' />' . __('Logged-out', 'my-profile-menu') . '</label>';
        echo '</div>';
    
        // Woo Memberships Field
        if ($this->is_woocommerce_membership_active()) {
            $memberships = wc_memberships_get_membership_plans();
            $selected_memberships = get_post_meta($item_id, '_mpm_membership', true);
            $membership_relation = get_post_meta($item_id, '_mpm_membership_relation', true) ?: 'AND';
    
            echo '<div class="hw-mymenu-field">';
            echo '<label for="edit-menu-item-membership-' . esc_attr($item_id) . '">' . __('Woo Memberships', 'my-profile-menu') . '</label>';
            echo '<select class="mpm-select2" multiple="multiple" id="edit-menu-item-membership-' . esc_attr($item_id) . '" name="menu-item-membership[' . esc_attr($item_id) . '][]">';
            foreach ($memberships as $membership) {
                echo '<option value="' . esc_attr($membership->get_id()) . '" ' . selected(in_array($membership->get_id(), (array) $selected_memberships), true, false) . '>' . esc_html($membership->get_name()) . '</option>';
            }
            echo '</select>';
            echo '<span class="hw-mymenu-description">' . __('Assign memberships to this menu item.', 'my-profile-menu') . '</span>';
            echo '</div>';
    
            echo '<div class="hw-mymenu-field">';
            echo '<label>' . __('Membership Relation', 'my-profile-menu') . '</label>';
            echo '<label><input type="radio" name="menu-item-membership-relation[' . esc_attr($item_id) . ']" value="AND" ' . checked($membership_relation, 'AND', false) . ' />' . __('AND', 'my-profile-menu') . '</label>';
            echo '<label><input type="radio" name="menu-item-membership-relation[' . esc_attr($item_id) . ']" value="OR" ' . checked($membership_relation, 'OR', false) . ' />' . __('OR', 'my-profile-menu') . '</label>';
            echo '</div>';
        }
    
        echo '</div>'; // Close fields container
        echo '</div>'; // Close main container
    }
    

    public function save_custom_menu_item_fields($menu_id, $menu_item_db_id) {

        if (isset($_POST['menu-item-badge'][$menu_item_db_id])) {
            $badge_text = sanitize_text_field($_POST['menu-item-badge'][$menu_item_db_id]);
            update_post_meta($menu_item_db_id, '_mpm_menu_badge', $badge_text);
        } else {
            delete_post_meta($menu_item_db_id, '_mpm_menu_badge');
        }


        if ($this->is_affiliatewp_active()) {
            $affiliate_only = isset($_POST['menu-item-affiliate-only'][$menu_item_db_id]) ? 'yes' : 'no';
            update_post_meta($menu_item_db_id, '_mpm_affiliate_only', $affiliate_only);
        }

        if (isset($_POST['menu-item-icon'][$menu_item_db_id])) {
            $icon_class = sanitize_text_field($_POST['menu-item-icon'][$menu_item_db_id]);
            update_post_meta($menu_item_db_id, '_mpm_menu_icon', $icon_class);
        } else {
            delete_post_meta($menu_item_db_id, '_mpm_menu_icon');
        }
        

        if (isset($_POST['menu-item-role'][$menu_item_db_id])) {
            $role = sanitize_text_field($_POST['menu-item-role'][$menu_item_db_id]);
            update_post_meta($menu_item_db_id, '_mpm_user_role', $role);
        } else {
            delete_post_meta($menu_item_db_id, '_mpm_user_role');
        }        

        if (isset($_POST['menu-item-device-type'][$menu_item_db_id])) {
            $device_types = array_map('sanitize_text_field', $_POST['menu-item-device-type'][$menu_item_db_id]);
            update_post_meta($menu_item_db_id, '_mpm_device_type', $device_types);
        } else {
            delete_post_meta($menu_item_db_id, '_mpm_device_type');
        }
        

        if ($this->is_woocommerce_subscriptions_active()) {
            $has_active_subscription = isset($_POST['menu-item-has-active-subscription'][$menu_item_db_id]) ? 'yes' : 'no';
            update_post_meta($menu_item_db_id, '_mpm_has_active_subscription', $has_active_subscription);
        }

        if (isset($_POST['menu-item-visibility'][$menu_item_db_id])) {
            $visibility = sanitize_text_field($_POST['menu-item-visibility'][$menu_item_db_id]);
            update_post_meta($menu_item_db_id, '_mpm_visibility', $visibility);
        } else {
            delete_post_meta($menu_item_db_id, '_mpm_visibility');
        }

        if ($this->is_woocommerce_membership_active()) {
        $membership_data = isset($_POST['menu-item-membership'][$menu_item_db_id]) ? (array) $_POST['menu-item-membership'][$menu_item_db_id] : [];
        update_post_meta($menu_item_db_id, '_mpm_membership', $membership_data);

        $membership_relation = isset($_POST['menu-item-membership-relation'][$menu_item_db_id]) ? $_POST['menu-item-membership-relation'][$menu_item_db_id] : 'AND';
        update_post_meta($menu_item_db_id, '_mpm_membership_relation', $membership_relation);
    }

    }

    public function filter_nav_menu_items($menu_items, $args) {
        if (empty($menu_items)) {
            return $menu_items;
        }
    
        $menu_item_ids = wp_list_pluck($menu_items, 'ID');
        $meta_query = [
            'relation' => 'OR',
            ['key' => '_mpm_affiliate_only'],
            ['key' => '_mpm_visibility'],
            ['key' => '_mpm_has_active_subscription'],
            ['key' => '_mpm_membership'],
            ['key' => '_mpm_device_type'],
            ['key' => '_mpm_user_role']
        ];
        $meta_results = new \WP_Query([
            'post_type' => 'nav_menu_item',
            'posts_per_page' => -1,
            'post__in' => $menu_item_ids,
            'fields' => 'ids',
            'meta_query' => $meta_query
        ]);
    
        $menu_item_metas = [];
        foreach ($meta_results->posts as $post_id) {
            $menu_item_metas[$post_id] = [
                'affiliate_only' => get_post_meta($post_id, '_mpm_affiliate_only', true),
                'visibility' => get_post_meta($post_id, '_mpm_visibility', true),
                'has_active_subscription' => get_post_meta($post_id, '_mpm_has_active_subscription', true),
                'membership' => get_post_meta($post_id, '_mpm_membership', true),
                'device_type' => get_post_meta($post_id, '_mpm_device_type', true),
                'user_role' => get_post_meta($post_id, '_mpm_user_role', true)
            ];
        }
    
        $user = wp_get_current_user();
        $is_affiliate = $this->is_affiliatewp_active() && affwp_is_affiliate($user->ID);
        $deviceDetector = \HelloWP\HWMyMenu\HWMyMenu::getDeviceDetector();
    
        foreach ($menu_items as $key => $menu_item) {
            $meta = $menu_item_metas[$menu_item->ID] ?? null;
            if (!$meta) {
                continue;
            }
    
            // AffiliateWP 
            if ($meta['affiliate_only'] === 'yes' && !$is_affiliate) {
                unset($menu_items[$key]);
                continue;
            }
    
            // user role
            $menu_role = $meta['user_role'] ?? null;
            if ($menu_role && !in_array($menu_role, $user->roles)) {
                unset($menu_items[$key]);
                continue;
            }
    
            // Device 
            $allowed_devices = $meta['device_type'] ?? [];
            if (!empty($allowed_devices)) {
                $is_device_allowed = (
                    ($deviceDetector->isMobile() && in_array('mobile', $allowed_devices)) ||
                    ($deviceDetector->isTablet() && in_array('tablet', $allowed_devices)) ||
                    ($deviceDetector->isDesktop() && in_array('desktop', $allowed_devices))
                );
    
                if (!$is_device_allowed) {
                    unset($menu_items[$key]);
                    continue;
                }
            }
    
            // User status
            if ($meta['visibility'] === 'logged_in' && !is_user_logged_in()) {
                unset($menu_items[$key]);
                continue;
            }
            if ($meta['visibility'] === 'logged_out' && is_user_logged_in()) {
                unset($menu_items[$key]);
                continue;
            }
    
            // Subs  
            $has_valid_subscription = $this->check_subscription_status($user, $meta['has_active_subscription']);
            if ($meta['has_active_subscription'] === 'yes' && !$has_valid_subscription) {
                unset($menu_items[$key]);
                continue;
            }
    
            // Membership 
            $has_membership = $this->check_membership_status($user, $meta['membership'], $menu_item->ID);
            if (!$has_membership) {
                unset($menu_items[$key]);
                continue;
            }
        }
    
        return $menu_items;
    }
    
    
    private function check_subscription_status($user, $has_active_subscription) {
        if ($has_active_subscription !== 'yes' || !$this->is_woocommerce_subscriptions_active()) {
            return false;
        }
        $user_subscriptions = wcs_get_users_subscriptions($user->ID);
        foreach ($user_subscriptions as $subscription) {
            if ($subscription->has_status('active')) {
                return true;
            }
        }
        return false;
    }
    
    private function check_membership_status($user, $membership_ids, $menu_item_id) {
        if (empty($membership_ids) || !$this->is_woocommerce_membership_active()) {
            return true; 
        }
    
        $membership_relation = get_post_meta($menu_item_id, '_mpm_membership_relation', true) ?: 'AND';
        $has_valid_membership = $membership_relation === 'OR' ? false : true;
    
        foreach ($membership_ids as $membership_id) {
            $membership = wc_memberships_get_user_membership($user->ID, $membership_id);
    
            if ($membership && $membership->has_status('active')) {
                if ($membership_relation === 'OR') {
                    return true; 
                }
            } else {
                if ($membership_relation === 'AND') {
                    return false; 
                }
            }
        }
    
        return $has_valid_membership;
    }
    
    
    private function should_remove_menu_item($meta, $is_affiliate, $has_valid_subscription, $has_membership) {
       
        if ($meta['affiliate_only'] === 'yes' && !$this->is_affiliatewp_active() && !$is_affiliate) {
            return false; 
        }
    
        if ($meta['has_active_subscription'] === 'yes' && !$this->is_woocommerce_subscriptions_active() && !$has_valid_subscription) {
            return false; 
        }
    
        if ($meta['visibility'] === 'logged_in' && !is_user_logged_in()) {
            return true;
        }
    
        if ($meta['visibility'] === 'logged_out' && is_user_logged_in()) {
            return true;
        }
    
        if ($meta['has_active_subscription'] === 'yes' && !$has_valid_subscription) {
            return true;
        }
    
        if (!$has_membership) {
            return true;
        }
    
        return false;
    }
    
    

    private function is_affiliatewp_active() {
        return class_exists('Affiliate_WP');
    }

    private function is_woocommerce_subscriptions_active() {
        return class_exists('WC_Subscriptions');
    }

    private function is_woocommerce_membership_active() {
        return function_exists('wc_memberships');
    }    
}
