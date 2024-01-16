<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pokemon extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'HP',
        'primera_edicion',
        'expansion',
        'tipo',
        'rareza',
        'precio',
        'imagen',
    ];
}
