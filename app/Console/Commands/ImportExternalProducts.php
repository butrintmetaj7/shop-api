<?php

namespace App\Console\Commands;

use App\Services\Product\ProductImportService;
use App\Services\Product\Importers\FakeStoreProductImporter;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Factory as HttpClient;

class ImportExternalProducts extends Command
{
     /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:import {--source=fakestore}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import products from external sources';

     /**
     * Execute the console command.
     */
    public function handle(HttpClient $httpClient): int
    {
        $source = $this->option('source');
        
        try {
            $importer = $this->getImporter($source, $httpClient);
            $importService = new ProductImportService($importer);
            
            $this->info("Starting product import from {$importer->getSource()}...");

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

    protected function getImporter(string $source, HttpClient $httpClient)
    {
        return match($source) {
            'fakestore' => new FakeStoreProductImporter($httpClient),
            default => throw new \InvalidArgumentException("Unknown import source: {$source}")
        };
    }
}
