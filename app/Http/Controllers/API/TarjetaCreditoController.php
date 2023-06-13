<?php

namespace App\Http\Controllers\API;

use App\Models\TarjetaCredito;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class TarjetaCreditoController extends BaseController
{
    public function store(){
        try {
            $cliente = User::find(Auth::id())->cliente;

            //Vamos a crear un numero de cuenta al azar y debemos verificar que no esta en uso
            $numeroTarjeta =$this->generarNumeroTarjeta();


            $datosTarjeta = [
                'numero_tarjeta'=>Crypt::encryptString($numeroTarjeta),
                'numero_tarjeta_hash' => hash('sha256',$numeroTarjeta),
                'id_tipo_tarjeta'=>1,
                'id_cliente'=>$cliente->id];

            $tarjeta = new TarjetaCredito($datosTarjeta);

            if($tarjeta->save()){
                $success['numero_tarjeta']=$numeroTarjeta;
                return $this->sendResponse($success,"Su tarjeta de credito se creo correctamente");
            }else{
                return $this->sendError("Error inesperado", ["Ocurrio un error al crear su tarjeta de credito"], 500);
            }
        }catch (Exception $e) {
            return $this->sendError("Fatal Error", ["Ocurrio un error inesperado, estamos trabajando en solventarlo lo antes posible"], 500);
        }
    }

    public function tarjetasCreditoByCliente(){
        try {
            $cliente = User::find(Auth::id())->cliente;
            $tarjetas = Collection::empty();
            foreach ($cliente->tarjetasCredito as $tarjeta){
                $tarjetas->push([
                    'numero_tarjeta'=>Crypt::decryptString($tarjeta->numero_tarjeta),
                    'monto'=>round($tarjeta->monto,2),
                    'fecha_emision'=>$tarjeta->fecha_emision,
                    'fecha_vencimiento'=>Carbon::parse($tarjeta->fecha_vencimiento)->format('m/Y'),
                    'tipo'=>$tarjeta->tipoTarjeta]);
            }
            return $this->sendResponse($tarjetas,"Tarjetas de credito");
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

        while (TarjetaCredito::where('numero_tarjeta_hash',hash('sha256',$numero))->first()) {
            for ($i = 0; $i < 16; $i++) {
                $numero .= random_int(0, 9);
            }
        }

        return $numero;
    }
}
