<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;

class HotelBookingController extends Controller
{
    public function index(Request $request){
        $bookings = Booking::where('user_id',$request->user()->id)->latest()->paginate(15);
        return view('bookings.index', compact('bookings'));
    }

    public function store(Request $request, Room $room){
        // Handle direct confirmation (legacy) or session from prepare step
        if(session()->has('hotel_booking')){
            $data = session('hotel_booking');
            session()->forget('hotel_booking');
        } else {
            $data = $request->validate([
                'check_in'=>'required|date|after_or_equal:today',
                'check_out'=>'required|date|after:check_in',
                'guests'=>'required|integer|min:1'
            ]);
        }

        $overlap = Booking::where('room_id',$room->id)->where('status','!=','canceled')->where(function($q) use ($data){
            $q->whereBetween('check_in', [$data['check_in'],$data['check_out']])
              ->orWhereBetween('check_out', [$data['check_in'],$data['check_out']])
              ->orWhere(function($qq) use ($data){ $qq->where('check_in','<=',$data['check_in'])->where('check_out','>=',$data['check_out']); });
        })->count();

        if ($overlap >= $room->total_rooms) return back()->withErrors(['check_in'=>'No availability for selected dates.'])->withInput();

    $nights = (new \DateTime($data['check_in']))->diff(new \DateTime($data['check_out']))->days;
    $total = $nights * $room->price_per_night;

        $booking = Booking::create([
            'user_id'=>$request->user()->id,'room_id'=>$room->id,
            'check_in'=>$data['check_in'],'check_out'=>$data['check_out'],'guests'=>$data['guests'],
            'status'=>'confirmed','payment_status'=>'paid','total_amount'=>$total,
        ]);

        return redirect()->route('bookings.index')->with('success','Booking confirmed: '.$booking->confirmation_code);
    }

    public function prepare(Request $request, Room $room){
        $data = $request->validate([
            'check_in'=>'required|date|after_or_equal:today',
            'check_out'=>'required|date|after:check_in',
            'guests'=>'required|integer|min:1'
        ]);

        $nights = (new \DateTime($data['check_in']))->diff(new \DateTime($data['check_out']))->days;
        if($nights <= 0){
            return back()->withErrors(['check_in'=>'Invalid date range'])->withInput();
        }
        $payload = $data + [
            'room_id'=>$room->id,
            'nights'=>$nights,
            'price_per_night'=>$room->price_per_night,
            'total'=>$nights * $room->price_per_night
        ];
        session(['hotel_booking'=>$payload]);
        return view('payments.hotel_checkout',[ 'room'=>$room,'data'=>$payload ]);
    }

    public function cancel(Booking $booking, Request $request){
        abort_unless($booking->user_id === $request->user()->id, 403);
        $booking->update(['status'=>'canceled']);
        return back()->with('success','Booking canceled.');
    }

    public function manage(Request $request){
        $query = Booking::with('room.hotel','user');
        
        if($request->filled('status')){
            $query->where('status', $request->get('status'));
        }
        
        $bookings = $query->latest()->paginate(20);
        $currentStatus = $request->get('status');
        
        return view('manage.bookings.index', compact('bookings', 'currentStatus'));
    }

    public function updateStatus(Request $request, Booking $booking){
        $data = $request->validate(['status'=>'required|in:pending,confirmed,canceled,completed']);
        $booking->update($data);
        return back()->with('success','Status updated');
    }
}
