<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CuentaPagoFactura extends Model
{
    use HasFactory;

    protected $table='cuenta_pago_facturas';

    protected $fillable=['from_cuenta_id','to_factura_id'];

    public function factura(){
        return $this->belongsTo(Factura::class,"to_factura_id");
    }
}
