<?php

namespace App\Http\Controllers;

use App\Business\Services\ProductService;
use App\Models\Product;
use Symfony\Component\HttpFoundation\Response;

class InfoController extends Controller
{
    public function __construct(protected ProductService $productService) {}

    public function iva(int $id)
    {
        $product = Product::find($id);

        if (! $product) {
            return response()->json(['message' => 'Producto no encontrado'], Response::HTTP_NOT_FOUND);
        }

        $priceWithIVA = $this->productService->calculateIVA($product->price);

        return response()->json(['price' => $product->price, 'priceWithIVA' => $priceWithIVA]);
    }
}
