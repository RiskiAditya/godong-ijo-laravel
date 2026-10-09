<?php

namespace App\Mail;

use Illuminate\Support\Facades\Http;
use InvalidArgumentException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;

class ResendTransport extends AbstractTransport
{
    public function __construct(private string $apiKey)
    {
        parent::__construct();

        if ($this->apiKey === '') {
            throw new InvalidArgumentException('The RESEND_API_KEY environment variable is not set.');
        }
    }

    protected function doSend(SentMessage $sentMessage): void
    {
        $email = $sentMessage->getOriginalMessage();

        if (! $email instanceof Email) {
            throw new InvalidArgumentException('The Resend transport only supports email messages.');
        }

        $payload = [
            'from' => $this->stringifyAddresses($email->getFrom())[0],
            'to' => $this->stringifyAddresses($email->getTo()),
            'subject' => $email->getSubject() ?? '',
        ];

        foreach (['cc' => $email->getCc(), 'bcc' => $email->getBcc(), 'reply_to' => $email->getReplyTo()] as $field => $addresses) {
            if ($addresses !== []) {
                $payload[$field] = $this->stringifyAddresses($addresses);
            }
        }

        if (($html = $this->bodyToString($email->getHtmlBody())) !== null) {
            $payload['html'] = $html;
        }

        if (($text = $this->bodyToString($email->getTextBody())) !== null) {
            $payload['text'] = $text;
        }

        if ($email->getAttachments() !== []) {
            $payload['attachments'] = array_map(fn (DataPart $attachment) => $this->formatAttachment($attachment), $email->getAttachments());
        }

        $response = Http::withToken($this->apiKey)
            ->acceptJson()
            ->post('https://api.resend.com/emails', $payload)
            ->throw();

        if ($messageId = $response->json('id')) {
            $sentMessage->setMessageId($messageId);
        }
    }

    private function bodyToString(mixed $body): ?string
    {
        if ($body === null) {
            return null;
        }

        return is_resource($body) ? stream_get_contents($body) : $body;
    }

    private function formatAttachment(DataPart $attachment): array
    {
        $formatted = [
            'filename' => $attachment->getFilename() ?? $attachment->getName() ?? 'attachment',
            'content' => base64_encode($attachment->getBody()),
        ];

        if ($attachment->getDisposition() === 'inline' && $attachment->hasContentId()) {
            $formatted['content_id'] = $attachment->getContentId();
        }

        return $formatted;
    }

    public function __toString(): string
    {
        return 'resend';
    }
}
