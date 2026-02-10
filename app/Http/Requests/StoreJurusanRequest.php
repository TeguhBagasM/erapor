<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJurusanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'nama_jurusan' => ['required', 'string', 'max:255', 'unique:jurusans,nama_jurusan'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_jurusan.required' => 'Nama jurusan harus diisi',
            'nama_jurusan.max' => 'Nama jurusan maksimal 255 karakter',
            'nama_jurusan.unique' => 'Nama jurusan sudah ada',
        ];
    }
}
