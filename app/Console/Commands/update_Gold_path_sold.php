<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class update_Gold_path_sold extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:update-paths-sold';

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
         // Update image paths using raw SQL query
         DB::statement("UPDATE gold_items_sold SET link = CONCAT('storage/Gold_catalog/', model, '.jpg' )");

         $this->info('Image paths updated successfully.');
    }
}
