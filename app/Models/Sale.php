<?php

namespace App\Models;

use App\Enums\CauseOfLoss;
use App\Enums\SalesStatus;
use Illuminate\Support\Str;
use App\Enums\WhereDidAFindUs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    protected $guarded = [];
    use SoftDeletes;
    protected $fillable = ['product_id',];

    protected $casts = [
        'status' => SalesStatus::class,
        'where_did_a_find_us' => WhereDidAFindUs::class,
        'cause_of_loss' => CauseOfLoss::class,
    ];

    protected $table = 'sales';

    public static function generateSaleEventId()
    {
        $lastSaleEventId = Sale::withTrashed()->latest('sale_event_id')->first();
        if ($lastSaleEventId) {
            $lastNumber = (int) substr($lastSaleEventId->sale_event_id, 3);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        return 'SE-' . str_pad($nextNumber, 7, '0', STR_PAD_LEFT);
    }

    public static function generateSaleEventKey()
    {
        do {
            $key = Str::random(12);
        } while (Sale::withTrashed()->where('sale_event_key', $key)->exists());

        return $key;
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
}
