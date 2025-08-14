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
            'today_trips' => FerryTrip::whereDate('date', today())->count(),
            'active_trips' => FerryTrip::whereDate('date', today())
                ->whereIn('status', ['scheduled', 'boarding', 'departed'])
                ->count(),
            'today_passengers' => FerryTicket::whereHas('trip', function($q) {
                $q->whereDate('date', today());
            })->where('status', 'paid')->sum('quantity'),
            'capacity_utilization' => $this->getTodayCapacityUtilization(),
            'today_revenue' => FerryTicket::whereHas('trip', function($q) {
                $q->whereDate('date', today());
            })->where('status', 'paid')->sum('total_amount'),
            'revenue_change' => 5.2, // Mock data
            'alerts_count' => 0, // Mock data
        ];
        
        $todayTrips = FerryTrip::whereDate('date', today())
            ->with(['tickets'])
            ->get()
            ->map(function($trip) {
                $bookedPassengers = $trip->tickets->where('status', 'paid')->sum('quantity');
                $utilizationPercentage = $trip->capacity > 0 
                    ? round(($bookedPassengers / $trip->capacity) * 100, 1) 
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
        
        // All users use the management interface
        return view('manage.ferry.dashboard', compact('stats', 'todayTrips', 'routePerformance', 'passengerChartData', 'recentAlerts'));
    }

    public function schedule(Request $request)
    {
        $query = FerryTrip::query();
        
        // Apply filters
        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        } else {
            $query->whereDate('date', '>=', today());
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        } else {
            $query->whereDate('date', '<=', today()->addDays(30));
        }
        
        if ($request->filled('route')) {
            $query->where(function($q) use ($request) {
                $q->where('origin', 'like', '%' . $request->route . '%')
                  ->orWhere('destination', 'like', '%' . $request->route . '%');
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $trips = $query->with(['tickets'])
            ->orderBy('date')
            ->orderBy('depart_time')
            ->paginate(20);
        
        // Add booked passengers count to each trip
        $trips->getCollection()->transform(function($trip) {
            $trip->booked_passengers = $trip->tickets->where('status', 'paid')->sum('quantity');
            return $trip;
        });
        
        $routes = FerryTrip::select('origin', 'destination')
            ->distinct()
            ->get()
            ->map(function($trip) {
                return $trip->origin . ' → ' . $trip->destination;
            })
            ->unique()
            ->values();
        
        // All users use the management interface
        return view('manage.ferry.schedule', compact('trips', 'routes'));
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
            'origin' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'date' => 'required|date',
            'depart_time' => 'required',
            'duration_hours' => 'required|numeric|min:0.5',
            'capacity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:scheduled,boarding,departed,arrived,canceled'
        ]);
        
        $data = $request->all();
        // Calculate arrival time if needed
        if (isset($data['duration_hours'])) {
            $data['arrival_time'] = date('H:i:s', strtotime($data['depart_time'] . ' + ' . $data['duration_hours'] . ' hours'));
        }
        
        FerryTrip::create($data);
        
        return response()->json(['success' => true]);
    }

    public function updateTrip(Request $request, FerryTrip $trip)
    {
        $request->validate([
            'origin' => 'required|string|max:255',
            'destination' => 'required|string|max:255', 
            'date' => 'required|date',
            'depart_time' => 'required',
            'duration_hours' => 'required|numeric|min:0.5',
            'capacity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:scheduled,boarding,departed,arrived,canceled'
        ]);
        
        $data = $request->all();
        // Calculate arrival time if needed
        if (isset($data['duration_hours'])) {
            $data['arrival_time'] = date('H:i:s', strtotime($data['depart_time'] . ' + ' . $data['duration_hours'] . ' hours'));
        }
        
        $trip->update($data);
        
        return response()->json(['success' => true]);
    }
    
    private function getTodayCapacityUtilization()
    {
        $todayTrips = FerryTrip::whereDate('date', today())->get();
        
        if ($todayTrips->isEmpty()) {
            return 0;
        }
        
        $totalCapacity = $todayTrips->sum('capacity');
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
                $q->whereDate('date', $date);
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
        
        // Preload booking data to avoid N+1 queries
        $userIds = $trips->flatMap(function($trip) {
            return $trip->tickets->pluck('user_id');
        })->unique();
        
        $validBookings = \App\Models\Booking::whereIn('user_id', $userIds)
            ->where('status', '!=', 'canceled')
            ->whereDate('check_in', '<=', $selectedDate)
            ->whereDate('check_out', '>=', $selectedDate)
            ->pluck('user_id')
            ->toArray();
        
        // Add booking status to each trip's tickets
        $trips->each(function($trip) use ($validBookings) {
            $trip->tickets->each(function($ticket) use ($validBookings) {
                $ticket->hasValidBooking = in_array($ticket->user_id, $validBookings);
            });
        });
        
        // All users use the management interface
        return view('manage.ferry.passengers', compact('trips', 'selectedDate'));
    }
    
    public function issueFerryPass(Request $request, $booking = null)
    {
        $request->validate([
            'ticket_code' => 'required|string',
            'booking_verification' => 'sometimes|boolean'
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
        
        if (!$hasValidBooking && $request->has('booking_verification') && $request->booking_verification) {
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
        
        return back()->with('passData', $passData);
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
        
        // All users use the management interface
        return view('manage.ferry.reports', compact('trips', 'stats', 'dateFrom', 'dateTo'));
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

    public function reports(Request $request)
    {
        $dateFrom = $request->get('dateFrom', now()->subDays(30)->format('Y-m-d'));
        $dateTo = $request->get('dateTo', now()->format('Y-m-d'));
        $routeFilter = $request->get('route');

        $query = FerryTrip::with(['tickets' => function($q) {
            $q->where('status', 'paid');
        }])
        ->whereBetween('date', [$dateFrom, $dateTo]);

        if ($routeFilter) {
            $routeParts = explode(' → ', $routeFilter);
            if (count($routeParts) === 2) {
                $query->where('origin', $routeParts[0])
                      ->where('destination', $routeParts[1]);
            }
        }

        $trips = $query->orderBy('date', 'desc')
                      ->orderBy('depart_time', 'desc')
                      ->paginate(20);

        // Calculate statistics
        $allTripsQuery = FerryTrip::with(['tickets' => function($q) {
            $q->where('status', 'paid');
        }])
        ->whereBetween('date', [$dateFrom, $dateTo]);

        if ($routeFilter) {
            $routeParts = explode(' → ', $routeFilter);
            if (count($routeParts) === 2) {
                $allTripsQuery->where('origin', $routeParts[0])
                             ->where('destination', $routeParts[1]);
            }
        }

        $allTrips = $allTripsQuery->get();
        
        $totalTrips = $allTrips->count();
        $totalPassengers = $allTrips->sum(function($trip) {
            return $trip->tickets->sum('quantity');
        });
        $totalTickets = $allTrips->sum(function($trip) {
            return $trip->tickets->count();
        });
        $totalRevenue = $allTrips->sum(function($trip) {
            return $trip->tickets->sum('total_amount');
        });
        
        $totalCapacity = $allTrips->sum('capacity');
        $avgOccupancy = $totalCapacity > 0 ? round(($totalPassengers / $totalCapacity) * 100, 1) : 0;

        // Prepare chart data
        $chartData = $this->prepareChartData($allTrips, $dateFrom, $dateTo);

        $stats = [
            'total_trips' => $totalTrips,
            'total_passengers' => $totalPassengers,
            'total_tickets' => $totalTickets,
            'total_revenue' => $totalRevenue,
            'avg_occupancy' => $avgOccupancy,
            'peak_time' => 'Morning' // Can be enhanced with actual calculation
        ];

        return view('manage.ferry.reports', compact(
            'trips', 
            'stats', 
            'dateFrom', 
            'dateTo',
            'chartData'
        ));
    }

    private function prepareChartData($trips, $dateFrom, $dateTo)
    {
        $chartData = [
            'dates' => [],
            'occupancy' => [],
            'revenue' => [],
            'routes' => [],
            'routeRevenue' => []
        ];

        // Group trips by date for daily charts
        $tripsByDate = $trips->groupBy(function($trip) {
            return $trip->date;
        });

        // Generate date range
        $start = \Carbon\Carbon::parse($dateFrom);
        $end = \Carbon\Carbon::parse($dateTo);
        
        while ($start <= $end) {
            $dateStr = $start->format('Y-m-d');
            $chartData['dates'][] = $start->format('M j');
            
            $dayTrips = $tripsByDate->get($dateStr, collect());
            
            // Calculate occupancy
            $totalCapacity = $dayTrips->sum('capacity');
            $totalPassengers = $dayTrips->sum(function($trip) {
                return $trip->tickets->sum('quantity');
            });
            $occupancy = $totalCapacity > 0 ? round(($totalPassengers / $totalCapacity) * 100, 1) : 0;
            $chartData['occupancy'][] = $occupancy;
            
            // Calculate revenue
            $dailyRevenue = $dayTrips->sum(function($trip) {
                return $trip->tickets->sum('total_amount');
            });
            $chartData['revenue'][] = round($dailyRevenue, 2);
            
            $start->addDay();
        }

        // Route performance data
        $routeRevenue = $trips->groupBy(function($trip) {
            return $trip->origin . ' → ' . $trip->destination;
        })->map(function($routeTrips) {
            return $routeTrips->sum(function($trip) {
                return $trip->tickets->sum('total_amount');
            });
        });

        $chartData['routes'] = $routeRevenue->keys()->toArray();
        $chartData['routeRevenue'] = $routeRevenue->values()->toArray();

        return $chartData;
    }

    public function exportTrips(Request $request)
    {
        $dateFrom = $request->get('dateFrom', now()->subMonth()->toDateString());
        $dateTo = $request->get('dateTo', now()->toDateString());
        $route = $request->get('route');

        $query = FerryTrip::with(['tickets.user'])
            ->whereBetween('date', [$dateFrom, $dateTo]);

        if ($route) {
            $query->where(function($q) use ($route) {
                $routeParts = explode(' → ', $route);
                if (count($routeParts) == 2) {
                    $q->where('origin', $routeParts[0])->where('destination', $routeParts[1]);
                }
            });
        }

        $trips = $query->orderBy('date')->orderBy('depart_time')->get();

        $csvData = [];
        $csvData[] = ['Date', 'Route', 'Departure', 'Capacity', 'Booked', 'Occupancy %', 'Revenue'];

        foreach ($trips as $trip) {
            $bookedSeats = $trip->tickets->where('status', 'paid')->sum('quantity');
            $revenue = $trip->tickets->where('status', 'paid')->sum('total_amount');
            $occupancy = $trip->capacity > 0 ? round(($bookedSeats / $trip->capacity) * 100, 1) : 0;

            $csvData[] = [
                $trip->date,
                $trip->origin . ' → ' . $trip->destination,
                $trip->depart_time,
                $trip->capacity,
                $bookedSeats,
                $occupancy . '%',
                '$' . number_format($revenue, 2)
            ];
        }

        $filename = 'ferry_trips_' . $dateFrom . '_to_' . $dateTo . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ];

        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportRevenue(Request $request)
    {
        $dateFrom = $request->get('dateFrom', now()->subMonth()->toDateString());
        $dateTo = $request->get('dateTo', now()->toDateString());
        $route = $request->get('route');

        $query = FerryTrip::with(['tickets.user'])
            ->whereBetween('date', [$dateFrom, $dateTo]);

        if ($route) {
            $query->where(function($q) use ($route) {
                $routeParts = explode(' → ', $route);
                if (count($routeParts) == 2) {
                    $q->where('origin', $routeParts[0])->where('destination', $routeParts[1]);
                }
            });
        }

        $trips = $query->orderBy('date')->orderBy('depart_time')->get();

        $csvData = [];
        $csvData[] = ['Date', 'Route', 'Departure', 'Tickets Sold', 'Total Passengers', 'Ticket Revenue', 'Avg Price per Passenger'];

        $totalRevenue = 0;
        $totalPassengers = 0;

        foreach ($trips as $trip) {
            $paidTickets = $trip->tickets->where('status', 'paid');
            $ticketCount = $paidTickets->count();
            $passengerCount = $paidTickets->sum('quantity');
            $revenue = $paidTickets->sum('total_amount');
            $avgPrice = $passengerCount > 0 ? $revenue / $passengerCount : 0;

            $totalRevenue += $revenue;
            $totalPassengers += $passengerCount;

            $csvData[] = [
                $trip->date,
                $trip->origin . ' → ' . $trip->destination,
                $trip->depart_time,
                $ticketCount,
                $passengerCount,
                '$' . number_format($revenue, 2),
                '$' . number_format($avgPrice, 2)
            ];
        }

        // Add summary row
        $csvData[] = ['', '', '', '', '', '', ''];
        $csvData[] = ['SUMMARY', '', '', '', $totalPassengers, '$' . number_format($totalRevenue, 2), '$' . number_format($totalPassengers > 0 ? $totalRevenue / $totalPassengers : 0, 2)];

        $filename = 'ferry_revenue_' . $dateFrom . '_to_' . $dateTo . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ];

        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
