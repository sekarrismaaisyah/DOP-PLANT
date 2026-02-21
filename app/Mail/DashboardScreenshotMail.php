<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DashboardScreenshotMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @param string $screenshotPath Path to PNG screenshot file
     * @param string $timeOfDay Pagi / Siang / Sore
     * @param array<string, mixed> $summary Ringkasan metrik dari DashboardEmailSummaryService
     * @param string $dashboardUrl URL dashboard untuk link CTA
     */
    public function __construct(
        public string $screenshotPath,
        public string $timeOfDay,
        public array $summary = [],
        public string $dashboardUrl = '#'
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $appName = config('app.name');
        return new Envelope(
            subject: "[{$this->timeOfDay}] Daily Report IKK - {$appName}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.dashboard-screenshot',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        if (!is_file($this->screenshotPath)) {
            return [];
        }

        return [
            Attachment::fromPath($this->screenshotPath)
                ->as('dashboard-dopm-' . $this->timeOfDay . '.png')
                ->withMime('image/png'),
        ];
    }
}
