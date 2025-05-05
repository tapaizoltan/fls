<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    protected $table = 'priceoffers'; // ugyanazt a táblát használja

    // Nem akarunk új rekordokat létrehozni, csak olvasni
    public $timestamps = false;
    public $incrementing = false;
    protected $guarded = [];

    // Csak a status = 5 rekordokat tekintjük "Delivery"-nek
    protected static function booted()
    {
        static::addGlobalScope('deliveryOnly', function ($query) {
            $query->where('status', 5);
        });
    }
}