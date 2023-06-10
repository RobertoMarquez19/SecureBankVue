<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CuentaBancaria extends Model
{
    use HasFactory;

    protected $table = 'cuenta_bancarias';

    protected $fillable = ['numero_cuenta','numero_cuenta_hash','monto_cuenta','fecha_apertura','estado_cuenta','id_producto','id_cliente'];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class,'id_cliente');
    }
}
