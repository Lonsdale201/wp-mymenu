<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}

class Menu_Extensions {
    public function __construct() {
        add_action('init', array($this, 'register_menus'));
        add_action('wp_nav_menu_item_custom_fields', array($this, 'custom_menu_item_fields'), 10, 4);
        add_action('wp_update_nav_menu_item', array($this, 'save_custom_menu_item_fields'), 10, 2);
        add_filter('wp_nav_menu_objects', array($this, 'filter_nav_menu_items'), 10, 2);
    }

    public function register_menus() {
        register_nav_menu('dropdown-profile-menu', __('DropDown Profile Menu', 'my-profile-menu'));
    }

    public function custom_menu_item_fields($item_id, $item, $depth, $args) {
        if ($this->is_affiliatewp_active()) {
            $field_value = get_post_meta($item_id, '_mpm_affiliate_only', true);
            ?>
            <p class="description description-wide">
                <label for="edit-menu-item-affiliate-only-<?php echo $item_id; ?>">
                    <input type="checkbox" id="edit-menu-item-affiliate-only-<?php echo $item_id; ?>" name="menu-item-affiliate-only[<?php echo $item_id; ?>]" <?php checked($field_value, 'yes'); ?> />
                    <?php _e('Csak Affiliate Bejelentkezett Partnereknek', 'my-profile-menu'); ?>
                </label>
            </p>
            <?php
        }

        if ($this->is_woocommerce_subscriptions_active()) {
            $has_active_subscription_value = get_post_meta($item_id, '_mpm_has_active_subscription', true);
            ?>
            <p class="description description-wide">
                <label for="edit-menu-item-has-active-subscription-<?php echo $item_id; ?>">
                    <input type="checkbox" id="edit-menu-item-has-active-subscription-<?php echo $item_id; ?>" name="menu-item-has-active-subscription[<?php echo $item_id; ?>]" <?php checked($has_active_subscription_value, 'yes'); ?> />
                    <?php _e('Ha van aktív előfizetés', 'my-profile-menu'); ?>
                </label>
            </p>
            <?php
        }

        $visibility_value = get_post_meta($item_id, '_mpm_visibility', true);
        ?>
        <p class="description description-wide">
            <label><strong><?php _e('Menü Elem Láthatósága', 'my-profile-menu'); ?></strong></label><br>
            <label for="edit-menu-item-visibility-any-<?php echo $item_id; ?>">
                <input type="radio" id="edit-menu-item-visibility-any-<?php echo $item_id; ?>" name="menu-item-visibility[<?php echo $item_id; ?>]" value="any" <?php checked($visibility_value, 'any', true); ?> />
                <?php _e('Bárki', 'my-profile-menu'); ?>
            </label><br>
            <label for="edit-menu-item-visibility-logged-in-<?php echo $item_id; ?>">
                <input type="radio" id="edit-menu-item-visibility-logged-in-<?php echo $item_id; ?>" name="menu-item-visibility[<?php echo $item_id; ?>]" value="logged_in" <?php checked($visibility_value, 'logged_in'); ?> />
                <?php _e('Bejelentkezett', 'my-profile-menu'); ?>
            </label><br>
            <label for="edit-menu-item-visibility-logged-out-<?php echo $item_id; ?>">
                <input type="radio" id="edit-menu-item-visibility-logged-out-<?php echo $item_id; ?>" name="menu-item-visibility[<?php echo $item_id; ?>]" value="logged_out" <?php checked($visibility_value, 'logged_out'); ?> />
                <?php _e('Kijelentkezett', 'my-profile-menu'); ?>
            </label>
        </p>
        <?php
    }

    public function save_custom_menu_item_fields($menu_id, $menu_item_db_id) {
        if ($this->is_affiliatewp_active()) {
            $affiliate_only = isset($_POST['menu-item-affiliate-only'][$menu_item_db_id]) ? 'yes' : 'no';
            update_post_meta($menu_item_db_id, '_mpm_affiliate_only', $affiliate_only);
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
    }

    public function filter_nav_menu_items($menu_items, $args) {
        if (!empty($menu_items)) {
            $menu_item_ids = wp_list_pluck($menu_items, 'ID');
            $meta_query = [
                'relation' => 'OR',
                ['key' => '_mpm_affiliate_only'],
                ['key' => '_mpm_visibility'],
                ['key' => '_mpm_has_active_subscription']
            ];
            $meta_results = new WP_Query([
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
                ];
            }
        }
    
        if(is_user_logged_in()) {
            $user = wp_get_current_user();
            $is_affiliate = $this->is_affiliatewp_active() ? affwp_is_affiliate($user->ID) : false;
            
            foreach ($menu_items as $key => $menu_item) {
                $meta = $menu_item_metas[$menu_item->ID] ?? null;
                if (!$meta) continue;
    
                $is_affiliate_only = $meta['affiliate_only'];
                $visibility = $meta['visibility'];
                $has_active_subscription = $meta['has_active_subscription'];
    
                $has_valid_subscription = false;
                if($has_active_subscription == 'yes' && $this->is_woocommerce_subscriptions_active()) {
                    $user_subscriptions = wcs_get_users_subscriptions($user->ID);
                    foreach($user_subscriptions as $subscription) {
                        if($subscription->has_status('active')) {
                            $has_valid_subscription = true;
                            break;
                        }
                    }
                }
    
                if (($is_affiliate_only == 'yes' && !$is_affiliate) || 
                    ($visibility == 'logged_in' && !is_user_logged_in()) || 
                    ($visibility == 'logged_out' && is_user_logged_in()) || 
                    ($has_active_subscription == 'yes' && !$has_valid_subscription)) {
                    unset($menu_items[$key]);
                }
            }
        } else {
            foreach ($menu_items as $key => $menu_item) {
                $meta = $menu_item_metas[$menu_item->ID] ?? null;
                if (!$meta) continue;
    
                $visibility = $meta['visibility'];
                $has_active_subscription = $meta['has_active_subscription'];
    
                if (($visibility == 'logged_in') || 
                    ($has_active_subscription == 'yes')) {
                    unset($menu_items[$key]);
                }
            }
        }
        return $menu_items;
    
    }

    private function is_affiliatewp_active() {
        return class_exists('Affiliate_WP');
    }

    private function is_woocommerce_subscriptions_active() {
        return class_exists('WC_Subscriptions');
    }
}
