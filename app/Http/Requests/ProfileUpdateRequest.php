<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
        ];

        if ($this->user()->hasRole('mahasiswa')) {
            $rules = array_merge($rules, [
                'nim' => ['required', 'string', 'max:50', Rule::unique('mahasiswa', 'nim')->ignore($this->user()->mahasiswa?->id)],
                'nama_lengkap' => ['required', 'string', 'max:255'],
                'program_studi' => ['required', 'string', 'max:255'],
                'fakultas' => ['required', 'string', 'max:255'],
                'angkatan' => ['required', 'integer', 'min:2000', 'max:2100'],
                'semester' => ['required', 'integer', 'min:1', 'max:14'],
                'no_telepon' => ['nullable', 'string', 'max:20'],
                'tanggal_lahir' => ['nullable', 'date'],
                'alamat' => ['nullable', 'string'],
            ]);
        } elseif ($this->user()->hasRole('dosen') || $this->user()->hasRole('admin')) {
            $rules = array_merge($rules, [
                'program_studi' => ['nullable', 'string', 'max:255'],
                'fakultas' => ['nullable', 'string', 'max:255'],
                'kode_dosen' => ['nullable', 'string', 'max:50'],
                'kuota_pembimbingan' => ['nullable', 'integer', 'min:0'],
            ]);
        }

        return $rules;
    }
}
