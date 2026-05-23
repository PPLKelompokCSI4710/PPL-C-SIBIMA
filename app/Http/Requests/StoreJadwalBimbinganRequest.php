<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreJadwalBimbinganRequest extends FormRequest
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
        return [
            'ketersediaan_jadwal_id' => ['required', 'exists:ketersediaan_jadwals,id'],
            'judul_ta' => ['required', 'string', 'max:255'],
            'topik_bimbingan' => ['required', 'string', 'max:1000'],
        ];
    }
}
