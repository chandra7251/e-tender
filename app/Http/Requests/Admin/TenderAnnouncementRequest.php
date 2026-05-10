<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class TenderAnnouncementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'title'        => ['required', 'string', 'max:255'],
            'content'      => ['required', 'string'],
            'published_at' => ['required', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'        => 'Judul aanwijzing wajib diisi.',
            'content.required'      => 'Isi aanwijzing wajib diisi.',
            'published_at.required' => 'Tanggal publikasi wajib diisi.',
            'published_at.date'     => 'Format tanggal tidak valid.',
        ];
    }
}
