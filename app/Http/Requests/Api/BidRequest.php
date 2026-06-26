<?php
namespace App\Http\Requests\Api;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class BidRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bid_amount'    => ['required', 'numeric', 'min:1'],
            'notes'         => ['nullable', 'string', 'max:1000'],
            // opsional: items untuk Bill of Quantity
            'items'         => ['nullable', 'array'],
            'items.*.tender_item_id' => ['required_with:items', 'integer', 'exists:tender_items,id'],
            'items.*.unit_price'     => ['required_with:items', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'bid_amount.required' => 'Jumlah bid wajib diisi.',
            'bid_amount.min'      => 'Jumlah bid harus lebih dari 0.',
        ];
    }

    protected function failedValidation(Validator $validator): never
    {
        throw new HttpResponseException(response()->json([
            'status'  => false,
            'message' => 'Validasi gagal.',
            'errors'  => $validator->errors(),
        ], 422));
    }
}
