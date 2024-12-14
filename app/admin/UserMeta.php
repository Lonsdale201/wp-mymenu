<?php
namespace HelloWP\HWMyMenu\App\Admin;

use HelloWP\HWMyMenu\App\Services\MonogramGen;

if (!defined('ABSPATH')) {
    exit;
}

class UserMeta {
    const MONOGRAM_META_KEY = 'hw_monogram';

    private static $instance = null;

    private function __construct() {
        add_action('show_user_profile', [$this, 'render_user_meta_field']);
        add_action('edit_user_profile', [$this, 'render_user_meta_field']);
        add_action('personal_options_update', [$this, 'save_user_meta']);
        add_action('edit_user_profile_update', [$this, 'save_user_meta']);

        // Hook for generating monograms on registration
        add_action('user_register', [$this, 'generate_monogram_on_register']);
        // Hook for regenerating monograms on profile update
        add_action('profile_update', [$this, 'regenerate_monogram_on_update'], 10, 2);
    }

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function render_user_meta_field($user) {
        $monogram = get_user_meta($user->ID, self::MONOGRAM_META_KEY, true);
        ?>
        <h3>MyMenu User Meta</h3>
        <table class="form-table">
            <tr>
                <th><label for="hw_monogram">User Monogram</label></th>
                <td>
                    <div style="
                        display: flex;
                        align-items: center;
                        margin-bottom: 10px;">
                        <div style="
                            width: 50px;
                            height: 50px;
                            background-color: #212121;
                            color: #ffffff;
                            border-radius: 50%;
                            display: flex;
                            justify-content: center;
                            align-items: center;
                            font-family: inherit;
                            font-size: 18px;
                            text-transform: uppercase;">
                            <?php echo esc_html($monogram ?: '--'); ?>
                        </div>
                        <p style="margin-left: 10px;">Preview</p>
                    </div>
                    <input type="text" name="<?php echo esc_attr(self::MONOGRAM_META_KEY); ?>" id="hw_monogram" value="<?php echo esc_attr($monogram); ?>" class="regular-text" />
                    <p class="description">Enter the monogram for this user. Leave blank to use the default generated value.</p>
                </td>
            </tr>
        </table>
        <?php
    }

    public function save_user_meta($user_id) {
        if (!current_user_can('edit_user', $user_id)) {
            return;
        }

        if (isset($_POST[self::MONOGRAM_META_KEY]) && $_POST[self::MONOGRAM_META_KEY] !== '') {
            $monogram = sanitize_text_field($_POST[self::MONOGRAM_META_KEY]);
            update_user_meta($user_id, self::MONOGRAM_META_KEY, $monogram);
        } else {
            // Regenerate monogram if the field is empty
            $user = get_userdata($user_id);
            MonogramGen::generate($user);
        }
    }

    public function generate_monogram_on_register($user_id) {
        $user = get_userdata($user_id);
        MonogramGen::generate($user);
    }

    public function regenerate_monogram_on_update($user_id, $old_user_data) {
        $user = get_userdata($user_id);

        // Always regenerate to account for changes in generation rules or user data
        MonogramGen::generate($user);
    }
}
