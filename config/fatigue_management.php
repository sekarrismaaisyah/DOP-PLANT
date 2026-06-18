<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Role GMO — akses semua mitra (dashboard & upload)
    |--------------------------------------------------------------------------
    */
    'gmo_roles' => [
        'fatigue-management-gmo',
        'admin',
        'administrator',
    ],

    /*
    |--------------------------------------------------------------------------
    | Alias awalan email / nama user → partner key
    | Contoh: pama@gmail.com atau nama "PAMA HSE" → PAMA
    |--------------------------------------------------------------------------
    */
    'email_partner_aliases' => [
        'pama' => 'PAMA',
        'hrb' => 'HRB',
        'sci' => 'SCI',
        'scci' => 'SCCI',
        'aci' => 'ACI',
        'bar' => 'BAR',
        'opp' => 'OPP',
        'buma' => 'BUMA',
        'kdc' => 'KDC',
        'mop' => 'MOP',
        'dnx' => 'DNX',
        'dan' => 'DAN',
        'tmu' => 'TMU',
        'mtl' => 'MTL',
        'mtn' => 'MTN',
        'fad' => 'FAD',
    ],
];
