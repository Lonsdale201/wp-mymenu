<div class="profile-dropdown-wrapper">
    <span class="user-nickname" role="button" aria-haspopup="true" aria-expanded="false" tabindex="0">
        <?php echo $user->nickname; ?>
        <span class="dropdown-arrow"></span>
    </span>
    <div class="user-monogram"><?php echo strtoupper($monogram); ?></div>
    <div class="dropdown-content" aria-hidden="true">
        <div class="dropdown-header">
            <div class="header-monogram"><?php echo strtoupper($monogram); ?></div>
            <div class="header-user-info">
                <span class="header-nickname"><?php echo $user->nickname; ?></span>
                <span class="header-username">@<?php echo $user->user_login; ?></span>
            </div>
        </div>
        <div class="dropdown-menu-content">
            <?php
            echo do_shortcode(get_option('mymenu_before_dropdown_content'));
            do_action('mymenu_before_dropdown_content');
            ?>

            <?php echo wp_nav_menu(array(
                'theme_location' => 'dropdown-profile-menu',
                'echo' => false,
            )); ?>

            <?php
            echo do_shortcode(get_option('mymenu_after_dropdown_content'));
            do_action('mymenu_after_dropdown_content');
            ?>
        </div>
    </div>
</div>
