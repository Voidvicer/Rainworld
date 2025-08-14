<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Output file
$dumpFile = 'database/sample_data_dump.sql';

// Start output buffering
ob_start();

echo "-- Stormshade Theme Park Sample Data Dump\n";
echo "-- Generated on: " . date('Y-m-d H:i:s') . "\n";
echo "-- WARNING: This will DELETE existing sample data and replace with sample data\n\n";

echo "-- Disable foreign key checks\n";
echo "PRAGMA foreign_keys = OFF;\n\n";

// Clear existing sample data first
echo "-- Clear existing sample data\n";
echo "DELETE FROM ferry_tickets;\n";
echo "DELETE FROM bookings;\n";
echo "DELETE FROM ferry_trips;\n";
echo "DELETE FROM users WHERE email NOT IN ('admin@stormshade.com', 'admin@picnic.test', 'manager@picnic.test', 'ferry@picnic.test', 'visitor@picnic.test');\n";
echo "DELETE FROM rooms WHERE type = 'premium' OR name LIKE '%Executive%' OR name LIKE '%Presidential%' OR name LIKE '%Royal%' OR name LIKE '%Diamond%' OR name LIKE '%Platinum%' OR name LIKE '%Premier%' OR name LIKE '%Elite%' OR name LIKE '%Imperial%';\n";
echo "DELETE FROM locations;\n";
echo "DELETE FROM promotions;\n\n";

// Get all tables that contain sample data - in dependency order
$tables = [
    'locations' => '',
    'promotions' => '',
    'rooms' => 'type = \'premium\' OR name LIKE \'%Executive%\' OR name LIKE \'%Presidential%\' OR name LIKE \'%Royal%\' OR name LIKE \'%Diamond%\' OR name LIKE \'%Platinum%\' OR name LIKE \'%Premier%\' OR name LIKE \'%Elite%\' OR name LIKE \'%Imperial%\'',
    'users' => 'email NOT IN (\'admin@stormshade.com\', \'admin@picnic.test\', \'manager@picnic.test\', \'ferry@picnic.test\', \'visitor@picnic.test\')',
    'ferry_trips' => '',
    'bookings' => '',
    'ferry_tickets' => '',
];

// Export data for each table in order
foreach ($tables as $table => $condition) {
    echo "-- Dumping data for table: $table\n";
    
    try {
        $query = DB::table($table);
        if (!empty($condition)) {
            $query->whereRaw($condition);
        }
        $records = $query->get();
        
        if ($records->count() > 0) {
            $columns = array_keys((array) $records->first());
            $columnsList = implode(', ', array_map(function($col) { return "`$col`"; }, $columns));
            
            foreach ($records as $record) {
                $values = [];
                foreach ($columns as $column) {
                    $value = $record->$column;
                    if (is_null($value)) {
                        $values[] = 'NULL';
                    } elseif (is_numeric($value) && !str_starts_with($value, '0') && $column !== 'code') {
                        $values[] = $value;
                    } else {
                        $values[] = "'" . str_replace("'", "''", $value) . "'";
                    }
                }
                
                echo "INSERT INTO `$table` ($columnsList) VALUES (" . implode(', ', $values) . ");\n";
            }
        }
        
        echo "\n";
        echo "-- Dumped " . $records->count() . " records from $table\n\n";
        
    } catch (Exception $e) {
        echo "-- Error dumping $table: " . $e->getMessage() . "\n\n";
    }
}

echo "-- Re-enable foreign key checks\n";
echo "PRAGMA foreign_keys = ON;\n\n";
echo "-- Sample data dump completed\n";

// Save to file
$sql = ob_get_clean();
file_put_contents($dumpFile, $sql);

echo "Database dump created at: $dumpFile\n";
echo "File size: " . number_format(filesize($dumpFile)) . " bytes\n";

// Show some stats
try {
    $userCount = DB::table('users')->whereNotIn('email', ['admin@stormshade.com', 'admin@picnic.test', 'manager@picnic.test', 'ferry@picnic.test', 'visitor@picnic.test'])->count();
    $bookingCount = DB::table('bookings')->count();
    $ferryTripCount = DB::table('ferry_trips')->count();
    $ferryTicketCount = DB::table('ferry_tickets')->count();
    $roomCount = DB::table('rooms')->where(function($q) {
        $q->where('type', 'premium')
          ->orWhere('name', 'like', '%Executive%')
          ->orWhere('name', 'like', '%Presidential%')
          ->orWhere('name', 'like', '%Royal%')
          ->orWhere('name', 'like', '%Diamond%')
          ->orWhere('name', 'like', '%Platinum%')
          ->orWhere('name', 'like', '%Premier%')
          ->orWhere('name', 'like', '%Elite%')
          ->orWhere('name', 'like', '%Imperial%');
    })->count();
    
    echo "\nDump includes:\n";
    echo "- Users: " . number_format($userCount) . "\n";
    echo "- Hotel Bookings: " . number_format($bookingCount) . "\n";
    echo "- Ferry Trips: " . number_format($ferryTripCount) . "\n";
    echo "- Ferry Tickets: " . number_format($ferryTicketCount) . "\n";
    echo "- Premium Rooms: " . number_format($roomCount) . "\n";
} catch (Exception $e) {
    echo "Error getting stats: " . $e->getMessage() . "\n";
}
