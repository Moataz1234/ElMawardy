<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use App\Models\Gold_Catalog;

class ImportImages extends Command
{
    protected $signature = 'images:import';
    protected $description = 'Import existing images from the public folder into the database';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $imagePaths = File::glob(public_path('storage/goldCatalog').'/*');
        foreach ($imagePaths as $path) {
            $filenameWithExtension = basename($path);
            $filename = pathinfo($filenameWithExtension, PATHINFO_FILENAME); // Get filename without extension
            $storedPath = str_replace(public_path('storage/goldCatalog').'/', '', $path);
            
            // Check if the image with the same filename already exists
            $image = Gold_Catalog::firstOrNew(['FileName' => $filename]);
            if (!$image->exists) {
                // Save image details to the database
                $image->Path = 'storage/goldCatalog/' . $storedPath;
                $image->save();
            }
        }
        $this->info('Images imported successfully.');
    }
}
