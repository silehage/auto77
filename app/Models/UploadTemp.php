<?php

namespace App\Models;

use Illuminate\Support\Facades\File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UploadTemp extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['filename', 'status'];

    public $appends = ['src'];

    public function getSrcAttribute()
    {
        return url('/upload/images/' . $this->filename);
    }

    public static function clearTemp()
    {
        static::where('status', 1)->delete();
        $temps = static::where('status', 0)->get();
        foreach ($temps as $temp) {
            File::delete('upload/images/' . $temp->filename);
            $temp->delete();
        }
    }
}
