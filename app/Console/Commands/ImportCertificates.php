<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ImportCertificates extends Command
{
    protected $signature = 'import:certificates';
    protected $description = 'Import certificate paths into the database';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Base path of the certificates on your local filesystem
        $basePath = 'C:/Users/Administrator/Documents/GitHub/Gold_Catalog/storage/app/public/diamondCatalog/Certificates/'; // Adjust the path as needed

        // Get all files in the directory
        $files = File::allFiles($basePath);

        if (empty($files)) {
            $this->error("No files found in the specified directory.");
            return;
        }

        foreach ($files as $file) {
            $extension = strtolower($file->getExtension());

            // Process only PDF, JPG, and JPEG files
            if (in_array($extension, ['pdf', 'jpg', 'jpeg'])) {
                // Extract the certificate code from the filename (without extension)
                $filename = pathinfo($file, PATHINFO_FILENAME);

                // Check if the file has already been imported
                $imported = DB::table('imported_certificates')->where('filename', $filename)->exists();

                if ($imported) {
                    $this->info("Skipping already imported file: $filename");
                    continue;
                }

                // Find the corresponding record in the database
                $record = DB::table('diamond_catalog')->where('CODE', $filename)->first();

                if ($record) {
                    // Convert the local filesystem path to a relative path
                    $relativePath = str_replace('C:/Users/Administrator/Documents/GitHub/Gold_Catalog/', '', $file);

                    // Update the certificate code and path in the database
                    DB::table('diamond_catalog')
                    ->where('CODE', $filename)
                    ->update([
                        'certificate_code' => $filename,
                        'certificate_path' => $relativePath
                    ]);

                    // Insert a record into the imported_certificates table
                    DB::table('imported_certificates')->insert([
                        'filename' => $filename,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    $this->info("Updated certificate for CODE: $filename");
                } else {
                    $this->warn("No record found for CODE: $filename");
                }
            }
        }

        $this->info('Certificate import completed.');
    }
}
