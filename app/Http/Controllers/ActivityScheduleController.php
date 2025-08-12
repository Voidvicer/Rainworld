<?php

namespace App\Http\Controllers;

use App\Models\ActivitySchedule;
use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityScheduleController extends Controller
{
    public function index(){
        $schedules = ActivitySchedule::with('activity')->orderBy('date')->orderBy('start_time')->paginate(20);
        return view('manage.schedules.index', compact('schedules'));
    }

    public function create(){ $activities = Activity::all(); return view('manage.schedules.create', compact('activities')); }
    public function store(Request $request){
        $data = $request->validate([
            'activity_id'=>'required|exists:activities,id','date'=>'required|date','start_time'=>'required','end_time'=>'required','capacity'=>'required|integer|min:1'
        ]);
        ActivitySchedule::create($data);
        return back()->with('success','Schedule created');
    }
    public function edit(ActivitySchedule $activity_schedule){
        $activities = Activity::all();
        return view('manage.schedules.edit', ['schedule'=>$activity_schedule,'activities'=>$activities]);
    }
    public function update(Request $request, ActivitySchedule $activity_schedule){
        $data = $request->validate([
            'activity_id'=>'required|exists:activities,id','date'=>'required|date','start_time'=>'required','end_time'=>'required','capacity'=>'required|integer|min:1'
        ]);
        $activity_schedule->update($data);
        return back()->with('success','Updated');
    }
    public function destroy(ActivitySchedule $activity_schedule){
        $activity_schedule->delete();
        return redirect()->route('manage.activity-schedules.index')->with('success','Deleted');
    }
}
