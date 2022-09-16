<?php

namespace App\Console\Commands;

use Exception;
use App\Models\Asset;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DuplicateProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:duplicate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $products = Product::oldest()->take(5)->get();;
        $length = Product::count();

        $categories = Category::all();

        DB::beginTransaction();

        try {

            foreach($categories as $category) {

                foreach($products as $product) {

                    $assets = $product->assets;
                    $productArr = $product->toArray();
                    
                    $length ++;
                    $title = 'Produk ' . $length;
                    
                    $productArr['title'] = $title;
                    $productArr['slug'] = Str::slug($title);
                    $productArr['category_id'] = $category->id;
        
                    $p = Product::create($productArr);
        
                    $p->assets()->create([
                        'filename' => $assets[0]->filename
                    ]);
                }
            }
            DB::commit();

        } catch (Exception $e) {
            
            DB::rollback();
        }

    }
}
