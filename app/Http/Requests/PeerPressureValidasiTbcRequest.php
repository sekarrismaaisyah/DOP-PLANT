<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PeerPressureValidasiTbcRequest extends FormRequest
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
            'validator' => ['nullable', 'string', 'max:255'],
            'tasklist' => ['nullable', 'string'],
            'to_be_concerned_hazard' => ['nullable', 'string'],
            'gr_pspp' => ['nullable', 'string', 'max:255'],
            'catatan' => ['nullable', 'string'],
            'no_item_pspp' => ['nullable', 'string', 'max:255'],
            'kategori_gr' => ['nullable', 'string', 'max:255'],
            'kategori_gr_valid_kpi' => ['nullable', 'string', 'max:255'],
            'blindspot_terlapor_bc' => ['nullable', 'string'],
            'pic_aktual' => ['nullable', 'string', 'max:500'],
            'kronologi_singkat' => ['nullable', 'string'],
            'rootcause_aktual' => ['nullable', 'string'],
            'detail_rootcause_aktual' => ['nullable', 'string'],
            'tindakan_perbaikan_aktual' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function attributesPayload(): array
    {
        $v = $this->validated();
        $out = [];
        foreach ($this->fillableKeys() as $key) {
            $val = $v[$key] ?? null;
            if (is_string($val)) {
                $val = trim($val);
            }
            $out[$key] = $val !== '' && $val !== null ? $val : null;
        }

        return $out;
    }

    /**
     * @return list<string>
     */
    private function fillableKeys(): array
    {
        return [
            'validator',
            'tasklist',
            'to_be_concerned_hazard',
            'gr_pspp',
            'catatan',
            'no_item_pspp',
            'kategori_gr',
            'kategori_gr_valid_kpi',
            'blindspot_terlapor_bc',
            'pic_aktual',
            'kronologi_singkat',
            'rootcause_aktual',
            'detail_rootcause_aktual',
            'tindakan_perbaikan_aktual',
        ];
    }
}
