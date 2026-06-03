<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class TenderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'title'               => ['required', 'string', 'max:255'],
            'description'         => ['required', 'string'],
            'specification'       => ['required', 'string'],
            'open_bidding_price'  => ['nullable', 'numeric', 'min:0'],
            'photo'               => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:3072'],
            'start_date'          => ['required', 'date'],
            'end_date'            => ['required', 'date', 'after:start_date'],
            'aanwijzing_date'     => ['nullable', 'date'],
            'bidding_start'       => ['required', 'date', 'after_or_equal:start_date'],
            'bidding_end'         => ['required', 'date', 'after:bidding_start', 'before_or_equal:end_date'],
            // 'status' dikelola eksklusif via PATCH /tenders/{tender}/status
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'               => 'Judul tender wajib diisi.',
            'description.required'         => 'Deskripsi tender wajib diisi.',
            'specification.required'       => 'Spesifikasi tender wajib diisi.',
            'open_bidding_price.numeric'   => 'Harga pembukaan harus berupa angka.',
            'open_bidding_price.min'       => 'Harga pembukaan tidak boleh negatif.',
            'start_date.required'          => 'Tanggal mulai wajib diisi.',
            'end_date.required'            => 'Tanggal selesai wajib diisi.',
            'end_date.after'               => 'Tanggal selesai harus setelah tanggal mulai.',
            'bidding_start.required'       => 'Tanggal mulai bidding wajib diisi.',
            'bidding_start.after_or_equal' => 'Bidding tidak boleh dimulai sebelum tender dimulai.',
            'bidding_end.required'         => 'Tanggal selesai bidding wajib diisi.',
            'bidding_end.after'            => 'Tanggal selesai bidding harus setelah bidding dimulai.',
            'bidding_end.before_or_equal'  => 'Bidding tidak boleh selesai setelah tender berakhir.',
        ];
    }
}
