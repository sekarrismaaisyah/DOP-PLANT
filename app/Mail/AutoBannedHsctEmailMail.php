<?php

declare(strict_types=1);

namespace App\Mail;

use App\Enums\AutoBannedHsctEmailType;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AutoBannedHsctEmailMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array<int, array{sid: string, karyawan: string, site: string, perusahaan: string, reason: string}>  $employees
     * @param  array<int, array{label: string, count: int}>  $perusahaanSummary
     * @param  array<int, array{label: string, count: int}>  $siteSummary
     */
    public function __construct(
        public AutoBannedHsctEmailType $emailType,
        public int $reminderNumber,
        public string $week,
        public string $isoYear,
        public array $employees,
        public int $totalInitial,
        public int $confirmedCount,
        public int $pendingCount,
        public string $excelPath,
        public string $excelFilename,
        public array $perusahaanSummary = [],
        public array $siteSummary = [],
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->emailType === AutoBannedHsctEmailType::Initial
            ? "[Auto Banned] List Banned HSECT {$this->week} {$this->isoYear} — Email Awal (Selasa)"
            : "[Auto Banned] Reminder #{$this->reminderNumber} — Belum Banned ({$this->pendingCount} SID) {$this->week}";

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.auto-banned-hsct');
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
