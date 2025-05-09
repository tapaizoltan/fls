<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    public function priceofferitems()
{
    return $this->hasManyThrough(
        Priceofferitem::class,
        Priceoffer::class,
        'id',              // local key on Delivery (vagy Priceoffer.id)
        'priceoffer_id',   // foreign key on Priceofferitem
        'id',              // local key on Delivery (vagy Priceoffer.id)
        'id'               // local key on Priceoffer
    );
}

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

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function priceOffers()
    {
        return $this->hasMany(Priceoffer::class);
    }

    /*
    public function priceofferitems()
    {
        return $this->hasMany(Priceofferitem::class);
    }
        */
}