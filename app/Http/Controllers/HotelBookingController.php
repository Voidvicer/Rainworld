<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use App\Models\Promotion;
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
        $baseTotal = $nights * $room->price_per_night;
        
        // Apply active hotel/global promotions
        $discount = 0;
        $appliedPromotion = null;
        $activePromotions = Promotion::where('active', true)
            ->where(function($query) {
                $query->where('scope', 'hotel')->orWhere('scope', 'global');
            })
            ->whereNotNull('discount_percentage')
            ->where('discount_percentage', '>', 0)
            ->orderBy('discount_percentage', 'desc')
            ->first();
            
        if ($activePromotions) {
            $discount = ($baseTotal * $activePromotions->discount_percentage) / 100;
            $appliedPromotion = $activePromotions->title;
        }
        
        $total = $baseTotal - $discount;

        $booking = Booking::create([
            'user_id'=>$request->user()->id,'room_id'=>$room->id,
            'check_in'=>$data['check_in'],'check_out'=>$data['check_out'],'guests'=>$data['guests'],
            'status'=>'confirmed','payment_status'=>'paid','total_amount'=>$total,
        ]);
        
        $successMessage = 'Booking confirmed: '.$booking->confirmation_code;
        if ($appliedPromotion) {
            $successMessage .= ' (Applied promotion: '.$appliedPromotion.' - $'.number_format($discount, 2).' discount)';
        }

        return redirect()->route('bookings.index')->with('success', $successMessage);
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
        
        $baseTotal = $nights * $room->price_per_night;
        
        // Apply active hotel/global promotions for preview
        $discount = 0;
        $appliedPromotion = null;
        $activePromotions = Promotion::where('active', true)
            ->where(function($query) {
                $query->where('scope', 'hotel')->orWhere('scope', 'global');
            })
            ->whereNotNull('discount_percentage')
            ->where('discount_percentage', '>', 0)
            ->orderBy('discount_percentage', 'desc')
            ->first();
            
        if ($activePromotions) {
            $discount = ($baseTotal * $activePromotions->discount_percentage) / 100;
            $appliedPromotion = $activePromotions;
        }
        
        $total = $baseTotal - $discount;
        
        $payload = $data + [
            'room_id'=>$room->id,
            'nights'=>$nights,
            'price_per_night'=>$room->price_per_night,
            'base_total'=>$baseTotal,
            'discount'=>$discount,
            'applied_promotion'=>$appliedPromotion,
            'total'=>$total
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
