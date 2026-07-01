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
