<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FerryTicket;
use App\Models\Booking;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelMedium;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;
use App\Models\FerryTrip;
use Carbon\Carbon;

class TicketValidationController extends Controller
{
    public function ferryForm(Request $request)
    { 
        $selectedDate = $request->get('date', today()->toDateString());
        
        // Get all tickets for the selected date
        $tickets = FerryTicket::with(['trip', 'user'])
            ->whereHas('trip', function($q) use ($selectedDate) {
                $q->whereDate('date', $selectedDate);
            })
            ->where('status', 'paid')
            ->orderBy('created_at', 'desc')
            ->paginate(50);
        
        // Add validation status to each ticket
        $tickets->getCollection()->transform(function($ticket) {
            $validHotel = Booking::where('user_id', $ticket->user_id)
                ->where('status', '!=', 'canceled')
                ->whereDate('check_in', '<=', $ticket->trip->date)
                ->whereDate('check_out', '>=', $ticket->trip->date)
                ->exists();
            
            $ticket->is_valid = $validHotel && $ticket->status === 'paid';
            $ticket->has_hotel_booking = $validHotel;
            $ticket->pass_issued = $ticket->pass_issued_at ? true : false;
            
            return $ticket;
        });
        
        return view('manage.ferry.validate', compact('tickets', 'selectedDate'));
    }

    public function ferryCheck(Request $request)
    {
        $data = $request->validate(['code'=>'required']);
        $ticket = FerryTicket::with('trip','user')->where('code',$data['code'])->first();
        if (!$ticket) return back()->withErrors(['code'=>'Ticket not found']);

        $validHotel = Booking::where('user_id',$ticket->user_id)
            ->where('status','!=','canceled')
            ->whereDate('check_in','<=',$ticket->trip->date)
            ->whereDate('check_out','>=',$ticket->trip->date)
            ->exists();

        return back()->with('result',[
            'valid'=> $validHotel && $ticket->status === 'paid',
            'ticket'=>$ticket->only(['id','code','status','total_amount']),
            'trip'=>$ticket->trip->only(['id','date','depart_time','origin','destination'])
        ]);
    }
    
    public function bulkValidate(Request $request)
    {
        $request->validate([
            'ticket_ids' => 'required|array',
            'ticket_ids.*' => 'exists:ferry_tickets,id'
        ]);
        
        $tickets = FerryTicket::with(['trip', 'user'])
            ->whereIn('id', $request->ticket_ids)
            ->get();
        
        $results = [];
        
        foreach ($tickets as $ticket) {
            $validHotel = Booking::where('user_id', $ticket->user_id)
                ->where('status', '!=', 'canceled')
                ->whereDate('check_in', '<=', $ticket->trip->date)
                ->whereDate('check_out', '>=', $ticket->trip->date)
                ->exists();
            
            $isValid = $validHotel && $ticket->status === 'paid';
            
            $results[] = [
                'ticket_id' => $ticket->id,
                'ticket_code' => $ticket->code,
                'passenger_name' => $ticket->user->name,
                'is_valid' => $isValid,
                'has_hotel_booking' => $validHotel,
                'trip_info' => $ticket->trip->origin . ' → ' . $ticket->trip->destination . ' on ' . $ticket->trip->date
            ];
        }
        
        return response()->json([
            'success' => true,
            'results' => $results
        ]);
    }
    
    public function issuePass(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'ticket_id' => 'required|integer|exists:ferry_tickets,id'
        ]);

        try {
            // Get the ticket with relations
            $ticket = FerryTicket::with(['user', 'trip'])->find($validated['ticket_id']);
            
            if (!$ticket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket not found'
                ], 404);
            }

            // Check if the ticket is in the correct status
            if (!in_array($ticket->status, ['paid'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket must be paid to issue a pass. Current status: ' . $ticket->status
                ], 400);
            }

            // Check if pass has already been issued
            if ($ticket->pass_issued_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ferry pass has already been issued for this ticket on ' . $ticket->pass_issued_at->format('M j, Y \a\t g:i A')
                ], 400);
            }

            // Verify hotel booking requirement
            $hasValidHotelBooking = Booking::where('user_id', $ticket->user_id)
                ->where('status', '!=', 'canceled')
                ->whereDate('check_in', '<=', $ticket->trip->date)
                ->whereDate('check_out', '>=', $ticket->trip->date)
                ->exists();

            if (!$hasValidHotelBooking) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid hotel booking found. Passenger must have an active hotel booking that covers the ferry trip date.'
                ], 400);
            }

            // Issue the ferry pass
            $ticket->update([
                'pass_issued_at' => now()
                // Keep the current status (paid) - don't change to 'confirmed' as it's not allowed
            ]);

            // Log the successful pass issuance
            \Log::info('Ferry pass issued', [
                'ticket_id' => $ticket->id,
                'ticket_code' => $ticket->code,
                'user_id' => $ticket->user_id,
                'user_name' => $ticket->user->name,
                'trip_id' => $ticket->trip->id,
                'trip_date' => $ticket->trip->date,
                'issued_by' => auth()->id(),
                'issued_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ferry pass successfully issued to ' . $ticket->user->name,
                'data' => [
                    'ticket_id' => $ticket->id,
                    'ticket_code' => $ticket->code,
                    'passenger_name' => $ticket->user->name,
                    'trip_date' => $ticket->trip->date,
                    'trip_time' => $ticket->trip->depart_time,
                    'route' => $ticket->trip->origin . ' → ' . $ticket->trip->destination,
                    'issued_at' => $ticket->pass_issued_at->format('Y-m-d H:i:s'),
                    'pass_url' => route('manage.ferry.pass.view', $ticket->id)
                ]
            ]);

        } catch (\Exception $e) {
            // Log the error
            \Log::error('Ferry pass issuance failed', [
                'ticket_id' => $validated['ticket_id'],
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while issuing the ferry pass. Please try again or contact support.'
            ], 500);
        }
    }

    public function viewPass(FerryTicket $ticket)
    {
        // Ensure the pass has been issued
        if (!$ticket->pass_issued_at) {
            abort(404, 'Ferry pass not found or not issued.');
        }

        // Only allow viewing if:
        // - user is admin or ferry_staff
        // - OR user owns the ticket
        $user = auth()->user();
        if (!$user || (!$user->hasRole(['admin', 'ferry_staff']) && $ticket->user_id !== $user->id)) {
            abort(403, 'You do not have permission to view this ferry pass.');
        }

        // Load relationships
        $ticket->load(['user', 'trip']);

        return view('manage.ferry.pass', compact('ticket'));
    }

    // Test method to check ferry pass system status
    public function testPassSystem(Request $request)
    {
        if (!auth()->user() || !auth()->user()->hasRole(['admin', 'ferry_staff'])) {
            abort(403);
        }

        $testResults = [
            'database_connection' => true,
            'models_loaded' => true,
            'routes_accessible' => true,
            'sample_data' => []
        ];

        try {
            // Test database connection
            $ticketCount = FerryTicket::count();
            $testResults['database_connection'] = true;
            $testResults['total_tickets'] = $ticketCount;

            // Test if we can fetch tickets with relationships
            $sampleTickets = FerryTicket::with(['user', 'trip'])
                ->where('status', 'paid')
                ->take(3)
                ->get();

            foreach ($sampleTickets as $ticket) {
                $hasValidBooking = Booking::where('user_id', $ticket->user_id)
                    ->where('status', '!=', 'canceled')
                    ->whereDate('check_in', '<=', $ticket->trip->date)
                    ->whereDate('check_out', '>=', $ticket->trip->date)
                    ->exists();

                $testResults['sample_data'][] = [
                    'ticket_id' => $ticket->id,
                    'ticket_code' => $ticket->code,
                    'passenger_name' => $ticket->user->name,
                    'trip_date' => $ticket->trip->date,
                    'status' => $ticket->status,
                    'pass_issued' => (bool) $ticket->pass_issued_at,
                    'has_valid_hotel_booking' => $hasValidBooking,
                    'eligible_for_pass' => $hasValidBooking && $ticket->status === 'paid' && !$ticket->pass_issued_at
                ];
            }

        } catch (\Exception $e) {
            $testResults['error'] = $e->getMessage();
            $testResults['database_connection'] = false;
        }

        return response()->json([
            'success' => true,
            'message' => 'Ferry pass system test completed',
            'system_status' => 'operational',
            'test_results' => $testResults,
            'timestamp' => now()->toISOString()
        ]);
    }

    private function generateFerryPassHtml($ticket)
    {
        return '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ferry Pass - ' . $ticket->code . '</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .ferry-pass {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
        }
        .ferry-pass::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 8px;
            background: linear-gradient(90deg, #007bff, #28a745, #ffc107, #dc3545);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px dashed #e9ecef;
        }
        .title {
            font-size: 28px;
            font-weight: bold;
            color: #2c3e50;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .subtitle {
            color: #6c757d;
            margin: 5px 0 0 0;
            font-size: 14px;
        }
        .pass-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 25px;
        }
        .detail-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            border-left: 4px solid #007bff;
        }
        .detail-label {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .detail-value {
            font-size: 16px;
            color: #2c3e50;
            font-weight: bold;
        }
        .route-section {
            background: linear-gradient(135deg, #e3f2fd, #f3e5f5);
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 25px;
            text-align: center;
        }
        .route {
            font-size: 24px;
            font-weight: bold;
            color: #1565c0;
            margin-bottom: 10px;
        }
        .departure-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
        }
        .code-section {
            background: #2c3e50;
            color: white;
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            margin-bottom: 20px;
        }
        .code-label {
            font-size: 14px;
            opacity: 0.8;
            margin-bottom: 10px;
        }
        .code-value {
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 4px;
            font-family: "Courier New", monospace;
        }
        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 2px dashed #e9ecef;
            color: #6c757d;
            font-size: 12px;
        }
        .valid-badge {
            display: inline-block;
            background: #28a745;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            margin-top: 10px;
        }
        @media print {
            body { background: white; }
            .ferry-pass { box-shadow: none; }
        }
    </style>
</head>
<body>
    <div class="ferry-pass">
        <div class="header">
            <h1 class="title">Sample Ferry Pass</h1>
            <p class="subtitle">Stormshade Ferry Services</p>
        </div>
        
        <div class="pass-details">
            <div class="detail-item">
                <div class="detail-label">Passenger Name</div>
                <div class="detail-value">' . htmlspecialchars($ticket->user->name) . '</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Ticket Quantity</div>
                <div class="detail-value">' . $ticket->quantity . ' passengers</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Trip Date</div>
                <div class="detail-value">' . date('F j, Y', strtotime($ticket->trip->date)) . '</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Departure Time</div>
                <div class="detail-value">' . date('g:i A', strtotime($ticket->trip->depart_time)) . '</div>
            </div>
        </div>
        
        <div class="route-section">
            <div class="route">' . htmlspecialchars($ticket->trip->origin) . ' → ' . htmlspecialchars($ticket->trip->destination) . '</div>
            <div class="departure-info">
                <span><strong>Departure:</strong> ' . date('M j, Y \a\t g:i A', strtotime($ticket->trip->date . ' ' . $ticket->trip->depart_time)) . '</span>
                <span><strong>Amount:</strong> $' . number_format($ticket->total_amount, 2) . '</span>
            </div>
        </div>
        
        <div class="code-section">
            <div class="code-label">BOARDING PASS CODE</div>
            <div class="code-value">' . $ticket->code . '</div>
        </div>
        
        <div class="footer">
            <div class="valid-badge">✓ VALID PASS</div>
            <p>Please present this pass at the ferry terminal<br>
            Keep this pass with you during the entire journey<br>
            <strong>Issued:</strong> ' . now()->format('M j, Y \a\t g:i A') . '</p>
        </div>
    </div>
</body>
</html>';
    }
    
    public function bulkIssuePass(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'ticket_ids' => 'required|array|min:1',
            'ticket_ids.*' => 'integer|exists:ferry_tickets,id'
        ]);

        try {
            $ticketIds = $validated['ticket_ids'];
            $results = [
                'success' => true,
                'total_tickets' => count($ticketIds),
                'issued_count' => 0,
                'failed_count' => 0,
                'issued' => [],
                'failed' => []
            ];

            // Get all tickets with relations
            $tickets = FerryTicket::with(['user', 'trip'])
                ->whereIn('id', $ticketIds)
                ->get();

            foreach ($tickets as $ticket) {
                try {
                    // Check if the ticket is in the correct status
                    if (!in_array($ticket->status, ['paid'])) {
                        $results['failed'][] = [
                            'ticket_id' => $ticket->id,
                            'ticket_code' => $ticket->code,
                            'passenger_name' => $ticket->user->name,
                            'reason' => 'Invalid ticket status: ' . $ticket->status . ' (must be paid)'
                        ];
                        continue;
                    }

                    // Check if pass has already been issued
                    if ($ticket->pass_issued_at) {
                        $results['failed'][] = [
                            'ticket_id' => $ticket->id,
                            'ticket_code' => $ticket->code,
                            'passenger_name' => $ticket->user->name,
                            'reason' => 'Pass already issued on ' . $ticket->pass_issued_at->format('M j, Y')
                        ];
                        continue;
                    }

                    // Verify hotel booking requirement
                    $hasValidHotelBooking = Booking::where('user_id', $ticket->user_id)
                        ->where('status', '!=', 'canceled')
                        ->whereDate('check_in', '<=', $ticket->trip->date)
                        ->whereDate('check_out', '>=', $ticket->trip->date)
                        ->exists();

                    if (!$hasValidHotelBooking) {
                        $results['failed'][] = [
                            'ticket_id' => $ticket->id,
                            'ticket_code' => $ticket->code,
                            'passenger_name' => $ticket->user->name,
                            'reason' => 'No valid hotel booking found'
                        ];
                        continue;
                    }

                    // Issue the ferry pass
                    $ticket->update([
                        'pass_issued_at' => now()
                        // Keep the current status (paid) - don't change to 'confirmed' as it's not allowed
                    ]);

                    $results['issued'][] = [
                        'ticket_id' => $ticket->id,
                        'ticket_code' => $ticket->code,
                        'passenger_name' => $ticket->user->name,
                        'trip_info' => $ticket->trip->origin . ' → ' . $ticket->trip->destination . ' on ' . $ticket->trip->date,
                        'issued_at' => $ticket->pass_issued_at->format('M j, Y \a\t g:i A')
                    ];

                    // Log the successful pass issuance
                    \Log::info('Ferry pass issued (bulk)', [
                        'ticket_id' => $ticket->id,
                        'ticket_code' => $ticket->code,
                        'user_id' => $ticket->user_id,
                        'user_name' => $ticket->user->name,
                        'trip_id' => $ticket->trip->id,
                        'issued_by' => auth()->id(),
                        'issued_at' => now()
                    ]);

                } catch (\Exception $e) {
                    $results['failed'][] = [
                        'ticket_id' => $ticket->id,
                        'ticket_code' => $ticket->code,
                        'passenger_name' => $ticket->user->name,
                        'reason' => 'System error: ' . $e->getMessage()
                    ];
                }
            }

            // Update counts
            $results['issued_count'] = count($results['issued']);
            $results['failed_count'] = count($results['failed']);

            // Log bulk operation summary
            \Log::info('Bulk ferry pass issuance completed', [
                'total_tickets' => $results['total_tickets'],
                'issued_count' => $results['issued_count'],
                'failed_count' => $results['failed_count'],
                'performed_by' => auth()->id()
            ]);

            return response()->json($results);

        } catch (\Exception $e) {
            \Log::error('Bulk ferry pass issuance failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred during bulk pass issuance. Please try again or contact support.'
            ], 500);
        }
    }
}
