<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Productmaincategory extends Model
{
    protected $guarded = [];
    use SoftDeletes;

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function productsubcategories()
    {
        return $this->hasMany(Product::class);
    }

}