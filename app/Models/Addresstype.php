<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Addresstype extends Model
{
    protected $guarded = [];

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
}
