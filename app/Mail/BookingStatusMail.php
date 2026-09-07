<?php

namespace App\Mail;

use App\Mail\Traits\HasBookingMessageId;
use App\Models\Pemesanan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingStatusMail extends Mailable
{
    use Queueable, SerializesModels, HasBookingMessageId;

    public function __construct(public Pemesanan $pemesanan, public string $oldStatus)
    {
    }

    public function build()
    {
        return $this->subject('Status Booking Diperbarui - ' . $this->pemesanan->kode_booking)
            ->view('emails.booking-status')
            ->text('emails.booking-status-text')
            ->with(['pemesanan' => $this->pemesanan, 'oldStatus' => $this->oldStatus])
            ->withHeaders([
                'X-Priority' => '3',
                'Precedence' => 'bulk',
                'Auto-Submitted' => 'auto-generated',
                'X-Mailer' => 'Laravel/' . app()->version(),
                'Message-ID' => $this->generateMessageId(),
            ]);
    }
}
