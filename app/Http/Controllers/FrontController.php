<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Store;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\ProductResource;

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
        $title = $this->shop->name;
        if($this->shop->description) {
            $title = $title . ' | ' . $this->shop->description;
        }
        return View::vue([
            'title' => $title,
            'description' => $this->shop->description,
            'featured_image' => $this->shop->logo_path? url('/upload/images/' . $this->shop->logo_path) : null,
            'data' => null
        ]);
    }
    
    public function products()
    {
        return View::vue([
            'title' => 'Produk Katalog | ' . $this->shop->name,
            'description' => $this->shop->description,
            'featured_image' => $this->shop->logo_path? url('/upload/images/' . $this->shop->logo_path) : null,
            'data' => null
        ]);
    }
    public function productDetail($slug)
    {
        $product = new ProductResource(Product::with(['assets', 'category:id,title,slug','reviews' => function($q) {
            $q->limit(5);
            $q->latest();
        }, 'variants.variant_items.variant_item_values'])
            ->withCount('reviews')
            ->withSum('variantItems', 'item_stock')
            ->withAvg('reviews', 'rating')
            ->where('slug', $slug) 
            ->first());
        
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
            'data' => null
        ]);

    }
    public function postIndex()
    {
        return View::vue([
            'title' => 'Artikel | ' . $this->shop->name,
            'description' => $this->shop->description,
            'featured_image' => $this->shop->logo_path? url('/upload/images/' . $this->shop->logo_path) : null,
            'data' => null
        ]);
    }
    public function postDetail($slug)
    {
        $post = Post::where('slug', $slug)->first();
        
        return View::vue([
            'title' => $post->title . ' | ' . $this->shop->name,
            'description' => $this->createTeaser($post->body),
            'featured_image' => url('/upload/images/' . $post->image),
            'data' => $post
        ]);
    }
    public function any()
    {
        return View::vue([
            'title' => $this->shop->name,
            'description' => $this->shop->description,
            'featured_image' => $this->shop->logo_path? url('/upload/images/' . $this->shop->logo_path) : null,
            'data' => null
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
