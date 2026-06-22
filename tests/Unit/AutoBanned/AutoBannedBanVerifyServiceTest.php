<?php

declare(strict_types=1);

use App\Services\AutoBanned\AutoBannedBanVerifyService;

beforeEach(function (): void {
    config([
        'auto_banned.ban_verify.executed_values' => ['NOT PASSED'],
    ]);
});

it('menganggap NOT PASSED sebagai banned dieksekusi', function (): void {
    $service = app(AutoBannedBanVerifyService::class);

    expect($service->isExecutedBan('NOT PASSED'))->toBeTrue()
        ->and($service->isExecutedBan('not passed'))->toBeTrue();
});

it('tidak menganggap status kosong atau passed sebagai banned', function (): void {
    $service = app(AutoBannedBanVerifyService::class);

    expect($service->isExecutedBan(''))->toBeFalse()
        ->and($service->isExecutedBan('PASSED'))->toBeFalse()
        ->and($service->isExecutedBan(null))->toBeFalse();
});

it('tidak menganggap NOT PASSED sebagai banned jika executed_values kosong dan fallback BANNED', function (): void {
    config(['auto_banned.ban_verify.executed_values' => []]);

    $service = app(AutoBannedBanVerifyService::class);

    expect($service->isExecutedBan('NOT PASSED'))->toBeFalse();
});
