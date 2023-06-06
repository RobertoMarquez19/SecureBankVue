<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends BaseController
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email'=>'required|email|unique:users',
            'password'=>'required|confirmed',
            'id_cliente'=>'required|unique:users'
        ]);

        if($validator->fails()){
            return $this->sendError("Errores de validacion",$validator->errors());
        }else{
            $input = $request->all();
            $input['password']=Hash::make($input['password']);

            $user = new User($input);
            if($user->save()){
                return $this->sendResponse("$user->email","Usuario creado exitosamente");
            }else{
                return $this->sendError("Error inesperado","Ocurrio un error al crear el usuario");
            }
        }
    }

    public function login(Request $request){
        $validator = Validator::make($request->all(),[
            'email'=>'required|email',
            'password'=>'required'
        ]);

        if($validator->fails()){
            return $this->sendError("Errores de validacion",$validator->errors());
        }else{
            $input = $request->all();
            if(Auth::attempt($input)){
                $user = Auth::user();
                $success['token'] =  $user->createToken('SecureBank')-> accessToken;
                return $this->sendResponse($success, 'Bienvenido a SecureBank');
            }else{
                return $this->sendError("Credenciales incorrectas",'Unauthorized',401);
            }

        }
    }
}
