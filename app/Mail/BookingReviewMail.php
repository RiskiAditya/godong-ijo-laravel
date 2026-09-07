<?php

namespace App\Mail;

use App\Mail\Traits\HasBookingMessageId;
use App\Models\Pemesanan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingReviewMail extends Mailable
{
    use Queueable, SerializesModels, HasBookingMessageId;

    public function __construct(public Pemesanan $pemesanan)
    {
    }

    public function build()
    {
        return $this->subject('Bagaimana Pengalaman Anda di Godong Ijo?')
            ->view('emails.booking-review')
            ->text('emails.booking-review-text')
            ->with([
                'pemesanan' => $this->pemesanan,
                'reviewUrl' => config('app.google_review_url'),
            ])
            ->withHeaders([
                'X-Priority' => '3',
                'Precedence' => 'bulk',
                'Auto-Submitted' => 'auto-generated',
                'X-Mailer' => 'Laravel/' . app()->version(),
                'Message-ID' => $this->generateMessageId(),
            ]);
    }
}
