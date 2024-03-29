<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TarjetaDebito extends Model
{
    use HasFactory;

    protected $table='tarjeta_debitos';

    protected $fillable=['numero_tarjeta','numero_tarjeta_hash','cvv','id_tipo_tarjeta','id_cuenta'];

    protected $hidden=['numero_tarjeta_hash'];

    public function cuenta()
    {
        return $this->belongsTo(CuentaBancaria::class,'id_cuenta');
    }

    public function tipoTarjeta(){
        return $this->belongsTo(TarjetaTipos::class,'id_tipo_tarjeta');
    }
}
