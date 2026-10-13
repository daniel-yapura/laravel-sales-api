<?php

namespace App\Http\Controllers;

use App\Business\Services\CreateSaleService;
use App\Http\Requests\SaleRequest;
use App\Models\Sale;
use Illuminate\Http\Response;

class SaleController extends Controller
{
    private $createSaleService;

    public function __construct(CreateSaleService $createSaleService)
    {
        $this->createSaleService = $createSaleService;
    }

    public function get()
    {
        return response()->json(Sale::all(), Response::HTTP_OK);
    }

    public function create(SaleRequest $request)
    {

        try {

            $saleEntity = $this->createSaleService->create($request);

            return response()->json($saleEntity, Response::HTTP_CREATED);

        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
