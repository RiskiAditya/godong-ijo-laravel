<?php

namespace App\Mail;

use App\Mail\Traits\HasBookingMessageId;
use App\Models\Pemesanan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingReminderMail extends Mailable
{
    use Queueable, SerializesModels, HasBookingMessageId;

    public function __construct(public Pemesanan $pemesanan, public string $visitDate)
    {
    }

    public function build()
    {
        return $this->subject('Pengingat Kunjungan Besok - ' . $this->pemesanan->kode_booking)
            ->view('emails.booking-reminder')
            ->text('emails.booking-reminder-text')
            ->with([
                'pemesanan' => $this->pemesanan,
                'paket' => $this->pemesanan->paketWisata,
                'visitDate' => $this->visitDate,
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
