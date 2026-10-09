<?php

namespace Tests\Unit;

use App\Mail\ResendTransport;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mime\Email;
use Tests\TestCase;

class ResendTransportTest extends TestCase
{
    public function test_it_sends_email_content_and_attachments_through_resend_api(): void
    {
        Http::fake();

        $email = (new Email)
            ->from('Godong Ijo <verified@example.com>')
            ->to('guest@example.com')
            ->subject('Booking confirmed')
            ->text('Your booking is confirmed.')
            ->html('<p>Your booking is confirmed.</p>')
            ->attach('ticket contents', 'ticket.pdf', 'application/pdf');

        (new ResendTransport('re_test_key'))->send($email);

        Http::assertSent(function (Request $request): bool {
            $payload = $request->data();

            return $request->url() === 'https://api.resend.com/emails'
                && $request->hasHeader('Authorization', 'Bearer re_test_key')
                && $payload['from'] === '"Godong Ijo" <verified@example.com>'
                && $payload['to'] === ['guest@example.com']
                && $payload['subject'] === 'Booking confirmed'
                && $payload['html'] === '<p>Your booking is confirmed.</p>'
                && $payload['text'] === 'Your booking is confirmed.'
                && $payload['attachments'][0]['filename'] === 'ticket.pdf'
                && $payload['attachments'][0]['content'] === base64_encode('ticket contents');
        });
    }

    public function test_laravel_resolves_the_resend_mailer_from_configuration(): void
    {
        Http::fake();
        config()->set('mail.mailers.resend', [
            'transport' => 'resend',
            'key' => 're_test_key',
        ]);
        config()->set('mail.from', [
            'address' => 'verified@example.com',
            'name' => 'Godong Ijo',
        ]);

        Mail::mailer('resend')->raw('Mail body', function ($message): void {
            $message->to('guest@example.com')->subject('Booking confirmed');
        });

        Http::assertSent(fn (Request $request): bool => $request->url() === 'https://api.resend.com/emails'
            && $request->data()['text'] === 'Mail body');
    }
}
