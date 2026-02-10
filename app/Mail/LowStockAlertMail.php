<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class LowStockAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Collection $outOfStock,
        public Collection $lowStock
    ) {}

    public function envelope(): Envelope
    {
        $count = $this->outOfStock->count() + $this->lowStock->count();
        return new Envelope(
            subject: '[S3VT Inventory] Low stock alert – ' . $count . ' product(s) need attention',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.low-stock-alert',
        );
    }
}
