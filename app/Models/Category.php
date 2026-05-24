<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['nombre', 'descripcion'];

    public function books()
    {
        return $this->hasMany(Book::class, 'categoria_id');
    }
}
