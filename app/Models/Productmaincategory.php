<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Productmaincategory extends Model
{
    protected $guarded = [];
    //use HasFactory;

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}