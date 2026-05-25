<?php

/*
 * Configuration for the Aporta backend integration.
 * Copy this file to Statamic's `config/aporta.php`.
 *
 * Companion .env entries (Statamic side):
 *
 *   APORTA_INTAKE_URL=https://interessenten.aporta-stiftung.ch/api/v1/applications
 *   APORTA_INTAKE_API_KEY=<long-random-string-from-backend-team>
 *   APORTA_LOOKUPS_URL=https://interessenten.aporta-stiftung.ch/api/v1/lookups
 *   APORTA_FAILURE_ALERT_EMAIL=engineering@example.com
 *
 * The backend stores the SHA-256 hash of the API key, never the plain key.
 * Rotate by generating a new random string, sending it to the backend team
 * to update their `APORTA_INTAKE_API_KEY_HASH`, then swapping yours.
 */

return [

	/*
	|--------------------------------------------------------------------------
	| Backend endpoints
	|--------------------------------------------------------------------------
	*/

	'intake_url' => env('APORTA_INTAKE_URL', 'https://interessenten.aporta-stiftung.ch/api/v1/applications'),

	'lookups_url' => env('APORTA_LOOKUPS_URL', 'https://interessenten.aporta-stiftung.ch/api/v1/lookups'),

	/*
	|--------------------------------------------------------------------------
	| Authentication
	|--------------------------------------------------------------------------
	|
	| Bearer token sent to the intake endpoint. Plain string here; the backend
	| only stores the SHA-256 hash. Never log this value.
	|
	*/

	'intake_api_key' => env('APORTA_INTAKE_API_KEY'),

	/*
	|--------------------------------------------------------------------------
	| Alerting
	|--------------------------------------------------------------------------
	|
	| Address that receives an email when a forwarding job permanently fails
	| (4xx response or retries exhausted).
	|
	*/

	'failure_alert_email' => env('APORTA_FAILURE_ALERT_EMAIL'),

];
