<?php

namespace App\Http\Controllers\API;

use App\Models\CuentaBancaria;
use App\Models\TransaccionesCuentas;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class TransaccionesCuentasController extends BaseController
{
    public function transferir(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'cuenta_destino' => 'required|max:20|min:20',
                'id_cuenta' => 'required',
                'monto' => 'required|numeric|min:0.01',
            ],
                [
                    'cuenta_destino.required' => 'El numero de cuenta destino es requerido',
                    'cuenta_destino.max' => 'El numero de cuenta destino debe poseer 20 caracteres maximos',
                    'cuenta_destino.min' => 'El numero de cuenta destino debe poseer 20 caracteres como minimo',
                    'id_cuenta.required' => 'El codigo de cuenta es requerido',
                    'monto.required' => 'El monto de la transferencia es requerido',
                    'monto.numeric' => 'El monto debe ser un valor numerico',
                    'monto.min' => 'El monto de la transaccion debe ser un centavo como minimo',
                ]);

            if ($validator->fails()) {
                return $this->sendError("Errores de validacion", $validator->messages()->all(), 422);
            } else {
                $cliente = User::find(Auth::id())->cliente;
                $input = $request->all();
                if (CuentaBancaria::where('id', $input['id_cuenta'])->where('id_cliente', $cliente->id)->first()) {
                    $cuenta_destino_hash = hash('sha256', $input['cuenta_destino']);
                    if ($cuenta_destino = CuentaBancaria::where('numero_cuenta_hash', $cuenta_destino_hash)->first()) {
                        if ($cuenta_destino->id == $input['id_cuenta']) {
                            return $this->sendError("Misma cuenta", ['No puedes realizar transferencias a esta misma cuenta'], 405);
                        } else {

                            //Verificamos el monto de la cuenta
                            $cuenta_origen = CuentaBancaria::where('id', $input['id_cuenta'])->first();
                            if ($cuenta_origen->monto_cuenta >= $input['monto']) {
                                $transferencia = new TransaccionesCuentas(['from_cuenta_id' => $input['id_cuenta'], 'to_cuenta_id' => $cuenta_destino->id,
                                    'monto' => $input['monto'],
                                    'concepto' => $input['concepto']]);

                                if ($transferencia->save()) {
                                    $success['id_transaccion']=$transferencia->id;
                                    $success['monto']=$transferencia->monto;
                                    $success['concepto']=$transferencia->concepto;
                                    return $this->sendResponse($success, "La transferencia se completo exitosamente");
                                } else {
                                    return $this->sendError("Error inesperado", ["Ocurrio un error al realizar la transferencia"], 500);
                                }
                            } else {
                                return $this->sendError("Fondos insuficientes", ["Su cuenta no posee los fondos suficientes para realizar la transaccion"], 405);
                            }
                        }
                    } else {
                        return $this->sendError("Not found", ['La cuenta ingresada no existe'], 404);
                    }
                } else {
                    return $this->sendError("Unauthorized", ['La cuenta ingresada no le pertenece a usted'], 401);
                }
            }
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), ["Ocurrio un error inesperado, estamos trabajando en solventarlo lo antes posible"], 500);
        }
    }

    public function transaccionesCuentas(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'id_cuenta' => 'required',
            ],
                [
                    'id_cuenta.required' => 'El codigo de cuenta es requerido',
                ]);

            if ($validator->fails()) {
                return $this->sendError("Errores de validacion", $validator->messages()->all(), 422);
            } else {
                $cliente = User::find(Auth::id())->cliente;
                $input = $request->all();
                if ($cuenta = CuentaBancaria::where('id', $input['id_cuenta'])->where('id_cliente', $cliente->id)->first()) {
                    //Armamos el array
                    $transacciones = Collection::empty();
                    foreach ($cuenta->transaccionesCuentas as $transaccion){
                        if($transaccion->from_cuenta_id==$cuenta->id){
                            //Salida
                            $transacciones->push(['id_transaccion'=>$transaccion->id,
                                'monto_antes'=>$transaccion->from_cuenta_monto_antes,
                                'monto_despues'=>$transaccion->from_cuenta_monto_despues,
                                'cuenta_destino'=>Crypt::decryptString($transaccion->cuentaA->numero_cuenta),
                                'fecha_operacion'=>$transaccion->created_at,
                                'concepto'=>$transaccion->concepto,
                                'operacion'=>'salida']);
                        }else{
                            //Entrada
                            $transacciones->push(['id_transaccion'=>$transaccion->id,
                                'monto_antes'=>$transaccion->to_cuenta_monto_antes,
                                'monto_despues'=>$transaccion->to_cuenta_monto_despues,
                                'cuenta_recibido'=>Crypt::decryptString($transaccion->cuentaDe->numero_cuenta),
                                'fecha_operacion'=>$transaccion->created_at,
                                'concepto'=>$transaccion->concepto,
                                'operacion'=>'entrada']);
                        }
                    }
                    return $this->sendResponse($transacciones, "Historial de transferencias");
                } else {
                    return $this->sendError("Unauthorized", ['La cuenta ingresada no le pertenece a usted'], 401);
                }
            }
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), ["Ocurrio un error inesperado, estamos trabajando en solventarlo lo antes posible"], 500);
        }
    }
}
