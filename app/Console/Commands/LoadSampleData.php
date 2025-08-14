<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class LoadSampleData extends Command
{
    protected $signature = 'db:load-sample-data {--force : Force load without confirmation}';
    protected $description = 'Load sample data from SQL dump file (much faster than seeding)';

    public function handle()
    {
        $dumpFile = database_path('sample_data_dump.sql');
        
        if (!File::exists($dumpFile)) {
            $this->error("Sample data dump file not found at: $dumpFile");
            $this->info("Run 'php create_database_dump.php' first to create the dump file.");
            return 1;
        }
        
        if (!$this->option('force')) {
            if (!$this->confirm('This will DELETE existing sample data and load from dump. Continue?')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }
        
        $this->info('Loading sample data from dump file...');
        
        try {
            $sql = File::get($dumpFile);
            
            // Split SQL into individual statements
            $statements = array_filter(
                array_map('trim', explode(';', $sql)),
                function($stmt) {
                    return !empty($stmt) && !str_starts_with($stmt, '--');
                }
            );
            
            $this->info("Executing " . count($statements) . " SQL statements...");
            
            DB::beginTransaction();
            
            foreach ($statements as $index => $statement) {
                if (trim($statement)) {
                    DB::statement($statement);
                    
                    if (($index + 1) % 100 == 0) {
                        $this->info("Executed " . ($index + 1) . " statements...");
                    }
                }
            }
            
            DB::commit();
            
            $this->info('✅ Sample data loaded successfully!');
            
            // Show some stats
            $userCount = DB::table('users')->count();
            $bookingCount = DB::table('bookings')->count();
            $ferryTripCount = DB::table('ferry_trips')->count();
            $ferryTicketCount = DB::table('ferry_tickets')->count();
            
            $this->table(
                ['Type', 'Count'],
                [
                    ['Users', $userCount],
                    ['Hotel Bookings', $bookingCount],
                    ['Ferry Trips', $ferryTripCount],
                    ['Ferry Tickets', $ferryTicketCount]
                ]
            );
            
        } catch (\Exception $e) {
            DB::rollback();
            $this->error('Error loading sample data: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
