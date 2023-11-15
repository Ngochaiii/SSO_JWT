<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChangePassController extends Controller
{
    public function index()
    {
        return view('auth.change_pass');
    }
}
