<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Address extends Model
{
    protected $guarded = [];
    use SoftDeletes;

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
