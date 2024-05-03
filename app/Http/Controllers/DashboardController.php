<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Generate the user a random name:
        $userName = \Faker\Factory::create()->name();
        return view('welcome', ['name' => $userName]);
    }
}
