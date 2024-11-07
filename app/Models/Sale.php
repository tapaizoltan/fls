<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    protected $guarded = [];
    use SoftDeletes;

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
