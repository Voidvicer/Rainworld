<?php

namespace App\Http\Controllers;

use App\Models\FerryTrip;
use App\Models\Booking;
use Illuminate\Http\Request;

class FerryTripController extends Controller
{
    public function index(Request $request){
        $query = FerryTrip::whereDate('date','>=', now()->toDateString())
            ->where('blocked',false);
        if($request->filled('date')){
            $query->whereDate('date',$request->get('date'));
        }
        $trips = $query->orderBy('date')->orderBy('depart_time')->paginate(50);
        
        // Check if user has any valid hotel booking
        $hasValidBooking = false;
        if ($request->user()) {
            $hasValidBooking = Booking::where('user_id', $request->user()->id)
                ->where('status', '!=', 'canceled')
                ->whereDate('check_out', '>=', now()->toDateString()) // Any booking that hasn't ended yet
                ->exists();
        }
        
        return view('ferry.trips.index', [
            'trips'=>$trips,
            'selectedDate'=>$request->get('date'),
            'hasValidBooking'=>$hasValidBooking
        ]);
    }

    public function create(){ return view('manage.ferry.trips.create'); }
    public function store(Request $request){
        $data = $request->validate([
            'date'=>'required|date','depart_time'=>'required','origin'=>'required','destination'=>'required','capacity'=>'required|integer|min:1','price'=>'required|numeric|min:0','blocked'=>'nullable|boolean'
        ]);
        $data['blocked'] = (bool)($data['blocked'] ?? false);
        FerryTrip::create($data);
        return back()->with('success','Trip created.');
    }
    public function edit(FerryTrip $ferry_trip){ return view('manage.ferry.trips.edit', ['trip'=>$ferry_trip]); }
    public function update(Request $request, FerryTrip $ferry_trip){
        $data = $request->validate([
            'date'=>'required|date','depart_time'=>'required','origin'=>'required','destination'=>'required','capacity'=>'required|integer|min:1','price'=>'required|numeric|min:0','blocked'=>'nullable|boolean'
        ]);
        $data['blocked'] = (bool)($data['blocked'] ?? false);
        $ferry_trip->update($data);
        return back()->with('success','Updated.');
    }
    public function destroy(FerryTrip $ferry_trip){ $ferry_trip->delete(); return redirect()->route('ferry.trips.index')->with('success','Deleted.'); }
}
