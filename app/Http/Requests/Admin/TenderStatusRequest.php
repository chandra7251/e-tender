<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class TenderStatusRequest extends FormRequest
{
    // Peta transisi status yang diizinkan — state machine ketat.
    // CATATAN: 'finished' TIDAK bisa dicapai dari sini.
    // Satu-satunya jalur ke 'finished' adalah via TenderResultController::finish()
    // yang memiliki guard: pemenang harus dipilih + PO harus sudah dibuat.
    private const ALLOWED_TRANSITIONS = [
        'draft'      => ['open'],
        'open'       => ['aanwijzing', 'bidding', 'closed'],
        'aanwijzing' => ['bidding', 'closed'],
        'bidding'    => ['closed'],
        'closed'     => [], // finished hanya via TenderResultController::finish()
        'finished'   => [], // terminal state — tidak bisa mundur
    ];

    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            // 'finished' tidak termasuk — tidak bisa diubah ke finished via form ubah status
            'status'      => ['required', 'in:draft,open,aanwijzing,bidding,closed'],
            'description' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $tender = $this->route('tender');

            if (!$tender) {
                return;
            }

            $current = $tender->status;
            $new     = $this->input('status');
            $allowed = self::ALLOWED_TRANSITIONS[$current] ?? [];

            if (!in_array($new, $allowed)) {
                $validator->errors()->add(
                    'status',
                    "Tidak dapat mengubah status dari '{$current}' ke '{$new}'. " .
                    (count($allowed)
                        ? "Status yang diizinkan: " . implode(', ', $allowed) . "."
                        : "Status '{$current}' adalah status akhir (terminal).")
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Status baru wajib dipilih.',
            'status.in'       => 'Status tender tidak valid.',
        ];
    }
}
