<?php

namespace App\Http\Controllers;

use App\Models\ActivitySchedule;
use App\Models\ActivityBooking;
use App\Models\ThemeParkTicket;
use Illuminate\Http\Request;

class ActivityBookingController extends Controller
{
    public function index(Request $request){
        $bookings = ActivityBooking::whereHas('ticket', function($q) use ($request){
            $q->where('user_id',$request->user()->id);
        })->with('schedule.activity')->latest()->paginate(20);
        return view('activity_bookings.index', compact('bookings'));
    }

    public function create(ActivitySchedule $schedule, Request $request){
        $tickets = ThemeParkTicket::where('user_id',$request->user()->id)
            ->where('visit_date',$schedule->date)->where('status','paid')->get();
        return view('activities.book', compact('schedule','tickets'));
    }

    public function prepare(ActivitySchedule $schedule, Request $request){
        $data = $request->validate([
            'theme_park_ticket_id'=>'required|exists:theme_park_tickets,id',
            'quantity'=>'required|integer|min:1'
        ]);

        if ($schedule->remaining() < $data['quantity']) {
            return back()->withErrors(['quantity'=>'Not enough capacity left.'])->withInput();
        }

        $ticket = ThemeParkTicket::where('id',$data['theme_park_ticket_id'])
            ->where('user_id',$request->user()->id)
            ->where('visit_date',$schedule->date)
            ->first();
        if(!$ticket){
            return back()->withErrors(['theme_park_ticket_id'=>'Selected ticket invalid for this date.']);
        }

        $payload = [
            'schedule_id'=>$schedule->id,
            'ticket_id'=>$ticket->id,
            'quantity'=>$data['quantity'],
            'price_each'=>$schedule->activity->base_price,
            'total'=>$schedule->activity->base_price * $data['quantity']
        ];
        session(['activity_purchase'=>$payload]);
        return view('payments.activity_checkout',[ 'schedule'=>$schedule,'ticket'=>$ticket,'data'=>$payload ]);
    }

    public function checkout(ActivitySchedule $schedule){
        abort_unless(session()->has('activity_purchase'),403);
        $payload = session('activity_purchase');
        $ticket = ThemeParkTicket::find($payload['ticket_id']);
        return view('payments.activity_checkout',[ 'schedule'=>$schedule,'ticket'=>$ticket,'data'=>$payload ]);
    }

    public function store(ActivitySchedule $schedule, Request $request){
        if(session()->has('activity_purchase')){
            $data = session('activity_purchase');
            session()->forget('activity_purchase');
        } else {
            $data = $request->validate([
                'theme_park_ticket_id'=>'required|exists:theme_park_tickets,id',
                'quantity'=>'required|integer|min:1'
            ]);
            $data['price_each'] = $schedule->activity->base_price;
            $data['total'] = $schedule->activity->base_price * $data['quantity'];
        }

        if ($schedule->remaining() < $data['quantity']) {
            return back()->withErrors(['quantity'=>'Not enough capacity left.']);
        }

        $ticket = ThemeParkTicket::where('id',$data['theme_park_ticket_id'] ?? $data['ticket_id'] ?? null)
            ->where('user_id',$request->user()->id)
            ->where('visit_date',$schedule->date)
            ->first();
        if(!$ticket){
            return back()->withErrors(['theme_park_ticket_id'=>'Selected ticket invalid for this date.']);
        }

        $total = $data['total'] ?? ($schedule->activity->base_price * $data['quantity']);
        $booking = ActivityBooking::create([
            'theme_park_ticket_id'=>$ticket->id,
            'activity_schedule_id'=>$schedule->id,
            'quantity'=>$data['quantity'],
            'status'=>'paid',
            'total_amount'=>$total,
        ]);

        return redirect()->route('activity.bookings.index')
            ->with('success','Activity booked & payment confirmed (Ref #'.$booking->id.').');
    }
}