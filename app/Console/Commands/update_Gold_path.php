<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class update_Gold_path extends Command
{
    protected $signature = 'images:update-paths';

    protected $description = 'Update image paths in the database';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Update image paths using raw SQL query
        DB::statement("UPDATE models SET scanned_image = CONCAT('storage/Gold_catalog/', model, '.jpg' )");

        $this->info('Image paths updated successfully.');
    }
}
