<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Booking;
use App\Models\FerryTicket;
use App\Models\ThemeParkTicket;
use App\Models\Promotion;
use App\Models\Location;

class AdminController extends Controller
{
    public function index(){
        $stats = [
            'users'=>User::count(),
            'hotel_bookings'=>Booking::count(),
            'ferry_tickets'=>FerryTicket::count(),
            'park_tickets'=>ThemeParkTicket::count(),
        ];
        return view('admin.index', compact('stats'));
    }

    public function reports(){
        $hotelRevenue = Booking::where('payment_status','paid')->sum('total_amount');
        $ferryRevenue = FerryTicket::where('status','paid')->sum('total_amount');
        $parkRevenue = ThemeParkTicket::where('status','paid')->sum('total_amount');
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
    ]);
    }

    public function ads(){ $promos = Promotion::latest()->paginate(20); return view('admin.ads', compact('promos')); }
    public function storeAd(){
        request()->validate(['title'=>'required','content'=>'nullable','starts_at'=>'nullable|date','ends_at'=>'nullable|date','active'=>'boolean','image_url'=>'nullable','scope'=>'required|in:global,hotel,ferry,park']);
        $data = request()->only(['title','content','starts_at','ends_at','image_url','scope']);
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
