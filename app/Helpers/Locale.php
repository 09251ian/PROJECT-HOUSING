<?php

/**
 * Fallback polyfill for PHP intl\Locale.
 *
 * CodeIgniter 4 uses the global `Locale` class (from the intl extension) in
 * `CodeIgniter\\I18n\\TimeTrait`. Some local Windows/XAMPP environments are
 * missing the intl extension, which causes a fatal error:
 *   "Class \"Locale\" not found"
 *
 * This polyfill provides only what CodeIgniter needs at boot time.
 */

if (! class_exists('Locale')) {
    class Locale
    {
        public static function getDefault(): string
        {
            // Keep it simple; CodeIgniter will still be able to run without intl.
            return 'en';
        }
    }
}