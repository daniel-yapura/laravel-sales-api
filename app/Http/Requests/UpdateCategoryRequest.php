<?php

namespace App\Http\Requests;

use App\Models\Category;

class UpdateCategoryRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $category = $this->route('category');
        $id = $category instanceof Category ? $category->id : $category;

        return [
            'name' => "required|string|max:255|unique:category,name,{$id}",
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre de la categoría es obligatorio.',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no puede superar los 255 caracteres.',
            'name.unique' => 'Ya existe una categoría con ese nombre.',
        ];
    }
}
