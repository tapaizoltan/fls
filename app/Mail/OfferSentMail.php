<?php

namespace App\Mail;

use App\Models\Priceoffer;
use Illuminate\Mail\Mailable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;

class OfferSentMail extends Mailable
{
    use Queueable, SerializesModels;

    public $offer;
    public $customerEmail;
    public $customerName;

    /**
     * Create a new message instance.
     *
     * @param Priceoffer $offer
     * @param string $customerEmail
     * @param string $customerName
     */
    public function __construct(Priceoffer $offer, $customerEmail, $customerName)
    {
        $this->offer = $offer;
        $this->customerEmail = $customerEmail;
        $this->customerName = $customerName;
    }

    /**
     * Build the message.
     *
     * @return \Illuminate\Mail\Mailable
     */
    public function build()
    {
        return $this->subject('Zsolaka Kft - Árajánlat - ' . now()->format('YmdHi') . '-' . $this->offer->price_offer_id . '-' . $this->offer->id)
                    ->to($this->customerEmail, $this->customerName)
                    ->view('emails.offer'); // Ez lesz a Blade sablon
    }
}
