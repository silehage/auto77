<?php

namespace App\Repositories\v2;

use stdClass;
use Exception;
use App\Models\Asset;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use App\Models\ProductVarian;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Intervention\Image\Facades\Image;
use App\Http\Resources\ProductResource;
use App\Models\UploadTemp;

class ProductRepository
{
    protected $limit = 6;

    public function show($slug)
    {
        $product = Cache::remember($slug, now()->addMinute(), function () use ($slug) {

            return new ProductResource(Product::with(['assets', 'category:id,title,slug'])
                ->where('slug', $slug)
                ->orWhere('id', $slug)
                ->first());
        });


        return $product;
    }

    public function getAll()
    {
        return Product::with(['assets', 'category:id,title,slug'])
            ->isAvailable()
            ->inRandomOrder()
            ->simplePaginate($this->limit);
    }

    public function getProductsFavorites($pids)
    {
        return Product::with(['assets', 'category:id,title,slug'])
            ->whereIn('id', $pids)
            ->get();
    }

    public function search($key)
    {

        return Product::with(['assets', 'category:id,title,slug'])
            ->where('title', 'like', '%' . $key . '%')
            ->get();
    }

    public function getProductsByCategory($req, $id)
    {
        if ($req->limit) {
            $this->limit = $req->limit;
        }
        return Product::with(['assets', 'category:id,title,slug'])
            ->where('category_id', $id)
            ->isAvailable()
            ->inRandomOrder()
            ->simplePaginate($this->limit);
    }

    public function getInitialProducts()
    {

        $data = Category::whereHas('products')
            ->with(['products' => function ($query) {
                $query->with('assets');
                $query->isAvailable();
                $query->inRandomOrder();
            }])
            ->where('is_front', 1)
            ->orderBy('weight')
            ->get()
            ->map(function ($cat) {

                $categoryItem = new stdClass();
                $categoryItem->title = $cat->title;
                $categoryItem->category_id = $cat->id;
                $categoryItem->category_slug = $cat->slug;
                $categoryItem->id = Str::random(16);
                $categoryItem->description = $cat->description ?? '';
                $categoryItem->banner_src = $cat->banner ? url('upload/images/' . $cat->banner) : '';

                $categoryItem->items = $cat->products->map(function ($product) use ($cat) {

                    // Prevent recursive loop
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
                        'is_available'  =>  $product->is_available,
                        'price' =>  $product->price_custom_label,
                        'category' => $newCat,
                        'assets'  =>  $product->assets,
                        'description' =>  $product->description,
                    ];
                });

                return $categoryItem;
            });

        return $data;
    }

    public function store($request)
    {
        $path = public_path('/upload/images');

        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true, true);
        }

        DB::beginTransaction();

        try {
            $slug = Str::slug($request->title);
            $product = new Product();

            $product->title = $request->title;
            $product->slug = $slug;
            $product->price = 1;

            $product->category_id =  $request->category_id;
            $product->price_custom_label =  $request->price_custom_label;

            $product->description = $request->description;
            $product->is_available =  $request->boolean('is_available');

            $product->save();

            if ($request->images) {
                $images = $request->images;

                for ($i = 0; $i < count($images); $i++) {

                    $temp = UploadTemp::find($images[$i]['id']);

                    $product->assets()->create([
                        'filename' => $temp->filename,
                        'sort' => $i + 1
                    ]);

                    $temp->status = 1;
                    $temp->save();
                }
            }

            $product->fresh();

            if ($request->varians) {
                $datas = $request->varians;

                foreach ($datas as $data) {

                    if ($request->boolean('has_subvarian') === true && count($data['subvarian']) > 0) {

                        $varian =  $product->varians()->create([
                            'has_subvarian' => $data['has_subvarian'],
                            'label' => $data['label'],
                            'value' => $data['value'],
                        ]);

                        foreach ($data['subvarian'] as $item) {
                            $varian->subvarian()->create($item);
                        }
                    } else {

                        $product->varians()->create($data);
                    }
                }
            }

            DB::commit();

            $this->clearCache();

            UploadTemp::clearTemp();

            return $product->load('assets', 'varians.subvarian');
        } catch (Exception $e) {

            DB::rollBack();

            throw new Exception($e);
        }
    }

    public function update($request)
    {
        $product = Product::find($request->id);

        $path = public_path('/upload/images');

        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true, true);
        }

        DB::beginTransaction();

        try {

            $product->title = $request->title;
            $product->price = 1;
            $product->description = $request->description;
            $product->price_custom_label = $request->price_custom_label;
            $product->category_id = $request->category_id;
            $product->is_available =  $request->boolean('is_available');

            if ($request->images) {
                $images = $request->images;

                for ($i = 0; $i < count($images); $i++) {

                    if (isset($images[$i]['sort'])) {
                        $asset = Asset::find($images[$i]['id']);
                        $asset->sort = $i + 1;
                        $asset->save();
                    } else {
                        $temp = UploadTemp::find($images[$i]['id']);

                        $product->assets()->create([
                            'filename' => $temp->filename,
                            'sort' => $i + 1
                        ]);
                        $temp->status = 1;
                        $temp->save();
                    }
                }
            }
            $product->save();

            // if ($request->remove_varian) {
            //     $varianIds = json_decode($request->remove_varian);

            //     ProductVarian::whereIn('id', $varianIds)->delete();
            // }

            // if ($request->remove_subvarian) {
            //     $subVarianIds = json_decode($request->remove_subvarian);

            //     ProductVarian::whereIn('id', $subVarianIds)->delete();
            // }

            // if ($request->varians) {
            //     $datas = json_decode($request->varians, true);

            //     foreach ($datas as $data) {

            //         if ($request->boolean('has_subvarian') === true) {

            //             if (isset($data['id'])) {

            //                 $varian =  ProductVarian::find($data['id']);
            //             } else {

            //                 $varian =  new ProductVarian();
            //             }

            //             $varian->product_id = $product->id;
            //             $varian->has_subvarian = 1;
            //             $varian->label = $data['label'];
            //             $varian->value = $data['value'];
            //             $varian->save();

            //             foreach ($data['subvarian'] as $item) {

            //                 if (isset($item['id'])) {

            //                     ProductVarian::find($item['id'])->update($item);
            //                 } else {
            //                     $varian->subvarian()->create($item);
            //                 }
            //             }
            //         } else {

            //             if (isset($data['id'])) {

            //                 ProductVarian::find($data['id'])->update($data);
            //             } else {

            //                 $product->varians()->create($data);
            //             }
            //         }
            //     }
            // }


            DB::commit();

            $product->fresh();
            $product->load('assets', 'category', 'varians.subvarian');

            Cache::forget($product->slug);

            $this->clearCache();

            UploadTemp::clearTemp();

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
            if ($product->assets) {

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

    protected function clearCache()
    {
        Cache::forget('products');
        Cache::forget('initial_products');
        Cache::forget('product_promo');
        Cache::forget('categories');
    }
}
