<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class WinnerSelectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'bid_id'           => ['required', 'integer', 'exists:bids,id'],
            'selection_method' => ['required', 'in:lowest_price,admin_consideration'],
            'notes'            => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'bid_id.required'           => 'Bid pemenang wajib dipilih.',
            'bid_id.exists'             => 'Bid tidak ditemukan.',
            'selection_method.required' => 'Metode seleksi wajib dipilih.',
            'selection_method.in'       => 'Metode seleksi tidak valid.',
        ];
    }
}
