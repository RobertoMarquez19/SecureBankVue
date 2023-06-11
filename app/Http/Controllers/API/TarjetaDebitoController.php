<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CuentaBancaria;
use App\Models\TarjetaCredito;
use App\Models\TarjetaDebito;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TarjetaDebitoController extends BaseController
{
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id_cuenta' => 'required',
            ],
                [
                    'id_cuenta.required' => 'El codigo de cuenta es requerido',
                ]);

            if($validator->fails()){
                return $this->sendError("Errores de validacion", $validator->messages()->all(), 422);
            }else{
                $cliente = User::find(Auth::id())->cliente;

                //Debemos revisar que la cuenta le pertenece al usuario iniciado

                $input = $request->all();

                if(CuentaBancaria::where('id',$input['id_cuenta'])->where('id_cliente',$cliente->id)->first()){
                    //Vamos a crear un numero de cuenta al azar y debemos verificar que no esta en uso
                    $numeroTarjeta = $this->generarNumeroTarjeta();

                    $datosTarjeta = [
                        'numero_tarjeta'=>Crypt::encryptString($numeroTarjeta),
                        'numero_tarjeta_hash' => hash('sha256',$numeroTarjeta),
                        'cvv'=>'731',
                        'id_tipo_tarjeta'=>1,
                        'id_cuenta'=>$input['id_cuenta']];

                   $tarjeta = new TarjetaDebito($datosTarjeta);

                    if($tarjeta->save()){
                        $success['numero_tarjeta']=$numeroTarjeta;
                        return $this->sendResponse($success,"Su tarjeta de debito se creo correctamente");
                    }else{
                        return $this->sendError("Error inesperado", ["Ocurrio un error al crear su tarjeta de credito"], 500);
                    }
                }else{
                   return $this->sendError("Unauthorized",['La cuenta ingresada no le pertenece a usted'],401);
                }

            }
        } catch (Exception $e) {
            return $this->sendError("Fatal Error", ["Ocurrio un error inesperado, estamos trabajando en solventarlo lo antes posible"], 500);
        }
    }

    public function tarjetasDebitoByCuenta(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'id_cuenta' => 'required',
            ],
                [
                    'id_cuenta.required' => 'El codigo de cuenta es requerido',
                ]);

            if($validator->fails()){
                return $this->sendError("Errores de validacion", $validator->messages()->all(), 422);
            }else{
                $cliente = User::find(Auth::id())->cliente;
                $input = $request->all();
                if(CuentaBancaria::where('id',$input['id_cuenta'])->where('id_cliente',$cliente->id)->first()){
                    $tarjetas = Collection::empty();
                    $cuenta = CuentaBancaria::where('id',$input['id_cuenta'])->first();
                    foreach ($cuenta->tarjetas as $tarjeta){
                        $tarjetas->push([
                            'id'=>$tarjeta->id,
                            'numero_tarjeta'=>Crypt::decryptString($tarjeta->numero_tarjeta),
                            'fecha_emision'=>$tarjeta->fecha_emision,
                            'fecha_vencimiento'=>$tarjeta->fecha_vencimiento,
                            'tipo'=>$tarjeta->tipoTarjeta]);
                    }
                    return $this->sendResponse($tarjetas,"Tarjetas de debito de la cuenta");
                }else{
                    return $this->sendError("Unauthorized",['La cuenta ingresada no le pertenece a usted'],401);
                }
            }
        }catch (Exception $e) {
            return $this->sendError("Fatal Error", ["Ocurrio un error inesperado, estamos trabajando en solventarlo lo antes posible"], 500);
        }
    }


    private function generarNumeroTarjeta(): string
    {
        $numero = '';
        for ($i = 0; $i < 16; $i++) {
            $numero .= random_int(0, 9);
        }

        while (TarjetaDebito::where('numero_tarjeta_hash', hash('sha256', $numero))->first()) {
            for ($i = 0; $i < 16; $i++) {
                $numero .= random_int(0, 9);
            }
        }

        return $numero;
    }
}
