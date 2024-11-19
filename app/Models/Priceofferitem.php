<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Priceofferitem extends Model
{
    protected $guarded = [];

    public function priceoffer()
    {
        return $this->belongsTo(Priceoffer::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
