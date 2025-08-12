<?php

namespace App\Http\Controllers;

use App\Models\ThemeParkTicket;
use Illuminate\Http\Request;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Writer\SvgWriter;

class ThemeParkTicketController extends Controller
{
    public function index(Request $request){
        $tickets = ThemeParkTicket::where('user_id',$request->user()->id)->latest()->paginate(15);
        return view('park.tickets.index', compact('tickets'));
    }

    public function prepare(Request $request){
        $data = $request->validate(['visit_date'=>'required|date|after_or_equal:today','quantity'=>'required|integer|min:1']);
        $price = 50; $total = $price * $data['quantity'];
        session(['park_purchase'=>$data + ['price'=>$price,'total'=>$total]]);
        return view('payments.park_checkout',['data'=>session('park_purchase')]);
    }

    public function checkout(){
        abort_unless(session()->has('park_purchase'),403);
        return view('payments.park_checkout',['data'=>session('park_purchase')]);
    }

    public function store(Request $request){
        // Allow coming directly (old flow) or from checkout session
        if(session()->has('park_purchase')){
            $data = session('park_purchase');
            session()->forget('park_purchase');
        } else {
            $data = $request->validate(['visit_date'=>'required|date|after_or_equal:today','quantity'=>'required|integer|min:1']);
            $data['price']=50; $data['total']=50*$data['quantity'];
        }

        $price = $data['price'] ?? 50; // fallback
        $total = $data['total'] ?? ($price * $data['quantity']);

        $ticket = ThemeParkTicket::create([
            'user_id'=>$request->user()->id,'visit_date'=>$data['visit_date'],
            'quantity'=>$data['quantity'],'status'=>'paid','total_amount'=>$total,
        ]);

        // Generate & store QR code (Endroid QR Code v6 immutable API)
        $payload = json_encode([
            'type' => 'park',
            'code' => $ticket->code,
            'date' => $ticket->visit_date,
        ]);
        $qr = new QrCode(
            $payload,
            new Encoding('UTF-8'),
            ErrorCorrectionLevel::High,
            300, // size
            10   // margin
        );
        $result = (new SvgWriter())->write($qr);
        $path = 'public/qrcodes/park_'.$ticket->id.'.svg';
        \Storage::put($path, $result->getString());
        $ticket->update(['qr_path'=>str_replace('public/','storage/',$path)]);

        return redirect()->route('park.tickets.index')->with('success','Theme park ticket purchased & payment confirmed.');
    }

    public function reports(){
        $tickets = ThemeParkTicket::with('user')->latest()->paginate(20);
        return view('manage.park.reports', compact('tickets'));
    }

    public function updateStatus(Request $request, ThemeParkTicket $themeParkTicket){
        $request->validate(['status' => 'required|in:confirmed,canceled,completed,expired']);
        $themeParkTicket->update(['status' => $request->status]);
        return back()->with('success', 'Ticket status updated.');
    }
}
