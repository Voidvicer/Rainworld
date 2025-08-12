<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FerryTrip;
use App\Models\FerryTicket;
use App\Models\Booking;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FerryManagementController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'today_trips' => FerryTrip::whereDate('departure_date', today())->count(),
            'active_trips' => FerryTrip::whereDate('departure_date', today())
                ->whereIn('status', ['scheduled', 'boarding', 'departed'])
                ->count(),
            'today_passengers' => FerryTicket::whereHas('trip', function($q) {
                $q->whereDate('departure_date', today());
            })->where('status', 'paid')->sum('quantity'),
            'capacity_utilization' => $this->getTodayCapacityUtilization(),
            'today_revenue' => FerryTicket::whereHas('trip', function($q) {
                $q->whereDate('departure_date', today());
            })->where('status', 'paid')->sum('total_amount'),
            'revenue_change' => 5.2, // Mock data
            'alerts_count' => 0, // Mock data
        ];
        
        $todayTrips = FerryTrip::whereDate('departure_date', today())
            ->with(['tickets'])
            ->get()
            ->map(function($trip) {
                $bookedPassengers = $trip->tickets->where('status', 'paid')->sum('quantity');
                $utilizationPercentage = $trip->passenger_capacity > 0 
                    ? round(($bookedPassengers / $trip->passenger_capacity) * 100, 1) 
                    : 0;
                
                $trip->booked_passengers = $bookedPassengers;
                $trip->utilization_percentage = $utilizationPercentage;
                $trip->utilization_color = $utilizationPercentage >= 80 ? 'red' : ($utilizationPercentage >= 60 ? 'amber' : 'green');
                
                return $trip;
            });
        
        $routePerformance = collect([
            (object)[
                'route_name' => 'Main Island ↔ North Island',
                'trip_count' => 12,
                'revenue' => 15420.50,
                'avg_utilization' => 78.5
            ],
            (object)[
                'route_name' => 'Main Island ↔ South Island', 
                'trip_count' => 8,
                'revenue' => 11250.00,
                'avg_utilization' => 65.2
            ]
        ]);
        
        $passengerChartData = $this->getPassengerChartData();
        $recentAlerts = collect(); // Mock empty alerts
        
        return view('admin.ferry.dashboard', compact('stats', 'todayTrips', 'routePerformance', 'passengerChartData', 'recentAlerts'));
    }

    public function schedule(Request $request)
    {
        $query = FerryTrip::query();
        
        // Apply filters
        if ($request->filled('start_date')) {
            $query->whereDate('departure_date', '>=', $request->start_date);
        } else {
            $query->whereDate('departure_date', '>=', today());
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('departure_date', '<=', $request->end_date);
        } else {
            $query->whereDate('departure_date', '<=', today()->addDays(30));
        }
        
        if ($request->filled('route')) {
            $query->where(function($q) use ($request) {
                $q->where('departure_location', 'like', '%' . $request->route . '%')
                  ->orWhere('arrival_location', 'like', '%' . $request->route . '%');
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $trips = $query->with(['tickets'])
            ->orderBy('departure_date')
            ->orderBy('departure_time')
            ->paginate(20);
        
        // Add booked passengers count to each trip
        $trips->getCollection()->transform(function($trip) {
            $trip->booked_passengers = $trip->tickets->where('status', 'paid')->sum('quantity');
            return $trip;
        });
        
        $routes = FerryTrip::select('departure_location', 'arrival_location')
            ->distinct()
            ->get()
            ->map(function($trip) {
                return $trip->departure_location . ' → ' . $trip->arrival_location;
            })
            ->unique()
            ->values();
        
        return view('admin.ferry.schedule', compact('trips', 'routes'));
    }

    public function updateTripStatus(Request $request, FerryTrip $trip)
    {
        $request->validate([
            'status' => 'required|in:scheduled,boarding,departed,arrived,canceled'
        ]);
        
        $trip->update(['status' => $request->status]);
        
        return response()->json(['success' => true]);
    }

    public function getTripData(FerryTrip $trip)
    {
        return response()->json($trip);
    }

    public function storeTrip(Request $request)
    {
        $request->validate([
            'departure_location' => 'required|string|max:255',
            'arrival_location' => 'required|string|max:255',
            'departure_date' => 'required|date',
            'departure_time' => 'required',
            'duration_hours' => 'required|numeric|min:0.5',
            'passenger_capacity' => 'required|integer|min:1',
            'price_per_person' => 'required|numeric|min:0',
            'status' => 'required|in:scheduled,boarding,departed,arrived,canceled'
        ]);
        
        $data = $request->all();
        $data['arrival_time'] = date('H:i:s', strtotime($data['departure_time'] . ' + ' . $data['duration_hours'] . ' hours'));
        $data['date'] = $data['departure_date']; // For backward compatibility
        
        FerryTrip::create($data);
        
        return response()->json(['success' => true]);
    }

    public function updateTrip(Request $request, FerryTrip $trip)
    {
        $request->validate([
            'departure_location' => 'required|string|max:255',
            'arrival_location' => 'required|string|max:255', 
            'departure_date' => 'required|date',
            'departure_time' => 'required',
            'duration_hours' => 'required|numeric|min:0.5',
            'passenger_capacity' => 'required|integer|min:1',
            'price_per_person' => 'required|numeric|min:0',
            'status' => 'required|in:scheduled,boarding,departed,arrived,canceled'
        ]);
        
        $data = $request->all();
        $data['arrival_time'] = date('H:i:s', strtotime($data['departure_time'] . ' + ' . $data['duration_hours'] . ' hours'));
        $data['date'] = $data['departure_date']; // For backward compatibility
        
        $trip->update($data);
        
        return response()->json(['success' => true]);
    }
    
    private function getTodayCapacityUtilization()
    {
        $todayTrips = FerryTrip::whereDate('departure_date', today())->get();
        
        if ($todayTrips->isEmpty()) {
            return 0;
        }
        
        $totalCapacity = $todayTrips->sum('passenger_capacity');
        $totalBooked = $todayTrips->sum(function($trip) {
            return $trip->tickets->where('status', 'paid')->sum('quantity');
        });
        
        return $totalCapacity > 0 ? round(($totalBooked / $totalCapacity) * 100, 1) : 0;
    }
    
    private function getPassengerChartData()
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $passengers = FerryTicket::whereHas('trip', function($q) use ($date) {
                $q->whereDate('departure_date', $date);
            })->where('status', 'paid')->sum('quantity');
            
            $data[] = [
                'date' => $date->format('M j'),
                'passengers' => $passengers
            ];
        }
        
        return $data;
    }
    
    public function passengerLists(Request $request)
    {
        $selectedDate = $request->get('date', today()->toDateString());
        
        $trips = FerryTrip::with(['tickets' => function($query) {
            $query->with('user')->where('status', 'paid');
        }])
        ->whereDate('date', $selectedDate)
        ->orderBy('depart_time')
        ->get();
        
        return view('admin.ferry.passengers', compact('trips', 'selectedDate'));
    }
    
    public function issueFerryPass(Request $request)
    {
        $request->validate([
            'ticket_code' => 'required|string',
            'booking_verification' => 'required|boolean'
        ]);
        
        $ticket = FerryTicket::with('trip', 'user')->where('code', $request->ticket_code)->first();
        
        if (!$ticket) {
            return back()->withErrors(['ticket_code' => 'Ticket not found.']);
        }
        
        // Verify user has valid hotel booking for trip date
        $hasValidBooking = Booking::where('user_id', $ticket->user_id)
            ->where('status', '!=', 'canceled')
            ->whereDate('check_in', '<=', $ticket->trip->date)
            ->whereDate('check_out', '>=', $ticket->trip->date)
            ->exists();
        
        if (!$hasValidBooking && $request->booking_verification) {
            return back()->withErrors(['booking_verification' => 'No valid hotel booking found for this passenger.']);
        }
        
        // Update ticket status to indicate pass has been issued
        $ticket->update(['status' => 'completed']);
        
        $passData = [
            'ticket' => $ticket,
            'passenger_name' => $ticket->user->name,
            'trip_details' => $ticket->trip,
            'quantity' => $ticket->quantity,
            'issued_at' => now(),
            'valid_booking' => $hasValidBooking
        ];
        
        return view('admin.ferry.pass', compact('passData'));
    }
    
    public function tripReports(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->subDays(7)->toDateString());
        $dateTo = $request->get('date_to', now()->toDateString());
        
        $trips = FerryTrip::with(['tickets' => function($query) {
            $query->where('status', 'paid');
        }])
        ->whereBetween('date', [$dateFrom, $dateTo])
        ->orderBy('date', 'desc')
        ->orderBy('depart_time')
        ->paginate(50);
        
        $stats = [
            'total_trips' => FerryTrip::whereBetween('date', [$dateFrom, $dateTo])->count(),
            'total_passengers' => FerryTicket::whereHas('trip', function($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('date', [$dateFrom, $dateTo]);
            })->where('status', 'paid')->sum('quantity'),
            'total_revenue' => FerryTicket::whereHas('trip', function($q) use ($dateFrom, $dateTo) {
                $q->whereBetween('date', [$dateFrom, $dateTo]);
            })->where('status', 'paid')->sum('total_amount'),
            'average_occupancy' => $this->calculateAverageOccupancy($dateFrom, $dateTo)
        ];
        
        return view('admin.ferry.reports', compact('trips', 'stats', 'dateFrom', 'dateTo'));
    }
    
    public function validateTicketAdvanced(Request $request)
    {
        $request->validate(['code' => 'required|string']);
        
        $ticket = FerryTicket::with('trip', 'user')->where('code', $request->code)->first();
        
        if (!$ticket) {
            return back()->withErrors(['code' => 'Ticket not found.']);
        }
        
        // Check hotel booking validity
        $hotelBooking = Booking::where('user_id', $ticket->user_id)
            ->where('status', '!=', 'canceled')
            ->whereDate('check_in', '<=', $ticket->trip->date)
            ->whereDate('check_out', '>=', $ticket->trip->date)
            ->first();
        
        $validationResult = [
            'ticket_valid' => $ticket->status === 'paid',
            'booking_valid' => (bool) $hotelBooking,
            'trip_today' => $ticket->trip->date === today()->toDateString(),
            'ticket_details' => $ticket,
            'booking_details' => $hotelBooking,
            'validation_timestamp' => now()
        ];
        
        return back()->with('validation_result', $validationResult);
    }
    
    private function calculateAverageOccupancy($dateFrom, $dateTo)
    {
        $trips = FerryTrip::whereBetween('date', [$dateFrom, $dateTo])->get();
        
        if ($trips->isEmpty()) return 0;
        
        $totalOccupancy = $trips->sum(function($trip) {
            $soldSeats = $trip->tickets()->where('status', 'paid')->sum('quantity');
            return ($soldSeats / $trip->capacity) * 100;
        });
        
        return round($totalOccupancy / $trips->count(), 1);
    }
}
