<?php
namespace HelloWP\HWMyMenu\App\Services;

use HelloWP\HWMyMenu\App\Helper\SettingsConfig;
use HelloWP\HWMyMenu\App\Admin\UserMeta;

if (!defined('ABSPATH')) {
    exit;
}

class MonogramGen {

    /**
     * Generate a monogram for the given user.
     *
     * @param \WP_User $user The user object.
     * @return string The generated monogram.
     */
    public static function generate($user) {
        $generation_rule = SettingsConfig::get('mymenu_monogram_generation', 'last_first');
        
        $first_letter = '';
        $last_letter = '';
    
        if ($generation_rule === 'email' || empty($user->first_name) || empty($user->last_name)) {
            $name_part = explode('@', $user->user_email)[0];
            $first_letter = self::get_first_valid_character($name_part);
            $last_letter = self::get_last_valid_character($name_part);
        } elseif ($generation_rule === 'first_last') {
            $first_letter = self::normalize_character(substr($user->first_name, 0, 1));
            $last_letter = self::normalize_character(substr($user->last_name, 0, 1));
        } elseif ($generation_rule === 'last_first') {
            $first_letter = self::normalize_character(substr($user->last_name, 0, 1));
            $last_letter = self::normalize_character(substr($user->first_name, 0, 1));
        } elseif ($generation_rule === 'nickname' && !empty($user->nickname)) {
            $first_letter = self::normalize_character(substr($user->nickname, 0, 1));
            $last_letter = self::normalize_character(substr($user->nickname, -1));
        } else {
            $name_or_email = $user->user_login ?: $user->user_email;
            $first_letter = self::get_first_valid_character($name_or_email);
            $last_letter = self::get_last_valid_character($name_or_email);
        }
    
        $new_monogram = strtolower($first_letter . $last_letter);
        $current_monogram = get_user_meta($user->ID, UserMeta::MONOGRAM_META_KEY, true);
    
        // Csak akkor frissítsen, ha az érték ténylegesen megváltozott
        if ($new_monogram !== $current_monogram) {
            update_user_meta($user->ID, UserMeta::MONOGRAM_META_KEY, $new_monogram);
        }
    
        return $new_monogram;
    }
    

    /**
     * Get the first valid alphabetic character from a string.
     *
     * @param string $string The input string.
     * @return string The first valid character or an empty string.
     */
    private static function get_first_valid_character($string) {
        foreach (str_split($string) as $char) {
            $normalized = self::normalize_character($char);
            if (ctype_alpha($normalized)) {
                return $normalized;
            }
        }
        return '';
    }

    /**
     * Get the last valid alphabetic character from a string.
     *
     * @param string $string The input string.
     * @return string The last valid character or an empty string.
     */
    private static function get_last_valid_character($string) {
        foreach (array_reverse(str_split($string)) as $char) {
            $normalized = self::normalize_character($char);
            if (ctype_alpha($normalized)) {
                return $normalized;
            }
        }
        return '';
    }

    /**
     * Normalize a character by converting accented characters to their base forms.
     *
     * @param string $char The character to normalize.
     * @return string The normalized character.
     */
    private static function normalize_character($char) {
        $transliteration_table = [
            'á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ö' => 'o', 'ő' => 'o',
            'ú' => 'u', 'ü' => 'u', 'ű' => 'u', 'Á' => 'A', 'É' => 'E', 'Í' => 'I',
            'Ó' => 'O', 'Ö' => 'O', 'Ő' => 'O', 'Ú' => 'U', 'Ü' => 'U', 'Ű' => 'U'
        ];

        return strtr($char, $transliteration_table) ?: $char;
    }
}
