<?php

declare(strict_types=1);

namespace App\Services\DopSafety;

use App\Enums\DopSafetyPlanStatus;
use App\Models\DopOjiPlan;
use App\Models\DopOjiPlanItem;
use Illuminate\Support\Facades\DB;

class DopOjiPlanPersistenceService
{
    /**
     * @param  array<string, mixed>  $header
     * @param  list<array<string, mixed>>  $items
     */
    public function create(array $header, array $items, ?int $userId = null): DopOjiPlan
    {
        return DB::transaction(function () use ($header, $items, $userId) {
            $plan = DopOjiPlan::query()->create([
                ...$this->mapHeaderAttributes($header),
                'user_id' => $userId,
            ]);

            $this->syncItems($plan, $items);

            return $plan->load('items');
        });
    }

    // /**
    //  * @param  array<string, mixed>  $header
    //  * @param  list<array<string, mixed>>  $items
    //  */
    // public function update(DopOjiPlan $plan, array $header, array $items): DopOjiPlan
    // {
    //     return DB::transaction(function () use ($plan, $header, $items) {
    //         $plan->update($this->mapHeaderAttributes($header));
    //         $plan->items()->delete();
    //         $this->syncItems($plan, $items);

    //         return $plan->fresh(['items']);
    //     });
    // }

    /**
     * @param  DopOjiPlan  $plan
     * @param  array<string, mixed>  $header
     * @param  list<array<string, mixed>>  $items
     */
    public function update(DopOjiPlan $plan, array $header, array $items): DopOjiPlan
    {
        return DB::transaction(function () use ($plan, $header, $items) {
            $plan->update($this->mapHeaderAttributes($header));

            $existingSafetyItemIds = $plan->items()
                ->whereNotNull('dop_safety_plan_item_id')
                ->pluck('dop_safety_plan_item_id', 'item_no')
                ->toArray(); 

            $keepOjiItemIds = [];

            foreach ($items as $idx => $itemData) {
                $itemNo = $itemData['item_no'] ?? ($idx + 1);

                $safetyPlanItemId = $existingSafetyItemIds[$itemNo] ?? null;

                $ojiItemPayload = array_merge($itemData, [
                    'item_no' => $itemNo,
                    'dop_safety_plan_item_id' => $safetyPlanItemId,
                ]);

                $ojiItem = $plan->items()->updateOrCreate(
                    [
                        'item_no' => $itemNo
                    ],
                    $ojiItemPayload
                );

                $keepOjiItemIds[] = $ojiItem->id;
            }

            if (!empty($keepOjiItemIds)) {
                $plan->items()->whereNotIn('id', $keepOjiItemIds)->delete();
            } else {
                $plan->items()->delete();
            }

            return $plan->fresh(['items']);
        });
    }


    /**
     * @param  array<string, mixed>  $header
     * @param  list<array<string, mixed>>  $items
     */
    // public function upsertByDocumentKey(array $header, array $items, ?int $userId = null): DopOjiPlan
    // {
    //     return DB::transaction(function () use ($header, $items, $userId) {
    //         $attrs = $this->mapHeaderAttributes($header);

    //         $plan = DopOjiPlan::query()->updateOrCreate(
    //             [
    //                 'site' => $attrs['site'],
    //                 'plan_date' => $attrs['plan_date'],
    //                 'shift' => $attrs['shift'],
    //             ],
    //             [
    //                 ...$attrs,
    //                 'user_id' => $userId,
    //             ],
    //         );

    //         $plan->items()->delete();
    //         $this->syncItems($plan, $items);

    //         return $plan->fresh(['items']);
    //     });
    // }

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
    private function syncItems(DopOjiPlan $plan, array $items): void
    {
        foreach ($items as $index => $item) {
            DopOjiPlanItem::query()->create([
                'dop_oji_plan_id' => $plan->id,
                'item_no' => (int) ($item['item_no'] ?? ($index + 1)),
                'section_name' => (string) ($item['section_name'] ?? ''),
                'unit_code' => (string) ($item['unit_code'] ?? 'N/A'),
                'location' => (string) ($item['location'] ?? ''),
                'job_detail' => (string) ($item['job_detail'] ?? ''),
                'work_permit' => (string) ($item['work_permit'] ?? 'N/A'),
                'tools' => $this->normalizeStringList($item['tools'] ?? []),
                'workers' => $this->normalizeWorkers($item['workers'] ?? []),
                'cctv' => $this->nullableString($item['cctv'] ?? null),
                'group_leader' => $this->nullableString($item['group_leader'] ?? null),

                'evidence_1' => $this->nullableString($item['evidence_1'] ?? null),
                'evidence_2' => $this->nullableString($item['evidence_2'] ?? null),
                'evidence_3' => $this->nullableString($item['evidence_3'] ?? null),
                'evidence_4' => $this->nullableString($item['evidence_4'] ?? null),

                'group_leader_sid' => $this->nullableString($item['group_leader_sid'] ?? null),
                'section_head' => $this->nullableString($item['section_head'] ?? null),
                'section_head_sid' => $this->nullableString($item['section_head_sid'] ?? null),
                'she_leader' => $this->nullableString($item['she_leader'] ?? null),
                'she_leader_sid' => $this->nullableString($item['she_leader_sid'] ?? null),
                'dept_head' => $this->nullableString($item['dept_head'] ?? null),
                'dept_head_sid' => $this->nullableString($item['dept_head_sid'] ?? null),
                'pja_bc' => $this->nullableString($item['pja_bc'] ?? null),
                'approval_status' => $this->nullableString(
                    $item['approval_status'] ?? 'waiting_dept_head'
                ),

                'reject_reason' => $this->nullableString(
                    $item['reject_reason'] ?? null
                ),
            ]);
        }
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
}
