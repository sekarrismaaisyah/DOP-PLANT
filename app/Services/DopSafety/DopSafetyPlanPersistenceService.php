<?php

declare(strict_types=1);

namespace App\Services\DopSafety;

use App\Enums\DopSafetyPlanStatus;
use App\Models\DopSafetyPlan;
use App\Models\DopSafetyPlanItem;
use App\Models\DopOjiPlan;
use App\Models\DopOjiPlanItem;
use Illuminate\Support\Facades\DB;

class DopSafetyPlanPersistenceService
{
    /**
     * @param  array<string, mixed>  $header
     * @param  list<array<string, mixed>>  $items
     */
    // public function create(array $header, array $items, ?int $userId = null): DopSafetyPlan
    // {
    //     return DB::transaction(function () use ($header, $items, $userId) {

    //         $attributes = [
    //             ...$this->mapHeaderAttributes($header),
    //             'user_id' => $userId,
    //         ];

    //         // Simpan DOP
    //         $plan = DopSafetyPlan::query()->create($attributes);

    //         $this->syncItems($plan, $items);

    //         // Simpan OJI
    //         $ojiPlan = DopOjiPlan::query()->updateOrCreate(
    //             [
    //                 'site' => $attributes['site'],
    //                 'company' => $attributes['company'],       // <--- Tambahkan Kunci
    //                 'department' => $attributes['department'],
    //                 'plan_date' => $attributes['plan_date'],
    //                 'shift' => $attributes['shift'],
    //             ],
    //             $attributes,
    //         );

    //         $ojiPlan->items()->delete();

    //         $this->syncOjiItems($ojiPlan, $items);

    //         return $plan->load('items');
    //     });
    // }
    public function create(array $header, array $items, ?int $userId = null): DopSafetyPlan
    {
        return DB::transaction(function () use ($header, $items, $userId) {

            $attributes = [
                ...$this->mapHeaderAttributes($header),
                'user_id' => $userId,
            ];

            // Simpan DOP
            $plan = DopSafetyPlan::query()->create($attributes);

            // TANGKAP HASILNYA!
            $savedSafetyItems = $this->syncItems($plan, $items);

            // Simpan OJI
            $ojiPlan = DopOjiPlan::query()->updateOrCreate(
                [
                    'site' => $attributes['site'],
                    'company' => $attributes['company'],
                    'department' => $attributes['department'],
                    'plan_date' => $attributes['plan_date'],
                    'shift' => $attributes['shift'],
                ],
                $attributes,
            );

            // GUNAKAN VARIABEL YANG DITANGKAP TADI!
            $this->syncOjiItems($ojiPlan, $savedSafetyItems);

            return $plan->load('items');
        });
    }

    // /**
    //  * @param  array<string, mixed>  $header
    //  * @param  list<array<string, mixed>>  $items
    //  */
    // public function update(DopSafetyPlan $plan, array $header, array $items): DopSafetyPlan
    // {
    //     return DB::transaction(function () use ($plan, $header, $items) {

    //         $attributes = $this->mapHeaderAttributes($header);

    //         $plan->update($attributes);

    //         $plan->items()->delete();

    //         $this->syncItems($plan, $items);

    //         $ojiPlan = DopOjiPlan::query()->updateOrCreate(
    //             [
    //                 'site' => $attributes['site'],
    //                 'company' => $attributes['company'],       // <--- Tambahkan Kunci
    //                 'department' => $attributes['department'],
    //                 'plan_date' => $attributes['plan_date'],
    //                 'shift' => $attributes['shift'],
    //             ],
    //             $attributes,
    //         );

    //         $ojiPlan->items()->delete();

    //         $this->syncOjiItems($ojiPlan, $items);

    //         return $plan->fresh(['items']);
    //     });
    // }

    /**
     * @param  array<string, mixed>  $header
     * @param  list<array<string, mixed>>  $items
     */
    public function update(DopSafetyPlan $plan, array $header, array $items): DopSafetyPlan
    {
        return DB::transaction(function () use ($plan, $header, $items) {
            
            // Tangkap nilai asli sebelum di-update (Perbaikan yang tadi)
            $originalSite       = $plan->getOriginal('site');
            $originalCompany    = $plan->getOriginal('company');
            $originalDepartment = $plan->getOriginal('department');
            $originalPlanDate   = $plan->getOriginal('plan_date');
            $originalShift      = $plan->getOriginal('shift');

            $attributes = $this->mapHeaderAttributes($header);

            $plan->update($attributes);

            $plan->items()->delete();

            // ========================================================
            // PERBAIKAN: TANGKAP HASILNYA KE DALAM VARIABEL
            // ========================================================
            $savedSafetyItems = $this->syncItems($plan, $items);

            $ojiPlan = DopOjiPlan::query()->updateOrCreate(
                [
                    'site'       => $originalSite,
                    'company'    => $originalCompany,
                    'department' => $originalDepartment,
                    'plan_date'  => $originalPlanDate,
                    'shift'      => $originalShift,
                ],
                $attributes 
            );

            // Karena data OJI lama sudah ketemu, kita tidak boleh main delete() sembarangan
            // Hapus baris ini: $ojiPlan->items()->delete(); (TIDAK PERLU LAGI karena pakai updateOrCreate di itemnya)

            // ========================================================
            // PERBAIKAN: GUNAKAN VARIABEL YANG SUDAH ADA ID-NYA
            // ========================================================
            $this->syncOjiItems($ojiPlan, $savedSafetyItems);

            return $plan->fresh(['items']);
        });
    }


    /**
     * @param  array<string, mixed>  $header
     * @param  list<array<string, mixed>>  $items
     */
    public function upsertByDocumentKey(array $header, array $items, ?int $userId = null): DopSafetyPlan
    {
        return DB::transaction(function () use ($header, $items, $userId) {

            $attrs = $this->mapHeaderAttributes($header);

            // ==========================
            // SIMPAN DOP SAFETY
            // ==========================
            $plan = DopSafetyPlan::query()->updateOrCreate(
                [
                    'site' => $attrs['site'],
                    'company' => $attrs['company'],       // <--- Tambahkan Kunci
                    'department' => $attrs['department'], 
                    'plan_date' => $attrs['plan_date'],
                    'shift' => $attrs['shift'],
                ],
                [
                    ...$attrs,
                    'user_id' => $userId,
                ],
            );

            // $plan->items()->delete();
            
            // Simpan ODP item dan tangkap hasilnya (beserta ID database)
            $savedSafetyItems = $this->syncItems($plan, $items);

            // ==========================
            // SIMPAN DOP OJI
            // ==========================
            $ojiPlan = DopOjiPlan::query()->updateOrCreate(
                [
                    'site' => $attrs['site'],
                    'plan_date' => $attrs['plan_date'],
                    'shift' => $attrs['shift'],
                ],
                [
                    ...$attrs,
                    'user_id' => $userId,
                ],
            );

            // $ojiPlan->items()->delete();

            // Jalankan sinkronisasi OJI item menggunakan data ODP ber-ID
            $this->syncOjiItems($ojiPlan, $savedSafetyItems);

            return $plan->fresh(['items']);
        });
    }

    /**
     * @param  array<string, mixed>  $header
     * @return array<string, mixed>
     */
    private function mapHeaderAttributes(array $header): array
    {
        $status = $header['status'] ?? DopSafetyPlanStatus::Draft->value;
        if ($status instanceof DopSafetyPlanStatus) {
            $status = $status->value;
        }

        return [
            'site' => (string) ($header['site'] ?? ''),
            'company' => (string) ($header['company'] ?? ''),       // <--- TAMBAHKAN INI
            'department' => (string) ($header['department'] ?? ''),
            'plan_date' => (string) ($header['plan_date'] ?? ''),
            'shift' => (int) ($header['shift'] ?? 1),
            'status' => $status,
            'auth_location_date' => $this->nullableString($header['auth_location_date'] ?? null),
            'created_by_name' => $this->nullableString($header['created_by_name'] ?? null),
            'created_by_position' => $this->nullableString($header['created_by_position'] ?? null),
            'acknowledged_1_name' => $this->nullableString($header['acknowledged_1_name'] ?? null),
            'acknowledged_1_position' => $this->nullableString($header['acknowledged_1_position'] ?? null),
            'acknowledged_2_name' => $this->nullableString($header['acknowledged_2_name'] ?? null),
            'acknowledged_2_position' => $this->nullableString($header['acknowledged_2_position'] ?? null),
            'acknowledged_3_name' => $this->nullableString($header['acknowledged_3_name'] ?? null),
            'acknowledged_3_position' => $this->nullableString($header['acknowledged_3_position'] ?? null),
        ];
    }

    /**
     * @param  list<array<string, mixed>>  $items
     */
    private function syncItems(DopSafetyPlan $plan, array $items): array
    {
        $savedItems = [];
        $lastItemNo = DopSafetyPlanItem::where('dop_safety_plan_id', $plan->id)->max('item_no') ?? 0;

        foreach ($items as $index => $item) {
            $lastItemNo++;
            $savedItems[] = DopSafetyPlanItem::query()->create([
                'dop_safety_plan_id' => $plan->id,
                'item_no' => $lastItemNo,
                'section_name' => (string) ($item['section_name'] ?? ''),
                'unit_code' => (string) ($item['unit_code'] ?? 'N/A'),
                'location' => (string) ($item['location'] ?? ''),
                'job_detail' => (string) ($item['job_detail'] ?? ''),
                'work_permit' => (string) ($item['work_permit'] ?? 'N/A'),
                'tools' => $this->normalizeStringList($item['tools'] ?? []),
                'workers' => $this->normalizeWorkers($item['workers'] ?? []),
                'cctv' => $this->nullableString($item['cctv'] ?? null),
                'group_leader' => $this->nullableString($item['group_leader'] ?? null),
                'group_leader_sid' => $this->nullableString($item['group_leader_sid'] ?? null),
                'section_head' => $this->nullableString($item['section_head'] ?? null),
                'section_head_sid' => $this->nullableString($item['section_head_sid'] ?? null),
                'she_leader' => $this->nullableString($item['she_leader'] ?? null),
                'she_leader_sid' => $this->nullableString($item['she_leader_sid'] ?? null),
                'dept_head' => $this->nullableString($item['dept_head'] ?? null),
                'dept_head_sid' => $this->nullableString($item['dept_head_sid'] ?? null),
                'pja_bc' => $this->nullableString($item['pja_bc'] ?? null),
            ]);
        }

        return $savedItems;
    }

    /**
     * @param  mixed  $value
     * @return list<string>
     */
    private function normalizeStringList(mixed $value): array
    {
        if (is_array($value)) {
            return array_values(array_filter(array_map(static fn ($v) => trim((string) $v), $value)));
        }

        if (is_string($value) && trim($value) !== '') {
            return array_values(array_filter(array_map('trim', preg_split('/[,;|]/', $value) ?: [])));
        }

        return [];
    }

    /**
     * @param  mixed  $value
     * @return list<array{name: string, sid: string}>
     */
    private function normalizeWorkers(mixed $value): array
    {
        if (is_string($value) && trim($value) !== '') {
            return \App\Support\DopSafety\DopSafetyPlanTableStructure::parseWorkersFromCells($value, '');
        }

        if (! is_array($value)) {
            return [];
        }

        $workers = [];
        foreach ($value as $worker) {
            if (is_array($worker)) {
                $name = trim((string) ($worker['name'] ?? ''));
                $sid = trim((string) ($worker['sid'] ?? ''));
            } else {
                $name = trim((string) $worker);
                $sid = '';
            }

            if ($name === '' && $sid === '') {
                continue;
            }

            $workers[] = ['name' => $name, 'sid' => $sid];
        }

        return $workers;
    }

    private function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim((string) $value);

        return $trimmed === '' ? null : $trimmed;
    }

    // private function syncOjiItems(DopOjiPlan $plan, array $items): void
    // {
    //     foreach ($items as $index => $item) {
    //         DopOjiPlanItem::query()->create([
    //             'dop_oji_plan_id' => $plan->id,

    //             'item_no' => (int) ($item['item_no'] ?? ($index + 1)),
    //             'section_name' => (string) ($item['section_name'] ?? ''),
    //             'unit_code' => (string) ($item['unit_code'] ?? 'N/A'),
    //             'location' => (string) ($item['location'] ?? ''),
    //             'job_detail' => (string) ($item['job_detail'] ?? ''),
    //             'work_permit' => (string) ($item['work_permit'] ?? 'N/A'),

    //             'tools' => $this->normalizeStringList($item['tools'] ?? []),
    //             'workers' => $this->normalizeWorkers($item['workers'] ?? []),

    //             'cctv' => $this->nullableString($item['cctv'] ?? null),

    //             'group_leader' => $this->nullableString($item['group_leader'] ?? null),
    //             'group_leader_sid' => $this->nullableString($item['group_leader_sid'] ?? null),

    //             // khusus OJI
    //             'evidence_1' => null,
    //             'evidence_2' => null,
    //             'evidence_3' => null,
    //             'evidence_4' => null,

    //             'section_head' => $this->nullableString($item['section_head'] ?? null),
    //             'section_head_sid' => $this->nullableString($item['section_head_sid'] ?? null),

    //             'she_leader' => $this->nullableString($item['she_leader'] ?? null),
    //             'she_leader_sid' => $this->nullableString($item['she_leader_sid'] ?? null),

    //             'dept_head' => $this->nullableString($item['dept_head'] ?? null),
    //             'dept_head_sid' => $this->nullableString($item['dept_head_sid'] ?? null),

    //             'pja_bc' => $this->nullableString($item['pja_bc'] ?? null),
    //         ]);
    //     }
    // }  

   private function syncOjiItems(DopOjiPlan $plan, array $savedSafetyItems): void
    {
        // Helper fungsi pembaca data agar kebal error (bisa baca Array maupun Object)
        $getVal = fn($item, $key) => is_array($item) ? ($item[$key] ?? null) : ($item->$key ?? null);

        foreach ($savedSafetyItems as $index => $safetyItem) {
            
            $safetyItemId = $getVal($safetyItem, 'id');

            // Pastikan ID tidak kosong
            if (! $safetyItemId) {
                continue;
            }

            // Gunakan updateOrCreate agar saat Edit (Update) datanya ditimpa, bukan ditambah (duplikat)
            DopOjiPlanItem::query()->updateOrCreate(
                [
                    // Kondisi Pencarian (Kunci Relasi)
                    'dop_oji_plan_id' => $plan->id,
                    'dop_safety_plan_item_id' => $safetyItemId,
                ],
                [
                    // Data yang akan diisi/diupdate
                    'item_no' => $getVal($safetyItem, 'item_no'),
                    'section_name' => $getVal($safetyItem, 'section_name'),
                    'unit_code' => $getVal($safetyItem, 'unit_code'),
                    'location' => $getVal($safetyItem, 'location'),
                    'job_detail' => $getVal($safetyItem, 'job_detail'),
                    'work_permit' => $getVal($safetyItem, 'work_permit'),

                    'tools' => $getVal($safetyItem, 'tools'),
                    'workers' => $getVal($safetyItem, 'workers'),
                    'cctv' => $getVal($safetyItem, 'cctv'),

                    'group_leader' => $getVal($safetyItem, 'group_leader'),
                    'group_leader_sid' => $getVal($safetyItem, 'group_leader_sid'),

                    'section_head' => $getVal($safetyItem, 'section_head'),
                    'section_head_sid' => $getVal($safetyItem, 'section_head_sid'),

                    'she_leader' => $getVal($safetyItem, 'she_leader'),
                    'she_leader_sid' => $getVal($safetyItem, 'she_leader_sid'),

                    'dept_head' => $getVal($safetyItem, 'dept_head'),
                    'dept_head_sid' => $getVal($safetyItem, 'dept_head_sid'),

                    'pja_bc' => $getVal($safetyItem, 'pja_bc'),
                ]
            );
        }
    }
}
