<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Priceoffer extends Model
{
    protected $guarded = [];
    use SoftDeletes;
    
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
}
