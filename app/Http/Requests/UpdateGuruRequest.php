<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGuruRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'nip' => ['required', 'string', 'max:255', 'unique:guru,nip,' . $this->route('guru')->id],
            'nama_guru' => ['required', 'string', 'max:255'],
            'user_id' => ['nullable', 'exists:users,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'nip.required' => 'NIP harus diisi',
            'nip.unique' => 'NIP sudah ada',
            'nama_guru.required' => 'Nama guru harus diisi',
            'nama_guru.max' => 'Nama guru maksimal 255 karakter',
            'user_id.exists' => 'User tidak valid',
        ];
    }
}
