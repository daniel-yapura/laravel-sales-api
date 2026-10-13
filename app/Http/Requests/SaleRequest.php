<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;

class SaleRequest extends ApiFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'sale_date' => 'required|date',
            'email' => 'required|email',
            'concepts' => 'required|array|min:1',
            'concepts.*.quantity' => 'required|numeric',
            'concepts.*.product_id' => 'required|exists:product,id',
        ];
    }

    public function messages()
    {
        return [
            'sale_date.required' => 'La fecha es obligatoria',
            'sale_date.date' => 'La fecha debe ser una fecha válida',
            'email.required' => 'El email es obligatorio',
            'email.email' => 'El email debe ser un email válido',
            'concepts.required' => 'Los conceptos son obligatorios',
            'concepts.array' => 'Los conceptos deben ser un arreglo',
            'concepts.min' => 'Debe haber al menos un concepto',
            'concepts.*.quantity.required' => 'La cantidad es obligatoria',
            'concepts.*.quantity.numeric' => 'La cantidad debe ser un número',
            'concepts.*.product_id.required' => 'El id del producto es obligatorio',
            'concepts.*.product_id.exists' => 'El id del producto no existe',
        ];
    }
}
