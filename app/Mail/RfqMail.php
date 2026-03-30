<?php

namespace App\Mail;

use App\Models\Rfq;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RfqMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public Rfq $rfq,
        public string $bodyHtml,
        public array $files = [],
    ) {}

    public function build(): self
    {
        $subject = sprintf('Request For Quotation #%d - %s', $this->rfq->id, $this->rfq->product_name);

        $mail = $this->subject($subject)
            ->view('emails.rfq');

        foreach ($this->files as $attachment) {
            $mail->attach($attachment['path'], [
                'as' => $attachment['name'] ?? basename($attachment['path']),
                'mime' => $attachment['mime'] ?? null,
            ]);
        }

        return $mail;
    }
}
