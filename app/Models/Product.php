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
        'price',
        'category_id',
        'sku',
        'is_available',
        'price_custom_label',
    ];
    // public $appends = ['rating'];

    protected $casts = [
        'is_available' > 'boolean',
        'category_id' => 'integer'
    ];

    public function scopeIsAvailable($query)
    {
        return $query->where('is_available', 1);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function assets()
    {
        return $this->morphMany(Asset::class, 'assetable')->orderBy('sort');
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
