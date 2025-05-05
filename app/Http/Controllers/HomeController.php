<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
  public function index()
{

    return view('fab_chaine.index'); // Default view for other users
}
    public function pir()
    {

        return view('home'); // Default view for other users
    }
    public function fabricationn()
    {
        return view('fabrication-order.index');
    }
}
