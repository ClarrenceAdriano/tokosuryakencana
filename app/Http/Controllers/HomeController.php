<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $carouselProducts = Product::whereNotNull('image')
            ->where('image', '!=', '') 
            ->inRandomOrder()         
            ->take(3)                
            ->get();

        $products = Product::with('category')
            ->inRandomOrder()
            ->take(4) 
            ->get();

        return view('home', compact('carouselProducts', 'products'));
    }
}