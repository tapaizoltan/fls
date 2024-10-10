<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Customer extends Model
{
    protected $guarded = [];
    use SoftDeletes;
    
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
}
