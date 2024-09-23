<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\DB;
class UpdateImagePath_Diamond extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-image-path_-diamond';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
                // Update image paths using raw SQL query
                DB::statement("UPDATE diamond_catalog SET Path = CONCAT('storage/diamondCatalog/', Code ,'.jpg' )");

                $this->info('Image paths updated successfully.');
    }
}
