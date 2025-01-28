<?php

namespace App\Jobs;

use App\Models\Priceoffer;
use App\Mail\OfferSentMail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendOfferEmail implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $offer;
    public $customerEmail;
    public $customerName;

    /**
     * Create a new job instance.
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
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // E-mail küldése a Mailable segítségével
        Mail::send(new OfferSentMail($this->offer, $this->customerEmail, $this->customerName));
    }
}
