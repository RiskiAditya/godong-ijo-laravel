<?php

namespace App\Mail;

use App\Mail\Traits\HasBookingMessageId;
use App\Models\Pemesanan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingConfirmationMail extends Mailable
{
    use Queueable, SerializesModels, HasBookingMessageId;

    public $pemesanan;

    /**
     * Create a new message instance.
     */
    public function __construct(Pemesanan $pemesanan)
    {
        $this->pemesanan = $pemesanan;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Konfirmasi Booking - ' . $this->pemesanan->kode_booking)
                    ->view('emails.booking-confirmation')
                    ->text('emails.booking-confirmation-text')
                    ->with([
                        'pemesanan' => $this->pemesanan,
                        'paket' => $this->pemesanan->paketWisata ?? $this->pemesanan->jadwal->paket,
                        'jadwal' => $this->pemesanan->jadwal,
                    ])
                    ->withHeaders([
                        'X-Priority' => '3',
                        'X-Mailer' => 'Laravel/' . app()->version(),
                        'Precedence' => 'bulk',
                        'Auto-Submitted' => 'auto-generated',
                        'Message-ID' => $this->generateMessageId(),
                    ]);
    }
}
