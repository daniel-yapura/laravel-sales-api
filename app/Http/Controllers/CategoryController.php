<?php

namespace App\Http\Controllers;

use App\Business\Services\CategoryService;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
    public function __construct(protected CategoryService $categoryService) {}

    public function index()
    {
        return response()->json($this->categoryService->all());
    }

    public function store(StoreCategoryRequest $request)
    {
        $category = $this->categoryService->create($request->validated());

        return response()->json($category, Response::HTTP_CREATED);
    }

    public function show(Category $category)
    {
        return response()->json($category);
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category = $this->categoryService->update($category, $request->validated());

        return response()->json($category);
    }

    public function destroy(Category $category)
    {
        $this->categoryService->delete($category);

        return response()->json(['message' => 'Categoría eliminada'], Response::HTTP_OK);
    }

    public function restore(int $id)
    {
        try {
            $category = $this->categoryService->restore($id);

            return response()->json($category);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
