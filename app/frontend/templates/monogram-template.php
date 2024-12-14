<?php

if (!defined('ABSPATH')) {
    exit;
}

if (is_user_logged_in()) {
    ?>
    <div class="<?php echo esc_attr($theme_class); ?> profile-dropdown-wrapper">
        <span class="<?php echo esc_attr($theme_class); ?> user-nickname" role="button" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-label="Open profile menu">
            <?php echo esc_html($display_name); ?>
            <span class="<?php echo esc_attr($theme_class); ?> dropdown-arrow"></span>
        </span>
        <div class="<?php echo esc_attr($theme_class); ?> user-monogram" aria-hidden="true" aria-label="User monogram">
            <?php echo strtoupper(esc_html($monogram)); ?>
        </div>
        <div class="<?php echo esc_attr($theme_class); ?> dropdown-content" aria-hidden="true" aria-labelledby="profile-menu">
            <div class="<?php echo esc_attr($theme_class); ?> dropdown-header">
                <div class="<?php echo esc_attr($theme_class); ?> header-monogram" aria-label="Monogram">
                    <?php echo strtoupper(esc_html($monogram)); ?>
                </div>
                <div class="<?php echo esc_attr($theme_class); ?> header-user-info">
                    <span class="<?php echo esc_attr($theme_class); ?> header-nickname" aria-label="Nickname">
                        <?php
                        $header_nickname = !empty($user->first_name) ? $user->first_name : (!empty($user->last_name) ? $user->last_name : $user->nickname);
                        echo esc_html($header_nickname);
                        ?>
                    </span>
                    <span class="<?php echo esc_attr($theme_class); ?> header-username" aria-label="Username">
                        @<?php echo esc_html($user->user_login); ?>
                    </span>
                </div>
                <?php if ($active_subs): ?>
                    <span class="<?php echo esc_attr($theme_class); ?> header-active-sub" aria-label="Active Subscription">
                        <?php _e('Subscription Active', 'hw-my-menu'); ?>
                    </span>
                <?php endif; ?>
            </div>

            <div class="<?php echo esc_attr($theme_class); ?> dropdown-menu-content" aria-label="Dropdown menu content">
                <?php
                echo do_shortcode(get_option('mymenu_before_dropdown_content'));
                do_action('mymenu_before_dropdown_content');
                ?>

                <?php
                echo wp_nav_menu([
                    'theme_location' => 'dropdown-profile-menu',
                    'echo' => false,
                ]);
                ?>

                <?php
                echo do_shortcode(get_option('mymenu_after_dropdown_content'));
                do_action('mymenu_after_dropdown_content');
                ?>
            </div>
        </div>
    </div>
    <?php
} else {
    ?>
    <div class="<?php echo esc_attr($theme_class); ?> profile-login-wrapper">
        <a class="<?php echo esc_attr($theme_class); ?> login-button" href="<?php echo esc_url($redirect_url); ?>" aria-label="Login or register">
            <?php echo esc_html($button_text); ?>
        </a>
    </div>
    <?php
}
