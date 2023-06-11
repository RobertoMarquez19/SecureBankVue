<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    use HasFactory;

    protected $table='productos';

    protected $fillable = ['codigo','nombre_producto','descripcion_producto'];

    public function cuentasBancarias():HasMany{
        return $this->hasMany(CuentaBancaria::class,'id_producto');
    }
}
