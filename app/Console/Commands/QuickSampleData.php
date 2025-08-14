<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\ImprovedSampleDataSeeder;

class QuickSampleData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:quick-sample {--force : Skip confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Quickly load sample data (improved seeder with optimizations)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('🗑️  This will replace existing sample data. Continue?')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }
        
        $this->info('🚀 Loading sample data using optimized seeder...');
        $startTime = microtime(true);
        
        try {
            $seeder = new ImprovedSampleDataSeeder();
            $seeder->setCommand($this);
            $seeder->run();
            
            $duration = round(microtime(true) - $startTime, 2);
            
            $this->newLine();
            $this->info("✅ Sample data loaded successfully in {$duration} seconds!");
            
            // Show some stats
            $userCount = \DB::table('users')->count();
            $bookingCount = \DB::table('bookings')->count();
            $ferryTripCount = \DB::table('ferry_trips')->count();
            $ferryTicketCount = \DB::table('ferry_tickets')->count();
            
            $this->newLine();
            $this->table(
                ['📊 Data Type', '🔢 Count'],
                [
                    ['👥 Total Users', number_format($userCount)],
                    ['🏨 Hotel Bookings', number_format($bookingCount)],
                    ['⛴️ Ferry Trips', number_format($ferryTripCount)],
                    ['🎫 Ferry Tickets', number_format($ferryTicketCount)],
                ]
            );
            
            $this->newLine();
            $this->line('🎯 <fg=green>Ready for testing and development!</>');
            
        } catch (\Exception $e) {
            $this->error('❌ Error loading sample data: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
