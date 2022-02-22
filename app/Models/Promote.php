<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Promote extends Model
{
    use HasFactory;
    protected $guarded = [];

    public $appends = ['is_active', 'now', 'start', 'diff_start', 'diff_end'];

    protected $with =['discount'];

    protected $casts = [
        'start_date' => 'datetime:d F Y H:i',
        'end_date' => 'datetime:d F Y H:i',
    ];

    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }
    public function getIsActiveAttribute()
    {
        if(Carbon::parse($this->start_date) < Carbon::now() && Carbon::parse($this->end_date) > Carbon::now()) {
            return true;
        }
        return false;
    }
    public function getNowAttribute()
    {
        return now();
    }
    public function getStartAttribute()
    {
        return Carbon::parse($this->start_date);
    }
    public function getDiffStartAttribute()
    {
        return Carbon::parse($this->start_date)->diffForHumans();
    }
    public function getDiffEndAttribute()
    {
        return Carbon::parse($this->end_date)->diffForHumans();
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }
    public function scopeActive($query)
    {
        return $query->where('start_date', '<', now())->where('end_date', '>', now());
    }
}
