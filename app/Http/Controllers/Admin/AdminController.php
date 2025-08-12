<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Booking;
use App\Models\FerryTicket;
use App\Models\ThemeParkTicket;
use App\Models\Promotion;
use App\Models\Location;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\FerryTrip;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index(){
        $stats = [
            'total_users' => User::count(),
            'users' => User::count(),
            'active_users' => User::where('active', true)->count(),
            'total_bookings' => Booking::count(),
            'hotel_bookings' => Booking::count(),
            'total_ferry_tickets' => FerryTicket::count(),
            'ferry_tickets' => FerryTicket::count(),
            'park_tickets' => ThemeParkTicket::count(),
            'total_revenue' => $this->getTotalRevenue(),
            'today_checkins' => Booking::whereDate('check_in', today())->where('status', 'confirmed')->count(),
            'today_ferry_passengers' => FerryTicket::whereHas('trip', function($q) {
                $q->whereDate('date', today());
            })->where('status', 'paid')->sum('quantity'),
        ];
        
        $recentActivity = $this->getRecentActivity();
        $quickStats = $this->getQuickStats();
        
        return view('admin.index', compact('stats', 'recentActivity', 'quickStats'));
    }

    public function reports(){
        $hotelRevenue = Booking::where('payment_status','paid')->sum('total_amount');
        $ferryRevenue = FerryTicket::where('status','paid')->sum('total_amount');
        $parkRevenue = ThemeParkTicket::where('status','paid')->sum('total_amount');
        
        // Enhanced analytics with more detailed breakdowns
        $monthlyStats = $this->getMonthlyBreakdown();
        $userGrowth = $this->getUserGrowthData();
        $occupancyRates = $this->getOccupancyRates();
        
        // Simple last 14 days daily revenue series for charts
        $days = collect(range(0,13))->map(fn($i)=>now()->subDays(13-$i)->startOfDay());
        $labels = $days->map(fn($d)=>$d->format('M d'));
        $hotelSeries = $days->map(fn($d)=> Booking::where('payment_status','paid')
            ->whereDate('created_at',$d->toDateString())->sum('total_amount'));
        $ferrySeries = $days->map(fn($d)=> FerryTicket::where('status','paid')
            ->whereDate('created_at',$d->toDateString())->sum('total_amount'));
        $parkSeries = $days->map(fn($d)=> ThemeParkTicket::where('status','paid')
            ->whereDate('created_at',$d->toDateString())->sum('total_amount'));
            
        return view('admin.reports', [
            'hotelRevenue'=>$hotelRevenue,
            'ferryRevenue'=>$ferryRevenue,
            'parkRevenue'=>$parkRevenue,
            'chartLabels'=>$labels,
            'hotelSeries'=>$hotelSeries,
            'ferrySeries'=>$ferrySeries,
            'parkSeries'=>$parkSeries,
            'monthlyStats'=>$monthlyStats,
            'userGrowth'=>$userGrowth,
            'occupancyRates'=>$occupancyRates,
        ]);
    }
    
    private function getTotalRevenue()
    {
        $hotel = Booking::where('payment_status', 'paid')->sum('total_amount');
        $ferry = FerryTicket::where('status', 'paid')->sum('total_amount');
        $park = ThemeParkTicket::where('status', 'paid')->sum('total_amount');
        
        return $hotel + $ferry + $park;
    }
    
    private function getRecentActivity()
    {
        $recentBookings = Booking::with('user', 'room.hotel')
            ->latest()
            ->take(5)
            ->get()
            ->map(function($booking) {
                return [
                    'type' => 'hotel_booking',
                    'description' => "{$booking->user->name} booked {$booking->room->hotel->name}",
                    'time' => $booking->created_at,
                    'amount' => $booking->total_amount
                ];
            });
            
        $recentTickets = FerryTicket::with('user', 'trip')
            ->latest()
            ->take(5)
            ->get()
            ->map(function($ticket) {
                return [
                    'type' => 'ferry_ticket',
                    'description' => "{$ticket->user->name} bought ferry ticket",
                    'time' => $ticket->created_at,
                    'amount' => $ticket->total_amount
                ];
            });
            
        return $recentBookings->concat($recentTickets)
            ->sortByDesc('time')
            ->take(10)
            ->values();
    }
    
    private function getQuickStats()
    {
        return [
            'hotels_count' => Hotel::where('active', true)->count(),
            'rooms_count' => Room::sum('total_rooms'),
            'ferry_trips_today' => FerryTrip::whereDate('date', today())->count(),
            'promotions_active' => Promotion::where('active', true)->count(),
        ];
    }
    
    private function getMonthlyBreakdown()
    {
        $months = collect(range(5, 0))->map(fn($i) => now()->subMonths($i)->startOfMonth());
        
        return $months->map(function($month) {
            return [
                'month' => $month->format('M Y'),
                'hotel_revenue' => Booking::where('payment_status', 'paid')
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->sum('total_amount'),
                'ferry_revenue' => FerryTicket::where('status', 'paid')
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->sum('total_amount'),
                'bookings' => Booking::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count(),
            ];
        });
    }
    
    private function getUserGrowthData()
    {
        $months = collect(range(11, 0))->map(fn($i) => now()->subMonths($i)->startOfMonth());
        
        return $months->map(function($month) {
            return [
                'month' => $month->format('M'),
                'new_users' => User::whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->count(),
            ];
        });
    }
    
    private function getOccupancyRates()
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
                'rate' => $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0
            ];
        });
        
        return $last7Days;
    }

    public function ads(){ $promos = Promotion::latest()->paginate(20); return view('admin.ads', compact('promos')); }
    public function storeAd(){
        request()->validate([
            'title'=>'required',
            'content'=>'nullable',
            'discount_percentage'=>'nullable|numeric|min:0|max:100',
            'starts_at'=>'nullable|date',
            'ends_at'=>'nullable|date',
            'active'=>'boolean',
            'image_url'=>'nullable',
            'scope'=>'required|in:global,hotel,ferry,park'
        ]);
        $data = request()->only(['title','content','discount_percentage','starts_at','ends_at','image_url','scope']);
        $data['active'] = request()->boolean('active');
        Promotion::create($data);
        return back()->with('success','Promotion created.');
    }

    public function map(){ $locations = Location::latest()->paginate(20); return view('admin.map', compact('locations')); }
    public function storeLocation(){
        request()->validate(['name'=>'required','lat'=>'required|numeric','lng'=>'required|numeric','description'=>'nullable','category'=>'nullable','active'=>'boolean']);
        $data = request()->only(['name','lat','lng','description','category']);
        $data['active'] = request()->boolean('active');
        Location::create($data);
        return back()->with('success','Location saved.');
    }
}
