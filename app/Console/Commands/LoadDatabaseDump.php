<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class LoadDatabaseDump extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:load-dump {--force : Skip confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load sample data from SQL dump (much faster than seeding)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dumpFile = database_path('sample_data_dump.sql');
        
        if (!File::exists($dumpFile)) {
            $this->error("❌ Sample data dump file not found!");
            $this->info("💡 Run 'php create_database_dump.php' first to create the dump file.");
            return 1;
        }
        
        if (!$this->option('force')) {
            if (!$this->confirm('🗑️  This will DELETE existing sample data and load from dump. Continue?')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }
        
        $this->info('📁 Loading sample data from dump file...');
        $this->info('📄 File size: ' . number_format(File::size($dumpFile)) . ' bytes');
        
        try {
            $sql = File::get($dumpFile);
            
            // Split SQL into individual statements
            $statements = array_filter(
                array_map('trim', preg_split('/;(\s*\n|$)/', $sql)),
                function($stmt) {
                    return !empty($stmt) && !str_starts_with($stmt, '--') && trim($stmt) !== '';
                }
            );
            
            $this->info("⚙️  Executing " . count($statements) . " SQL statements...");
            
            $bar = $this->output->createProgressBar(count($statements));
            $bar->start();
            
            DB::beginTransaction();
            
            foreach ($statements as $statement) {
                if (trim($statement)) {
                    DB::statement($statement);
                    $bar->advance();
                }
            }
            
            DB::commit();
            $bar->finish();
            $this->newLine();
            
            $this->info('✅ Sample data loaded successfully!');
            
            // Show some stats
            $userCount = DB::table('users')->count();
            $bookingCount = DB::table('bookings')->count();
            $ferryTripCount = DB::table('ferry_trips')->count();
            $ferryTicketCount = DB::table('ferry_tickets')->count();
            $roomCount = DB::table('rooms')->count();
            
            $this->newLine();
            $this->table(
                ['📊 Data Type', '🔢 Count'],
                [
                    ['👥 Users', number_format($userCount)],
                    ['🏨 Hotel Bookings', number_format($bookingCount)],
                    ['⛴️ Ferry Trips', number_format($ferryTripCount)],
                    ['🎫 Ferry Tickets', number_format($ferryTicketCount)],
                    ['🛏️ Rooms', number_format($roomCount)],
                ]
            );
            
        } catch (\Exception $e) {
            DB::rollback();
            $this->error('❌ Error loading sample data: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}
