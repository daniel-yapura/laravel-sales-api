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
        try {
            $result = $this->categoryService->create($request->validated());

            $status = $result['created']
                ? Response::HTTP_CREATED
                : Response::HTTP_OK;

            return response()->json($result['category'], $status);
        } catch (\RuntimeException $e) {
            return response()->json(['errors' => ['name' => [$e->getMessage()]]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function show(Category $category)
    {
        return response()->json($category);
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        try {
            $category = $this->categoryService->update($category, $request->validated());

            return response()->json($category);
        } catch (\RuntimeException $e) {
            return response()->json(['errors' => ['name' => [$e->getMessage()]]], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    public function destroy(Category $category)
    {
        $this->categoryService->delete($category);

        return response()->json(['message' => 'Categoría eliminada'], Response::HTTP_OK);
    }
}
