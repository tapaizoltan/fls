<?php

namespace App\Models;

use Carbon\Carbon;
use App\Enums\PriceOffersStatus;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Priceoffer extends Model
{
    protected $guarded = [];
    use SoftDeletes;

    protected $casts = [
        'status' => PriceOffersStatus::class,
    ];

    public static function generatePriceOfferId()
    {
        $lastPriceOfferId = Priceoffer::withTrashed()->latest('price_offer_id')->first();
        if ($lastPriceOfferId) {
            $lastNumber = (int) substr($lastPriceOfferId->price_offer_id, 3);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        return 'PO-' . str_pad($nextNumber, 7, '0', STR_PAD_LEFT);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function priceofferitems()
    {
        return $this->hasMany(Priceofferitem::class);
    }

    public function sendOfferEmail()
    {
        $sale = $this->sale; // Az ajánlathoz tartozó értékesítés
        $customer = $sale->customer; // Az értékesítéshez tartozó ügyfél
        $financialContact = $customer->contacts->where('financial_relationship', true)->first(); // Kapcsolattartó keresése

        if (!$financialContact) {
            throw new \Exception('Nem található pénzügyi kapcsolattartó az ügyfélhez.');
        }

        $email = $financialContact->email;

        $subject = 'Zsolaka Kft - Árajánlat - ' . now()->format('YmdHi') . '-' . $sale->id . '-' . $this->id;

        $content = view('emails.offer', [
            'customerName' => $customer->name,
            'offer' => $this,
        ])->render();

        Mail::send([], [], function ($message) use ($email, $subject, $content) {
            $message->to($email)
                ->subject($subject)
                ->setBody($content, 'text/html');
        });
    }

    
}
