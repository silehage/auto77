<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $guarded = [];

    public $appends = ['src', 'banner_src'];

    protected $casts = [
        'is_front' => 'boolean',
        'is_special' => 'boolean',
        'description' => 'string'
    ];

    public function getSrcAttribute()
    {
        return $this->filename ?  url('/upload/images/' . $this->filename) : '';
    }

    public function getBannerSrcAttribute()
    {
        return $this->banner ? url('/upload/images/' . $this->banner) : '';
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public function productItems()
    {
        return $this->hasMany(Product::class)->take(2);
    }
    public function productReviewRatings()
    {
        return $this->hasManyThrough(Review::class, Product::class)->avg('rating');
    }
}
