<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}

class MyMenu_Settings {
    private static $instance = null;

    private function __construct() {
        add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
        add_action( 'admin_init', array( $this, 'initialize_settings' ) );
    }

    public static function get_instance() {
        if ( null === self::$instance ) {
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
            array( $this, 'settings_page_content' ) 
        );
    }

    public function settings_page_content() {
        ?>
        <div class="wrap">
            <h2>MyMenu Settings</h2>
            <form method="post" action="options.php">
                <?php
                    settings_fields( 'mymenu_settings_group' );
                    do_settings_sections( 'mymenu-settings' );
                    submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function initialize_settings() {
        register_setting(
            'mymenu_settings_group', 
            'mymenu_text_field'      
        );

        register_setting(
            'mymenu_settings_group', 
            'mymenu_redirect_url'
        );

        register_setting(
            'mymenu_settings_group', 
            'mymenu_before_dropdown_content'
        );
        
        register_setting(
            'mymenu_settings_group', 
            'mymenu_after_dropdown_content'
        );    

        add_settings_section(
            'mymenu_settings_section', 
            'Main Settings',           
            array( $this, 'settings_section_callback' ), 
            'mymenu-settings'         
        );

        add_settings_field(
            'mymenu_text_field',      
            'Logged out link text',             
            array( $this, 'text_field_callback' ), 
            'mymenu-settings',         
            'mymenu_settings_section'  
        );

        add_settings_field(
            'mymenu_redirect_url',      
            'Redirect URL',             
            array( $this, 'redirect_url_field_callback' ), 
            'mymenu-settings',         
            'mymenu_settings_section'  
        );

        add_settings_field(
            'mymenu_before_dropdown_content',      
            'Insert shortcode before dropdown menu content',             
            array( $this, 'before_dropdown_content_callback' ), 
            'mymenu-settings',         
            'mymenu_settings_section'  
        );

        add_settings_field(
            'mymenu_after_dropdown_content',      
            'Insert shortcode after dropdown menu content',             
            array( $this, 'after_dropdown_content_callback' ), 
            'mymenu-settings',         
            'mymenu_settings_section'  
        );
       
    }

    public function settings_section_callback() {
        echo '<p>General settings are available here.</p>';
    }


    public function text_field_callback() {
        $value = get_option( 'mymenu_text_field' );
        echo '<input type="text" name="mymenu_text_field" value="' . esc_attr( $value ) . '" />';
    }

    public function redirect_url_field_callback() {
        $redirect_url = get_option('mymenu_redirect_url');
        echo '<input type="url" name="mymenu_redirect_url" value="' . esc_attr($redirect_url) . '" placeholder="https://example.com">';
    }

    public function before_dropdown_content_callback() {
        $content = get_option('mymenu_before_dropdown_content');
        echo '<textarea name="mymenu_before_dropdown_content">' . esc_textarea($content) . '</textarea>';
    }
    
    public function after_dropdown_content_callback() {
        $content = get_option('mymenu_after_dropdown_content');
        echo '<textarea name="mymenu_after_dropdown_content">' . esc_textarea($content) . '</textarea>';
    }
    
}

MyMenu_Settings::get_instance();
