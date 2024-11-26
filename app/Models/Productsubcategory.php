<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Productsubcategory extends Model
{
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
