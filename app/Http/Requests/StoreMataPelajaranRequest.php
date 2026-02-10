<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMataPelajaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'kode_mapel' => ['required', 'string', 'max:255', 'unique:mata_pelajarans,kode_mapel'],
            'nama_mapel' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'kode_mapel.required' => 'Kode mata pelajaran harus diisi',
            'kode_mapel.unique' => 'Kode mata pelajaran sudah ada',
            'nama_mapel.required' => 'Nama mata pelajaran harus diisi',
            'nama_mapel.max' => 'Nama mata pelajaran maksimal 255 karakter',
        ];
    }
}
