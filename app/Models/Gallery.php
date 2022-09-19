<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;

    protected $fillable = [
        'weight', 'filename'
    ];
    public $timestamps = false;
    public $appends = ['src'];

    public function getSrcAttribute()
    {
        return url('/upload/images/' . $this->filename);
    }
}
