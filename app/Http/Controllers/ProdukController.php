<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    // Produk controller functions

    public function index()
    {
        $produks = Produk::all();
        return view('produk.index', compact('produks'));
    }
}
