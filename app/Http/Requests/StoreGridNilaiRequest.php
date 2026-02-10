<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGridNilaiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin() || $this->user()->hasRole('wali_kelas');
    }

    public function rules(): array
    {
        return [
            'kelas_id' => ['required', 'exists:kelas,id'],
            'tahun_ajaran_id' => ['required', 'exists:tahun_ajarans,id'],
            'nilai' => ['required', 'array', 'min:1'],
            'nilai.*.siswa_id' => ['required', 'exists:siswa,id'],
            'nilai.*.mata_pelajaran_id' => ['required', 'exists:mata_pelajarans,id'],
            'nilai.*.guru_id' => ['required', 'exists:guru,id'],
            'nilai.*.nilai_angka' => ['required', 'numeric', 'min:0', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'kelas_id.required' => 'Kelas harus dipilih',
            'tahun_ajaran_id.required' => 'Tahun ajaran harus dipilih',
            'nilai.required' => 'Data nilai harus diisi',
            'nilai.min' => 'Minimal satu nilai harus diisi',
            'nilai.*.siswa_id.required' => 'Siswa harus ada',
            'nilai.*.mata_pelajaran_id.required' => 'Mata pelajaran harus ada',
            'nilai.*.guru_id.required' => 'Guru harus ada',
            'nilai.*.nilai_angka.required' => 'Nilai angka harus diisi',
            'nilai.*.nilai_angka.numeric' => 'Nilai harus berupa angka',
            'nilai.*.nilai_angka.min' => 'Nilai minimal 0',
            'nilai.*.nilai_angka.max' => 'Nilai maksimal 100',
        ];
    }
}
