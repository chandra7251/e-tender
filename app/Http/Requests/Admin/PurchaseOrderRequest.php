<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'po_number'   => ['required', 'string', 'max:100', 'unique:purchase_orders,po_number'],
            'amount'      => ['required', 'numeric', 'min:0'],
            'issued_date' => ['required', 'date'],
            'notes'       => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'po_number.required' => 'Nomor PO wajib diisi.',
            'po_number.unique'   => 'Nomor PO sudah digunakan.',
            'amount.required'    => 'Jumlah PO wajib diisi.',
            'amount.min'         => 'Jumlah PO tidak boleh negatif.',
            'issued_date.required' => 'Tanggal PO wajib diisi.',
        ];
    }
}
