<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Models;
use App\Models\GoldItem;

class UpdateModelsSource extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'models:update-source';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the source field to Production in both models and gold_items tables';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to update sources...');

        try {
            // Show current sources in Models table
            $currentModelSources = Models::select('source')
                ->distinct()
                ->get()
                ->pluck('source');

            $this->info('Current sources in models table:');
            foreach ($currentModelSources as $source) {
                $this->line("- '$source'");
            }

            // Show current sources in GoldItems table
            $currentGoldItemSources = GoldItem::select('source')
                ->distinct()
                ->get()
                ->pluck('source');

            $this->info('Current sources in gold_items table:');
            foreach ($currentGoldItemSources as $source) {
                $this->line("- '$source'");
            }

            // Update Models table
            $modelsCount = Models::query()
                ->whereRaw('LOWER(TRIM(source)) = ?', ['production'])
                ->update(['source' => 'Production']);

            // Update GoldItems table
            $goldItemsCount = GoldItem::query()
                ->whereRaw('LOWER(TRIM(source)) = ?', ['production'])
                ->update(['source' => 'Production']);

            $this->info("Successfully updated:");
            $this->line("- {$modelsCount} records in models table");
            $this->line("- {$goldItemsCount} records in gold_items table");

            // Show final sources in Models table
            $updatedModelSources = Models::select('source')
                ->distinct()
                ->get()
                ->pluck('source');

            $this->info('Final sources in models table:');
            foreach ($updatedModelSources as $source) {
                $this->line("- '$source'");
            }

            // Show final sources in GoldItems table
            $updatedGoldItemSources = GoldItem::select('source')
                ->distinct()
                ->get()
                ->pluck('source');

            $this->info('Final sources in gold_items table:');
            foreach ($updatedGoldItemSources as $source) {
                $this->line("- '$source'");
            }

        } catch (\Exception $e) {
            $this->error("An error occurred while updating sources: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
