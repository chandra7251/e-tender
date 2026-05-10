<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class VendorVerificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        // 'action' is set in route: approve or reject
        if ($this->route()->named('admin.vendors.reject')) {
            return [
                'notes' => ['required', 'string', 'max:1000'],
            ];
        }

        // approve: notes optional
        return [
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'notes.required' => 'Alasan penolakan wajib diisi.',
            'notes.max'      => 'Alasan penolakan maksimal 1000 karakter.',
        ];
    }
}
