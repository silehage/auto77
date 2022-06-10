<?php

namespace App\Repositories;

use stdClass;
use Exception;
use App\Models\Asset;
use App\Models\Promo;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use App\Http\Resources\ProductResource;

class ProductRepository
{
    protected $limit = 6;
    
    public function show($slug)
    {
        Cache::flush();
        $product = Cache::remember($slug, now()->addMinute(), function() use ($slug) {

            return new ProductResource(Product::with(['assets', 'category', 'varians.subvarian', 'productPromo' => function($query) {
                $query->whereHas('promoActive');
            }])
                ->withCount('reviews')
                ->withAvg('reviews', 'rating')
                ->where('slug', $slug) 
                ->orWhere('id', $slug)
                ->first());
        });


        return $product;
    }

    public function getAll()
    {
            return Product::with(['assets', 'category:id,title,slug', 'productPromo' => function($query) {
                $query->whereHas('promoActive');
            }])
            ->withAvg('reviews', 'rating')
            ->simplePaginate($this->limit);
        
    }

    public function getProductsFavorites($pids)
    {
        return Product::with(['assets', 'category:id,title,slug', 'productPromo' => function($query) {
            $query->whereHas('promoActive');
        }])
            ->whereIn('id', $pids)
            ->withAvg('reviews', 'rating')
            ->get();

    }
  
    public function search($key)
    {

        return Product::with(['assets', 'category:id,title,slug', 'productPromo' => function($query) {
            $query->whereHas('promoActive');
        }])
            ->where('title', 'like', '%'.$key.'%')
            ->withAvg('reviews', 'rating')
            ->get();

    }

    public function getProductsByCategory($id)
    {
        return Product::with(['assets', 'category:id,title,slug', 'productPromo' => function($query) {
            $query->whereHas('promoActive');
        }])
            ->where('category_id', $id)
            ->withAvg('reviews', 'rating')
            ->simplePaginate($this->limit);

        }

    public function getProductPromo()
    {
        return Promo::active()->with(['products' => function($query) {
            $query->with('assets');
            $query->with('productPromo', function($q) {
                $q->whereHas('promoActive');
            });
            $query->withAvg('reviews', 'rating');
        }])->get()->map(function($item) {

            $promo = new stdClass();
            $promo->id = $item->id;
            $promo->label = $item->label;
            $promo->start_date = $item->start_date;
            $promo->end_date = $item->end_date;

            $promo->products = $item->products->map(function($product) {

                return [
                    'id'      => $product->id,
                    'title'   => $product->title,
                    'slug'    => $product->slug,
                    'status'  =>  $product->status,
                    'rating'  =>  $product->reviews_avg_rating ? (float) number_format($product->reviews_avg_rating, 1) : 0,
                    'pricing' =>  $this->setPricing($product),
                    'assets'  =>  $product->assets,
                ];
            });

            return $promo;

        });
        
    }

    public function getInitialProducts()
    {

        $data = Category::whereHas('products')
            ->with(['products' => function($query) {
                $query->with('assets');
                $query->with('productPromo', function($q) {
                    $q->whereHas('promoActive');
                });
                $query->with('varians.subvarian');
                $query->withAvg('reviews', 'rating');
            }])
            ->where('is_front', 1)
            ->orderBy('weight')
            ->get()
            ->map(function($cat) {

                $categoryItem = new stdClass();
                $categoryItem->title = $cat->title;
                $categoryItem->category_id = $cat->id;
                $categoryItem->category_slug = $cat->slug;
                $categoryItem->id = Str::random(16);
                $categoryItem->description = $cat->description ?? '';
                $categoryItem->banner = $cat->banner ? url('upload/images/' . $cat->banner) : '';

                $categoryItem->items = $cat->products->map(function($product) use($cat) {

                    $newCat = new stdClass();
                    $newCat->id = $cat->id;
                    $newCat->title = $cat->title;
                    $newCat->slug = $cat->slug;

                    $product->category = $newCat;

                    return [
                        'id'      => $product->id,
                        'title'   => $product->title,
                        'sku'   => $product->sku,
                        'slug'    => $product->slug,
                        'status'  =>  $product->status,
                        'rating'  =>  $product->reviews_avg_rating ? (float) number_format($product->reviews_avg_rating, 1) : 0,
                        'pricing' =>  $this->setPricing($product),
                        'category' => $newCat,
                        'assets'  =>  $product->assets,
                        'description' =>  $product->description,
                        // 'promo' => $product->promo
                    ];
                });

                return $categoryItem;
            });

        return $data;

    }
  
    public function store($request)
    {
        $path = public_path('/upload/images');

        if(! File::exists($path)) {
            File::makeDirectory($path, 0755, true, true);
        }

        DB::beginTransaction();
        
        try {
            $slug = Str::slug($request->title);
            $product = new Product();

            $product->title = $request->title;
            $product->slug = $slug;
            $product->price = $request->price;
            $product->stock = $request->stock;
            $product->weight = $request->weight;
            $product->sku = 'PF-'. $request->sku;
            
            $product->category_id =  $request->category_id;

            $product->description = $request->description;

            $product->save();

            if($request->images && count($request->images) > 0) {
                foreach($request->images as $file) {
                    
                    $filename = Str::random(41).'.' . $file->extension();

                    $file->move($path, $filename);

                    $product->assets()->create([
                        'filename' => $filename
                    ]);
                }
            }

            $product->fresh();

            if($request->varians) {
                $datas = json_decode($request->varians, true);

                foreach($datas as $data) {

                    if($request->boolean('has_subvarian') === true) {

                        $varian =  $product->varians()->create([
                                'has_subvarian' => $data['has_subvarian'],
                                'label' => $data['label'],
                                'value' => $data['value'],
                            ]);
        
                            foreach($data['subvarian'] as $item) {
        
                                $varian->subvarian()->create($item);
                            }
        
                    } else {
                    
                        $product->varians()->create($data);
                    }

                } 

                
            }

            DB::commit();

            $this->clearCache();

            return $product->load('assets','varians.subvarian');


        } catch (Exception $e) {

            DB::rollBack();

            throw new Exception($e);
        }

            
    }

    public function update($request)
    {
        $product = Product::find($request->id);

        $path = public_path('/upload/images');

        if(! File::exists($path)) {
            File::makeDirectory($path, 0755, true, true);
        }

        DB::beginTransaction();

        $product->title = $request->title;
        $product->price = $request->price;
        $product->stock = $request->stock;
        $product->weight = $request->weight;
        $product->description = $request->description;
        $product->category_id = $request->category_id;

        try {

            if($request->images) {
                foreach($request->images as $file) {

                    $filename = Str::random(42).'.' . $file->extension();
                    
                    if($file->move($path, $filename)){

                        $product->assets()->create([
                            'filename' => $filename
                        ]);
                    }

                }
            }
            if($request->del_images) {
                foreach($request->del_images as $filename) {
                    File::delete('upload/images/'. $filename);
                    Asset::where('filename', $filename)->delete();
                }
            }

            $product->save();

            $product->varians()->delete();

            if($request->varians) {
                $datas = json_decode($request->varians, true);

                foreach($datas as $data) {

                    if($request->boolean('has_subvarian') === true) {

                        $varian =  $product->varians()->create([
                                'has_subvarian' => $data['has_subvarian'],
                                'label' => $data['label'],
                                'value' => $data['value'],
                            ]);
        
                            foreach($data['subvarian'] as $item) {
        
                                $varian->subvarian()->create($item);
                            }
        
                    } else {
                    
                        $product->varians()->create($data);
                    }

                } 

                
            }

            $product->fresh();

            DB::commit();

            $this->clearCache();

            return $product;

        } catch (Exception $e) {

            DB::rollBack();
            
            throw new Exception($e);
        }

        
    }

    public function destroy($id)
    {
        $product = Product::find($id);

        DB::beginTransaction();

        try {
            if($product->assets) {

                foreach($product->assets as $asset){
                    File::delete('upload/images/'. $asset->filename);
                }
                $product->assets()->delete();
            }
            $product->delete();

            DB::commit();

            $this->clearCache();

            return true;


        } catch (Exception $e) {

            DB::rollBack();

            throw new Exception($e);
        }
    }

    protected function setPricing($product)
    {
        $defaultPrice = $product->price;

            $pricing = [
                'default_price' => $defaultPrice,
                'current_price' => $defaultPrice,
                'discount_percent' => 0,
                'discount_amount' => 0,
                'is_discount' => false,
            ];


            $disc = null;
    
            if($product->productPromo) {
                $disc = $product->productPromo;
            } 

            if($disc) {

                $pricing['is_discount'] = true;

                $discountVal = 0;
                

                if($disc->discount_type == 'PERCENT') {
    
                    $discountVal = ($defaultPrice*$disc->discount_amount) / 100;

                    $pricing['current_price'] = $defaultPrice - (int) $discountVal;

                    $pricing['discount_percent'] = (int) $disc->discount_amount;
                    
                } else{
    
                    $discountVal = $disc->discount_amount;

                    $pricing['current_price'] = $defaultPrice - (int) $discountVal;

                    $pricing['discount_percent'] = number_format(((int)$disc->discount_amount / $defaultPrice)*100, 0);
    
                }

                $pricing['discount_amount'] = $discountVal;
            }
        
            return $pricing;
    }
    
    protected function clearCache()
    {
        Cache::forget('products');
        Cache::forget('initial_products');
        Cache::forget('product_promo');
    }

}