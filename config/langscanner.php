<?php

return [
    'lang_dir_path' => base_path('lang'),
    /*
    |--------------------------------------------------------------------------
    | Translation methods
    |--------------------------------------------------------------------------
    |
    | Which methods to look for when scanning translations.
    |
    */
    'translation_methods' => [
        '__',
        'trans',
        'trans_choice',
        '@lang',
        'Lang::get',
        'Lang::choice',
        'Lang::trans',
        'Lang::transChoice',
        '@choice'
    ],

    /*
    |--------------------------------------------------------------------------
    | Scan paths
    |--------------------------------------------------------------------------
    |
    | Which directories to scan for missing translations.
    |
    */
    'paths' => [app_path(), resource_path()],

    /*
    |--------------------------------------------------------------------------
    | Scan excluded paths
    |--------------------------------------------------------------------------
    |
    | Which directories to exclude when scanning for missing translations.
    |
    */
    'excluded_paths' => [],

    /*
    |--------------------------------------------------------------------------
    | Save dotted items as array
    |--------------------------------------------------------------------------
    |
    | Uses collect()->undot() to save the items in the JSON files as multi-level
    | array instead of a flat array. Better for reability and management for large
    | json files
    |
    */
    'save_dotted_items_as_array' => false
];
