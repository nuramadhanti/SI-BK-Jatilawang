<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePermohonanKonselingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && (
            auth()->user()->role === 'siswa' ||
            (auth()->user()->role === 'guru' && auth()->user()->guru && auth()->user()->guru->role_guru === 'walikelas')
        );
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'siswa_id' => 'required_if:role,guru|exists:siswa,id',
            'deskripsi_permasalahan' => 'required|string|min:10|max:1000',
            'bukti_masalah' => 'nullable|file|mimes:jpeg,jpg,png,mp4,mov,avi|max:50000',
            // Validate at least 3 criteria selected (Urgensi, Dampak, Kategori)
            // Riwayat is auto-detected
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'deskripsi_permasalahan.required' => 'Deskripsi permasalahan tidak boleh kosong',
            'deskripsi_permasalahan.min' => 'Deskripsi minimal 10 karakter',
            'deskripsi_permasalahan.max' => 'Deskripsi maksimal 1000 karakter',
            'bukti_masalah.mimes' => 'Format file tidak didukung. Gunakan: JPG, PNG, MP4, MOV, AVI',
            'bukti_masalah.max' => 'Ukuran file maksimal 50MB',
            'siswa_id.exists' => 'Siswa tidak ditemukan',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'deskripsi_permasalahan' => 'Deskripsi Permasalahan',
            'bukti_masalah' => 'Bukti Masalah',
            'siswa_id' => 'Siswa',
        ];
    }
}
