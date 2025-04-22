<?php

namespace App\Http\Controllers\v2;

use App\Models\Asset;
use App\Models\UploadTemp;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

class AssetController extends Controller
{
    public function uploadFile(Request $request)
    {
        $path = public_path('/upload/images');

        if (!File::exists($path)) {
            File::makeDirectory($path, 0755, true, true);
        }

        $assets = [];

        foreach ($request->images as $file) {

            $rawFile = Image::make($file);

            $filename =  uniqid() . '-' . Str::random(20) . '.webp';

            $filepath = 'upload/images/' . $filename;

            $rawFile->resize(1200, null, function ($constraint) {
                $constraint->aspectRatio();
            })->encode('webp')->save($filepath);

            $asset = UploadTemp::create(['filename' => $filename]);

            $assets[] = $asset;
        }


        return response()->json([
            'success' => true,
            'results' => $assets
        ]);
    }

    public function deleteFile($id)
    {

        if ($temp = UploadTemp::find($id)) {
            UploadTemp::where('filename', $temp->filename)->delete();
            File::delete('upload/images/' . $temp->filename);
        } else if ($asset = Asset::find($id)) {
            Asset::where('filename', $asset->filename)->delete();
            File::delete('upload/images/' . $asset->filename);
        }

        return response()->json([
            'success' => true,
        ]);
    }
}
