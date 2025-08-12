<?php

namespace App\Http\Controllers;

use App\Models\FerryTicket;
use App\Models\FerryTrip;
use App\Models\Booking;
use Illuminate\Http\Request;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\SvgWriter;

class FerryTicketController extends Controller
{
    public function index(Request $request){
        $tickets = FerryTicket::with('trip')->where('user_id',$request->user()->id)->latest()->paginate(15);
        return view('ferry.tickets.index', compact('tickets'));
    }

    public function prepare(Request $request, FerryTrip $trip){
        $data = $request->validate(['quantity'=>'required|integer|min:1']);
        if ($trip->remainingSeats() < $data['quantity']) {
            return back()->withErrors(['quantity'=>'Not enough seats remaining.']);
        }
        $data['trip_id']=$trip->id; $data['price']=$trip->price; $data['date']=$trip->date; $data['time']=$trip->depart_time; $data['total']=$trip->price*$data['quantity'];
        session(['ferry_purchase'=>$data]);
        return view('payments.ferry_checkout',['trip'=>$trip,'data'=>$data]);
    }

    public function checkout(FerryTrip $trip){
        abort_unless(session()->has('ferry_purchase'),403);
        return view('payments.ferry_checkout',['trip'=>$trip,'data'=>session('ferry_purchase')]);
    }

    public function store(Request $request, FerryTrip $trip){
        if(session()->has('ferry_purchase')){
            $data = session('ferry_purchase');
            session()->forget('ferry_purchase');
        } else {
            $data = $request->validate(['quantity'=>'required|integer|min:1']);
        }

        $hasBooking = Booking::where('user_id',$request->user()->id)
            ->where('status','!=','canceled')
            ->whereDate('check_in','<=',$trip->date)
            ->whereDate('check_out','>=',$trip->date)
            ->exists();
        if (!$hasBooking) return back()->withErrors(['quantity'=>'You must have a valid hotel booking covering the trip date to purchase ferry tickets.']);

        if ($trip->remainingSeats() < $data['quantity']) return back()->withErrors(['quantity'=>'Not enough seats remaining.']);

        $total = $trip->price * $data['quantity'];
        $ticket = FerryTicket::create([
            'user_id'=>$request->user()->id,'ferry_trip_id'=>$trip->id,
            'quantity'=>$data['quantity'],'status'=>'paid','total_amount'=>$total,
        ]);

        $payload = json_encode(['type'=>'ferry','code'=>$ticket->code,'trip'=>$trip->id]);
        $qr = new QrCode(
            $payload,
            new Encoding('UTF-8'),
            ErrorCorrectionLevel::High,
            300,
            10
        );
        $result = (new SvgWriter())->write($qr);
        $path = 'public/qrcodes/ferry_'.$ticket->id.'.svg';
        \Storage::put($path, $result->getString());
        $ticket->update(['qr_path'=>str_replace('public/','storage/',$path)]);

        return redirect()->route('ferry.tickets.index')->with('success','Ferry ticket purchased & payment confirmed.');
    }

    public function bulkPrepare(Request $request) {
        // Handle GET requests (redirects from validation failures)
        if ($request->isMethod('GET')) {
            return redirect()->route('ferry.trips.index')->withErrors(['error' => 'Invalid access. Please use the ferry booking form.']);
        }
        
        $bookingData = json_decode($request->input('booking_data'), true);
        
        if (!$bookingData || !isset($bookingData['trips']) || empty($bookingData['trips'])) {
            return back()->withErrors(['error' => 'Invalid booking data.']);
        }

        // Check if user has valid hotel booking for the travel date
        $hasBooking = Booking::where('user_id', $request->user()->id)
            ->where('status', '!=', 'canceled')
            ->whereDate('check_in', '<=', $bookingData['date'])
            ->whereDate('check_out', '>=', $bookingData['date'])
            ->exists();
            
        if (!$hasBooking) {
            return redirect()->route('ferry.trips.index')->withErrors(['error' => 'You must have a valid hotel booking covering the trip date to purchase ferry tickets.']);
        }

        $total = 0;
        $tripDetails = [];

        foreach ($bookingData['trips'] as $tripData) {
            $total += $tripData['price'] * $tripData['quantity'];
            $tripDetails[] = $tripData;
        }

        session(['ferry_bulk_purchase' => [
            'date' => $bookingData['date'],
            'trips' => $tripDetails,
            'total' => $total
        ]]);

        return view('payments.ferry_bulk_checkout', [
            'bookingData' => $bookingData,
            'total' => $total
        ]);
    }

    public function bulkStore(Request $request) {
        $purchaseData = session('ferry_bulk_purchase');
        
        if (!$purchaseData) {
            return redirect()->route('ferry.trips.index')->withErrors(['error' => 'Session expired. Please try again.']);
        }

        // Check if user has valid hotel booking for the travel date
        $hasBooking = Booking::where('user_id', $request->user()->id)
            ->where('status', '!=', 'canceled')
            ->whereDate('check_in', '<=', $purchaseData['date'])
            ->whereDate('check_out', '>=', $purchaseData['date'])
            ->exists();
            
        if (!$hasBooking) {
            return back()->withErrors(['error' => 'You must have a valid hotel booking covering the trip date to purchase ferry tickets.']);
        }

        // Create tickets for each trip
        foreach ($purchaseData['trips'] as $tripData) {
            // Find the ferry trip by ID
            $trip = FerryTrip::find($tripData['trip_id']);
            
            if (!$trip) {
                return back()->withErrors(['error' => 'One or more selected trips are no longer available.']);
            }
            
            // Check if there are enough seats
            if ($trip->remainingSeats() < $tripData['quantity']) {
                return back()->withErrors(['error' => 'Not enough seats remaining for ' . $trip->origin . ' to ' . $trip->destination . ' at ' . $trip->depart_time . '.']);
            }

            $total = $tripData['price'] * $tripData['quantity'];
            $ticket = FerryTicket::create([
                'user_id' => $request->user()->id,
                'ferry_trip_id' => $trip->id,
                'quantity' => $tripData['quantity'],
                'status' => 'paid',
                'total_amount' => $total,
            ]);

            // Generate QR code
            $payload = json_encode(['type' => 'ferry', 'code' => $ticket->code, 'trip' => $trip->id]);
            $qr = new QrCode(
                $payload,
                new Encoding('UTF-8'),
                ErrorCorrectionLevel::High,
                300,
                10
            );
            $result = (new SvgWriter())->write($qr);
            $path = 'public/qrcodes/ferry_' . $ticket->id . '.svg';
            \Storage::put($path, $result->getString());
            $ticket->update(['qr_path' => str_replace('public/', 'storage/', $path)]);
        }

        session()->forget('ferry_bulk_purchase');
        return redirect()->route('ferry.tickets.index')->with('success', 'Ferry tickets purchased & payment confirmed.');
    }

    public function cancel(Request $request, FerryTicket $ferry_ticket) {
        // Check if the ticket belongs to the authenticated user
        if ($ferry_ticket->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized action.');
        }
        
        // Check if the ticket is still cancellable (not past the departure time and status is paid)
        $tripDateTime = strtotime($ferry_ticket->trip->date . ' ' . $ferry_ticket->trip->depart_time);
        if ($tripDateTime <= time() || $ferry_ticket->status !== 'paid') {
            return back()->withErrors(['error' => 'This ticket cannot be canceled.']);
        }
        
        // Update ticket status to refunded (since 'cancelled' is not in the enum)
        $ferry_ticket->update(['status' => 'refunded']);
        
        return back()->with('success', 'Ferry ticket has been cancelled successfully.');
    }

    public function reports(){
        $tickets = FerryTicket::with('trip','user')->latest()->paginate(20);
        return view('manage.ferry.reports', compact('tickets'));
    }

    public function updateStatus(Request $request, FerryTicket $ferryTicket){
        $request->validate(['status' => 'required|in:confirmed,canceled,completed,expired']);
        $ferryTicket->update(['status' => $request->status]);
        return back()->with('success', 'Ferry ticket status updated.');
    }
}
