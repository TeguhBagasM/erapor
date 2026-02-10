<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSiswaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'nis' => ['required', 'string', 'max:255', 'unique:siswa,nis'],
            'nama_siswa' => ['required', 'string', 'max:255'],
            'kelas_id' => ['required', 'exists:kelas,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'nis.required' => 'NIS harus diisi',
            'nis.unique' => 'NIS sudah ada',
            'nama_siswa.required' => 'Nama siswa harus diisi',
            'nama_siswa.max' => 'Nama siswa maksimal 255 karakter',
            'kelas_id.required' => 'Kelas harus dipilih',
            'kelas_id.exists' => 'Kelas tidak valid',
        ];
    }
}
