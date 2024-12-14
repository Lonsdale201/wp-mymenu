# Simple My menu

A simple plugin that allows you to create and display a dropdown profile menu.

This plugin provides a shortcode, a few tweaks, a monogram creation, and a dropdown menu for logged in users.

## HOW TO

* First go to the wp admin Settings / Mymenu page. Setup your Logout text, and redirect link, and other stuffs
* Second, go to the Appearance / Menus. Create a new menu, or use an existing one. In the Menu sttings / Display location, check the new DropDown Profile Menu option
* Third, go to your header (best option to place the shortcode in the header). open your editor, like elementor Brick etc.. Place the following shortcode: **[user_monogram]** use the ** theme="light"** attributum if you would like to make a custom css for specific theme styles. Read below how it work.
* Done!

![image](https://github.com/Lonsdale201/wp-mymenu/assets/23199033/43d26cda-ac60-4b38-abcd-90e5a74e4106)


## HOW IT WORK

The shortcode automatically displays the dropdown menu if the user is already logged in, if not a link text is displayed, which redirects to the registration login page when clicked (you can configure these in the settings)

The user's profile picture will be a monogram, generated when new users register and when existing users log in, and saved in a user meta. So in fact it's not a picture, but two characters, which can only contain letters. The generation is done by the plugin based on the programmed parameters. You can configure how generate the monogram in the settings page.

## Additional information

For menus, you can specify the following options for each menu item, not just the dropdown items:

* Show only to logged out or logged in users
* Only if you have a subscription (Woo Subscriptions)
* If you are an Affiliate partner (AffiliateWP)
* Based on user role
* Show for the selected Woo Membership plans (multiple + relation support)
* You can add badge
* You can enable to use, and render icons in the menu 

> [!IMPORTANT]
> These settings only hide the menu item, but still if someone has the link they can view the page.

### Compatibiliy plugins

* Woo Membership
* Woo Subscriptions
* AffiliateWP

## For Developers

Monograme meta key: **hw_monogram**

Add content before or after the dropdown menu:
(now this can done via the mymenu settings / with shortcode)

Example 1
```
function my_custom_dropdown_content() {
    echo '<p>This is a simple text</p>';
}
add_action('mymenu_before_dropdown_content', 'my_custom_dropdown_content');
```

Exmple 2

```
function my_custom_dropdown_content() {
    echo '<p>This is a simple text</p>';
}
add_action('mymenu_after_dropdown_content', 'my_custom_dropdown_content');
```

## Customization

Currently you can customize the display using css. don't worry it's not complicated.
You can add your own theme name, in the shortcode: [user_monogram theme="light"] , if not add, the basic setup is called "dark", this is a specific css system. You can make anything.



### TESTED

Php 8.1
WP 6.7.1
Accessibility

### TODO

- [X] Language loaclization
- [X] Role-based display condition
- [X] Monogram regeneration manually

## CHANGELOG

### V 3.0 - 2024.12.14

IMPORTANT!

New requirments: Php 8.0
Wp 6.0 !

The plugin has been completely rewritten. While the HTML structure remains the same, we’ve introduced a host of new features and enhancements.

We have redesigned the entire codebase and made the monogram generation process much more efficient. You can now regenerate monograms manually if needed, and even preview and modify them directly within the admin profile menu. But that’s not all! Thanks to this refactor, the system is now more efficient, and you can configure how monograms are generated. CSS and JS files are now only loaded where the shortcode is displayed, ensuring no unnecessary scripts are included.

New features have been added to the menu settings, including role-based visibility, icon additions, and badge displays.

For icons, simply enter the icon’s class name into the field, for example: fa-solid fa-house. Don’t forget to ensure that your chosen icon library is loaded on the frontend.

Accessibility improvements have also been implemented. You can now use the Escape key to close menus, navigate with arrow keys, and tabs/arrows will no longer leave the dropdown menu, even at the bottom. ARIA labels have been added as well.

You can now display active subscriptions directly within the dropdown menu. Additionally, the dropdown label text is fully customizable. Previously, it defaulted to the user's nickname, but now you can set it to static text or other dynamic values.

Version 3.0 fixes the issue with incorrectly generated monograms and includes a complete overhaul of the settings organization and saving process. Unfortunately, this required breaking backward compatibility, meaning you’ll need to reconfigure your previous settings.

We’re not stopping here; many exciting plans are in store for this plugin!

Welcome to My Menu, the second member of the Simple plugin family!



 
### V 2.1 - 2024.02.17

* **TWEAK** Code refactored
* **FIX** Fixed some bugs with the third party settings. (AffiliateWp, Woo Subs, and Woo memberships visibility)
* **NEW** Plugin added to the updater server. 

### V 2.0 - 2024.01.31

* **NEW** Membership compatibility for the Wp Navmenu visibility (multiple and relation support)
* **NEW** Two new settings: Insert shortcode before dropdown menu content and Insert shortcode after dropdown menu content - now you can add shortcode before and after the dropdown content. 
* **TWEAK** some code refactored
* **BUGFIX** Fixed the bad dropdown calculations


