<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class SalePageController extends Controller
{
    public function index()
    {
        return view('sales.index');
    }
}
