<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;
    protected $guarded = [];
    public $appends = ['logo_url', 'app_url', 'favicon_url'];

    public function getLogoUrlAttribute()
    {
        return $this->logo_path? url($this->logo_path) : '';
    }
    public function getFaviconUrlAttribute()
    {
        return $this->favicon? url($this->favicon) : '';
    }
    public function getAppUrlAttribute()
    {
        return rtrim(config('app.url'), '/');
    }
}
