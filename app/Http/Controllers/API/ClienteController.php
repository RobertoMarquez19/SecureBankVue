<?php

namespace App\Http\Controllers\API;

use App\Models\Cliente;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ClienteController extends BaseController
{
    public function store(Request $request){
        try {
            $validator = Validator::make($request->all(),[
                'nombres'=>'required|string',
                'apellidos'=>'required|string',
                'fecha_nacimiento'=>'required',
                'email'=>'required|email|unique_encrypted:clientes,email_hash',
                'telefono'=>'required|min:8|max:8|unique_encrypted:clientes,telefono_hash',
                'direccion'=>'required|string',
                'genero'=>'required|in:M,F',
                'estado_civil'=>'required|in:Soltero,Casado,Divorciado,Viudo',
                'dui'=>'required|string|min:10|max:10|unique_encrypted:clientes,dui_hash',
                'nit'=>'required|string|min:17|max:17|unique_encrypted:clientes,nit_hash'
            ],
                [
                    'nombres.required'=>'El campo nombre es requerido',
                    'nombres.string'=>'El campo nombre debe ser una cadena de texto',
                    'apellidos.required'=>'El campo apellidos es requerido',
                    'apellidos.string'=>'El campo apellidos debe ser una cadena de texto',
                    'fecha_nacimmiento.required'=>'El campo fecha de nacimiento es requerido',
                    'email.required'=>'El campo email es requerido',
                    'email.email'=>'El campo email debe ser una direccion de correo valido',
                    'email.unique_encrypted'=>'Este correo electronico ya se encuentra en uso',
                    'telefono.required'=>'El campo telefono es requerido',
                    'telefono.max'=>'El campo telefono debe contener 8 digitos como maximo',
                    'telefono.min'=>'El campo telefono debe contener 8 digitos',
                    'telefono.unique_encrypted'=>'Este numero de telefono ya se encuentra en uso',
                    'genero.required'=>'El campo genero es requerido',
                    'genero.in'=>'El campo genero debe ser un valor valido',
                    'estado_civil.required'=>'El campo estado civil es requerido',
                    'estado_civil.in'=>'El campo estado civil debe ser un valor valido',
                    'dui.required'=>'El campo DUI es requerido',
                    'dui.string'=>'El campo DUI debe ser una cadena de texto',
                    'dui.min'=>'El campo DUI debe contener 10 carateres incluido un guion',
                    'dui.unique_encrypted'=>'Este numero de DUI ya se encuentra en uso',
                    'nit.required'=>'El campo NIT es requerido',
                    'nit.string'=>'El campo NIT debe ser una cadena de texto',
                    'nit.min'=>'El campo NIT debe contener 17 carateres incluido 3 guiones',
                    'nit.unique_encrypted'=>'Este numero de NIT ya se encuentra en uso'
                ]);

            if($validator->fails()){
                return $this->sendError('Errores de validacion',$validator->errors());
            }else{
                //Encriptamos la infotmacion
                $input = $request->all();
                $input['dui_hash'] = \hash('sha256',$input['dui']);
                $input['nit_hash'] = \hash('sha256',$input['nit']);
                $input['email_hash'] = \hash('sha256',$input['email']);
                $input['telefono_hash'] = \hash('sha256',$input['telefono']);
                $input['dui'] = Crypt::encryptString($input['dui']);
                $input['nit'] = Crypt::encryptString($input['nit']);
                $input['email']=Crypt::encryptString($input['email']);
                $input['telefono']=Crypt::encryptString($input['telefono']);
                $input['telefono_trabajo'] = Crypt::encryptString($input['telefono_trabajo']);
                $input['direccion']=Crypt::encryptString($input['direccion']);

                $cliente = new Cliente($input);
                if($cliente->save()){
                    return $this->sendResponse($cliente->id,"Cliente creado exitosamente");
                }else{
                    return $this->sendError("Error inesperado","Ocurrio un error al crear al cliente");
                }
            }
        }catch (Exception $e){
            return $this->sendError($e->getMessage(), "Ocurrio un error inesperado, estamos trabajando en solventarlo lo antes posible", 500);
        }
    }

}
