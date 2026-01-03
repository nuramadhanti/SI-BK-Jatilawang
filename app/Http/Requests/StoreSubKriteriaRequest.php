<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubKriteriaRequest extends FormRequest
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
        return [
            'label' => 'required|string|max:100',
            'skor' => 'required|integer|min:1|max:100',
            'deskripsi' => 'nullable|string|max:500',
            'urutan' => 'required|integer|min:1|max:50',
            'aktif' => 'boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'label.required' => 'Label sub-kriteria tidak boleh kosong',
            'label.max' => 'Label sub-kriteria maksimal 100 karakter',
            'skor.required' => 'Skor tidak boleh kosong',
            'skor.integer' => 'Skor harus berupa angka bulat',
            'skor.min' => 'Skor minimal 1',
            'skor.max' => 'Skor maksimal 100',
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
            'label' => 'Label',
            'skor' => 'Skor',
            'deskripsi' => 'Deskripsi',
            'urutan' => 'Urutan',
            'aktif' => 'Status Aktif',
        ];
    }
}
