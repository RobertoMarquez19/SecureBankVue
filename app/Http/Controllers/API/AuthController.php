<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Twilio\Rest\Client;

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
                $user = User::find(Auth::id());
                $clientePhone = (new ClienteController())->getClientePhoneNumber($user->id_cliente);
                $this->sendConfirmationMessage($clientePhone);
                return $this->sendResponse("Mensaje confirmacion","Se ha enviado un mensaje con un codigo de verificacion, al numero vinculado a su cuenta");
            }else{
                return $this->sendError("Credenciales incorrectas",'Unauthorized',401);
            }

        }
    }

    private function sendConfirmationMessage($phoneNumber){
        $sid = getenv("TWILIO_ACCOUNT_SID");
        $token = getenv("TWILIO_AUTH_TOKEN");
        $twilio = new Client($sid, $token);

        $verification = $twilio->verify->v2->services("VAc9d9e734d582f59b69b541efb82e3078")
            ->verifications
            ->create("+503".$phoneNumber, "sms");

        return $this->sendResponse($verification,"Se ha enviado un codigo de verificacion al numero telefonico vinculado a su cuenta");
    }
}
