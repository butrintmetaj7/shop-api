<?php

namespace App\Console\Commands;

use App\Services\Product\ProductImportService;
use Illuminate\Console\Command;

class ImportFakeStoreProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import products from Fake Store API';

    /**
     * Execute the console command.
     */
    public function handle(ProductImportService $importService): int
    {
        $this->info('Starting product import from Fake Store API...');

        try {
            $result = $importService->importProducts();

            if (!$result['success']) {
                $this->error($result['message']);
                return Command::FAILURE;
            }

            $this->info($result['message']);
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Error during import: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
