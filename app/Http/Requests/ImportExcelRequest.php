<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportExcelRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->isAdmin() || $this->user()->hasRole('wali_kelas');
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'], // Max 5MB
            'tipe_import' => ['required', 'in:siswa,nilai'],
            'tahun_ajaran_id' => ['required_if:tipe_import,nilai', 'nullable', 'exists:tahun_ajarans,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'file.required' => 'File harus diunggah',
            'file.file' => 'File harus berupa file Excel atau CSV',
            'file.mimes' => 'File harus bertipe xlsx, xls, atau csv',
            'file.max' => 'Ukuran file maksimal 5MB',
            'tipe_import.required' => 'Tipe import harus dipilih',
            'tipe_import.in' => 'Tipe import harus siswa atau nilai',
            'tahun_ajaran_id.required_if' => 'Tahun ajaran harus dipilih saat import nilai',
            'tahun_ajaran_id.exists' => 'Tahun ajaran tidak ditemukan',
        ];
    }
}
