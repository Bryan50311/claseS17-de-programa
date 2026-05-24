<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'nombre',
        'autor',
        'editorial',
        'edicion',
        'fecha_lanzamiento',
        'imagen_portada',
        'categoria_id'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'categoria_id');
    }
}
