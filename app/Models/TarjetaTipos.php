<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TarjetaTipos extends Model
{
    protected $table="tarjeta_tipos";

    public function tarjetas():HasMany{
        return $this->hasMany(TarjetaCredito::class,'id_tipo_tarjeta');
    }
}
