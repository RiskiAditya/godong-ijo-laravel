<?php

namespace App\Mail;

use App\Mail\Traits\HasBookingMessageId;
use App\Models\Pemesanan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentSuccessMail extends Mailable
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
        return $this->subject('Pembayaran Berhasil - E-Ticket ' . $this->pemesanan->kode_booking)
                    ->view('emails.payment-success')
                    ->text('emails.payment-success-text')
                    ->with([
                        'pemesanan' => $this->pemesanan,
                        'pembayaran' => $this->pemesanan->pembayaran,
                        'paket' => $this->pemesanan->jadwal->paket,
                    ])
                    ->withHeaders([
                        'X-Priority' => '1',
                        'Precedence' => 'bulk',
                        'Auto-Submitted' => 'auto-generated',
                        'X-Mailer' => 'Laravel/' . app()->version(),
                        'Message-ID' => $this->generateMessageId(),
                    ]);
    }
}
