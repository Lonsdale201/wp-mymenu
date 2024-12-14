<?php
namespace HelloWP\HWMyMenu\App\Helper;

if (!defined('ABSPATH')) {
    exit;
}

class SettingsConfig {
    /**
     * The option key used to store all settings.
     */
    private const OPTION_KEY = 'mymenu_settings';

    /**
     * Cached settings.
     *
     * @var array
     */
    private static $settings = null;

    /**
     * Load settings from the database if not already loaded.
     */
    private static function load_settings() {
        if (self::$settings === null) {
            self::$settings = get_option(self::OPTION_KEY, []);
        }
    }

    /**
     * Get a specific setting value.
     *
     * @param string $key     The key of the setting to retrieve.
     * @param mixed  $default The default value to return if the setting is not set.
     *
     * @return mixed The setting value or the default value if not set.
     */
    public static function get($key, $default = null) {
        self::load_settings();

        return isset(self::$settings[$key]) ? self::$settings[$key] : $default;
    }

    /**
     * Update a specific setting value.
     *
     * @param string $key   The key of the setting to update.
     * @param mixed  $value The value to set.
     */
    public static function set($key, $value) {
        self::load_settings();

        self::$settings[$key] = $value;
        update_option(self::OPTION_KEY, self::$settings);
    }

    /**
     * Remove a specific setting.
     *
     * @param string $key The key of the setting to remove.
     */
    public static function remove($key) {
        self::load_settings();

        if (isset(self::$settings[$key])) {
            unset(self::$settings[$key]);
            update_option(self::OPTION_KEY, self::$settings);
        }
    }

    /**
     * Get all settings.
     *
     * @return array The entire settings array.
     */
    public static function all() {
        self::load_settings();

        return self::$settings;
    }

    /**
     * Reset all settings to the default state.
     */
    public static function reset() {
        self::$settings = [];
        update_option(self::OPTION_KEY, self::$settings);
    }
}
