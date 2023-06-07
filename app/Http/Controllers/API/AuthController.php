<?php

namespace App\Http\Controllers\API;

use App\Models\Cliente;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Twilio\Exceptions\RestException;
use Twilio\Rest\Client;

class AuthController extends BaseController
{
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|unique_encrypted:clientes,email_hash',
                'password' => 'required|confirmed|min:8',
                'nombres' => 'required|string',
                'apellidos' => 'required|string',
                'fecha_nacimiento' => 'required',
                'telefono' => 'required|min:8|max:8|unique_encrypted:clientes,telefono_hash',
                'direccion' => 'required|string',
                'genero' => 'required|in:M,F',
                'estado_civil' => 'required|in:Soltero,Casado,Divorciado,Viudo',
                'dui' => 'required|string|min:10|max:10|unique_encrypted:clientes,dui_hash',
                'nit' => 'required|string|min:17|max:17|unique_encrypted:clientes,nit_hash'
            ],
                [
                    'nombres.required' => 'El campo nombre es requerido',
                    'nombres.string' => 'El campo nombre debe ser una cadena de texto',
                    'apellidos.required' => 'El campo apellidos es requerido',
                    'apellidos.string' => 'El campo apellidos debe ser una cadena de texto',
                    'fecha_nacimmiento.required' => 'El campo fecha de nacimiento es requerido',
                    'email.required' => 'El campo email es requerido',
                    'email.email' => 'El campo email debe ser una direccion de correo valido',
                    'email.unique_encrypted' => 'Este correo electronico ya se encuentra en uso',
                    'telefono.required' => 'El campo telefono es requerido',
                    'telefono.max' => 'El campo telefono debe contener 8 digitos como maximo',
                    'telefono.min' => 'El campo telefono debe contener 8 digitos',
                    'telefono.unique_encrypted' => 'Este numero de telefono ya se encuentra en uso',
                    'genero.required' => 'El campo genero es requerido',
                    'genero.in' => 'El campo genero debe ser un valor valido',
                    'estado_civil.required' => 'El campo estado civil es requerido',
                    'estado_civil.in' => 'El campo estado civil debe ser un valor valido',
                    'dui.required' => 'El campo DUI es requerido',
                    'dui.string' => 'El campo DUI debe ser una cadena de texto',
                    'dui.min' => 'El campo DUI debe contener 10 carateres incluido un guion',
                    'dui.unique_encrypted' => 'Este numero de DUI ya se encuentra en uso',
                    'nit.required' => 'El campo NIT es requerido',
                    'nit.string' => 'El campo NIT debe ser una cadena de texto',
                    'nit.min' => 'El campo NIT debe contener 17 carateres incluido 3 guiones',
                    'nit.unique_encrypted' => 'Este numero de NIT ya se encuentra en uso',
                    'password.required' => 'El campo contraseña es requerido',
                    'password.confirmed' => 'Las contraseñas no coinciden',
                    'password.min' => 'La contraseña debe tener como minimo 8 caracteres',
                    'id_cliente.required' => 'El codigo de cliente es requerido',
                    'id_cliente.unique' => 'El codigo de cliente ya se encuentra en uso'
                ]);

            if ($validator->fails()) {
                return $this->sendError("Errores de validacion", $validator->errors(), 422);
            } else {
                DB::beginTransaction();
                //Encriptamos la infotmacion
                $input = $request->all();
                $input['dui_hash'] = \hash('sha256', $input['dui']);
                $input['nit_hash'] = \hash('sha256', $input['nit']);
                $input['email_hash'] = \hash('sha256', $input['email']);
                $input['telefono_hash'] = \hash('sha256', $input['telefono']);
                $input['dui'] = Crypt::encryptString($input['dui']);
                $input['nit'] = Crypt::encryptString($input['nit']);
                $input['email'] = Crypt::encryptString($input['email']);
                $input['telefono'] = Crypt::encryptString($input['telefono']);
                $input['telefono_trabajo'] = Crypt::encryptString($input['telefono_trabajo']);
                $input['direccion'] = Crypt::encryptString($input['direccion']);

                $cliente = new Cliente($input);
                if ($cliente->save()) {
                    $inputUsuario = $request->only(['email', 'password']);
                    $inputUsuario['id_cliente'] = $cliente->id;

                    $usuario = new User($inputUsuario);

                    if ($usuario->save()) {
                        DB::commit();
                        return $this->sendResponse("Exito", "Usuario creado exitosamente");
                    } else {
                        DB::rollBack();
                        return $this->sendError("Error inesperado", "Ocurrio un error al crear el usuario",500);
                    }
                } else {
                    DB::rollBack();
                    return $this->sendError("Error inesperado", "Ocurrio un error al crear al cliente",500);
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
                    'email.required' => 'El campo email es requerido',
                    'email.email' => 'El campo email debe ser una direccion de correo electronico valido',
                    'email.unique' => 'Este correo electronico ya se encuentra en uso',
                    'password.required' => 'El campo contraseña es requerido',
                    'password.confirmed' => 'Las contraseñas no coinciden',
                    'password.min' => 'La contraseña debe tener como minimo 8 caracteres',
                ]);

            if ($validator->fails()) {
                return $this->sendError("Errores de validacion", $validator->errors(), 422);
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
                    $verification = $twilio->verify->v2->services("VA157fa2a46260742e82182155fc34a906")
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

        } catch (RestException $e) {
            return $this->sendError($e->getMessage(), "Ocurrio on error con Twilio", 401);
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
                    'email.required' => 'El campo email es requerido',
                    'email.email' => 'El campo email debe ser una direccion de correo electronico valido',
                    'email.unique' => 'Este correo electronico ya se encuentra en uso',
                    'password.required' => 'El campo contraseña es requerido',
                    'password.confirmed' => 'Las contraseñas no coinciden',
                    'password.min' => 'La contraseña debe tener como minimo 8 caracteres',
                    'code.required' => 'El codigo de verificacion SMS es requerido',
                    'code.min' => 'El codigo de verfificacion SMS debe contener 6 digitos',
                    'code.max' => 'El codigo de verfificacion SMS debe contener 6 digitos',
                    'verification_sid.required' => 'El SID de verificacion es requerido'
                ]);

            if ($validator->fails()) {
                return $this->sendError("Errores de validacion", $validator->errors(), 422);
            } else {
                $input = $request->all();
                if (Auth::attempt($request->only('email', 'password'))) {
                    $sid = getenv("TWILIO_ACCOUNT_SID");
                    $token = getenv("TWILIO_AUTH_TOKEN");
                    $twilio = new Client($sid, $token);

                    $verification_check = $twilio->verify->v2->services("VA157fa2a46260742e82182155fc34a906")
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
                            return $this->sendResponse($success, 'Bienvenido a SecureBank');

                        default:
                            return $this->sendError("Estado invalido", 'El estado del codigo es desconocido', 500);
                    }

                } else {
                    return $this->sendError("Credenciales incorrectas", 'Unauthorized', 401);
                }
            }
        } catch (RestException $e) {
            return $this->sendError($e->getMessage(), "La verificacion por SMS ya ha expirado", 401);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage(), "Ocurrio un error inesperado, estamos trabajando en solventarlo lo antes posible", 500);
        }
    }
}
