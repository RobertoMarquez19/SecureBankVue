<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TarjetaCredito extends Model
{
    use HasFactory;

    protected $table="tarjeta_creditos";

    protected $fillable=['numero_tarjeta','numero_tarjeta_hash','id_tipo_tarjeta','id_cliente'];

    protected $hidden=['numero_tarjeta_hash'];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class,'id_cliente');
    }

    public function tipoTarjeta(){
        return $this->belongsTo(TarjetaTipos::class,'id_tipo_tarjeta');
    }

    public function pagosFacturas(){
        return $this->hasMany(TarjetaPagoFactura::class,'from_tarjeta_id','id');
    }
}
