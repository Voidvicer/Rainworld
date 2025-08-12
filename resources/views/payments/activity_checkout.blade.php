@extends('layouts.app')
@section('content')
<h1 class="text-2xl font-semibold mb-6">Activity Payment</h1>
<div class="max-w-xl bg-white rounded-xl shadow ring-1 ring-slate-200 p-6 space-y-6">
  <div>
    <h2 class="text-lg font-semibold text-slate-800 mb-2">Order Summary</h2>
    <ul class="text-sm text-slate-600 space-y-1">
      <li><span class="font-medium text-slate-700">Activity:</span> {{ $schedule->activity->name }}</li>
      <li><span class="font-medium text-slate-700">Date:</span> {{ $schedule->date }} ({{ $schedule->start_time }}-{{ $schedule->end_time }})</li>
      <li><span class="font-medium text-slate-700">Ticket:</span> {{ $ticket->code }} (Qty {{ $ticket->quantity }})</li>
      <li><span class="font-medium text-slate-700">Quantity:</span> {{ $data['quantity'] }}</li>
      <li><span class="font-medium text-slate-700">Price Each:</span> ${{ number_format($data['price_each'],2) }}</li>
      <li><span class="font-medium text-slate-700">Total:</span> <span class="font-semibold text-slate-900">${{ number_format($data['total'],2) }}</span></li>
    </ul>
  </div>
  <form method="POST" action="{{ route('activity.book.store',$schedule) }}" class="space-y-4">@csrf
    <input type="hidden" name="theme_park_ticket_id" value="{{ $ticket->id }}">
    <input type="hidden" name="quantity" value="{{ $data['quantity'] }}">
    <p class="text-xs text-slate-500">Mock payment: click confirm to finalize booking.</p>
    <button class="btn-primary w-full">Confirm & Pay</button>
    <a href="{{ route('activity.book.create',$schedule) }}" class="block text-center text-sm text-slate-500 hover:text-slate-700">Modify</a>
  </form>
</div>
@endsection
