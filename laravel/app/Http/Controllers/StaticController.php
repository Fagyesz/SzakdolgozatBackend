<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class StaticController extends Controller
{
    public function home(): Factory|View|Application
    {
        return view('pages.welcome');
    }
}
