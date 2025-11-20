<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InventoryTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'productId'      => ['required', 'string'],
            'sourceStoreId'  => ['required', 'string'],
            'targetStoreId'  => ['required', 'string', 'different:sourceStoreId'],
            'quantity'       => ['required', 'integer', 'min:1'],
        ];
    }
}
