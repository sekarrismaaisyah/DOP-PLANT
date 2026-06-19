<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk tabel scrape verifikasi status banned (eksternal).
 * Nama tabel di-set via config auto_banned.ban_verify.table.
 */
class ScrAutoBannedBanVerify extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    public function getTable(): string
    {
        return (string) config('auto_banned.ban_verify.table', '');
    }
}
