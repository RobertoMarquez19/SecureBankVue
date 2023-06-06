<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Twilio\Exceptions\RestException;
use Twilio\Rest\Client;

class AuthController extends BaseController
{
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|unique:users',
                'password' => 'required|confirmed|min:8',
                'id_cliente' => 'required|unique:users'
            ],
            [
                'email.required'=>'El campo email es requerido',
                'email.email'=>'El campo email debe ser una direccion de correo electronico valido',
                'email.unique'=>'Este correo electronico ya se encuentra en uso',
                'password.required'=>'El campo contraseña es requerido',
                'password.confirmed'=>'Las contraseñas no coinciden',
                'password.min'=>'La contraseña debe tener como minimo 8 caracteres',
                'id_cliente.required'=>'El codigo de cliente es requerido',
                'id_cliente.unique'=>'El codigo de cliente ya se encuentra en uso'
            ]);

            if ($validator->fails()) {
                return $this->sendError("Errores de validacion", $validator->errors(),500);
            } else {
                $input = $request->all();
                $input['password'] = Hash::make($input['password']);

                $user = new User($input);
                if ($user->save()) {
                    return $this->sendResponse("$user->email", "Usuario creado exitosamente");
                } else {
                    return $this->sendError("Error inesperado", "Ocurrio un error al crear el usuario",500);
                }
            }
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), "Ocurrio un error inesperado, estamos trabajando en solventarlo lo antes posible", 500);
        }
    }

    public function requestSMS(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required'
            ],
            [
                'email.required'=>'El campo email es requerido',
                'email.email'=>'El campo email debe ser una direccion de correo electronico valido',
                'email.unique'=>'Este correo electronico ya se encuentra en uso',
                'password.required'=>'El campo contraseña es requerido',
                'password.confirmed'=>'Las contraseñas no coinciden',
                'password.min'=>'La contraseña debe tener como minimo 8 caracteres',
            ]);

            if ($validator->fails()) {
                return $this->sendError("Errores de validacion", $validator->errors(),500);
            } else {
                $input = $request->all();
                if (Auth::attempt($input)) {
                    //Credenciales correctas, obtenemos el numero telefonico del usuario
                    $cliente = User::find(Auth::id())->cliente;

                    //Generamos un template de numero al que se envio el codigo
                    $replaceValue = str_repeat("X", 5);
                    $telefono_secret = substr_replace(Crypt::decryptString($cliente->telefono), $replaceValue, 0, 5);

                    $sid = getenv("TWILIO_ACCOUNT_SID");
                    $token = getenv("TWILIO_AUTH_TOKEN");
                    $twilio = new Client($sid, $token);

                    //Enviamos el SMS
                    $verification = $twilio->verify->v2->services("VAc9d9e734d582f59b69b541efb82e3078")
                        ->verifications
                        ->create("+503" . Crypt::decryptString($cliente->telefono), "sms");

                    $suceess['telefono_secret'] = "+503 " . $telefono_secret;
                    $suceess['verification_sid'] = $verification->sid;
                    //Informamos que hemos enviado el mensaje al usuario
                    return $this->sendResponse($suceess, "Mensaje enviado al numero telefonico vinculado al usuario");
                } else {
                    return $this->sendError("Credenciales incorrectas", 'Unauthorized', 401);
                }

            }
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), "Ocurrio un error inesperado, estamos trabajando en solventarlo lo antes posible", 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'code' => 'required|min:6|max:6',
                'email' => 'required|email',
                'password' => 'required',
                'verification_sid' => 'required'
            ],
            [
                'email.required'=>'El campo email es requerido',
                'email.email'=>'El campo email debe ser una direccion de correo electronico valido',
                'email.unique'=>'Este correo electronico ya se encuentra en uso',
                'password.required'=>'El campo contraseña es requerido',
                'password.confirmed'=>'Las contraseñas no coinciden',
                'password.min'=>'La contraseña debe tener como minimo 8 caracteres',
                'code.required'=>'El codigo de verificacion SMS es requerido',
                'code.min'=>'El codigo de verfificacion SMS debe contener 6 digitos',
                'code.max'=>'El codigo de verfificacion SMS debe contener 6 digitos',
                'verification_sid.required'=>'El SID de verificacion es requerido'
            ]);

            if ($validator->fails()) {
                return $this->sendError("Errores de validacion", $validator->errors(),500);
            } else {
                $input = $request->all();
                if (Auth::attempt($request->only('email', 'password'))) {
                    $sid = getenv("TWILIO_ACCOUNT_SID");
                    $token = getenv("TWILIO_AUTH_TOKEN");
                    $twilio = new Client($sid, $token);

                    $verification_check = $twilio->verify->v2->services("VAc9d9e734d582f59b69b541efb82e3078")
                        ->verificationChecks
                        ->create([
                                "code" => $input['code'],
                                "verificationSid" => $input['verification_sid']
                            ]
                        );

                    switch ($verification_check->status) {
                        case 'pending':
                            return $this->sendError("Codigo incorrecto", "El codigo ingresado no es valido", 401);

                        case 'approved':
                            $success['token'] = Auth::user()->createToken('SecureBank')->accessToken;
                            $success['user_id'] = Auth::id();
                            return $this->sendResponse($success, 'Bienvenido a SecureBank');

                        default:
                            return $this->sendError("Estado invalido", 'El estado del codigo es desconocido', 401);
                    }

                } else {
                    return $this->sendError("Credenciales incorrectas", 'Unauthorized', 401);
                }
            }
        } catch (RestException $e) {
            return $this->sendError("Twilio Error", "La verificacion por SMS ya ha expirado", 401);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), "Ocurrio un error inesperado, estamos trabajando en solventarlo lo antes posible", 500);
        }
    }
}
