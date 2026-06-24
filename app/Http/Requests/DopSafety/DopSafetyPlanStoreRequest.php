<?php

declare(strict_types=1);

namespace App\Http\Requests\DopSafety;

use App\Enums\DopSafetyPlanStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DopSafetyPlanStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'site' => [
                'required',
                'string',
                'max:50',
                Rule::unique('dop_safety_plans')->where(function ($query) {
                    $query->where('site', $this->input('site'))
                        ->where('plan_date', $this->input('plan_date'))
                        ->where('shift', (int) $this->input('shift'));
                }),
            ],
            'plan_date' => ['required', 'date'],
            'shift' => ['required', 'integer', Rule::in([1, 2])],
            'status' => ['required', 'string', Rule::enum(DopSafetyPlanStatus::class)],
            'auth_location_date' => ['nullable', 'string', 'max:255'],
            'created_by_name' => ['nullable', 'string', 'max:255'],
            'created_by_position' => ['nullable', 'string', 'max:255'],
            'acknowledged_1_name' => ['nullable', 'string', 'max:255'],
            'acknowledged_1_position' => ['nullable', 'string', 'max:255'],
            'acknowledged_2_name' => ['nullable', 'string', 'max:255'],
            'acknowledged_2_position' => ['nullable', 'string', 'max:255'],
            'acknowledged_3_name' => ['nullable', 'string', 'max:255'],
            'acknowledged_3_position' => ['nullable', 'string', 'max:255'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.section_name' => ['required', 'string', Rule::in(config('dop_safety.sections', []))],
            'items.*.unit_code' => ['nullable', 'string', 'max:50'],
            'items.*.unit_category' => ['required', 'string', Rule::in(config('dop_safety.unit_categories', []))],
            'items.*.location' => ['required', 'string', 'max:255'],
            'items.*.job_detail' => ['required', 'string'],
            'items.*.work_permit' => ['nullable', 'string', 'max:255'],
            'items.*.tools' => ['nullable', 'string'],
            'items.*.workers' => ['nullable', 'string'],
            'items.*.cctv' => ['nullable', 'string', 'max:100'],
            'items.*.group_leader' => ['nullable', 'string', 'max:255'],
            'items.*.section_head' => ['nullable', 'string', 'max:255'],
            'items.*.she_leader' => ['nullable', 'string', 'max:255'],
            'items.*.dept_head' => ['nullable', 'string', 'max:255'],
            'items.*.pja_bc' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'site.unique' => 'DOP untuk Site, Tanggal, dan Shift ini sudah ada. Gunakan edit atau import untuk memperbarui.',
            'items.required' => 'Minimal satu item pekerjaan wajib diisi.',
            'items.min' => 'Minimal satu item pekerjaan wajib diisi.',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function headerPayload(): array
    {
        return $this->only([
            'site', 'plan_date', 'shift', 'status',
            'auth_location_date', 'created_by_name', 'created_by_position',
            'acknowledged_1_name', 'acknowledged_1_position',
            'acknowledged_2_name', 'acknowledged_2_position',
            'acknowledged_3_name', 'acknowledged_3_position',
        ]);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function itemsPayload(): array
    {
        $items = [];
        foreach ($this->input('items', []) as $index => $item) {
            $items[] = [
                'item_no' => $index + 1,
                'section_name' => $item['section_name'] ?? '',
                'unit_code' => ($item['unit_code'] ?? '') !== '' ? $item['unit_code'] : 'N/A',
                'unit_category' => $item['unit_category'] ?? '',
                'location' => $item['location'] ?? '',
                'job_detail' => $item['job_detail'] ?? '',
                'work_permit' => ($item['work_permit'] ?? '') !== '' ? $item['work_permit'] : 'N/A',
                'tools' => $item['tools'] ?? '',
                'workers' => $item['workers'] ?? '',
                'cctv' => $item['cctv'] ?? null,
                'group_leader' => $item['group_leader'] ?? null,
                'section_head' => $item['section_head'] ?? null,
                'she_leader' => $item['she_leader'] ?? null,
                'dept_head' => $item['dept_head'] ?? null,
                'pja_bc' => $item['pja_bc'] ?? null,
            ];
        }

        return $items;
    }
}
