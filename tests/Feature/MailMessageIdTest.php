<?php

namespace Tests\Feature;

use App\Mail\BookingConfirmationMail;
use App\Mail\CancellationNotificationMail;
use App\Mail\PaymentSuccessMail;
use App\Models\Pemesanan;
use Tests\TestCase;

class MailMessageIdTest extends TestCase
{
    public function test_booking_confirmation_generates_valid_message_id(): void
    {
        $messageId = $this->messageIdFor('TEST123', BookingConfirmationMail::class);

        $this->assertMatchesRegularExpression('/^<[a-f0-9]+\.TEST123@/', $messageId);
        $this->assertStringContainsString(config('mail.domain', 'thewaterfall.com'), $messageId);
    }

    public function test_payment_success_generates_valid_message_id(): void
    {
        $messageId = $this->messageIdFor('PAY456', PaymentSuccessMail::class);

        $this->assertMatchesRegularExpression('/^<[a-f0-9]+\.PAY456@/', $messageId);
    }

    public function test_cancellation_generates_valid_message_id(): void
    {
        $messageId = $this->messageIdFor('CANCEL789', CancellationNotificationMail::class);

        $this->assertMatchesRegularExpression('/^<[a-f0-9]+\.CANCEL789@/', $messageId);
    }

    public function test_message_id_includes_booking_code(): void
    {
        $codes = ['BOOK001', 'EVENT2024', 'EDU-999'];
        
        foreach ($codes as $code) {
            $messageId = $this->messageIdFor($code, BookingConfirmationMail::class);
            
            $this->assertStringContainsString($code, $messageId);
        }
    }

    public function test_mail_domain_config_used_in_message_id(): void
    {
        config(['mail.domain' => 'custom.example.com']);
        
        $messageId = $this->messageIdFor('CONFIG01', BookingConfirmationMail::class);
        
        $this->assertStringContainsString('custom.example.com', $messageId);
    }

    private function messageIdFor(string $bookingCode, string $mailClass): string
    {
        $pemesanan = new Pemesanan(['kode_booking' => $bookingCode]);
        $mail = $mailClass === CancellationNotificationMail::class
            ? new $mailClass($pemesanan, 'Test reason')
            : new $mailClass($pemesanan);
        $reflection = new \ReflectionMethod($mail, 'generateMessageId');
        $reflection->setAccessible(true);

        return $reflection->invoke($mail);
    }
}
