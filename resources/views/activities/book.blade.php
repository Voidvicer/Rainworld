@extends('layouts.app')
@section('content')
<h1 class="text-2xl font-semibold mb-2">Book Activity</h1>
<p class="text-sm text-slate-600 mb-6">
  <strong class="font-semibold text-slate-800">{{ $schedule->activity->name }}</strong>
  on {{ $schedule->date }} ({{ $schedule->start_time }}-{{ $schedule->end_time }}) – Base price
  ${{ number_format($schedule->activity->base_price,2) }} per participant.
</p>

@if($tickets->isEmpty())
  <div class="max-w-xl p-6 rounded-xl bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 shadow mb-8">
    <h2 class="text-lg font-semibold text-amber-800 mb-2">Park Ticket Required</h2>
    <p class="text-sm text-amber-700 mb-4">You need a valid <strong>theme park ticket for {{ $schedule->date }}</strong> before booking this activity.</p>
    <a href="{{ route('park.tickets.index',[ 'date'=>$schedule->date ]) }}" class="inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-500 text-white font-medium px-4 py-2 rounded shadow">
      Get Park Ticket
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
    </a>
  </div>
@else
  <form method="POST" action="{{ route('activity.book.prepare',$schedule) }}" class="max-w-xl space-y-6 bg-white rounded-xl shadow ring-1 ring-slate-200 p-6 relative overflow-hidden" oninput="calcActTotal()">@csrf
    <div class="absolute inset-0 pointer-events-none bg-gradient-to-br from-indigo-50/60 via-white to-sky-50/40"></div>
    <div class="relative space-y-4">
      <div>
        <label class="block text-xs uppercase font-semibold text-slate-500 mb-1">Select Park Ticket (same date)</label>
        <select class="w-full rounded border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 bg-white" name="theme_park_ticket_id" required onchange="updateTicketInfo()">
          <option value="">-- choose ticket --</option>
          @foreach($tickets as $t)
            <option value="{{ $t->id }}" data-qty="{{ $t->quantity }}" data-code="{{ $t->code }}">{{ $t->code }} (Qty {{ $t->quantity }})</option>
          @endforeach
        </select>
        <div id="ticketInfo" class="mt-3 hidden">
          <div class="flex items-center gap-4 p-3 rounded-lg bg-indigo-50 border border-indigo-200">
            <div class="text-indigo-600">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            </div>
            <div class="text-sm">
              <p class="font-semibold text-slate-800">Ticket <span id="tiCode"></span> selected</p>
              <p class="text-slate-600"><span id="tiQty"></span> participants available for this date.</p>
            </div>
          </div>
        </div>
      </div>
      <div>
        <label class="block text-xs uppercase font-semibold text-slate-500 mb-1">Quantity</label>
        <input type="number" class="w-full rounded border-slate-300 focus:border-indigo-500 focus:ring-indigo-500" name="quantity" min="1" value="1">
      </div>
      <div id="actTotal" class="text-sm text-slate-600">Total: ${{ number_format($schedule->activity->base_price,2) }}</div>
      <div class="pt-2">
  <button class="btn-primary w-full">Proceed to Payment</button>
      </div>
    </div>
  </form>
@endif

<script>
  function calcActTotal(){
    const qty=parseInt(document.querySelector('[name=quantity]')?.value||1); const price={{ $schedule->activity->base_price }}; const total=qty*price;
    const el=document.getElementById('actTotal'); if(el) el.innerHTML='Total: <span class="font-semibold text-slate-800">$'+total.toFixed(2)+'</span>';
  }
  function updateTicketInfo(){
    const sel=document.querySelector('select[name=theme_park_ticket_id]');
    const opt=sel.options[sel.selectedIndex];
    if(!opt.value){ document.getElementById('ticketInfo').classList.add('hidden'); return; }
    document.getElementById('tiCode').textContent=opt.dataset.code;
    document.getElementById('tiQty').textContent='Qty '+opt.dataset.qty;
    document.getElementById('ticketInfo').classList.remove('hidden');
  }
  calcActTotal();
</script>
@endsection
