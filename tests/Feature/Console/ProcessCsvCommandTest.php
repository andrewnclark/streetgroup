<?php

namespace Tests\Feature\Console;

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProcessCsvCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        // Clean up any test files
        Storage::disk('public')->delete('streetgroup.csv');
        parent::tearDown();
    }

    /**
     * Test that the command fails when the CSV file is missing
     */
    public function testCommandFailsWhenCsvMissing()
    {
        $this->artisan('app:process-csv')
            ->expectsOutputToContain('Unfortunately, the streetgroup.csv cannot be located')
            ->assertFailed();
    }

    /**
     * Test that the command processes a valid CSV file
     */
    public function testCommandProcessesCsvSuccessfully()
    {
        // Create a test CSV file
        $csvContent = "homeowner\nMr John Smith\nMrs Jane Smith";
        Storage::disk('public')->put('streetgroup.csv', $csvContent);

        $this->artisan('app:process-csv')
            ->expectsOutputToContain('Processing complete: 2 successful, 0 failed')
            ->assertSuccessful();
    }

    /**
     * Test that the command handles parsing errors
     */
    public function testCommandHandlesParsingErrors()
    {
        // Create a test CSV file with some invalid entries
        $csvContent = "homeowner\nMr John Smith\ninvalid\n";
        Storage::disk('public')->put('streetgroup.csv', $csvContent);

        $this->artisan('app:process-csv')
            ->expectsOutputToContain('Error parsing homeowner: invalid')
            ->expectsOutputToContain('Processing complete: 1 successful, 1 failed')
            ->assertSuccessful();
    }
}
