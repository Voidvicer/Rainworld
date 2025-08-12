<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    public function index(){ $hotels = Hotel::where('active',true)->paginate(10); return view('hotels.index', compact('hotels')); }
    public function show(Hotel $hotel){ $hotel->load('rooms'); return view('hotels.show', compact('hotel')); }

    public function create(){ return view('manage.hotels.create'); }
    public function store(Request $request){
        $data = $request->validate(['name'=>'required','description'=>'nullable','address'=>'nullable','contact'=>'nullable','active'=>'boolean']);
        $data['active'] = $request->boolean('active');
        Hotel::create($data);
        return redirect()->route('hotels.index')->with('success','Hotel created.');
    }
    public function edit(Hotel $hotel){ return view('manage.hotels.edit', compact('hotel')); }
    public function update(Request $request, Hotel $hotel){
        $data = $request->validate(['name'=>'required','description'=>'nullable','address'=>'nullable','contact'=>'nullable','active'=>'boolean']);
        $data['active'] = $request->boolean('active');
        $hotel->update($data);
        return back()->with('success','Updated.');
    }
    public function destroy(Hotel $hotel){ $hotel->delete(); return redirect()->route('hotels.index')->with('success','Deleted.'); }
}
