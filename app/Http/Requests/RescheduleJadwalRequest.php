<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RescheduleJadwalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $ketersediaanId = $this->input('ketersediaan_jadwal_id');
        $isMockData = in_array($ketersediaanId, [8888, 8889, 8890]);

        return [
            'ketersediaan_jadwal_id' => ['required', $isMockData ? '' : 'exists:ketersediaan_jadwals,id'],
            'topik_bimbingan' => ['required', 'string'],
        ];
    }
    
    public function messages(): array
    {
        return [
            'ketersediaan_jadwal_id.required' => 'Jadwal baru harus dipilih.',
            'ketersediaan_jadwal_id.exists' => 'Jadwal yang dipilih tidak valid atau tidak ditemukan.',
            'topik_bimbingan.required' => 'Topik bimbingan tidak boleh kosong.',
        ];
    }
}
