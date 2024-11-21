<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Settlement extends Model
{
    protected $guarded = [];
    //use HasFactory;
    use SoftDeletes;

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
}
