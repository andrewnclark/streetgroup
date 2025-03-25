<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use League\Csv\Reader;
use App\Homeowners\Homeowner;
use App\Homeowners\Parser\Exceptions\StringCouldNotBeParsedException;
use Illuminate\Support\Facades\Storage;

class ProcessCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-csv';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process the CSV provided by Street Group and output the required format.';

    /**
     * Execute the console command.
     */
    public function handle(Homeowner $homeowner)
    {
        if (Storage::disk('public')->exists('streetgroup.csv')) {
            $reader = Reader::createFromPath(storage_path('app/public/streetgroup.csv'), 'r');
            $reader->setHeaderOffset(0);

            $progress = $this->output->createProgressBar(count($reader));
            $successCount = 0;
            $errorCount = 0;

            foreach($reader->getRecords() as $record) {
                try {
                    $output = $homeowner->parseHomeownerStringToArray($record['homeowner']);
                    $successCount++;
                } catch (StringCouldNotBeParsedException $e) {
                    $this->error("Error parsing homeowner: " . $record['homeowner']);
                    $this->line("  " . $e->getMessage());
                    $errorCount++;
                }
                $progress->advance();
            }
            
            $progress->finish();
            $this->newLine(2);
            $this->info("Processing complete: $successCount successful, $errorCount failed");

            return 0;
        }
        
        $this->error("Unfortunately, the streetgroup.csv cannot be located in the public storage directory");
        $this->fail();
    }
}
