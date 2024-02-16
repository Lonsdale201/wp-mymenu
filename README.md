# My Profile menu plugin

A simple plugin that allows you to create and display a dropdown profile menu.

This plugin provides a shortcode, a few tweaks, a monogram creation, and a dropdown menu for logged in users.

## HOW TO

* First go to the wp admin Settings / Mymenu page. Setup your Logout text, and redirect link.
* Second, go to the Appearance / Menus. Create a new menu, or use an existing one. In the Menu sttings / Display location, check the new DropDown Profile Menu option
* Third, go to your header (best option to place the shortcode in the header). open your editor, like elementor Brick etc.. Place the following shortcode: **[user_monogram]**
* Done!

![image](https://github.com/Lonsdale201/wp-mymenu/assets/23199033/43d26cda-ac60-4b38-abcd-90e5a74e4106)


## HOW IT WORK

The shortcode automatically displays the dropdown menu if the user is already logged in, if not a link text is displayed, which redirects to the registration login page when clicked (you can configure these in the settings)

The user's profile picture will be a monogram, generated when new users register and when existing users log in, and saved in a user meta. So in fact it's not a picture, but two characters, which can only contain letters. The generation is done by the plugin based on the programmed parameters (not configurable at the moment). If the user has a first and last name, it is generated from the two initials. If not, it is generated from the first and last letters of the email address.

The user's nickname will appear in front of the monogram, and if you have set up a drop-down menu, it will open with a click, displaying the menu items you have specified. 

## Additional information

For menus, you can specify the following options for each menu item, not just the dropdown items:

* Show only to logged out or logged in users
* Only if you have a subscription (Woo Subscriptions)
* If you are an Affiliate partner (AffiliateWP)
* Show for the selected Woo Membership plans (multiple + relation support)

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

Main CLASSES:

- MAIN DIV: <code>profile-dropdown-wrapper</code>
- MAIN Monogram <code>user-monogram</code>
- MIAN Nickname <code> user-nickname </code>

MAIN CLASSES IN THE DROPDOWN

- MAIN WRAPPER <code>dropdown-content</code>
- HEADER section <code>dropdown-header</code>
- DROPDOWN Content <code>dropdown-menu-content</code>

### TESTED

Php 8.1
WP 6.4.4
Accessibility

### TODO

- [ ] Language loaclization
- [ ] Role-based display condition
- [ ] Monogram regeneration manually

## CHANGELOG

### V 2.1 - 2024.02.17

* **TWEAK** Code refactored
* **FIX** Fixed some bugs with the third party settings. (AffiliateWp, Woo Subs, and Woo memberships visibility)
* **NEW** Plugin added to the updater server. 

### V 2.0 - 2024.01.31

* **NEW** Membership compatibility for the Wp Navmenu visibility (multiple and relation support)
* **NEW** Two new settings: Insert shortcode before dropdown menu content and Insert shortcode after dropdown menu content - now you can add shortcode before and after the dropdown content. 
* **TWEAK** some code refactored
* **BUGFIX** Fixed the bad dropdown calculations


