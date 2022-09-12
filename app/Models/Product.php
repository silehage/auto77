<?php

namespace App\Models;

use App\Models\Asset;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid as Generator; 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'stock',
        'price',
        'sold',
        'status',
        'category_id',
        'weight',
        'sku'
    ];
    // public $appends = ['rating'];

    protected $casts = [
        'status' > 'boolean',
        'category_id' => 'integer'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function assets()
    {
        return $this->morphMany(Asset::class, 'assetable');
    }
    public function reviews()
    {
        return $this->hasMany(Review::class)->latest();
    }
    public function reviewsLimit()
    {
        return $this->hasMany(Review::class)->take(4);
    }
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
    public function getRatingAttribute()
    {
        return $this->productRating();
    }
    public function productRating()
    {
        return $this->hasMany(Review::class)->avg('rating');
    }
    public function getRealStockAttribute()
    {

        if($this->variantItems()) {
            return $this->variantItems()->sum('item_stock');
            
        } else {

            return $this->stock;
        }

    }
    public function variantItems()
    {
        return $this->hasMany(ProductVariantValue::class);
    }
    public function discount()
    {
        return $this->belongsTo(Discount::class, 'discount_id', 'id');
    }
    public function promote()
    {
        return $this->belongsTo(Promote::class, 'promote_id', 'id')->where('start_date', '<', now())->where('end_date', '>', now());
    }
    public function promo()
    {
        return $this->belongsToMany(Promo::class, 'product_promos', 'product_id', 'promo_id')->withPivot('discount_type', 'discount_amount')->where('end_date', '>', now());
    }
    public function productPromo()
    {
        return $this->hasOne(ProductPromo::class, 'product_id', 'id');
    }
    public function varians()
    {
        return $this->hasMany(ProductVarian::class);
    }
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            try {
                $model->sku = Generator::uuid4()->toString();
            } catch (\Exception $e) {
                $model->sku = Str::upper(Str::random(32));
                Log::info($e->getMessage());
            }
        });
    }
}
