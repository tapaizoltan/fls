<?php

namespace App\Models;

use App\Enums\CauseOfLoss;
use App\Enums\EventTypes;
use App\Enums\SalesStatus;
use App\Enums\WhereDidAFindUs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    protected $guarded = [];
    use SoftDeletes;

    protected $casts = [
        'event_type' => EventTypes::class,
        'status' => SalesStatus::class,
        'where_did_a_find_us' => WhereDidAFindUs::class,
        'cause_of_loss' => CauseOfLoss::class,
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
