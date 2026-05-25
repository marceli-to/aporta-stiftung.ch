<?php

/*
 * Routes for the public application form.
 * Merge these into your existing routes/web.php.
 *
 * The middleware group below assumes the visitor-facing password gate is
 * applied via a middleware named 'password.gate' (replace with whatever the
 * Statamic project uses today). DO NOT remove the gate — visitor access
 * control is unrelated to the backend integration.
 */

use App\Http\Controllers\ApplicationFormController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'password.gate'])->group(function () {
	Route::get('/anmeldung', fn () => view('applications.form'))->name('application.form');
	Route::post('/anmelden', [ApplicationFormController::class, 'store'])->name('application.store');
	Route::get('/danke', [ApplicationFormController::class, 'thanks'])->name('application.thanks');
});
