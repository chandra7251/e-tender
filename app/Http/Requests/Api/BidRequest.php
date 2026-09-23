<?php

namespace App\Http\Requests\Api;

use App\Models\Tender;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class BidRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tender = $this->route('tender');
        $tenderId = $tender instanceof Tender ? $tender->id : $tender;

        return [
            'bid_amount' => ['required', 'numeric', 'min:1'],
            'notes' => ['nullable', 'string', 'max:1000'],
            // opsional: items untuk Bill of Quantity
            'items' => ['nullable', 'array'],
            'items.*.tender_item_id' => [
                'required_with:items',
                'integer',
                'distinct',
                Rule::exists('tender_items', 'id')->when(
                    $tenderId,
                    fn ($rule) => $rule->where('tender_id', $tenderId)
                ),
            ],
            'items.*.unit_price' => ['required_with:items', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'bid_amount.required' => 'Jumlah bid wajib diisi.',
            'bid_amount.min' => 'Jumlah bid harus lebih dari 0.',
        ];
    }

    protected function failedValidation(Validator $validator): never
    {
        throw new HttpResponseException(response()->json([
            'status' => false,
            'message' => 'Validasi gagal.',
            'errors' => $validator->errors(),
        ], 422));
    }
}
