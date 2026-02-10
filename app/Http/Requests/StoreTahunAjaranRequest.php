<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTahunAjaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'tahun_ajaran' => ['required', 'string', 'max:255'],
            'semester' => ['required', 'in:ganjil,genap'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'tahun_ajaran.required' => 'Tahun ajaran harus diisi',
            'tahun_ajaran.max' => 'Tahun ajaran maksimal 255 karakter',
            'semester.required' => 'Semester harus dipilih',
            'semester.in' => 'Semester harus ganjil atau genap',
        ];
    }
}
