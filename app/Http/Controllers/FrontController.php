<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;

class FrontController extends Controller
{
    public $shop;

    public function __construct()
    {
        $this->shop = Cache::rememberForever('shop', function() {
            return Store::first();
        });
    }

    public function homepage()
    {
        return View::vue([
            'title' => $this->shop->name,
            'description' => $this->shop->description,
            'featured_image' => $this->shop->logo_path? url('/upload/images/' . $this->shop->logo_path) : null
        ]);
    }
    
    public function products()
    {
        return View::vue([
            'title' => 'Produk Katalog | ' . $this->shop->name,
            'description' => $this->shop->description,
            'featured_image' => $this->shop->logo_path? url('/upload/images/' . $this->shop->logo_path) : null
        ]);
    }
    public function productDetail($id)
    {
        $product = Product::with('assets', 'category','reviews')->withCount('reviews')->where('id', $id)->first();
        $desc = $product->description;
        
        return View::vue([
            'title' => $product->title . ' | ' . $this->shop->name,
            'description' => $desc ? substr(strip_tags($product->description),0,100) : $this->shop->description,
            'featured_image' => $product->assets[0]->src,
            'data' => $product
        ]);

    }

    public function productCategory(Category $category)
    {
        return View::vue([
            'title' => $category->title . ' | ' . $this->shop->name,
            'description' => $categor->description?? $this->shop->description,
            'featured_image' => url('/upload/images/' . $category->filename),
        ]);

    }
}
