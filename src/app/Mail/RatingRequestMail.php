<?php

namespace App\Mail;

// app/Mail/RatingRequestMail.php

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Purchase;

class RatingRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $purchase;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\Purchase  $purchase
     * @return void
     */
    public function __construct(Purchase $purchase)
    {
        $this->purchase = $purchase;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('【フリマアプリ】商品「' . $this->purchase->item->name . '」の評価をお願いします')
                    ->markdown('emails.rating_request');
    }
}