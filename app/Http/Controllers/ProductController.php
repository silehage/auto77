<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Review;
use App\Models\Product;
use App\Models\Promote;
use Illuminate\Http\Request;
use App\Repositories\ProductRepository;

class ProductController extends Controller
{
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function index()
    {
        return $this->productRepository->getAll();

    }
    public function getAdminProducts()
    {
        return response([
            'success' => true, 
            'results' => Product::with('assets', 'category', 'variants.variant_items.variant_item_values')->latest()->get()
        ],200);
    }

    public function getProductsFavorites(Request $request)
    {
        $request->validate([
            'pids' => 'required'
        ]);

        return $this->productRepository->getProductsFavorites($request->pids);
       
    }
    public function getProductsByCategory($id)
    {     
        return $this->productRepository->getProductsByCategory($id);   
       
    }

    public function search(Request $request)
    {
        if(!$request->q) {
           return response([
               'success' => false,
           ], 404);
        }

        $key = trim($request->q);
        $key = strip_tags($key);
        $key = filter_var($key, FILTER_SANITIZE_SPECIAL_CHARS);

        return response([
            'success' => true, 
            'results' => [
                'products' => $this->productRepository->searchProduct($key)
            ]
            
        ],200);
    }

    public function show($slug)
    {
        return response([
            'success' => true, 
            'results' => $this->productRepository->show($slug)
        ],200);
    }
    public function productById($id)
    {
        return response([
            'success' => true, 
            'results' => Product::with('assets', 'category', 'variants.variant_items.variant_item_values')
            ->where('id', $id) 
            ->first()
        ],200);
    }

    public function store(ProductRequest $request)
    {
        return $this->productRepository->store($request);
        
    }

    public function update(Request $request)
    {
        $product = Product::find($request->id);
        $request->validate([
            'id' => 'required',
            'title' => 'required',
            'price' => 'required|numeric',
            'weight' => 'required|numeric',
            'stock' => 'required|numeric',
            'description' => 'required',
            'images' => $request->del_images && count($product->assets) == count($request->del_images) && !$request->images?'required' : 'nullable'
        ]);

        return $this->productRepository->update($request);
    }
    public function addProductReview(Request $request)
    {
        $request->validate([
            'product_id' => ['required'],
            'name' => ['required'],
            'comment' => ['required'],
            'rating' => ['required', 'numeric', 'min:1', 'max:5'],
        ]);

        return $this->productRepository->addProductReview($request);

    }
    public function loadProductReview(Request $request, $id)
    {
        return response()->json([
            'success' => true,
            'results' => Review::where('product_id', $id)->latest()->skip($request->skip?? 0)->take(6)->get()
        ]);
    }

    public function findNotDiscountProduct()
    {
        return response()->json([
            'success' => true,
            'results' => Product::whereNull('promote_id')->whereNull('discount_id')->get(),
        ]);

    }

    public function getProductPromo($promoId)
    {
        $promote = Promote::find($promoId);

        return response()->json([
            'success' => true,
            'results' => Product::withSum('variantItems', 'item_stock')
            ->where('promote_id', $promote->id)
            ->get(),
        ]);

    }

    public function toggleProductPromo(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'promote_id' => 'required',
        ]);

        $product = Product::find($request->product_id);

        if($product->promote_id) {
            $product->promote_id = null;
        } else {
            $product->promote_id = $request->promote_id;
        }

        $product->save();

        return response()->json([
            'success' => true,
        ]);
    }
    public function destroy($id)
    {
        return $this->productRepository->destroy($id);
    }
}
