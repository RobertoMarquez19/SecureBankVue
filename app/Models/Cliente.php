<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    protected $fillable = ['dui','dui_hash', 'nit','nit_hash', 'nombres', 'apellidos', 'fecha_nacimiento', 'email','email_hash','telefono','telefono_hash', 'telefono_trabajo', 'direccion', 'genero', 'estado_civil'];

    protected $hidden = ['id'];

    public function user():HasOne{
        return $this->hasOne(User::class);
    }

    public function cuentas():HasMany{
        return $this->hasMany(CuentaBancaria::class);
    }
}
