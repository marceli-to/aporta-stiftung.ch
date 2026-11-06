<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FormController;
use App\Http\Controllers\Api\RenewalController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::post('/form/register/authenticate', [FormController::class, 'authenticate']);
Route::post('/form/register', [FormController::class, 'store']);

// Renewal mode (link from the renewal email). The token is checked by the
// Interessenten backend; the password gate does not apply.
Route::middleware('throttle:30,1')->group(function () {
	Route::get('/form/renewal/{token}', [RenewalController::class, 'show']);
	Route::put('/form/renewal/{token}', [RenewalController::class, 'update']);
});
