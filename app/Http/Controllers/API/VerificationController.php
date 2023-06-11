<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use function Symfony\Component\Translation\t;

class VerificationController extends BaseController
{
    public function verify($user_id, Request $request)
    {
        try {
            if (!$request->hasValidSignature()) {
                return $this->sendError("URL Invalido", ['El enlace de verificacion es invalido o ya ha expirado'], 401);
            }

            $user = User::findOrFail($user_id);

            if (!$user->hasVerifiedEmail()) {
                $user->markEmailAsVerified();
            }

            return redirect()->to('/');
        } catch (Exception $e) {
            return $this->sendError("Fatal Error", ["Ocurrio un error inesperado, estamos trabajando en solventarlo lo antes posible"], 500);
        }
    }

    public function resend(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|min:8'
            ],
                [
                    'email.required' => 'El campo email es requerido',
                    'email.email' => 'El campo email debe ser una direccion de correo electronico valido',
                    'password.required' => 'El campo contraseña es requerido',
                    'password.min' => 'La contraseña debe tener como minimo 8 caracteres',
                ]);
            if ($validator->fails()) {
                return $this->sendError("Errores de validacion", $validator->messages()->all(), 422);
            } else {
                $input = $request->all();
                if (Auth::attempt($input)) {
                    if (auth()->user()->hasVerifiedEmail()) {
                        return $this->sendError("Correo verificado", ['Su cuenta de correo electronico ya fue verificada'], 400);
                    }

                    auth()->user()->sendEmailVerificationNotification();

                    return $this->sendResponse("Success", "Se ha enviado un enlace de verificacion a su correo");
                }else{
                    return $this->sendError("Unauthorized", ['Credenciales incorrectas'], 401);
                }
            }
        } catch (Exception $e) {
            return $this->sendError("Fatal Error", ["Ocurrio un error inesperado, estamos trabajando en solventarlo lo antes posible"], 500);
        }
    }
}
