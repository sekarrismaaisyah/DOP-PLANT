<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AutoBannedDailyBannedEmailMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array<int, array{perusahaan: string, site: string, count: int}>  $summaryRows
     */
    public function __construct(
        public string $filterDate,
        public string $filterShift,
        public string $scrapedAt,
        public int $totalBanned,
        public array $summaryRows,
        public int $perusahaanCount,
        public int $siteCount,
        public string $excelPath,
        public string $excelFilename,
        public ?string $dashboardUrl = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[Daily Banned] Notifikasi Banned {$this->filterDate} — {$this->filterShift} ({$this->totalBanned} karyawan)",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.auto-banned-daily-banned');
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        if ($this->excelPath === '' || ! is_file($this->excelPath)) {
            return [];
        }

        return [
            Attachment::fromPath($this->excelPath)
                ->as($this->excelFilename)
                ->withMime('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),
        ];
    }
}
