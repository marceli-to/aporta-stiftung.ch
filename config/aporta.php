<?php

return [

    'intake_url' => env('APORTA_INTAKE_URL', 'https://interessenten.aporta-stiftung.ch/api/v1/applications'),

    'lookups_url' => env('APORTA_LOOKUPS_URL', 'https://interessenten.aporta-stiftung.ch/api/v1/lookups'),

    'intake_api_key' => env('APORTA_INTAKE_API_KEY'),

    'failure_alert_email' => env('APORTA_FAILURE_ALERT_EMAIL'),

    // Dev-only: serve a transformed random legacy-JSON sample to pre-fill the
    // form. Active only when this flag is on AND the app environment is local.
    // To disable: set FORM_DEV_SAMPLE=0 (or remove the var).
    'dev_sample' => (bool) env('FORM_DEV_SAMPLE', false),

];
