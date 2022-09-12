<?php

namespace App\Console\Commands;

use App\Models\Asset;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Console\Command;

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
        $product = Product::first();
        $assets = $product->assets;
        $length = Product::count();
        $asset = Asset::first();

        $productArr = $product->toArray();

        $categories = Category::all();

        foreach($categories as $category) {

            for($i = 0; $i < 5; $i++) {
                $length ++;
                $title = 'Produk #' . $length;
                
                $productArr['title'] = 'Produk #' . $length;
                $productArr['slug'] = Str::slug($title);
                $productArr['category_id'] = $category->id;
    
                $p = Product::create($productArr);
    
                $p->assets()->create([
                    'filename' => $asset->filename
                ]);
            }
        }

    }
}
