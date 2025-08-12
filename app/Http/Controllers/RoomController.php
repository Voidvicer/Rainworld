<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Hotel;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function show(Room $room){ $room->load('hotel'); return view('rooms.show', compact('room')); }

    public function index(){ $rooms = Room::with('hotel')->paginate(20); return view('manage.rooms.index', compact('rooms')); }
    public function create(){ $hotels = Hotel::all(); return view('manage.rooms.create', compact('hotels')); }
    public function store(Request $request){
        $data = $request->validate([
            'hotel_id'=>'required|exists:hotels,id','name'=>'required','type'=>'required',
            'capacity'=>'required|integer|min:1','total_rooms'=>'required|integer|min:1','price_per_night'=>'required|numeric|min:0','amenities'=>'nullable|array'
        ]);
        Room::create($data);
        return redirect()->route('rooms.index')->with('success','Room created');
    }
    public function edit(Room $room){ $hotels = Hotel::all(); return view('manage.rooms.edit', compact('room','hotels')); }
    public function update(Request $request, Room $room){
        $data = $request->validate([
            'hotel_id'=>'required|exists:hotels,id','name'=>'required','type'=>'required',
            'capacity'=>'required|integer|min:1','total_rooms'=>'required|integer|min:1','price_per_night'=>'required|numeric|min:0','amenities'=>'nullable|array'
        ]);
        $room->update($data);
        return back()->with('success','Updated');
    }
    public function destroy(Room $room){ $room->delete(); return redirect()->route('rooms.index')->with('success','Deleted'); }
}
