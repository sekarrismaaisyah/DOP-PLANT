<?php

declare(strict_types=1);

namespace App\Support\AutoBanned;

/**
 * Nama kolom aktual pada tabel scr_daily_banned (scraping Tableau Daily Banned).
 */
final class ScrDailyBannedColumns
{
    public const TABLE = 'scr_daily_banned';

    public const SID = 'sid_pelapor_all_karyawan';

    public const NIK = 'nik_pelapor_all_karyawan';

    public const NAMA = 'pelapor_all_karyawan';

    public const PERUSAHAAN = 'perusahaan_pelapor_all_karyawan';

    public const SITE = 'site_dedicated_pelapor_all_karyawan';

    public const BANNED_REASON = 'Banned_Daily_Reason';

    public const BANNED_STATUS = 'Status_Banned_Daily';

    public const ONSITE_STATUS = 'Status_Onsite_Daily';

    public const HZR = 'n_HZR_Daily';

    public const INS = 'n_INS_Daily';

    public const OBS_OAK = 'n_OBS_OAK_Daily';

    public const RFID = 'n_RFID_Daily';

    public const RFID_QUALITY = 'RFID_Data_Quality_Daily';

    public const SAP_LABEL = 'SAP_Daily_Label';

    public const ACTIVITY_SOURCE = 'Sumber_Aktivitas_Daily';
}
