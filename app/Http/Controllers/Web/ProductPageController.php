<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class ProductPageController extends Controller
{
    public function index()
    {
        return view('products.index');
    }
}
