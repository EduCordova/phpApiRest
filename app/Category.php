<?php

namespace App;

use App\Product;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];
    //muchos a muchos
    //belong to many / pertenece a mucho
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
