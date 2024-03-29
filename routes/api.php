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
Route::post('email/resend',  [\App\Http\Controllers\API\VerificationController::class,'resend'])->name('verification.resend');

Route::middleware(['auth:api'])->group( function () {

    Route::get('cliente/sesion/renovar',[\App\Http\Controllers\API\AuthController::class,'refreshToken']);
    Route::post('cliente/sesion/logout',[\App\Http\Controllers\API\AuthController::class,'logout']);

    Route::post("cliente/cuentas",[\App\Http\Controllers\API\CuentaBancariaController::class,'store']);
    Route::get('cliente/cuentas',[\App\Http\Controllers\API\CuentaBancariaController::class,'cuentasByCliente']);

    Route::post("cliente/tarjetascredito",[\App\Http\Controllers\API\TarjetaCreditoController::class,'store']);
    Route::get('cliente/tarjetascredito',[\App\Http\Controllers\API\TarjetaCreditoController::class,'tarjetasCreditoByCliente']);

    Route::post("cliente/cuentas/tarjetasdebito",[\App\Http\Controllers\API\TarjetaDebitoController::class,'store']);
    Route::post('cliente/cuentas/tarjetasdebito',[\App\Http\Controllers\API\TarjetaDebitoController::class,'tarjetasDebitoByCuenta']);

    Route::post('cliente/transferencia/cuenta',[\App\Http\Controllers\API\CuentaBancariaController::class,'checkCuentaExiste']);

    Route::post('cliente/facturas',[\App\Http\Controllers\API\FacturasController::class,'checkFacturaExiste']);

    Route::post('cliente/transferencia/transferir',[\App\Http\Controllers\API\TransaccionesCuentasController::class,'transferir']);

    Route::post('cliente/cuentas/transferencias',[\App\Http\Controllers\API\TransaccionesCuentasController::class,'transaccionesCuentas']);

    Route::post('cliente/cuentas/facturas/pago',[\App\Http\Controllers\API\CuentaPagoFacturaController::class,'store']);


    Route::post('cliente/tarjetascredito/facturas/pago',[\App\Http\Controllers\API\TarjetaPagoFacturaController::class,'store']);
    Route::post('cliente/tarjetascredito/transferencias',[\App\Http\Controllers\API\TarjetaPagoFacturaController::class,'transaccionesTarjeta']);
});
