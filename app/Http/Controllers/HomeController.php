<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * INDEX: Menampilkan Halaman Utama (Homepage)
     */
    public function index()
    {
        // 1. DATA CAROUSEL (HERO SECTION)
        // Logika: Carousel membutuhkan gambar background yang jelas.
        // Maka kita memfilter produk yang kolom image-nya TIDAK NULL dan TIDAK KOSONG.
        $carouselProducts = Product::whereNotNull('image')
            ->where('image', '!=', '') // Pastikan string path gambar tidak kosong
            ->inRandomOrder()          // Acak urutan agar tampilan web selalu segar saat refresh
            ->take(3)                  // Ambil 3 saja cukup untuk slider
            ->get();

        // 2. DATA GRID (REKOMENDASI PRODUK)
        // Logika: Menampilkan 4 produk acak di bawah carousel.
        // Menggunakan with('category') -> Eager Loading.
        // Tujuannya agar saat view memanggil {{ $product->category->name }},
        // Laravel tidak perlu query ke database berulang kali (N+1 Problem Solved).
        $products = Product::with('category')
            ->inRandomOrder()
            ->take(4) // Mengambil 4 produk
            ->get();

        // Kirim kedua variabel ($carouselProducts dan $products) ke View 'home'
        return view('home', compact('carouselProducts', 'products'));
    }
}