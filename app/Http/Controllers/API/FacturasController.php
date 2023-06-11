<?php

namespace App\Http\Controllers\API;

use App\Models\Factura;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FacturasController extends BaseController
{
    public function checkFacturaExiste(Request $request){
        try {
            $validator = Validator::make($request->all(), [
                'npe' => 'required|max:34|min:34'
            ],
                [
                    'npe.required'=>'El NPE es requerido',
                    'npe.min' => 'El NPE debe contener 34 caracteres minimos',
                    'npe.max' => 'El NPE debe contener 34 caracteres maximos'
                ]);

            if($validator->fails()){
                return $this->sendError("Errores de validacion", $validator->messages()->all(), 422);
            }else{
                $input = $request->all();
                if ($factura = Factura::where('npe', $input['npe'])->first()) {
                    $datosFactura['monto']=$factura->monto;
                    $datosFactura['colector']=$factura->colector;
                    return $this->sendResponse($datosFactura,"Datos factura");
                }else{
                    return $this->sendError("Not found", ['El NPE ingresado no existe'], 404);
                }
            }
        } catch (Exception $e) {
            return $this->sendError("Fatal Error", ["Ocurrio un error inesperado, estamos trabajando en solventarlo lo antes posible"], 500);
        }
    }
}
