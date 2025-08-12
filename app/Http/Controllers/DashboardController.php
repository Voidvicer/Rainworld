<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use App\Models\Location;
use App\Models\Hotel;

class DashboardController extends Controller
{
    public function home()
    {
        $promos = Promotion::where('active', true)
            ->where(function($q){ $q->whereNull('starts_at')->orWhere('starts_at','<=',now()); })
            ->where(function($q){ $q->whereNull('ends_at')->orWhere('ends_at','>=',now()); })
            ->latest()->take(5)->get();

        $hotels = Hotel::where('active', true)->with('rooms')->take(6)->get();
        $locations = Location::where('active', true)->get();

        return view('home', compact('promos','hotels','locations'));
    }
}
