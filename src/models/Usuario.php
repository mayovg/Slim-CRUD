<?php
namespace Models;

//importa Eloquent para usarlo en el modelo
use Illuminate\Database\Eloquent\Model as Eloquent;

class Usuario extends Eloquent
{
    // Define la llave primaria de la tabla usuarios
    protected $primaryKey = 'id';

    // Define el nombre de la tabla 
    protected $table = 'usuario';
}