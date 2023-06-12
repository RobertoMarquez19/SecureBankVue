<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaccionesCuentas extends Model
{
    use HasFactory;

    protected $table='transacciones_cuentas';

    protected $fillable=['from_cuenta_id','to_cuenta_id','monto','concepto'];

    protected $casts=[
        'created_at'=>'datetime'
    ];
    public function cuentaDe()
    {
        return $this->belongsTo(CuentaBancaria::class, 'from_cuenta_id', 'id');
    }

    public function cuentaA()
    {
        return $this->belongsTo(CuentaBancaria::class, 'to_cuenta_id', 'id');
    }
}
