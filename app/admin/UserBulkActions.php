<?php
namespace HelloWP\HWMyMenu\App\Admin;

use HelloWP\HWMyMenu\App\Services\MonogramGen;

if (!defined('ABSPATH')) {
    exit;
}

class UserBulkActions {

    /**
     * Initialize the class and register the necessary hooks.
     */
    public static function init() {
        add_filter('bulk_actions-users', [self::class, 'register_bulk_action']);
        add_filter('handle_bulk_actions-users', [self::class, 'handle_bulk_action'], 10, 3);
        add_action('admin_notices', [self::class, 'admin_notices']);
    }

    /**
     * Register a custom bulk action for regenerating monograms in the users table.
     *
     * @param array $bulk_actions The list of available bulk actions.
     * @return array The modified bulk actions list.
     */
    public static function register_bulk_action($bulk_actions) {
        $bulk_actions['regenerate_monograms'] = __('Regenerate Monograms', 'my-profile-menu');
        return $bulk_actions;
    }

    /**
     * Handle the custom bulk action for regenerating monograms.
     *
     * @param string $redirect_to The URL to redirect to after processing the action.
     * @param string $action The name of the bulk action.
     * @param array $user_ids The IDs of the selected users.
     * @return string The updated URL with result parameters.
     */
    public static function handle_bulk_action($redirect_to, $action, $user_ids) {
        if ($action !== 'regenerate_monograms') {
            return $redirect_to;
        }

        $processed = 0;
        foreach ($user_ids as $user_id) {
            $user = get_userdata($user_id);
            if ($user) {
                MonogramGen::generate($user);
                $processed++;
            }
        }

        $redirect_to = add_query_arg([
            'bulk_regenerate_monograms' => $processed,
        ], $redirect_to);

        return $redirect_to;
    }

    /**
     * Display an admin notice with the result of the bulk action.
     */
    public static function admin_notices() {
        if (!empty($_GET['bulk_regenerate_monograms'])) {
            $count = intval($_GET['bulk_regenerate_monograms']);
            printf(
                '<div class="notice notice-success is-dismissible"><p>%s</p></div>',
                sprintf(
                    _n('%s monogram regenerated successfully.', '%s monograms regenerated successfully.', $count, 'my-profile-menu'),
                    $count
                )
            );
        }
    }
}
