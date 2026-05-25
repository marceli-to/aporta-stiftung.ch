<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FormController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::post('/form/register/authenticate', [FormController::class, 'authenticate']);
Route::post('/form/register', [FormController::class, 'store']);

// Dev-only sample autofill — gated by APP_ENV=local AND FORM_DEV_SAMPLE=1.
if (app()->environment('local') && config('aporta.dev_sample')) {
    Route::get('/form/dev/sample-data', [\App\Http\Controllers\Api\DevSampleController::class, 'show']);
}
