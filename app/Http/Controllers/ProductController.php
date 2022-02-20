<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Review;
use App\Models\Product;
use App\Models\Promote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ProductRequest;
use Illuminate\Support\Facades\Cache;
use App\Repositories\ProductRepository;

class ProductController extends Controller
{
    private $productRepository;

    protected $result = ['status' => 200, 'success' => true];

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function index()
    {

        try {

            $this->result['results'] = $this->productRepository->getAll();

        } catch (Exception $e) {

            $this->result = [
                'status' => 500,
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        return response()->json($this->result, $this->result['status']);


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

        try {

            $this->result['results'] = $this->productRepository->getProductsFavorites($request->pids);

        } catch (Exception $e) {

            $this->result = [
                'status' => 500,
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        return response()->json($this->result, $this->result['status']);
       
    }
    public function getProductsByCategory($id)
    {     

        try {
            
            $this->result['results'] = $this->productRepository->getProductsByCategory($id);

        } catch (Exception $e) {

            $this->result = [
                'status' => 500,
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        return response()->json($this->result, $this->result['status']);
       
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

        try {
            
            $this->result['results'] = $this->productRepository->search($key);

        } catch (Exception $e) {

            $this->result = [
                'status' => 500,
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        return response()->json($this->result, $this->result['status']);
    }

    public function show($slug)
    {
       
        try {
            
            $this->result['results'] = $this->productRepository->show($slug);

        } catch (Exception $e) {

            $this->result = [
                'status' => 500,
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        return response()->json($this->result, $this->result['status']);
    }
    public function productById($id)
    {

        try {
            
            $this->result['results'] = Product::with('assets', 'category', 'variants.variant_items.variant_item_values')
            ->where('id', $id) 
            ->first();

        } catch (Exception $e) {

            $this->result = [
                'status' => 500,
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        return response()->json($this->result, $this->result['status']);
    }

    public function store(ProductRequest $request)
    {

        try {
            
            $this->result['results'] = $this->productRepository->store($request);

        } catch (Exception $e) {

            $this->result = [
                'status' => 500,
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        return response()->json($this->result, $this->result['status']);
        
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

        try {
            
            $this->result['results'] = $this->productRepository->update($request);

        } catch (Exception $e) {

            $this->result = [
                'status' => 500,
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        return response()->json($this->result, $this->result['status']);

    }
    public function addProductReview(Request $request)
    {
        $request->validate([
            'product_id' => ['required'],
            'name' => ['required'],
            'comment' => ['required'],
            'rating' => ['required', 'numeric', 'min:1', 'max:5'],
        ]);

        try {
            
            $this->result['results'] = $this->productRepository->addProductReview($request);

        } catch (Exception $e) {

            $this->result = [
                'status' => 500,
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        return response()->json($this->result, $this->result['status']);

    }
    public function loadProductReview(Request $request, $id)
    {

        try {
            
            $this->result['results'] = Review::where('product_id', $id)->latest()->skip($request->skip?? 0)->take(6)->get();

        } catch (Exception $e) {

            $this->result = [
                'status' => 500,
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        return response()->json($this->result, $this->result['status']);
    }

    public function findNotDiscountProduct()
    {
        try {
            
            $this->result['results'] = Product::whereNull('promote_id')->whereNull('discount_id')->get();

        } catch (Exception $e) {

            $this->result = [
                'status' => 500,
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        return response()->json($this->result, $this->result['status']);

    }

    public function getProductPromo($promoId)
    {
        
        try {
            $promote = Promote::find($promoId);
            
            $this->result['results'] = Product::withSum('variantItems', 'item_stock')
            ->where('promote_id', $promote->id)
            ->get();

        } catch (Exception $e) {

            $this->result = [
                'status' => 500,
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        return response()->json($this->result, $this->result['status']);

    }

    public function toggleProductPromo(Request $request)
    {
        $request->validate([
            'product_id' => 'required',
            'promote_id' => 'required',
        ]);

        try {
            
            $product = Product::find($request->product_id);

            if($product->promote_id) {

                $product->promote_id = null;

            } else {

                $product->promote_id = $request->promote_id;
            }
    
            $product->save();

        } catch (Exception $e) {

            $this->result = [
                'status' => 500,
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        return response()->json($this->result, $this->result['status']);
    }
    public function destroy($id)
    {
        try {
            
            $this->productRepository->destroy($id);

        } catch (Exception $e) {

            $this->result = [
                'status' => 500,
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        return response()->json($this->result, $this->result['status']);
    }
}
