<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\Booking;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HotelManagementController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_hotels' => Hotel::count(),
            'active_hotels' => Hotel::where('active', true)->count(),
            'total_rooms' => Room::sum('total_rooms'),
            'today_checkouts' => Booking::whereDate('check_out', today())->where('status', 'confirmed')->count(),
            'today_checkins' => Booking::whereDate('check_in', today())->where('status', 'confirmed')->count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
        ];
        
        $recentBookings = Booking::with('user', 'room.hotel')
            ->latest()
            ->take(10)
            ->get();
        
        $occupancyData = $this->getOccupancyData();
        
        // All users use the management interface
        return view('manage.hotel.dashboard', compact('stats', 'recentBookings', 'occupancyData'));
    }
    
    public function roomAvailability(Request $request)
    {
        $selectedDate = $request->get('date', today()->toDateString());
        $hotels = Hotel::with(['rooms' => function($query) use ($selectedDate) {
            $query->withCount(['bookings as occupied_rooms' => function($q) use ($selectedDate) {
                $q->where('status', '!=', 'canceled')
                  ->where(function($subQ) use ($selectedDate) {
                      $subQ->whereDate('check_in', '<=', $selectedDate)
                           ->whereDate('check_out', '>', $selectedDate);
                  });
            }]);
        }])->where('active', true)->get();
        
        // All users use the management interface
        return view('manage.hotel.availability', compact('hotels', 'selectedDate'));
    }
    
    public function bookingReports(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->subDays(30)->toDateString());
        $dateTo = $request->get('date_to', now()->toDateString());
        
        $bookings = Booking::with('user', 'room.hotel')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->orderBy('created_at', 'desc')
            ->paginate(50);
            
        // Calculate comprehensive statistics
        $allBookings = Booking::with('room.hotel')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->get();
            
        $totalBookings = $allBookings->count();
        $totalRevenue = $allBookings->where('payment_status', 'paid')->sum('total_amount');
        $confirmedBookings = $allBookings->where('status', 'confirmed');
        
        // Calculate occupancy rate (simplified)
        $totalRoomNights = $confirmedBookings->sum(function($booking) {
            return \Carbon\Carbon::parse($booking->check_in)
                ->diffInDays(\Carbon\Carbon::parse($booking->check_out));
        });
        
        $stats = [
            'total_bookings' => $totalBookings,
            'total_revenue' => $totalRevenue,
            'avg_daily_rate' => $confirmedBookings->count() > 0 
                ? $totalRevenue / $totalRoomNights 
                : 0,
            'occupancy_rate' => $this->calculateOccupancyRate($confirmedBookings),
            'top_hotel' => $this->getTopHotel($allBookings),
            'top_hotel_revenue' => $this->getTopHotelRevenue($allBookings),
            'avg_stay_duration' => $confirmedBookings->count() > 0 
                ? round($totalRoomNights / $confirmedBookings->count(), 1) 
                : 0
        ];
        
        // Prepare chart data
        $chartData = $this->prepareHotelChartData($allBookings, $dateFrom, $dateTo);
        
        return view('manage.hotel.reports', compact('bookings', 'stats', 'dateFrom', 'dateTo', 'chartData'));
    }
    
    private function calculateOccupancyRate($bookings)
    {
        // Simplified occupancy calculation
        if ($bookings->isEmpty()) return 0;
        
        $totalBookings = $bookings->count();
        $totalPossibleBookings = \App\Models\Room::count() * 30; // Rough estimate
        
        return $totalPossibleBookings > 0 ? round(($totalBookings / $totalPossibleBookings) * 100, 1) : 0;
    }
    
    private function getTopHotel($bookings)
    {
        if ($bookings->isEmpty()) return 'N/A';
        
        return $bookings->groupBy('room.hotel.name')
            ->map(function($hotelBookings) {
                return $hotelBookings->where('payment_status', 'paid')->sum('total_amount');
            })
            ->sortDesc()
            ->keys()
            ->first() ?? 'N/A';
    }
    
    private function getTopHotelRevenue($bookings)
    {
        if ($bookings->isEmpty()) return 0;
        
        return $bookings->groupBy('room.hotel.name')
            ->map(function($hotelBookings) {
                return $hotelBookings->where('payment_status', 'paid')->sum('total_amount');
            })
            ->sortDesc()
            ->first() ?? 0;
    }
    
    private function prepareHotelChartData($bookings, $dateFrom, $dateTo)
    {
        $chartData = [
            'dates' => [],
            'dailyBookings' => [],
            'dailyRevenue' => [],
            'occupancyRate' => [],
            'hotels' => [],
            'hotelRevenue' => [],
            'roomTypes' => [],
            'roomTypeBookings' => []
        ];

        // Generate date range
        $start = \Carbon\Carbon::parse($dateFrom);
        $end = \Carbon\Carbon::parse($dateTo);
        
        $bookingsByDate = $bookings->groupBy(function($booking) {
            return $booking->created_at->format('Y-m-d');
        });
        
        while ($start <= $end) {
            $dateStr = $start->format('Y-m-d');
            $chartData['dates'][] = $start->format('M j');
            
            $dayBookings = $bookingsByDate->get($dateStr, collect());
            
            // Daily bookings count
            $chartData['dailyBookings'][] = $dayBookings->count();
            
            // Daily revenue
            $dailyRevenue = $dayBookings->where('payment_status', 'paid')->sum('total_amount');
            $chartData['dailyRevenue'][] = round($dailyRevenue, 2);
            
            // Simplified occupancy rate
            $occupancy = $dayBookings->count() > 0 ? rand(40, 85) : 0; // Mock data for demo
            $chartData['occupancyRate'][] = $occupancy;
            
            $start->addDay();
        }

        // Hotel performance data
        $hotelRevenue = $bookings->groupBy('room.hotel.name')->map(function($hotelBookings) {
            return $hotelBookings->where('payment_status', 'paid')->sum('total_amount');
        });

        $chartData['hotels'] = $hotelRevenue->keys()->toArray();
        $chartData['hotelRevenue'] = $hotelRevenue->values()->toArray();

        // Room type distribution
        $roomTypeBookings = $bookings->groupBy('room.name')->map->count();
        $chartData['roomTypes'] = $roomTypeBookings->keys()->toArray();
        $chartData['roomTypeBookings'] = $roomTypeBookings->values()->toArray();

        return $chartData;
    }
    
    public function promotionManagement()
    {
        $promotions = \App\Models\Promotion::where('scope', 'hotel')
            ->orWhere('scope', 'global')
            ->latest()
            ->paginate(20);
            
        return view('admin.hotels.promotions', compact('promotions'));
    }
    
    public function storePromotion(Request $request)
    {
        // Check if user can create promotions (only hotel managers and admins)
        if (!auth()->user()->hasRole(['admin', 'hotel_manager'])) {
            return back()->with('error', 'You do not have permission to create promotions.');
        }
        
        // Check active promotion limit (max 2 active promotions)
        $activePromotions = \App\Models\Promotion::where('active', true)
            ->where(function($query) {
                $query->where('scope', 'hotel')->orWhere('scope', 'global');
            })->count();
            
        if ($activePromotions >= 2 && $request->boolean('active')) {
            return back()->with('error', 'Maximum of 2 active promotions allowed. Please deactivate an existing promotion first.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'active' => 'boolean',
            'image_url' => 'nullable|url'
        ]);

        \App\Models\Promotion::create([
            'title' => $request->title,
            'content' => $request->content,
            'discount_percentage' => $request->discount_percentage,
            'starts_at' => $request->starts_at,
            'ends_at' => $request->ends_at,
            'active' => $request->boolean('active'),
            'image_url' => $request->image_url,
            'scope' => 'hotel'
        ]);

        return back()->with('success', 'Hotel promotion created successfully.');
    }
    
    public function updatePromotion(Request $request, $id)
    {
        if (!auth()->user()->hasRole(['admin', 'hotel_manager'])) {
            return back()->with('error', 'You do not have permission to modify promotions.');
        }
        
        $promotion = \App\Models\Promotion::findOrFail($id);
        
        // Check active promotion limit if activating
        if (!$promotion->active && $request->boolean('active')) {
            $activePromotions = \App\Models\Promotion::where('active', true)
                ->where('id', '!=', $id)
                ->where(function($query) {
                    $query->where('scope', 'hotel')->orWhere('scope', 'global');
                })->count();
                
            if ($activePromotions >= 2) {
                return back()->with('error', 'Maximum of 2 active promotions allowed. Please deactivate an existing promotion first.');
            }
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'active' => 'boolean',
            'image_url' => 'nullable|url'
        ]);
        
        $promotion->update([
            'title' => $request->title,
            'content' => $request->content,
            'discount_percentage' => $request->discount_percentage,
            'starts_at' => $request->starts_at,
            'ends_at' => $request->ends_at,
            'active' => $request->boolean('active'),
            'image_url' => $request->image_url,
        ]);
        
        return back()->with('success', 'Promotion updated successfully.');
    }
    
    public function deactivatePromotion($id)
    {
        if (!auth()->user()->hasRole(['admin', 'hotel_manager'])) {
            return back()->with('error', 'You do not have permission to modify promotions.');
        }
        
        $promotion = \App\Models\Promotion::findOrFail($id);
        $promotion->update(['active' => false]);
        
        return back()->with('success', 'Promotion deactivated successfully.');
    }
    
    public function deletePromotion($id)
    {
        if (!auth()->user()->hasRole(['admin', 'hotel_manager'])) {
            return back()->with('error', 'You do not have permission to delete promotions.');
        }
        
        $promotion = \App\Models\Promotion::findOrFail($id);
        $promotion->delete();
        
        return back()->with('success', 'Promotion deleted successfully.');
    }
    
    private function getOccupancyData()
    {
        $last7Days = collect(range(6, 0))->map(function($daysAgo) {
            $date = now()->subDays($daysAgo);
            $totalRooms = Room::sum('total_rooms');
            $occupiedRooms = Booking::where('status', '!=', 'canceled')
                ->where(function($q) use ($date) {
                    $q->whereDate('check_in', '<=', $date->toDateString())
                      ->whereDate('check_out', '>', $date->toDateString());
                })
                ->count();
                
            return [
                'date' => $date->format('M j'),
                'occupancy_rate' => $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0
            ];
        });
        
        return $last7Days;
    }
}
