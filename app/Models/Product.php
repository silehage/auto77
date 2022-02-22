<?php

namespace App\Models;

use App\Models\Asset;
use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $guarded = [];
    // public $appends = ['rating'];

    protected $casts = [
        'status' > 'boolean'
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
        return $this->hasMany(Review::class);
    }
    public function reviewsLimit()
    {
        return $this->hasMany(Review::class)->take(4);
    }
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
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
        return $this->belongsTo(Promote::class, 'promote_id', 'id');
    }
    public function promoDiscount()
    {
        if($this->promote()) {
            
            return $this->promote();

        } else {

            return $this->discount();
        }
    }
    public function getPricingAttribute()
    {
        $pricing = [
            'default_price' => $this->price,
            'current_price' => $this->price,
            'discount_value' => 0,
            'discount_percent' => 0,
            'is_discount' => false 
        ];

        $disc = null;

        if($this->discount_id) {
            $disc = $this->discount;
        } elseif($this->promote_id) {
            $disc = $this->promote->discount;
        }

        if($disc) {

            $pricing['is_discount'] = true;

            $discValue = 0;
            

            if($disc->unit == 'percent') {
 
                $discValue = ($this->price*$disc->value) / 100;

                $pricing['current_price'] = $this->price - ($this->price*$disc->value / 100);
                $pricing['discount_percent'] = $disc->value;
                
             } else{
 
                 $discValue = $disc->value;
                 $pricing['current_price'] = $this->price - (int) $disc->value;
                 $pricing['discount_percent'] = number_format(((int)$disc->value / $this->price)*100, 1);
 
            }

            $pricing['discount_value'] = $discValue;
         }

        return $pricing;
    }
}
