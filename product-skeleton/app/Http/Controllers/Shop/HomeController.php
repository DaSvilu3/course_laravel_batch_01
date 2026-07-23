<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Service;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('shop.home', [
            'services' => Service::active()->featured()->ordered()->take(6)->get(),
            'products' => Product::active()->inStock()->featured()->ordered()->take(6)->get(),
        ]);
    }
}
