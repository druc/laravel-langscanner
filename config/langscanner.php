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
    | Language of the translation keys
    |--------------------------------------------------------------------------
    |
    | If this is set to some language the translation values for this language
    | will be the same as the keys instead of empty strings
    |
    */
    'keys_lang'=>'',
];
