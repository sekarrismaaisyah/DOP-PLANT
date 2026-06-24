<?php

declare(strict_types=1);

namespace App\Services\DopSafety;

use App\Enums\DopSafetyPlanStatus;
use App\Models\DopSafetyPlan;
use App\Models\DopSafetyPlanItem;
use Illuminate\Support\Facades\DB;

class DopSafetyPlanPersistenceService
{
    /**
     * @param  array<string, mixed>  $header
     * @param  list<array<string, mixed>>  $items
     */
    public function create(array $header, array $items, ?int $userId = null): DopSafetyPlan
    {
        return DB::transaction(function () use ($header, $items, $userId) {
            $plan = DopSafetyPlan::query()->create([
                ...$this->mapHeaderAttributes($header),
                'user_id' => $userId,
            ]);

            $this->syncItems($plan, $items);

            return $plan->load('items');
        });
    }

    /**
     * @param  array<string, mixed>  $header
     * @param  list<array<string, mixed>>  $items
     */
    public function update(DopSafetyPlan $plan, array $header, array $items): DopSafetyPlan
    {
        return DB::transaction(function () use ($plan, $header, $items) {
            $plan->update($this->mapHeaderAttributes($header));
            $plan->items()->delete();
            $this->syncItems($plan, $items);

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

            $plan = DopSafetyPlan::query()->updateOrCreate(
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

            $plan->items()->delete();
            $this->syncItems($plan, $items);

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
    private function syncItems(DopSafetyPlan $plan, array $items): void
    {
        foreach ($items as $index => $item) {
            DopSafetyPlanItem::query()->create([
                'dop_safety_plan_id' => $plan->id,
                'item_no' => (int) ($item['item_no'] ?? ($index + 1)),
                'section_name' => (string) ($item['section_name'] ?? ''),
                'unit_code' => (string) ($item['unit_code'] ?? 'N/A'),
                'unit_category' => (string) ($item['unit_category'] ?? ''),
                'location' => (string) ($item['location'] ?? ''),
                'job_detail' => (string) ($item['job_detail'] ?? ''),
                'work_permit' => (string) ($item['work_permit'] ?? 'N/A'),
                'tools' => $this->normalizeStringList($item['tools'] ?? []),
                'workers' => $this->normalizeStringList($item['workers'] ?? []),
                'cctv' => $this->nullableString($item['cctv'] ?? null),
                'group_leader' => $this->nullableString($item['group_leader'] ?? null),
                'section_head' => $this->nullableString($item['section_head'] ?? null),
                'she_leader' => $this->nullableString($item['she_leader'] ?? null),
                'dept_head' => $this->nullableString($item['dept_head'] ?? null),
                'pja_bc' => $this->nullableString($item['pja_bc'] ?? null),
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

    private function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim((string) $value);

        return $trimmed === '' ? null : $trimmed;
    }
}
