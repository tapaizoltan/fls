<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    protected $guarded = [];
    use SoftDeletes;
    
    public function brands()
    {
        return $this->hasMany(Brand::class);
    }

    public function productprices()
    {
        return $this->hasMany(Productprice::class);
    }
}
