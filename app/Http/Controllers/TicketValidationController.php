<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FerryTicket;
use App\Models\ThemeParkTicket;
use App\Models\Booking;

class TicketValidationController extends Controller
{
    public function ferryForm(){ return view('manage.ferry.validate'); }

    public function ferryCheck(Request $request){
        $data = $request->validate(['code'=>'required']);
        $ticket = FerryTicket::with('trip','user')->where('code',$data['code'])->first();
        if (!$ticket) return back()->withErrors(['code'=>'Ticket not found']);

        $validHotel = Booking::where('user_id',$ticket->user_id)
            ->where('status','!=','canceled')
            ->whereDate('check_in','<=',$ticket->trip->date)
            ->whereDate('check_out','>=',$ticket->trip->date)
            ->exists();

        return back()->with('result',[
            'valid'=> $validHotel && $ticket->status === 'paid',
            'ticket'=>$ticket->only(['id','code','status','total_amount']),
            'trip'=>$ticket->trip->only(['id','date','depart_time','origin','destination'])
        ]);
    }

    public function parkForm(){ return view('manage.park.validate'); }

    public function parkCheck(Request $request){
        $data = $request->validate(['code'=>'required']);
        $ticket = ThemeParkTicket::with('user')->where('code',$data['code'])->first();
        if (!$ticket) return back()->withErrors(['code'=>'Ticket not found']);

        $valid = $ticket->status === 'paid' && $ticket->visit_date === now()->toDateString();
        return back()->with('result',[
            'valid'=>$valid,
            'ticket'=>$ticket->only(['id','code','status','visit_date','quantity','total_amount']),
        ]);
    }
}
