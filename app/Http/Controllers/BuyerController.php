<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BuyerController extends Controller
{
   public function index() {
    // Si l'utilisateur n'a rien, $orders sera une collection vide []
    $orders = auth()->user()->orders()->latest()->take(5)->get();
    return view('dashboard', compact('orders'));
}
}