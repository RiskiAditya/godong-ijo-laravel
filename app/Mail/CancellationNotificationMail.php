<?php

namespace App\Mail;

use App\Mail\Traits\HasBookingMessageId;
use App\Models\Pemesanan;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CancellationNotificationMail extends Mailable
{
    use HasBookingMessageId, Queueable, SerializesModels;

    public $pemesanan;

    public $reason;

    public $refundInfo;

    /**
     * Create a new message instance.
     */
    public function __construct(Pemesanan $pemesanan, string $reason, ?string $refundInfo = null)
    {
        $this->pemesanan = $pemesanan;
        $this->reason = $reason;
        $this->refundInfo = $refundInfo;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Pembatalan Booking - '.$this->pemesanan->kode_booking)
            ->view('emails.cancellation-notification')
            ->text('emails.cancellation-notification-text')
            ->with([
                'pemesanan' => $this->pemesanan,
                'paket' => $this->pemesanan->jadwal?->paket ?? $this->pemesanan->paketWisata,
                'reason' => $this->reason,
                'refundInfo' => $this->refundInfo,
            ])
            ->withHeaders([
                'X-Priority' => '3',
                'X-Mailer' => 'Laravel/'.app()->version(),
                'Precedence' => 'bulk',
                'Auto-Submitted' => 'auto-generated',
                'Message-ID' => $this->generateMessageId(),
            ]);
    }
}
