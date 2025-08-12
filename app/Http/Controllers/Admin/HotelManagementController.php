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
        
        return view('admin.hotels.dashboard', compact('stats', 'recentBookings', 'occupancyData'));
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
        
        return view('admin.hotels.availability', compact('hotels', 'selectedDate'));
    }
    
    public function bookingReports(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->subDays(30)->toDateString());
        $dateTo = $request->get('date_to', now()->toDateString());
        
        $bookings = Booking::with('user', 'room.hotel')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->orderBy('created_at', 'desc')
            ->paginate(50);
            
        $stats = [
            'total_bookings' => Booking::whereBetween('created_at', [$dateFrom, $dateTo])->count(),
            'total_revenue' => Booking::whereBetween('created_at', [$dateFrom, $dateTo])
                ->where('payment_status', 'paid')->sum('total_amount'),
            'average_booking_value' => Booking::whereBetween('created_at', [$dateFrom, $dateTo])
                ->where('payment_status', 'paid')->avg('total_amount'),
            'by_status' => Booking::whereBetween('created_at', [$dateFrom, $dateTo])
                ->selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status')
        ];
        
        return view('admin.hotels.reports', compact('bookings', 'stats', 'dateFrom', 'dateTo'));
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
