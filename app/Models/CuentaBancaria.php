<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CuentaBancaria extends Model
{
    use HasFactory;

    protected $table = 'cuenta_bancarias';

    protected $fillable = ['numero_cuenta','numero_cuenta_hash','fecha_apertura','id_producto','id_cliente'];

    protected $hidden=['numero_cuenta_hash'];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class,'id_cliente');
    }

    public function producto(){
        return $this->belongsTo(Producto::class,'id_producto');
    }

    public function tarjetas(){
        return $this->hasMany(TarjetaDebito::class,'id_cuenta');
    }
}
