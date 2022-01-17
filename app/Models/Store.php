<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;
    protected $guarded = [];
    public $appends = ['logo'];

    public function getLogoAttribute()
    {
        return $this->logo_path? url($this->logo_path) : '';
    }
}
