<?php

namespace App\Mail;

use App\Models\Lead;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LeadSubmitted extends Mailable
{
    use Queueable, SerializesModels;

    public Lead $lead;
    public ?Product $product;

    /**
     * Create a new message instance.
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
        $this->product = Product::where('slug', $lead->plan)->first();
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this->subject('新的聯絡我們需求')
            ->view('emails.lead-submitted')
            ->with([
                'lead' => $this->lead,
                'product' => $this->product,
            ]);
    }
}
