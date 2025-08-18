<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Form Builder Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration values for the form builder application
    |
    */

    'valid_locales' => ['en', 'de'],

    'valid_field_types' => [
        'text', 'email', 'password', 'number',
        'select', 'radio', 'checkbox', 'textarea',
        'file', 'date', 'time', 'datetime-local',
        'url', 'tel', 'search', 'color', 'range', 'hidden',
    ],

    'default_locale' => 'en',

    'max_locales' => 10,

    'max_fields_per_form' => 50,
];
