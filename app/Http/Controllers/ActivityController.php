<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function listPublic(){
        $activities = Activity::where('active',true)->with('schedules')->paginate(15);
        return view('activities.public', compact('activities'));
    }

    public function index(){ $activities = Activity::paginate(20); return view('manage.activities.index', compact('activities')); }
    public function create(){ return view('manage.activities.create'); }
    public function store(Request $request){
        $data = $request->validate([
            'name'=>'required','type'=>'required|in:ride,show,beach','description'=>'nullable','base_price'=>'required|numeric|min:0','location_id'=>'nullable|integer','active'=>'boolean'
        ]);
        $data['active'] = $request->boolean('active');
        Activity::create($data);
        return back()->with('success','Activity created');
    }
    public function edit(Activity $activity){ return view('manage.activities.edit', compact('activity')); }
    public function update(Request $request, Activity $activity){
        $data = $request->validate([
            'name'=>'required','type'=>'required|in:ride,show,beach','description'=>'nullable','base_price'=>'required|numeric|min:0','location_id'=>'nullable|integer','active'=>'boolean'
        ]);
        $data['active'] = $request->boolean('active');
        $activity->update($data);
        return back()->with('success','Updated');
    }
    public function destroy(Activity $activity){ $activity->delete(); return redirect()->route('manage.activities.index')->with('success','Deleted'); }
}
