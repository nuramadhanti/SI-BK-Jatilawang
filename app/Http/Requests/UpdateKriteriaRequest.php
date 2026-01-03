<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKriteriaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && 
               auth()->user()->role === 'guru' && 
               auth()->user()->guru && 
               auth()->user()->guru->role_guru === 'bk';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $kriteriaId = $this->route('kriteria')?->id ?? null;

        return [
            'nama' => 'required|string|max:100|unique:kriterias,nama,' . $kriteriaId,
            'deskripsi' => 'nullable|string|max:500',
            'bobot' => 'required|numeric|min:0|max:1|regex:/^\d+(\.\d{1,2})?$/',
            'urutan' => 'required|integer|min:1|max:100',
            'aktif' => 'boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nama.required' => 'Nama kriteria tidak boleh kosong',
            'nama.unique' => 'Nama kriteria sudah ada',
            'nama.max' => 'Nama kriteria maksimal 100 karakter',
            'bobot.required' => 'Bobot tidak boleh kosong',
            'bobot.numeric' => 'Bobot harus berupa angka',
            'bobot.min' => 'Bobot minimal 0',
            'bobot.max' => 'Bobot maksimal 1',
            'bobot.regex' => 'Bobot harus dalam format decimal (contoh: 0.25)',
            'urutan.required' => 'Urutan tidak boleh kosong',
            'urutan.integer' => 'Urutan harus berupa angka',
            'urutan.min' => 'Urutan minimal 1',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'nama' => 'Nama Kriteria',
            'deskripsi' => 'Deskripsi',
            'bobot' => 'Bobot',
            'urutan' => 'Urutan',
            'aktif' => 'Status Aktif',
        ];
    }
}
