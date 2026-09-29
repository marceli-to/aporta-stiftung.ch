<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Master password
    |--------------------------------------------------------------------------
    |
    | Optional override password that authenticates against the application
    | form without consuming an entry from key.json. Set FORM_MASTER_PASSWORD
    | in .env. Leave empty to disable the override.
    |
    */

    'master_password' => env('FORM_MASTER_PASSWORD'),

    /*
    |--------------------------------------------------------------------------
    | Public mode
    |--------------------------------------------------------------------------
    |
    | When enabled, the password gate is skipped: the form renders directly
    | and submissions are accepted without an X-Form-Token. Meant for short
    | open-registration windows. Toggle FORM_PUBLIC in .env, then run
    | `php artisan config:clear` (or config:cache) to apply.
    |
    */

    'public' => (bool) env('FORM_PUBLIC', false),

];
