<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}

class Monogram {
    public function __construct() {
        add_shortcode('user_monogram', array($this, 'render_shortcode'));
        add_action('user_register', array($this, 'handle_user_register'));
    }

    public function generate_monogram($user) {
        $first_letter = $last_letter = '';

        if (!empty($user->last_name) && !empty($user->first_name)) {
            $first_letter = substr($user->last_name, 0, 1);
            $last_letter = substr($user->first_name, 0, 1);
        } else {
            $name_or_email = (!empty($user->user_login)) ? $user->user_login : $user->user_email;
            preg_match_all('/[a-zA-Z]/', $name_or_email, $matches);
            if (count($matches[0]) > 1) {
                $first_letter = $matches[0][0];
                $last_letter = end($matches[0]);
            } else {
                $first_letter = $last_letter = $matches[0][0];
            }
        }

        $monogram = strtolower($first_letter . $last_letter);
        update_user_meta($user->ID, 'hw_monogram', $monogram);

        return $monogram;
    }

    public function render_shortcode() {
        if (is_user_logged_in()) {
            $user = wp_get_current_user();
            $monogram = get_user_meta($user->ID, 'hw_monogram', true);
    
            if (empty($monogram)) {
                $monogram = $this->generate_monogram($user);
            }
    
            $before_content = get_option('mymenu_before_dropdown_content');
            $after_content = get_option('mymenu_after_dropdown_content');
    
            ob_start();
            if (!empty($before_content)) {
                echo do_shortcode($before_content);
            }
            include plugin_dir_path( __FILE__ ) . '../templates/monogram-template.php';
            if (!empty($after_content)) {
                echo do_shortcode($after_content);
            }
            return ob_get_clean();
        } else {
            $redirect_url = get_option('mymenu_redirect_url');
            $logged_out_text = get_option('mymenu_text_field', 'Bejelentkezés / Regisztráció');
        
            if (!$redirect_url) {
                $redirect_url = site_url('/fiokom');
            }
        
            return '<a class="logged-out-button" href="' . esc_url($redirect_url) . '">' . esc_html($logged_out_text) . '</a>';
        }
    }
    

    public function handle_user_register($user_id) {
        $user = get_userdata($user_id);
        $this->generate_monogram($user);
    }
}
