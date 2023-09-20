<?php

namespace App\Console\Commands;

use App\Models\Asset;
use Illuminate\Console\Command;

class GenerateAssetFilepath extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'asset:generate-filepath';

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
        Asset::orderBy('id')->chunk(100, function ($assets) {
            foreach ($assets as $asset) {
                $asset->filepath = 'upload/images/' . $asset->filename;
                $asset->save();
            }
        });
    }
}
