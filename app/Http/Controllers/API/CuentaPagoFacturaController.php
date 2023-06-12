<?php

namespace App\Http\Controllers\API;

use App\Models\CuentaBancaria;
use App\Models\CuentaPagoFactura;
use App\Models\Factura;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class CuentaPagoFacturaController extends BaseController
{
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id_cuenta' => 'required',
                'npe' => 'required|min:34|max:34'
            ],
                [
                    'id_cuenta.required' => 'El codigo de cuenta es requerido',
                    'npe.required' => 'El NPE es requerido',
                    'npe.min' => 'El NPE debe contener 34 caracteres minimos',
                    'npe.max' => 'El NPE debe contener 34 caracteres maximos'
                ]);

            if ($validator->fails()) {
                return $this->sendError("Errores de validacion", $validator->messages()->all(), 422);
            } else {

                $cliente = User::find(Auth::id())->cliente;
                $input = $request->all();
                if ($cuenta = CuentaBancaria::where('id', $input['id_cuenta'])->where('id_cliente', $cliente->id)->first()) {
                    if ($factura = Factura::where('npe', $input['npe'])->first()) {
                        if ($cuenta->monto_cuenta >= $factura->monto) {
                            $pago = new CuentaPagoFactura(['from_cuenta_id' => $cuenta->id,
                                'to_factura_id' => $factura->id]);
                            if ($factura->estado == 'pendiente') {
                                if ($pago->save()) {
                                    $success['id_transaccion'] = $pago->id;
                                    $success['monto'] = $pago->factura->monto;
                                    $success['concepto'] = $pago->factura->colector;
                                    return $this->sendResponse($success, "El pago se proceso exitosamente");
                                } else {
                                    return $this->sendError("Error inesperado", ["Ocurrio un error al realizar la transferencia"], 500);
                                }
                            } else {
                                return $this->sendError("Factura pagada", ["La factura ingresada ya fue pagada"], 405);
                            }
                        } else {
                            return $this->sendError("Fondos insuficientes", ["Su cuenta no posee los fondos suficientes para realizar el pago"], 405);
                        }
                    } else {
                        return $this->sendError("Not found", ['El NPE ingresado no existe'], 404);
                    }
                } else {
                    return $this->sendError("Unauthorized", ['La cuenta ingresada no le pertenece a usted'], 401);
                }
            }
        } catch (Exception $e) {
            return $this->sendError("Fatal Error", ["Ocurrio un error inesperado, estamos trabajando en solventarlo lo antes posible"], 500);
        }
    }
}
