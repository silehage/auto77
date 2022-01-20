<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Product;
use App\Models\Category;
use App\Models\Post;
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
    public function productDetail($slug)
    {
        $product = Product::with('assets', 'category','reviews')->withCount('reviews')->where('slug', $slug)->first();
        
        return View::vue([
            'title' => $product->title . ' | ' . $this->shop->name,
            'description' => $product->description ? $this->createTeaser($product->description) : $this->shop->description,
            'featured_image' => $product->assets[0]->src,
            'data' => $product
        ]);

    }

    public function productCategory(Category $category)
    {
        return View::vue([
            'title' => $category->title . ' | ' . $this->shop->name,
            'description' => $category->description?? $this->shop->description,
            'featured_image' => url('/upload/images/' . $category->filename),
        ]);

    }
    public function postIndex()
    {
        return View::vue([
            'title' => 'Artikel | ' . $this->shop->name,
            'description' => $this->shop->description,
            'featured_image' => $this->shop->logo_path? url('/upload/images/' . $this->shop->logo_path) : null
        ]);
    }
    public function postDetail($slug)
    {
        $post = Post::where('slug', $slug)->first();
        
        return View::vue([
            'title' => $post->title . ' | ' . $this->shop->name,
            'description' => $this->createTeaser($post->body),
            'featured_image' => url('/upload/images/' . $post->image),
        ]);
    }
    public function any()
    {
        return View::vue([
            'title' => $this->shop->name,
            'description' => $this->shop->description,
            'featured_image' => $this->shop->logo_path? url('/upload/images/' . $this->shop->logo_path) : null
        ]);
    }
    public function clearCache()
    {   
        Cache::flush();
        return redirect('/');
    }
    protected function createTeaser($html)
    {
        $str = strip_tags($html);

        return substr($str, 0, 130) . '...'; 
    }
}
