# My Profile menu plugin

A simple plugin that allows you to create and display a dropdown profile menu.

This plugin provides a shortcode, a few tweaks, a monogram creation, and a dropdown menu for logged in users.

## HOW TO

* First go to the wp admin Settings / Mymenu page. Setup your Logout text, and redirect link.
* Second, go to the Appearance / Menus. Create a new menu, or use an existing one. In the Menu sttings / Display location, check the new DropDown Profile Menu option
* Third, go to your header (best option to place the shortcode in the header). open your editor, like elementor Brick etc.. Place the following shortcode: **[user_monogram]**
* Done!


## HOW IT WORK

The shortcode automatically displays the dropdown menu if the user is already logged in, if not a link text is displayed, which redirects to the registration login page when clicked (you can configure these in the settings)

The user's profile picture will be a monogram, generated when new users register and when existing users log in, and saved in a user meta. So in fact it's not a picture, but two characters, which can only contain letters. The generation is done by the plugin based on the programmed parameters (not configurable at the moment). If the user has a first and last name, it is generated from the two initials. If not, it is generated from the first and last letters of the email address.

The user's nickname will appear in front of the monogram, and if you have set up a drop-down menu, it will open with a click, displaying the menu items you have specified. 

## Additional information

For menus, you can specify the following options for each menu item, not just the dropdown items:

* Show only to logged out or logged in users
* Only if you have a subscription (Woo Subscriptions)
* If you are an Affiliate partner (AffiliateWP)

> [!IMPORTANT]
> These settings only hide the menu item, but still if someone has the link they can view the page.

## For Developers

Monograme meta key: **hw_monogram**

Add content before or after the dropdown menu:

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

### TODO

- [ ] Language loaclization
- [ ] Role-based display condition
- [ ] Monogram regeneration manually


