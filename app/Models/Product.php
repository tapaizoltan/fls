<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    protected $guarded = [];
    use SoftDeletes;

    //protected $with = ['productmaincategory','productsubcategory', 'brand'];

    // public function brand()
    // {
    //     return $this->belongsTo(Brand::class, 'brand_id', 'id');
    // }

    // public function productmaincategory()
    // {
    //     return $this->belongsTo(Productmaincategory::class, 'productmaincategory_id', 'id');
    // }

    // public function productsubcategory()
    // {
    //     return $this->belongsTo(Productsubcategory::class, 'productsubcategory_id', 'id');
    // }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function productmaincategory()
    {
        return $this->belongsTo(Productmaincategory::class);
    }

    public function productsubcategory()
    {
        return $this->belongsTo(Productsubcategory::class);
    }
}
