<?php

namespace App\Console\Commands;

use App\Models\Slider;
use App\Models\Gallery;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ResizeImage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:resize-image';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $sliders = Slider::where('filename', 'like', '%.png')->orWhere('filename', 'like', '%.jpg')->get();

        foreach ($sliders as $item) {

            $filename = $this->resize($item);
            $item->update([
                'filename' => $filename
            ]);
        }

        $galleries = Gallery::where('filename', 'like', '%.png')->orWhere('filename', 'like', '%.jpg')->get();

        foreach ($galleries as $item) {

            $filename = $this->resize($item);
            $item->update([
                'filename' => $filename
            ]);
        }

        $categories = Category::where('filename', 'like', '%.png')->orWhere('filename', 'like', '%.jpg')->get();

        foreach ($categories as $item) {

            $filename = $this->resize($item);
            $item->update([
                'filename' => $filename
            ]);
        }

        $products = Category::where('filename', 'like', '%.png')->orWhere('filename', 'like', '%.jpg')->get();

        foreach ($products as $item) {

            $filename = $this->resize($item);
            $item->update([
                'filename' => $filename
            ]);
        }
    }

    protected function resize($item)
    {

        $filesource = 'upload/images/' . $item->filename;
        $file = file_get_contents(public_path($filesource));
        $rawFile = Image::make($file);

        $filename =  uniqid() . '-' . Str::random(20) . '.webp';

        $savePath = public_path('/upload/images/' . $filename);

        $file = $rawFile->resize(1200, null, function ($constraint) {
            $constraint->aspectRatio();
        })->encode('webp')->save($savePath);

        unlink(public_path($filesource));

        return $filename;
    }
}
