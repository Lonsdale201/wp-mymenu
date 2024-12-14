<?php
namespace HelloWP\HWMyMenu\App\Helper;

if (!defined('ABSPATH')) {
    exit;
}

class Dependency {
    /**
     * Check if AffiliateWP is active.
     *
     * @return bool True if AffiliateWP is active, false otherwise.
     */
    public static function is_affiliatewp_active() {
        return class_exists('Affiliate_WP');
    }

    /**
     * Check if WooCommerce Subscriptions is active.
     *
     * @return bool True if WooCommerce Subscriptions is active, false otherwise.
     */
    public static function is_woocommerce_subscriptions_active() {
        return class_exists('WC_Subscriptions');
    }

    /**
     * Check if WooCommerce Memberships is active.
     *
     * @return bool True if WooCommerce Memberships is active, false otherwise.
     */
    public static function is_woocommerce_membership_active() {
        return function_exists('wc_memberships');
    }
}
