<?php

namespace App\Http\Controllers\API;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductoController extends BaseController
{
    public function allProductos(){
        try {
            return $this->sendResponse(Producto::all(),"");
        }catch (\Exception $e){
            return $this->sendError($e->getMessage(),"Ocurrio un error inesperado",501);
        }
    }

    public function store(Request $request){
        try {
            $validator = Validator::make($request->all(),
            [
                'codigo'=>'required|string|unique:productos',
                'nombre_producto'=>'required|string',
                'descripcion_producto'=>'required|string'
            ],
            [
                'codigo.required'=>'El campo codigo es requerido',
                'codigo.string'=>'El campo codigo debe ser una cadena de texto',
                'codigo.unique'=>'Este codigo ya se encuentra en uso',
                'nombre_producto.required'=>'El campo nombre producto es requerido',
                'nombre_producto.string'=>'El campo nombe producto debe ser una cadena de texto',
                'descripcion_producto.required'=>'El campo descripcion producto es requerida',
                'descripcion_producto.string'=>'El campo descripcion producto debe ser una cadena de texto'
            ]);
        }catch (\Exception $e){

        }
    }
}
