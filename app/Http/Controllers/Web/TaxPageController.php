<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class TaxPageController extends Controller
{
    public function index()
    {
        return view('tax.index');
    }
}
