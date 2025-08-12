@extends('layouts.app')
@section('content')
<div class="bg-gradient-to-br from-indigo-50 via-white to-teal-50 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 rounded-2xl p-6 shadow ring-1 ring-slate-200 dark:ring-slate-700 mb-8">
  <div class="flex items-center gap-3 mb-4">
    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-teal-500 grid place-content-center text-white text-lg shadow">📋</div>
    <div>
      <h1 class="text-2xl font-semibold tracking-tight text-slate-800 dark:text-slate-100">My Hotel Bookings</h1>
      <p class="text-sm text-slate-600 dark:text-slate-400">Manage your accommodation reservations across the island</p>
    </div>
  </div>
</div>

<div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur rounded-2xl shadow-lg ring-1 ring-slate-200 dark:ring-slate-700 overflow-hidden">
  <div class="bg-gradient-to-r from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-700 px-6 py-4 border-b border-slate-200 dark:border-slate-600">
    <h2 class="font-semibold text-slate-800 dark:text-slate-200 flex items-center gap-2">
      <span class="w-6 h-6 rounded bg-indigo-100 dark:bg-indigo-900/50 text-indigo-600 dark:text-indigo-400 grid place-content-center text-sm">🏨</span>
      Reservation History
    </h2>
  </div>
  <div class="overflow-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-400 uppercase text-xs tracking-wide">
        <tr>
          <th class="px-6 py-3 text-left font-semibold">Confirmation</th>
          <th class="px-6 py-3 text-left font-semibold">Accommodation</th>
          <th class="px-6 py-3 text-left font-semibold">Stay Period</th>
          <th class="px-6 py-3 text-center font-semibold">Status</th>
          <th class="px-6 py-3 text-right font-semibold">Total</th>
          <th class="px-6 py-3 text-center font-semibold">Action</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100 dark:divide-slate-700 bg-white/60 dark:bg-slate-900/20">
        @forelse($bookings as $b)
          <tr class="hover:bg-indigo-50/60 dark:hover:bg-indigo-900/20 transition-colors group">
            <td class="px-6 py-4">
              <span class="font-mono text-xs text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-800/50 rounded-md px-2 py-1">{{ $b->confirmation_code }}</span>
            </td>
            <td class="px-6 py-4">
              <div class="text-slate-700 dark:text-slate-300">
                <div class="font-medium">{{ $b->room->hotel->name }}</div>
                <div class="text-xs text-slate-500 dark:text-slate-500">{{ $b->room->name }}</div>
              </div>
            </td>
            <td class="px-6 py-4 text-slate-700 dark:text-slate-300">
              <div class="flex items-center gap-2">
                <span>{{ $b->check_in }}</span>
                <span class="text-slate-400 dark:text-slate-500">→</span>
                <span>{{ $b->check_out }}</span>
              </div>
            </td>
            <td class="px-6 py-4 text-center">
              <span class="px-3 py-1 rounded-full text-xs font-semibold
                @if($b->status==='confirmed') bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-400 ring-1 ring-emerald-200 dark:ring-emerald-800
                @elseif($b->status==='canceled') bg-rose-100 dark:bg-rose-900/50 text-rose-700 dark:text-rose-400 ring-1 ring-rose-200 dark:ring-rose-800
                @else bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-400 ring-1 ring-amber-200 dark:ring-amber-800 @endif">
                {{ ucfirst($b->status) }}
              </span>
            </td>
            <td class="px-6 py-4 text-right font-bold text-lg text-slate-800 dark:text-slate-200">${{ number_format($b->total_amount,2) }}</td>
            <td class="px-6 py-4 text-center">
              @if($b->status!='canceled')
                <form method="POST" action="{{ route('bookings.cancel',$b) }}" onsubmit="return confirm('Are you sure you want to cancel this booking?');" class="inline">@csrf @method('DELETE')
                  <button class="inline-flex items-center gap-1 bg-gradient-to-r from-rose-600 to-red-600 hover:from-rose-500 hover:to-red-500 text-white px-3 py-1.5 rounded-lg text-xs font-semibold shadow transition-all transform hover:scale-[1.02] active:scale-[0.98]">
                    <span>Cancel</span>
                  </button>
                </form>
              @else 
                <span class="text-xs text-slate-400 dark:text-slate-500 bg-slate-100 dark:bg-slate-800 px-2 py-1 rounded">Canceled</span>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="px-6 py-12 text-center">
              <div class="flex flex-col items-center gap-3">
                <div class="w-16 h-16 rounded-full bg-slate-100 dark:bg-slate-800 grid place-content-center text-2xl">🏨</div>
                <div>
                  <p class="text-slate-500 dark:text-slate-400 font-medium">No hotel bookings yet</p>
                  <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Browse our hotels to make your first reservation!</p>
                </div>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="mt-6">{{ $bookings->links() }}</div>
@endsection
