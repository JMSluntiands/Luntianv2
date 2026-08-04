<?php

namespace App\Http\Controllers;

class HrController extends Controller
{
    public function index()
    {
        return view('hr.index', [
            'sidebar_active' => 'hr',
        ]);
    }
}
