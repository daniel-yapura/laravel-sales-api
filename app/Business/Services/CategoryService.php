<?php

namespace App\Business\Services;

use App\Models\Category;

class CategoryService
{
    public function all()
    {
        return Category::orderBy('name')->get();
    }

    public function find(int $id): Category
    {
        return Category::findOrFail($id);
    }

    public function findTrashed(int $id): Category
    {
        return Category::withTrashed()->findOrFail($id);
    }

    public function create(array $data): Category
    {
        return Category::create($data);
    }

    public function update(Category $category, array $data): Category
    {
        $category->update($data);

        return $category;
    }

    public function delete(Category $category): void
    {
        $category->delete();
    }

    public function restore(int $id): Category
    {
        $category = Category::withTrashed()->findOrFail($id);

        if (! $category->trashed()) {
            throw new \RuntimeException('La categoría ya está activa.');
        }

        if (Category::where('name', $category->name)->whereNull('deleted_at')->exists()) {
            throw new \RuntimeException('Ya existe una categoría activa con ese nombre.');
        }

        $category->restore();

        return $category;
    }
}
