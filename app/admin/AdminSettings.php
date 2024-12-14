<?php
namespace HelloWP\HWMyMenu\App\Admin;
use HelloWP\HWMyMenu\App\Helper\Dependency;

if (!defined('ABSPATH')) {
    exit;
}

class AdminSettings {
    private static $instance = null;
    private $option_key = 'mymenu_settings'; 

    private function __construct() {
        add_action('admin_menu', [$this, 'add_settings_page']);
        add_action('admin_init', [$this, 'initialize_settings']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_menu_scripts']);

    }

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function add_settings_page() {
        add_options_page(
            'MyMenu Settings',
            'MyMenu',
            'manage_options',
            'mymenu-settings',
            [$this, 'settings_page_content']
        );
    }

    public function settings_page_content() {
        ?>
        <div class="wrap">
            <h2>MyMenu Settings</h2>
            <form method="post" action="options.php">
                <?php
                settings_fields('mymenu_settings_group');
                do_settings_sections('mymenu-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function enqueue_admin_scripts($hook) {
        if ($hook === 'settings_page_mymenu-settings') { 
            wp_enqueue_script(
                'mymenu-admin-scripts',
                HWMYMENU_ADMIN_ASSETS . 'mymenuadminscr.js',
                ['jquery'],
                '1.0.0',
                true
            );
            wp_enqueue_style(
                'mymenu-admin-styles',
                HWMYMENU_ADMIN_ASSETS . 'mymenubackend.css',
                [],
                '1.0.0'
            );
        }
    }

    public function enqueue_admin_menu_scripts($hook) {
        if ($hook === 'nav-menus.php') {
            wp_enqueue_style(
                'mymenu-admin-styles',
                HWMYMENU_ADMIN_ASSETS . 'mymenubackend.css',
                [],
                '1.0.0'
            );
    
            wp_enqueue_script(
                'mymenu-admin-scripts',
                HWMYMENU_ADMIN_ASSETS . 'mymenuadminscr.js',
                ['jquery'],
                '1.0.0',
                true
            );
        }
    }
    

    public function initialize_settings() {
        register_setting(
            'mymenu_settings_group',
            $this->option_key,
            ['sanitize_callback' => [$this, 'sanitize_settings']] 
        );

        add_settings_section(
            'mymenu_settings_section',
            'Main Settings',
            [$this, 'settings_section_callback'],
            'mymenu-settings'
        );

        add_settings_field(
            'mymenu_monogram_generation',
            'How to generate the monogram',
            [$this, 'monogram_generation_callback'],
            'mymenu-settings',
            'mymenu_settings_section'
        );

        add_settings_field(
            'mymenu_dropdown_label',
            'Dropdown Label',
            [$this, 'dropdown_label_callback'],
            'mymenu-settings',
            'mymenu_settings_section'
        );

        add_settings_field(
            'mymenu_custom_label',
            'Custom Dropdown Label',
            [$this, 'custom_label_callback'],
            'mymenu-settings',
            'mymenu_settings_section'
        );

       
    if (Dependency::is_woocommerce_subscriptions_active()) {
        add_settings_field(
            'mymenu_show_active_subs',
            'Show Active Woo Subscriptions tag in Dropdown Header',
            [$this, 'show_active_subs_callback'],
            'mymenu-settings',
            'mymenu_settings_section'
        );
    }


        add_settings_field(
            'mymenu_text_field',
            'Logged out link text',
            [$this, 'text_field_callback'],
            'mymenu-settings',
            'mymenu_settings_section'
        );

        add_settings_field(
            'mymenu_redirect_url',
            'Redirect URL',
            [$this, 'redirect_url_field_callback'],
            'mymenu-settings',
            'mymenu_settings_section'
        );

        add_settings_field(
            'mymenu_enable_icon_menu',
            'Enable icon for the menus',
            [$this, 'enable_icon_menu_callback'],
            'mymenu-settings',
            'mymenu_settings_section'
        );        

        add_settings_field(
            'mymenu_before_dropdown_content',
            'Insert shortcode before dropdown menu content',
            [$this, 'before_dropdown_content_callback'],
            'mymenu-settings',
            'mymenu_settings_section'
        );

        add_settings_field(
            'mymenu_after_dropdown_content',
            'Insert shortcode after dropdown menu content',
            [$this, 'after_dropdown_content_callback'],
            'mymenu-settings',
            'mymenu_settings_section'
        );

    }

    public function settings_section_callback() {
        echo '<p>General settings are available here.</p>';
    }

    public function monogram_generation_callback() {
        $options = get_option($this->option_key);
        $selected = isset($options['mymenu_monogram_generation']) ? $options['mymenu_monogram_generation'] : 'last_first';

        echo '<select name="' . $this->option_key . '[mymenu_monogram_generation]">
            <option value="email" ' . selected($selected, 'email', false) . '>Email (first-last letter)</option>
            <option value="first_last" ' . selected($selected, 'first_last', false) . '>First name Last name (first-last letter)</option>
            <option value="last_first" ' . selected($selected, 'last_first', false) . '>Last name First name (first-last letter)</option>
            <option value="nickname" ' . selected($selected, 'nice_name', false) . '>Nickname (first-last letter)</option>
        </select>';
    }

     public function dropdown_label_callback() {
        $options = get_option($this->option_key);
        $selected = isset($options['mymenu_dropdown_label']) ? $options['mymenu_dropdown_label'] : 'nickname';

        echo '<select name="' . $this->option_key . '[mymenu_dropdown_label]" id="mymenu_dropdown_label">
            <option value="nickname" ' . selected($selected, 'nickname', false) . '>Nickname</option>
            <option value="first_name" ' . selected($selected, 'first_name', false) . '>First name</option>
            <option value="last_name" ' . selected($selected, 'last_name', false) . '>Last name</option>
            <option value="email" ' . selected($selected, 'email', false) . '>Email (before @)</option>
            <option value="custom" ' . selected($selected, 'custom', false) . '>Custom text</option>
        </select>';
    }

    public function custom_label_callback() {
        $options = get_option($this->option_key);
        $custom_label = isset($options['mymenu_custom_label']) ? $options['mymenu_custom_label'] : '';
        $selected = isset($options['mymenu_dropdown_label']) ? $options['mymenu_dropdown_label'] : 'nickname';
        echo '<input type="text" name="' . $this->option_key . '[mymenu_custom_label]" id="mymenu_custom_label" value="' . esc_attr($custom_label) . '" class="regular-text" />';
        echo '<p class="description">Enter your custom dropdown label.</p>';
    }

    public function text_field_callback() {
        $options = get_option($this->option_key);
        $value = isset($options['mymenu_text_field']) ? $options['mymenu_text_field'] : '';
        echo '<input type="text" name="' . $this->option_key . '[mymenu_text_field]" value="' . esc_attr($value) . '" />';
    }

    public function redirect_url_field_callback() {
        $options = get_option($this->option_key);
        $redirect_url = isset($options['mymenu_redirect_url']) ? $options['mymenu_redirect_url'] : '';
        echo '<input type="url" name="' . $this->option_key . '[mymenu_redirect_url]" value="' . esc_attr($redirect_url) . '" placeholder="https://example.com">';
    }

    public function show_active_subs_callback() {
        if (!Dependency::is_woocommerce_subscriptions_active()) {
            echo '<p style="color: #a00;">' . __('WooCommerce Subscriptions is not active. This setting is not available.', 'hw-my-menu') . '</p>';
            return;
        }
    
        $options = get_option($this->option_key);
        $checked = isset($options['mymenu_show_active_subs']) ? $options['mymenu_show_active_subs'] : false;
    
        echo '<label>
                <input type="checkbox" name="' . $this->option_key . '[mymenu_show_active_subs]" value="1" ' . checked(1, $checked, false) . ' />
                ' . __('Enable this option to show the user\'s active Woo Subscriptions in the dropdown header.', 'hw-my-menu') . '
              </label>';
    }

    public function enable_icon_menu_callback() {
        $options = get_option($this->option_key);
        $checked = isset($options['mymenu_enable_icon_menu']) ? $options['mymenu_enable_icon_menu'] : false;
        echo '<label>
                <input type="checkbox" name="' . $this->option_key . '[mymenu_enable_icon_menu]" value="1" ' . checked(1, $checked, false) . ' />
                ' . __('Enable Icons for the menu items', 'hw-my-menu') . '
              </label>';
    }    

    public function before_dropdown_content_callback() {
        $options = get_option($this->option_key);
        $content = isset($options['mymenu_before_dropdown_content']) ? $options['mymenu_before_dropdown_content'] : '';
        echo '<textarea name="' . $this->option_key . '[mymenu_before_dropdown_content]">' . esc_textarea($content) . '</textarea>';
    }

    public function after_dropdown_content_callback() {
        $options = get_option($this->option_key);
        $content = isset($options['mymenu_after_dropdown_content']) ? $options['mymenu_after_dropdown_content'] : '';
        echo '<textarea name="' . $this->option_key . '[mymenu_after_dropdown_content]">' . esc_textarea($content) . '</textarea>';
    }

    public function sanitize_settings($input) {
        $sanitized = [];
        $sanitized['mymenu_monogram_generation'] = isset($input['mymenu_monogram_generation']) ? sanitize_text_field($input['mymenu_monogram_generation']) : 'last_first';
        $sanitized['mymenu_dropdown_label'] = isset($input['mymenu_dropdown_label']) ? sanitize_text_field($input['mymenu_dropdown_label']) : 'nickname';
        $sanitized['mymenu_custom_label'] = isset($input['mymenu_custom_label']) ? sanitize_text_field($input['mymenu_custom_label']) : '';
        $sanitized['mymenu_text_field'] = isset($input['mymenu_text_field']) ? sanitize_text_field($input['mymenu_text_field']) : '';
        $sanitized['mymenu_redirect_url'] = isset($input['mymenu_redirect_url']) ? esc_url_raw($input['mymenu_redirect_url']) : '';
        $sanitized['mymenu_enable_icon_menu'] = isset($input['mymenu_enable_icon_menu']) ? (bool) $input['mymenu_enable_icon_menu'] : false;
        $sanitized['mymenu_before_dropdown_content'] = isset($input['mymenu_before_dropdown_content']) ? wp_kses_post($input['mymenu_before_dropdown_content']) : '';
        $sanitized['mymenu_after_dropdown_content'] = isset($input['mymenu_after_dropdown_content']) ? wp_kses_post($input['mymenu_after_dropdown_content']) : '';
        $sanitized['mymenu_show_active_subs'] = isset($input['mymenu_show_active_subs']) && Dependency::is_woocommerce_subscriptions_active() 
        ? (bool) $input['mymenu_show_active_subs'] 
        : false;

        return $sanitized;
    }
}