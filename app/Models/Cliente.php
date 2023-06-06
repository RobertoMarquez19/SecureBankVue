<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    protected $fillable = ['dui','dui_hash', 'nit','nit_hash', 'nombres', 'apellidos', 'fecha_nacimiento', 'email','email_hash','telefono','telefono_hash', 'telefono_trabajo', 'direccion', 'genero', 'estado_civil'];

    protected $hidden = ['id'];
}
