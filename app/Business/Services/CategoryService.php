<?php

namespace App\Business\Services;

use App\Models\Category;
use Illuminate\Support\Str;

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

    public function create(array $data): array
    {
        $slug = Str::slug($data['name']);

        $activeCategory = Category::where('slug', $slug)->first();

        if ($activeCategory) {
            throw new \RuntimeException('Ya existe una categoría activa con ese nombre.');
        }

        $deletedCategory = Category::onlyTrashed()->where('slug', $slug)->first();

        if ($deletedCategory) {
            $deletedCategory->update([
                'name' => $data['name'],
                'slug' => $slug,
            ]);

            $deletedCategory->restore();

            return [
                'category' => $deletedCategory,
                'created' => false,
                'restored' => true,
            ];
        }

        $category = Category::create([
            'name' => $data['name'],
            'slug' => $slug,
        ]);

        return [
            'category' => $category,
            'created' => true,
            'restored' => false,
        ];
    }

    public function update(Category $category, array $data): Category
    {
        $slug = Str::slug($data['name']);

        $exists = Category::where('slug', $slug)
            ->where('id', '!=', $category->id)
            ->exists();

        if ($exists) {
            throw new \RuntimeException('Ya existe una categoría activa con ese nombre.');
        }

        $category->update([
            'name' => $data['name'],
            'slug' => $slug,
        ]);

        return $category;
    }

    public function delete(Category $category): void
    {
        $category->delete();
    }
}
