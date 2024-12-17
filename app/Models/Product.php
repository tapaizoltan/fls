<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

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

    public function supplier(): HasOneThrough
    {
        return $this->hasOneThrough(Supplier::class, Brand::class, 'supplier_id', 'id', );
    }

    public function productmaincategory()
    {
        return $this->belongsTo(Productmaincategory::class);
    }

    public function productsubcategory()
    {
        return $this->belongsTo(Productsubcategory::class);
    }

    public function productprice()
    {
        return $this->hasOne(Productprice::class);
    }
}
