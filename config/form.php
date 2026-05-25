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

];
