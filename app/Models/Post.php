<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;

    protected $guarded = [];
    public $appends = [
        'image_url',
        'created_locale',
        'last_update'
    ];

    public $casts = [
        'is_listing' => 'boolean',
        'is_promote' => 'boolean'
    ];

    public function getLastUpdateAttribute()
    {
        return Carbon::parse($this->updated_at)->tz('UTC')->toAtomString();
    }
    public function scopeListing()
    {
        return $this->where('is_listing', 1);
    }
    public function scopePromote()
    {
        return $this->where('is_promote', 1);
    }

    public function getImageUrlAttribute()
    {
        return $this->image ? config('app.url') . '/upload/images/' . $this->image : '';
    }

    public function getCreatedLocaleAttribute()
    {
        return Carbon::parse($this->created_at)->translatedFormat('d F Y');
    }
    public function galleries()
    {
        return $this->morphMany(Asset::class, 'assetable');
    }
}
