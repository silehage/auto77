<?php

namespace App\Models;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Config extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $fillable = [
        'theme', 
        'theme_color', 
        'home_view_mode', 
        'product_view_mode', 

    ];
    public $appends = [
        'is_mail_ready',
    ];

    public function getIsMailReadyAttribute()
    {
        $mail = config('mail.mailers.smtp');
        if($mail['username'] && $mail['host'] && $mail['port'] && $mail['encryption'] && $mail['password']) {
            return true;
        }
        return false;
    }
}
