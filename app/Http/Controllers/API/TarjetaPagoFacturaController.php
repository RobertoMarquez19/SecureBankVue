<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CuentaBancaria;
use App\Models\Factura;
use App\Models\TarjetaCredito;
use App\Models\TarjetaPagoFactura;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class TarjetaPagoFacturaController extends BaseController
{
    public function store(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'id_tarjeta' => 'required',
                'npe' => 'required|min:34|max:34'
            ],
                [
                    'id_tarjeta.required' => 'El codigo de tarjeta es requerido',
                    'npe.required' => 'El NPE es requerido',
                    'npe.min' => 'El NPE debe contener 34 caracteres minimos',
                    'npe.max' => 'El NPE debe contener 34 caracteres maximos'
                ]);

            if ($validator->fails()) {
                return $this->sendError("Errores de validacion", $validator->messages()->all(), 422);
            } else {

                $cliente = User::find(Auth::id())->cliente;
                $input = $request->all();
                if ($tarjetaCredito = TarjetaCredito::where('id', $input['id_tarjeta'])->where('id_cliente', $cliente->id)->first()) {
                    if ($factura = Factura::where('npe', $input['npe'])->first()) {
                        if ($tarjetaCredito->monto >= $factura->monto) {
                            $pago = new TarjetaPagoFactura(['from_tarjeta_id' => $tarjetaCredito->id,
                                'to_factura_id' => $factura->id]);
                            if ($factura->estado === 'pendiente') {
                                if ($pago->save()) {
                                    $success['id_transaccion'] = $pago->id;
                                    $success['monto'] = $pago->factura->monto;
                                    return $this->sendResponse($success, "El pago se proceso exitosamente");
                                } else {
                                    return $this->sendError("Error inesperado", ["Ocurrio un error al realizar la transferencia"], 500);
                                }
                            } else {
                                return $this->sendError("Factura pagada", ["La factura ingresada ya fue pagada"], 405);
                            }
                        } else {
                            return $this->sendError("Fondos insuficientes", ["Su tarjeta no posee los fondos suficientes para realizar el pago"], 405);
                        }
                    } else {
                        return $this->sendError("Not found", ['El NPE ingresado no existe'], 404);
                    }
                } else {
                    return $this->sendError("Unauthorized", ['La tarjeta ingresada no le pertenece a usted'], 401);
                }
            }
        } catch (Exception $e) {
            return $this->sendError("Fatal Error", ["Ocurrio un error inesperado, estamos trabajando en solventarlo lo antes posible"], 500);
        }
    }

    public function transaccionesTarjeta(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'id_tarjeta' => 'required',
            ],
                [
                    'id_tarjeta.required' => 'El codigo de tarjeta es requerido',
                ]);

            if ($validator->fails()) {
                return $this->sendError("Errores de validacion", $validator->messages()->all(), 422);
            } else {
                $cliente = User::find(Auth::id())->cliente;
                $input = $request->all();
                if ($tarjetaCredito = TarjetaCredito::where('id', $input['id_tarjeta'])->where('id_cliente', $cliente->id)->first()) {
                    //Armamos el array
                    $transacciones = Collection::empty();
                    foreach ($tarjetaCredito->pagosFacturas as $pago){
                        $transacciones->push(['id_transaccion'=>$pago->id,
                            'monto'=>round($pago->factura->monto,2),
                            'colector'=>$pago->factura->colector,
                            'monto_despues'=>round($pago->from_tarjeta_monto_despues,2),
                            'fecha_operacion'=>$pago->created_at,
                            'fecha_operacion_string'=>Carbon::parse($pago->created_at)->format('d/m/Y h:i:s A'),
                            'operacion'=>'factura']);
                    }
                    $sorted = $transacciones->sortByDesc(function ($obj, $key) {
                        return $obj['fecha_operacion']->getTimestamp();
                    });
                    return $this->sendResponse($sorted->values()->all(), "Historial de transferencias");
                } else {
                    return $this->sendError("Unauthorized", ['La tarjeta ingresada no le pertenece a usted'], 401);
                }
            }
        } catch (Exception $e) {
            return $this->sendError($e->getTrace(), ["Ocurrio un error inesperado, estamos trabajando en solventarlo lo antes posible"], 500);
        }
    }
}
