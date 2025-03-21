<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use League\Csv\Reader;
use App\Homeowners\Homeowner;

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
        $reader = Reader::createFromPath(storage_path('app/public/streetgroup.csv'), 'r');
        $reader->setHeaderOffset(0);

        $records = $reader->getRecords();

        foreach($records as $record) {
            $output = $homeowner->parseHomeownerString($record['homeowner']);
            var_dump($output);
        }
    }
}
