<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FormController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/form/register/authenticate', [FormController::class, 'authenticate']);
Route::post('/form/register', [FormController::class, 'store']);

Route::post('/form/register-existing/authenticate', [FormController::class, 'authenticateExisting']);
Route::post('/form/register-existing', [FormController::class, 'storeExisting']);
