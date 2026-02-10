<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBulkNilaiRequest extends FormRequest
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
            'tahun_ajaran_id' => ['required', 'exists:tahun_ajarans,id'],
            'mata_pelajaran_id' => ['required', 'exists:mata_pelajarans,id'],
            'guru_id' => ['required', 'exists:guru,id'],
            'nilai' => ['required', 'array', 'min:1'],
            'nilai.*.siswa_id' => ['required', 'exists:siswa,id'],
            'nilai.*.nilai_angka' => ['required', 'numeric', 'min:0', 'max:100'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'tahun_ajaran_id.required' => 'Tahun ajaran harus dipilih',
            'tahun_ajaran_id.exists' => 'Tahun ajaran tidak valid',
            'mata_pelajaran_id.required' => 'Mata pelajaran harus dipilih',
            'mata_pelajaran_id.exists' => 'Mata pelajaran tidak valid',
            'guru_id.required' => 'Guru harus dipilih',
            'guru_id.exists' => 'Guru tidak valid',
            'nilai.required' => 'Data nilai harus diisi',
            'nilai.*.siswa_id.required' => 'Siswa ID harus ada',
            'nilai.*.nilai_angka.required' => 'Nilai angka harus diisi',
            'nilai.*.nilai_angka.numeric' => 'Nilai angka harus berupa angka',
            'nilai.*.nilai_angka.min' => 'Nilai angka minimal 0',
            'nilai.*.nilai_angka.max' => 'Nilai angka maksimal 100',
        ];
    }
}
