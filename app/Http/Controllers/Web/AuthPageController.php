<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class AuthPageController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function register()
    {
        return view('auth.register');
    }
}
