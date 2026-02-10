<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNilaiWaliKelasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('wali_kelas');
    }

    public function rules(): array
    {
        return [
            'kelas_id' => ['required', 'exists:kelas,id'],
            'mata_pelajaran_id' => ['required', 'exists:mata_pelajarans,id'],
            'tahun_ajaran_id' => ['required', 'exists:tahun_ajarans,id'],
            'nilai' => ['required', 'array', 'min:1'],
            'nilai.*.siswa_id' => ['required', 'exists:siswa,id'],
            'nilai.*.nilai_angka' => ['required', 'numeric', 'min:0', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'kelas_id.required' => 'Kelas harus dipilih',
            'kelas_id.exists' => 'Kelas tidak valid',
            'mata_pelajaran_id.required' => 'Mata pelajaran harus dipilih',
            'mata_pelajaran_id.exists' => 'Mata pelajaran tidak valid',
            'tahun_ajaran_id.required' => 'Tahun ajaran harus dipilih',
            'tahun_ajaran_id.exists' => 'Tahun ajaran tidak valid',
            'nilai.required' => 'Data nilai harus diisi',
            'nilai.*.siswa_id.required' => 'Siswa ID harus ada',
            'nilai.*.siswa_id.exists' => 'Siswa tidak valid',
            'nilai.*.nilai_angka.required' => 'Nilai angka harus diisi',
            'nilai.*.nilai_angka.numeric' => 'Nilai angka harus berupa angka',
            'nilai.*.nilai_angka.min' => 'Nilai angka minimal 0',
            'nilai.*.nilai_angka.max' => 'Nilai angka maksimal 100',
        ];
    }
}
