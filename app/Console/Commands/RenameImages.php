<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class RenameImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:rename';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rename images from 201-xxxx to 01/xxxx while keeping them in the same directory';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $directory = public_path('storage/diamondCatalog'); // Adjust this path to your images directory
        $files = File::files($directory);

        foreach ($files as $file) {
            $filename = $file->getFilename();

            if (strpos($filename, '214-') === 0) {
                $newFilename = '014-' . substr($filename, 4); // '01_' prefix and removes '201-'
                $newFilePath = $directory . DIRECTORY_SEPARATOR . $newFilename;

                // Rename the file
                File::move($file->getRealPath(), $newFilePath);

                $this->info("Renamed: {$filename} to {$newFilename}");
            }
        }

        $this->info('All files renamed successfully.');
        return 0;
    }
}
