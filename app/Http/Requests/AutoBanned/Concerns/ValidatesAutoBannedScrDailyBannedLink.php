<?php

declare(strict_types=1);

namespace App\Http\Requests\AutoBanned\Concerns;

use App\Support\AutoBanned\AutoBannedSchema;
use App\Support\AutoBanned\ScrDailyBannedColumns;
use Illuminate\Validation\Rule;

trait ValidatesAutoBannedScrDailyBannedLink
{
    /**
     * @return array<string, mixed>
     */
    protected function scrDailyBannedIdRules(): array
    {
        if (! AutoBannedSchema::hasScrDailyBannedTable()) {
            return ['scr_daily_banned_id' => ['nullable', 'integer']];
        }

        $sid = strtoupper(trim((string) $this->input('sid', '')));

        return [
            'scr_daily_banned_id' => [
                'required',
                'integer',
                Rule::exists('scr_daily_banned', 'id')->where(function ($query) use ($sid): void {
                    $query->whereRaw('UPPER(TRIM('.ScrDailyBannedColumns::SID.')) = ?', [$sid]);
                }),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function scrDailyBannedIdMessages(): array
    {
        return [
            'scr_daily_banned_id.required' => 'Pilih record Daily Banned yang terkait.',
            'scr_daily_banned_id.exists' => 'Record Daily Banned tidak valid untuk SID ini.',
        ];
    }
}
