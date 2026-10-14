<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class CategoryPageController extends Controller
{
    public function index()
    {
        return view('categories.index');
    }
}
