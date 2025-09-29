<?php

namespace App\Console\Commands;

use App\Services\ProductImportService;
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
                return 1;
            }

            $this->info($result['message']);
            return 0;

        } catch (\Exception $e) {
            $this->error('Error during import: ' . $e->getMessage());
            return 1;
        }
    }
}
