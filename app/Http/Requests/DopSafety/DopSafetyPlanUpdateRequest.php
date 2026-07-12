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
        
        $plan = $this->route('plan');

        $tableName = $plan ? $plan->getTable() : 'dop_safety_plans';

        $rules['site'] = [
            'required',
            'string',
            'max:50',
            Rule::unique($tableName) // <--- Sekarang tabelnya dinamis!
                ->where(function ($query) {
                    $query->where('site', $this->input('site'))
                        ->where('plan_date', $this->input('plan_date'))
                        ->where('shift', (int) $this->input('shift'));
                })
                ->ignore($plan?->id),
        ];

        $rules['plan_date'] = ['required', 'date'];

        return $rules;
    }
}
