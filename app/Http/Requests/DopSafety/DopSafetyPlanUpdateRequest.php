<?php

declare(strict_types=1);

namespace App\Http\Requests\DopSafety;

use Illuminate\Validation\Rule;

class DopSafetyPlanUpdateRequest extends DopSafetyPlanStoreRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = parent::rules();
        /** @var \App\Models\DopSafetyPlan|null $plan */
        $plan = $this->route('plan');

        $rules['site'] = [
            'required',
            'string',
            'max:50',
            Rule::unique('dop_safety_plans')
                ->where(function ($query) {
                    $query->where('site', $this->input('site'))
                        ->where('plan_date', $this->input('plan_date'))
                        ->where('shift', (int) $this->input('shift'));
                })
                ->ignore($plan?->id),
        ];

        return $rules;
    }
}
