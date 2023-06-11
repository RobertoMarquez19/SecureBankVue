<?php

namespace App\Http\Controllers\API;
use App\Models\CuentaBancaria;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class CuentaBancariaController extends BaseController
{
    public function store(){
        try {
            $cliente = User::find(Auth::id())->cliente;

            //Vamos a crear un numero de cuenta al azar y debemos verificar que no esta en uso
            $numeroCuenta =$this->generarNumeroCuenta();


            $datosCuenta = [
                'numero_cuenta'=>Crypt::encryptString($numeroCuenta),
                'numero_cuenta_hash' => hash('sha256',$numeroCuenta),
                'id_producto'=>1,
                'id_cliente'=>$cliente->id];
            $cuenta = new CuentaBancaria($datosCuenta);

            if($cuenta->save()){
                $success['numero_cuenta']=$numeroCuenta;
                return $this->sendResponse($success,"Su cuenta fue creada exitosamente");
            }else{
                return $this->sendError("Error inesperado", ["Ocurrio un error al crear su cuenta"], 500);
            }
        }catch (Exception $e) {
            return $this->sendError($e->getMessage(), ["Ocurrio un error inesperado, estamos trabajando en solventarlo lo antes posible"], 500);
        }
    }

    public function checkCuentaExiste(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'cuenta_destino' => 'required|max:20|min:20'
            ],
                [
                    'cuenta_destino.required' => 'El codigo de cuenta es requerido',
                    'cuenta_destino.min'=>'El numero de cuenta debe tener 20 caracteres como minimo',
                    'cuenta_destino.max'=>'El numero de cuenta debe tener 20 caracteres como maximo'
                ]);

            if($validator->fails()){
                return $this->sendError("Errores de validacion", $validator->messages()->all(), 422);
            }else{
                $input = $request->all();
                $cuenta_hash = hash('sha256',$input['cuenta_destino']);
                if(CuentaBancaria::where('numero_cuenta_hash',$cuenta_hash)->first()){
                    $cuenta = CuentaBancaria::where('numero_cuenta_hash',$cuenta_hash)->first();
                    $datosCuenta['nombre']=$cuenta->cliente->nombres;
                    $datosCuenta['apellidos']=$cuenta->cliente->apellidos;
                    return $this->sendResponse($datosCuenta,"Datos cuenta");
                }else{
                    return $this->sendError("Not found",['La cuenta ingresada no existe'],404);
                }
            }
        } catch (Exception $e) {
            return $this->sendError("Fatal Error", ["Ocurrio un error inesperado, estamos trabajando en solventarlo lo antes posible"], 500);
        }
    }

    public function cuentasByCliente(){
        try {
            $cliente = User::find(Auth::id())->cliente;
            $cuentas = Collection::empty();
            foreach ($cliente->cuentas as $cuenta){
                $cuentas->push([
                    'id'=>$cuenta->id,
                    'numero_cuenta'=>Crypt::decryptString($cuenta->numero_cuenta),
                    'monto'=>$cuenta->monto_cuenta,
                    'fecha_apertura'=>$cuenta->fecha_apertura,
                    'estado'=>$cuenta->estado_cuenta]);
            }
            return $this->sendResponse($cuentas,"Cuentas");
        }catch (Exception $e) {
            return $this->sendError("Fatal Error", ["Ocurrio un error inesperado, estamos trabajando en solventarlo lo antes posible"], 500);
        }
    }
    /**
     * @throws Exception
     */
    private function generarNumeroCuenta(): string
    {
        $numero = '';
        for ($i = 0; $i < 20; $i++) {
            $numero .= random_int(0, 9);
        }

        while (CuentaBancaria::where('numero_cuenta_hash',hash('sha256',$numero))->first()) {
            for ($i = 0; $i < 20; $i++) {
                $numero .= random_int(0, 9);
            }
        }

        return $numero;
    }
}
