<?php

namespace App\Http\Controllers;

use App\Models\Product; 
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
      

        // On affiche 8 produits par page
        $products = Product::paginate(8);

        // On les envoie à la page "welcome"
        return view('welcome', compact('products'));
    }
}
