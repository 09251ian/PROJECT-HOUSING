<?php

// Global polyfill to satisfy CodeIgniter's TimeTrait when PHP intl/Locale
// extension is not installed.
if (! class_exists('Locale')) {
    class Locale
    {
        public static function getDefault(): string
        {
            return 'en';
        }
    }
}

