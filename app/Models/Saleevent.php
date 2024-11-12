<?php

namespace App\Models;

use App\Enums\EventTypes;
use App\Enums\CauseOfLoss;
use App\Enums\SalesStatus;
use App\Enums\WhereDidAFindUs;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;

class Saleevent extends Model
{
    use LogsActivity;
    protected $guarded = [];
    use SoftDeletes;

    protected $casts = [
        'event_type' => EventTypes::class,
        'status' => SalesStatus::class,
        'where_did_a_find_us' => WhereDidAFindUs::class,
        'cause_of_loss' => CauseOfLoss::class,
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['*']);
        // Chain fluent methods for configuration options
    }
    
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
