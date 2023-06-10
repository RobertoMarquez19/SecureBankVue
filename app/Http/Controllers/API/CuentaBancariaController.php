<?php

namespace App\Http\Controllers\API;
use App\Models\CuentaBancaria;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

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
                'monto_cuenta'=>100,
                'estado_cuenta'=>'activa',
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
            return $this->sendError("Fatal Error", ["Ocurrio un error inesperado, estamos trabajando en solventarlo lo antes posible"], 500);
        }
    }

    public function cuentasByCliente(){
        try {

            $cliente = User::find(Auth::id())->cliente;
            return $this->sendResponse($cliente->cuentas(),"Cuentas");
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
