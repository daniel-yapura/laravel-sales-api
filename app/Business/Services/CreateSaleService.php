<?php

namespace App\Business\Services;

use App\Business\Entities\ConceptEntity;
use App\Business\Entities\SaleEntity;
use App\Http\Requests\SaleRequest;
use App\Models\Concept;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;

class CreateSaleService
{
    public function create(SaleRequest $request): SaleEntity
    {
        $conceptData = $request->concepts;
        $productIds = collect($conceptData)->pluck('product_id')->unique()->values();

        $products = Product::whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        $conceptsEntity = [];

        foreach ($conceptData as $concept) {
            $product = $products->get($concept['product_id']);

            if (! $product) {
                throw new \RuntimeException("Producto no encontrado: {$concept['product_id']}");
            }

            if ($product->stock < $concept['quantity']) {
                throw new \RuntimeException("Stock insuficiente para el producto {$product->id}");
            }

            $conceptsEntity[] = new ConceptEntity(
                (int) $concept['quantity'],
                (float) $product->price,
                (int) $concept['product_id']
            );
        }

        $saleEntity = new SaleEntity(null, $request->email, $request->sale_date, $conceptsEntity);

        return DB::transaction(function () use ($conceptsEntity, $saleEntity) {
            foreach ($conceptsEntity as $conceptEntity) {
                $product = Product::where('id', $conceptEntity->product_id)
                    ->lockForUpdate()
                    ->first();

                if (! $product || $product->stock < $conceptEntity->quantity) {
                    throw new \RuntimeException("Stock insuficiente para el producto {$conceptEntity->product_id}");
                }

                $product->decrement('stock', $conceptEntity->quantity);
            }

            $sale = Sale::create([
                'email' => $saleEntity->email,
                'sale_date' => $saleEntity->sale_date,
                'total' => $saleEntity->total,
            ]);

            foreach ($conceptsEntity as $conceptEntity) {
                Concept::create([
                    'quantity' => $conceptEntity->quantity,
                    'price' => $conceptEntity->price,
                    'product_id' => $conceptEntity->product_id,
                    'sale_id' => $sale->id,
                ]);
            }

            $saleEntity->id = $sale->id;

            return $saleEntity;
        });
    }
}
