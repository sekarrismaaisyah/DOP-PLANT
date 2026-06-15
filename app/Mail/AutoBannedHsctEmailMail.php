<?php

declare(strict_types=1);

namespace App\Mail;

use App\Enums\AutoBannedHsctEmailType;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AutoBannedHsctEmailMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array<int, array{sid: string, karyawan: string, site: string, perusahaan: string, reason: string}>  $employees
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
}
