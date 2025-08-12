@extends('layouts.app')
@section('content')
<div class="bg-gradient-to-br from-indigo-50 via-white to-teal-50 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 rounded-2xl p-6 shadow ring-1 ring-slate-200 dark:ring-slate-700 mb-8">
  <div class="flex items-center gap-3 mb-4">
    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-teal-500 grid place-content-center text-white text-lg shadow">🎫</div>
    <div>
      <h1 class="text-2xl font-semibold tracking-tight text-slate-800 dark:text-slate-100">My Ferry Tickets</h1>
      <p class="text-sm text-slate-600 dark:text-slate-400">Your ferry travel history and active tickets</p>
    </div>
  </div>
</div>

<div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur rounded-2xl shadow-lg ring-1 ring-slate-200 dark:ring-slate-700 overflow-hidden">
  <div class="bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-700 px-6 py-4 border-b border-slate-200 dark:border-slate-600">
    <h2 class="font-semibold text-slate-800 dark:text-slate-200 flex items-center gap-2">
      <span class="w-6 h-6 rounded bg-blue-100 dark:bg-blue-900/50 text-blue-600 dark:text-blue-400 grid place-content-center text-sm">⛴️</span>
      Ferry Ticket History
    </h2>
  </div>
  <div class="overflow-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-400 uppercase text-xs tracking-wide">
        <tr>
          <th class="px-6 py-3 text-left font-semibold">Code</th>
          <th class="px-6 py-3 text-left font-semibold">Trip</th>
          <th class="px-6 py-3 text-center font-semibold">Qty</th>
          <th class="px-6 py-3 text-center font-semibold">Status</th>
          <th class="px-6 py-3 text-right font-semibold">Total</th>
          <th class="px-6 py-3 text-center font-semibold">QR Code</th>
          <th class="px-6 py-3 text-center font-semibold">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100 dark:divide-slate-700 bg-white/60 dark:bg-slate-900/20">
        @forelse($tickets as $t)
          <tr class="hover:bg-indigo-50/60 dark:hover:bg-indigo-900/20 transition-colors group">
            <td class="px-6 py-4">
              <span class="font-mono text-xs text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-800/50 rounded-md px-2 py-1">{{ $t->code }}</span>
            </td>
            <td class="px-6 py-4">
              <div class="text-slate-700 dark:text-slate-300">
                <div class="font-medium">{{ $t->trip->date }} at <span class="font-mono bg-slate-50 dark:bg-slate-800/50 rounded-md px-2 py-1">{{ $t->trip->depart_time }}</span></div>
                <div class="text-xs text-slate-500 dark:text-slate-500 mt-1 flex items-center gap-2">
                  <span class="font-medium">{{ $t->trip->origin }}</span>
                  <span class="text-slate-400 dark:text-slate-500">→</span>
                  <span class="font-medium">{{ $t->trip->destination }}</span>
                </div>
              </div>
            </td>
            <td class="px-6 py-4 text-center text-slate-700 dark:text-slate-300 font-semibold">{{ $t->quantity }}</td>
            <td class="px-6 py-4 text-center">
              <span class="px-3 py-1 rounded-full text-xs font-semibold 
                {{ $t->status==='paid' ? 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-400 ring-1 ring-emerald-200 dark:ring-emerald-800' : 
                   ($t->status==='refunded' ? 'bg-rose-100 dark:bg-rose-900/50 text-rose-700 dark:text-rose-400 ring-1 ring-rose-200 dark:ring-rose-800' : 
                   'bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-400 ring-1 ring-amber-200 dark:ring-amber-800') }}">
                {{ $t->status === 'refunded' ? 'Cancelled' : ucfirst($t->status) }}
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
            <td class="px-6 py-4 text-center">
              @if($t->status === 'paid' && strtotime($t->trip->date . ' ' . $t->trip->depart_time) > time())
                <form action="{{ route('ferry.tickets.cancel', $t->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this ferry ticket?')" class="inline">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="inline-flex items-center gap-1 bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-500 hover:to-red-500 text-white px-3 py-1.5 rounded-lg text-xs font-semibold shadow transition-all transform hover:scale-[1.02] active:scale-[0.98]">
                    <span>Cancel</span>
                  </button>
                </form>
              @else
                <span class="text-xs text-slate-400 dark:text-slate-500">-</span>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="px-6 py-12 text-center">
              <div class="flex flex-col items-center gap-3">
                <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 grid place-content-center text-2xl">⛴️</div>
                <div>
                  <p class="text-slate-500 dark:text-slate-400 font-medium">No ferry tickets yet</p>
                  <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Book your first ferry trip to get started!</p>
                  <a href="{{ route('ferry.trips.index') }}" class="inline-flex items-center gap-2 mt-3 px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white rounded-lg text-sm font-medium shadow-md transition-all transform hover:scale-[1.02] active:scale-[0.98]">
                    <span class="w-4 h-4 rounded bg-white/20 grid place-content-center text-xs">⛴️</span>
                    Book Ferry Tickets
                  </a>
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
@endsection
