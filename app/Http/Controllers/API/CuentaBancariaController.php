<?php

namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CuentaBancariaController extends BaseController
{
    public function store(Request $request){
        $validator = Validator::make($request->all(),
        [

        ]);
    }
}
