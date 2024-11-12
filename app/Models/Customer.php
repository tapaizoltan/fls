<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Customer extends Model
{
    protected $guarded = [];
    use SoftDeletes;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logOnly(['financial_risk_rate', 'justification_of_risk']);
        // Chain fluent methods for configuration options
    }
    
    public function industrytypes(): BelongsToMany
    {
        return $this->belongsToMany(Industrytype::class, 'customer_industrytype');
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function financialrisks()
    {
        return $this->hasMany(Financialrisk::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function saleevents()
    {
        return $this->hasMany(Saleevent::class);
    }
}
