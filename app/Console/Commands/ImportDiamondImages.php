<?php

namespace App\Console\Commands;

use App\Models\Diamond_Catalog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ImportDiamondImages extends Command
{
    protected $signature = 'diamond_images:import';
    protected $description = 'Import existing images from the public folder into the database';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $imagePaths = File::glob(public_path('storage/diamondCatalog').'/*');
        foreach ($imagePaths as $path) {
            $filenameWithExtension = basename($path);
            $filename = pathinfo($filenameWithExtension, PATHINFO_FILENAME); // Get filename without extension
            $storedPath = str_replace(public_path('storage/diamondCatalog').'/', '', $path);
            
            // Check if the image with the same filename already exists
            $image = Diamond_Catalog::firstOrNew(['CODE' => $filename]);
            if (!$image->exists) {
                // Save image details to the database
                $image->Path = 'storage/diamondCatalog/' . $storedPath;
                $image->save();
            }
        }
        $this->info('Images imported successfully.');
    }
}
