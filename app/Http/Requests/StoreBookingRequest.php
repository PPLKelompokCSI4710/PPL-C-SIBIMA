<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Pastikan hanya mahasiswa yang bisa melakukan pengajuan
        return Auth::check() && Auth::user()->hasRole('mahasiswa');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'dosen_id' => 'required|exists:dosen,id',
            'schedule_id' => 'required|exists:schedules,id',
            'topik_bimbingan' => 'required|string|max:500',
            'tipe' => 'required|in:online,offline',
        ];
    }
}
