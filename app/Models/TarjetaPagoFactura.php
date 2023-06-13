<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TarjetaPagoFactura extends Model
{
    use HasFactory;

    protected $table='tarjeta_pago_facturas';

    protected $fillable=['from_tarjeta_id','to_factura_id'];

    public function factura(){
        return $this->belongsTo(Factura::class,"to_factura_id");
    }


}
