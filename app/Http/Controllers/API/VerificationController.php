<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use function Symfony\Component\Translation\t;

class VerificationController extends BaseController
{
    public function verify($user_id, Request $request) {
        if (!$request->hasValidSignature()) {
            return $this->sendError("URL Invalido",['El enlace de verificacion es invalido o ya ha expirado'],401);
        }

        $user = User::findOrFail($user_id);

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return redirect()->to('/');
    }

    public function resend() {
        if (auth()->user()->hasVerifiedEmail()) {
            return $this->sendError("Correo verificado",['Su cuenta de correo electronico ya fue verificada'],400);
        }

        auth()->user()->sendEmailVerificationNotification();

        return $this->sendResponse("Success","Se ha enviado un enlace de verificacion a su correo");
    }
}
