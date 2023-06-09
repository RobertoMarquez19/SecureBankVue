<?php

use Illuminate\Support\Facades\Route;

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

Route::post('usuario/register', [\App\Http\Controllers\API\AuthController::class, 'register']);
Route::post('usuario/sms', [\App\Http\Controllers\API\AuthController::class, 'requestSMS']);
Route::post('usuario/login', [\App\Http\Controllers\API\AuthController::class, 'login']);

//Email
Route::get('email/verify/{id}', [\App\Http\Controllers\API\VerificationController::class,'verify'])->name('verification.verify');
Route::get('email/resend',  [\App\Http\Controllers\API\VerificationController::class,'resend'])->name('verification.resend');

Route::middleware(['auth:api','verfied'])->group( function () {
    //Rutas productos
    Route::get("cliente/cuentas",[\App\Http\Controllers\API\CuentaBancariaController::class,'cuentas']);
});
