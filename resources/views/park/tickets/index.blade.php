@extends('layouts.app')
@section('content')
<div class="bg-gradient-to-br from-indigo-50 via-white to-teal-50 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 rounded-2xl p-6 shadow ring-1 ring-slate-200 dark:ring-slate-700 mb-8">
  <div class="flex items-center gap-3 mb-4">
    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-teal-500 grid place-content-center text-white text-lg shadow">🎢</div>
    <div>
      <h1 class="text-2xl font-semibold tracking-tight text-slate-800 dark:text-slate-100">Theme Park Tickets</h1>
      <p class="text-sm text-slate-600 dark:text-slate-400">Access to all rides & attractions across our magical island theme park</p>
    </div>
  </div>
  <div class="bg-white/60 dark:bg-slate-800/60 backdrop-blur rounded-xl p-4 border border-slate-200 dark:border-slate-700">
    <div class="grid md:grid-cols-2 gap-4">
      <div class="space-y-2">
        <div class="flex items-center gap-2 text-sm">
          <span class="w-5 h-5 rounded bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 grid place-content-center text-xs">💰</span>
          <span class="text-slate-600 dark:text-slate-400">Price:</span>
          <span class="font-semibold text-lg text-emerald-600 dark:text-emerald-400">$50.00</span>
          <span class="text-xs text-slate-500 dark:text-slate-500">per person per day</span>
        </div>
        <div class="flex items-start gap-2 text-sm">
          <span class="w-5 h-5 rounded bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 grid place-content-center text-xs mt-0.5">ℹ️</span>
          <span class="text-slate-600 dark:text-slate-400 leading-snug">Book your ticket first, then use it to reserve activities & special events throughout the park</span>
        </div>
      </div>
      <form method="POST" action="{{ route('park.tickets.prepare') }}" class="bg-white/80 dark:bg-slate-800/80 backdrop-blur rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-5">@csrf
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
          <div>
            <label class="label text-slate-700 dark:text-slate-300">Visit Date</label>
            <input type="date" name="visit_date" value="{{ request('date') }}" class="input bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-600 text-slate-800 dark:text-slate-200" required min="{{ date('Y-m-d') }}">
          </div>
          <div>
            <label class="label text-slate-700 dark:text-slate-300">Quantity</label>
            <input type="number" name="quantity" class="input bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-600 text-slate-800 dark:text-slate-200 w-full" min="1" value="1" required oninput="calcTotal()">
          </div>
          <div>
            <button class="w-full h-10 px-4 py-2 bg-gradient-to-r from-indigo-600 to-teal-600 hover:from-indigo-500 hover:to-teal-500 text-white font-semibold rounded-lg shadow-md transition-all transform hover:scale-[1.02] active:scale-[0.98] text-sm">
              Pay Now
            </button>
          </div>
          <div class="h-10 flex items-center justify-center bg-slate-50 dark:bg-slate-900/50 rounded-lg px-3 py-2 border border-slate-200 dark:border-slate-700" id="estTotal">
            <div class="flex items-center gap-2">
              <span class="text-slate-500 dark:text-slate-400 text-xs">Total:</span>
              <span class="font-bold text-indigo-600 dark:text-indigo-400">$50.00</span>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur rounded-2xl shadow-lg ring-1 ring-slate-200 dark:ring-slate-700 overflow-hidden">
  <div class="bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-700 px-6 py-4 border-b border-slate-200 dark:border-slate-600">
    <h2 class="font-semibold text-slate-800 dark:text-slate-200 flex items-center gap-2">
      <span class="w-6 h-6 rounded bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 grid place-content-center text-sm">📋</span>
      Your Park Tickets
    </h2>
  </div>
  <div class="overflow-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-400 uppercase text-xs tracking-wide">
        <tr>
          <th class="px-6 py-3 text-left font-semibold">Code</th>
          <th class="px-6 py-3 text-left font-semibold">Date</th>
          <th class="px-6 py-3 text-center font-semibold">Qty</th>
          <th class="px-6 py-3 text-center font-semibold">Status</th>
          <th class="px-6 py-3 text-right font-semibold">Total</th>
          <th class="px-6 py-3 text-center font-semibold">QR Code</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100 dark:divide-slate-700 bg-white/60 dark:bg-slate-900/20">
      @forelse($tickets as $t)
        <tr class="hover:bg-indigo-50/60 dark:hover:bg-indigo-900/20 transition-colors group">
          <td class="px-6 py-4">
            <span class="font-mono text-xs text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-800/50 rounded-md px-2 py-1">{{ $t->code }}</span>
          </td>
          <td class="px-6 py-4 text-slate-700 dark:text-slate-300 font-medium">{{ $t->visit_date }}</td>
          <td class="px-6 py-4 text-center text-slate-700 dark:text-slate-300 font-semibold">{{ $t->quantity }}</td>
          <td class="px-6 py-4 text-center">
            <span class="px-3 py-1 rounded-full text-xs font-semibold 
              @if($t->status==='confirmed') bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-400 ring-1 ring-emerald-200 dark:ring-emerald-800
              @elseif($t->status==='canceled') bg-rose-100 dark:bg-rose-900/50 text-rose-700 dark:text-rose-400 ring-1 ring-rose-200 dark:ring-rose-800
              @elseif($t->status==='completed') bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-400 ring-1 ring-indigo-200 dark:ring-indigo-800
              @elseif($t->status==='expired') bg-gray-100 dark:bg-gray-900/50 text-gray-700 dark:text-gray-400 ring-1 ring-gray-200 dark:ring-gray-800
              @else bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-400 ring-1 ring-amber-200 dark:ring-amber-800 @endif">
              {{ ucfirst($t->status) }}
            </span>
          </td>
          <td class="px-6 py-4 text-right font-bold text-lg text-slate-800 dark:text-slate-200">${{ number_format($t->total_amount,2) }}</td>
          <td class="px-6 py-4 text-center">
            @if($t->qr_path)
              <div class="inline-flex p-2 bg-white dark:bg-slate-900 rounded-lg shadow-sm ring-1 ring-slate-200 dark:ring-slate-700">
                <img src="{{ asset('storage/' . $t->qr_path) }}" alt="QR Code" class="w-16 h-16 object-contain">
              </div>
            @else
              <span class="text-xs text-slate-400 dark:text-slate-500 bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded">Processing...</span>
            @endif
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="6" class="px-6 py-12 text-center">
            <div class="flex flex-col items-center gap-3">
              <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 grid place-content-center text-2xl">🎫</div>
              <div>
                <p class="text-slate-500 dark:text-slate-400 font-medium">No tickets purchased yet</p>
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Book your first park ticket above to get started!</p>
              </div>
            </div>
          </td>
        </tr>
      @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="mt-6">{{ $tickets->links() }}</div>

<script>
  function calcTotal(){
    const qty = document.querySelector('input[name=quantity]').value||1;
    const total = 50 * parseInt(qty);
    document.getElementById('estTotal').innerHTML = '<div class="flex items-center gap-2"><span class="text-slate-500 dark:text-slate-400 text-xs">Total:</span><span class="font-bold text-indigo-600 dark:text-indigo-400">$'+total.toFixed(2)+'</span></div>';
  }
  
  // Initialize on page load
  document.addEventListener('DOMContentLoaded', function() {
    calcTotal();
  });
</script>
@endsection