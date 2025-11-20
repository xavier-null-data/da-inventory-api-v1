<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Aquí podrías agregar lógica de permisos real
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => 'sometimes|string',
            'description' => 'sometimes|string|nullable',
            'category' => 'sometimes|string',
            'price' => 'sometimes|numeric|min:0',
            'sku' => 'sometimes|string',
        ];

        if ($this->isMethod('post')) {
            $rules['name'] = 'required|string';
            $rules['category'] = 'required|string';
            $rules['price'] = 'required|numeric|min:0';
            $rules['sku'] = 'required|string';
        }

        return $rules;
    }

}
