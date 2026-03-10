<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PlanningSummaryMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param array<int, array{lokasi: string, detail_lokasi: string, aktivitas: string, karyawans: array<int, string>}> $summary
     */
    public function __construct(
        public array $summary,
        public string $tanggal,
        public string $siteLabel
    ) {}

    public function envelope(): Envelope
    {
        $dateFormatted = Carbon::parse($this->tanggal)->locale('id')->translatedFormat('d F Y');
        return new Envelope(
            subject: 'Site Notice  - ' . $this->siteLabel . ' - ' . $dateFormatted,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.planning-summary',
        );
    }
}
