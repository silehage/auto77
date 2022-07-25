<?php

namespace App\Models;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid as Generator; 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductVarian extends Model
{
    use HasFactory;
    protected $fillable = [
        'label',
        'value',
        'sku',
        'price',
        'stock',
        'has_subvarian',
        'varian_id',
    ];
    protected $casts = [
        'has_subvarian' => 'boolean'
    ];

    public function subvarian()
    {
        return $this->hasMany(ProductVarian::class, 'varian_id', 'id');
    }
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            try {
                $model->sku = Generator::uuid4()->toString();
            } catch (Exception $e) {
                $model->sku = Str::upper(Str::random(32));
                Log::info($e->getMessage());
            }
        });
    }
}
