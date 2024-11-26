<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Productprice extends Model
{
    protected $guarded = [];
    use SoftDeletes;
}
